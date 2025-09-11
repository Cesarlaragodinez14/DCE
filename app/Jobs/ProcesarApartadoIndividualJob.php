<?php

namespace App\Jobs;

use App\Models\Auditorias;
use App\Models\AuditoriaEtiqueta;
use App\Models\CatEtiqueta;
use App\Models\Apartado;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;
use Throwable;

class ProcesarApartadoIndividualJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutos de timeout
    public $tries = 5; // 5 intentos máximo
    public $backoff = [30, 60, 120, 300]; // Backoff progresivo: 30s, 1m, 2m, 5m
    public $maxExceptions = 3; // Máximo 3 excepciones antes de fallar
    
    protected $auditoriaId;
    protected $apartadoPadreId;
    protected $nombreEtiqueta;
    protected $respuestaIA;
    protected $razonAsignacion;
    protected $comentarioFuente;
    protected $procesadoPor;
    
    // Contador de reintentos específico para duplicados
    protected $intentosDuplicado = 0;
    protected $maxIntentosDuplicado = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        int $auditoriaId,
        int $apartadoPadreId,
        string $nombreEtiqueta,
        string $respuestaIA,
        string $razonAsignacion,
        string $comentarioFuente,
        ?int $procesadoPor = null,
        int $intentosDuplicado = 0
    ) {
        $this->auditoriaId = $auditoriaId;
        $this->apartadoPadreId = $apartadoPadreId;
        $this->nombreEtiqueta = $nombreEtiqueta;
        $this->respuestaIA = $respuestaIA;
        $this->razonAsignacion = $razonAsignacion;
        $this->comentarioFuente = $comentarioFuente;
        $this->procesadoPor = $procesadoPor;
        $this->intentosDuplicado = $intentosDuplicado;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("🔄 Procesando apartado individual {$this->apartadoPadreId} (intento duplicado: {$this->intentosDuplicado})");
        
        try {
            // Verificar que la auditoría aún existe
            $auditoria = Auditorias::find($this->auditoriaId);
            if (!$auditoria) {
                Log::error("❌ Auditoría {$this->auditoriaId} no encontrada para apartado {$this->apartadoPadreId}");
                $this->fail(new Exception("Auditoría no encontrada"));
                return;
            }

            // Verificar que el apartado aún existe
            $apartado = Apartado::find($this->apartadoPadreId);
            if (!$apartado) {
                Log::error("❌ Apartado {$this->apartadoPadreId} no encontrado");
                $this->fail(new Exception("Apartado no encontrado"));
                return;
            }

            // Buscar la etiqueta en el catálogo
            $etiqueta = CatEtiqueta::where('nombre', $this->nombreEtiqueta)->first();
            if (!$etiqueta) {
                Log::error("❌ Etiqueta '{$this->nombreEtiqueta}' no encontrada en catálogo para apartado {$this->apartadoPadreId}");
                $this->crearEtiquetaFallbackSeguro($auditoria, "Etiqueta no encontrada: {$this->nombreEtiqueta}");
                return;
            }

            // Intentar crear o actualizar la etiqueta dentro de una transacción
            DB::transaction(function () use ($auditoria, $etiqueta) {
                $this->crearEtiquetaConBloqueo($auditoria, $etiqueta);
            });

            Log::info("✅ Apartado individual {$this->apartadoPadreId} procesado exitosamente");

        } catch (\Illuminate\Database\QueryException $e) {
            $this->manejarErrorBaseDatos($e);
        } catch (Exception $e) {
            Log::error("❌ Error procesando apartado individual {$this->apartadoPadreId}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Crear etiqueta con bloqueo para evitar race conditions
     */
    private function crearEtiquetaConBloqueo(Auditorias $auditoria, CatEtiqueta $etiqueta): void
    {
        // Usar SELECT FOR UPDATE para bloquear la fila y evitar race conditions
        $etiquetaExistente = AuditoriaEtiqueta::where('auditoria_id', $this->auditoriaId)
            ->where('apartado_id', $this->apartadoPadreId)
            ->lockForUpdate()
            ->first();

        if ($etiquetaExistente) {
            Log::info("ℹ️ Etiqueta existente encontrada para apartado {$this->apartadoPadreId}, actualizando...");
            
            // Solo actualizar si es diferente para evitar actualizaciones innecesarias
            if ($etiquetaExistente->etiqueta_id != $etiqueta->id || 
                $etiquetaExistente->respuesta_ia != $this->respuestaIA) {
                
                $etiquetaExistente->update([
                    'etiqueta_id' => $etiqueta->id,
                    'respuesta_ia' => $this->respuestaIA,
                    'razon_asignacion' => $this->razonAsignacion,
                    'comentario_fuente' => $this->comentarioFuente,
                    'procesado_en' => now(),
                    'updated_at' => now()
                ]);
                
                Log::info("✅ Etiqueta actualizada para apartado {$this->apartadoPadreId}: {$this->nombreEtiqueta}");
            } else {
                Log::info("ℹ️ Etiqueta para apartado {$this->apartadoPadreId} ya está actualizada");
            }
        } else {
            // Crear nueva etiqueta
            AuditoriaEtiqueta::create([
                'auditoria_id' => $this->auditoriaId,
                'etiqueta_id' => $etiqueta->id,
                'apartado_id' => $this->apartadoPadreId,
                'respuesta_ia' => $this->respuestaIA,
                'razon_asignacion' => $this->razonAsignacion,
                'comentario_fuente' => $this->comentarioFuente,
                'confianza_ia' => $this->nombreEtiqueta === 'Procesado' ? 0.1 : 0.85,
                'procesado_en' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            Log::info("✅ Nueva etiqueta creada para apartado {$this->apartadoPadreId}: {$this->nombreEtiqueta}");
        }
    }

    /**
     * Manejar errores específicos de base de datos
     */
    private function manejarErrorBaseDatos(\Illuminate\Database\QueryException $e): void
    {
        if ($e->getCode() == 23000 && strpos($e->getMessage(), '1062') !== false) {
            // Error de constraint de integridad (duplicado)
            $this->intentosDuplicado++;
            
            Log::warning("⚠️ Error de duplicado para apartado {$this->apartadoPadreId} (intento {$this->intentosDuplicado}/{$this->maxIntentosDuplicado})");
            
            if ($this->intentosDuplicado < $this->maxIntentosDuplicado) {
                // Reencolar con mayor delay
                $delay = $this->intentosDuplicado * 60; // 1min, 2min, 3min
                
                Log::info("🔄 Reencolando apartado {$this->apartadoPadreId} con delay de {$delay} segundos");
                
                static::dispatch(
                    $this->auditoriaId,
                    $this->apartadoPadreId,
                    $this->nombreEtiqueta,
                    $this->respuestaIA,
                    $this->razonAsignacion,
                    $this->comentarioFuente,
                    $this->procesadoPor,
                    $this->intentosDuplicado
                )->delay(now()->addSeconds($delay));
                
            } else {
                // Después de varios intentos, crear etiqueta de fallback
                Log::warning("⚠️ Máximo de intentos de duplicado alcanzado para apartado {$this->apartadoPadreId}, creando fallback");
                
                try {
                    $auditoria = Auditorias::find($this->auditoriaId);
                    if ($auditoria) {
                        $this->crearEtiquetaFallbackSeguro($auditoria, "Error de duplicado después de {$this->maxIntentosDuplicado} intentos");
                    }
                } catch (Exception $fallbackException) {
                    Log::error("❌ Error creando fallback para apartado {$this->apartadoPadreId}: {$fallbackException->getMessage()}");
                    throw $e; // Re-lanzar el error original
                }
            }
        } else {
            // Otros errores de base de datos
            Log::error("❌ Error de base de datos para apartado {$this->apartadoPadreId}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Crear etiqueta fallback de manera segura
     */
    private function crearEtiquetaFallbackSeguro(Auditorias $auditoria, string $motivoError): void
    {
        $etiquetaProcesado = CatEtiqueta::where('nombre', 'Procesado')->first();
        
        if (!$etiquetaProcesado) {
            Log::error("❌ No se encontró la etiqueta 'Procesado' en el catálogo");
            return;
        }

        $apartado = Apartado::find($this->apartadoPadreId);
        $nombreApartado = $apartado ? $apartado->nombre : "Apartado {$this->apartadoPadreId}";
        
        $razonFallback = "Apartado '{$nombreApartado}' marcado como procesado debido a: {$motivoError}. Requiere revisión manual.";
        $comentarioFallback = "Procesamiento automático fallido: {$nombreApartado}";

        try {
            // Verificar si ya existe una etiqueta (incluso de fallback)
            $etiquetaExistente = AuditoriaEtiqueta::where('auditoria_id', $this->auditoriaId)
                ->where('apartado_id', $this->apartadoPadreId)
                ->first();

            if ($etiquetaExistente) {
                Log::info("ℹ️ Ya existe etiqueta para apartado {$this->apartadoPadreId}, no se crea fallback");
                return;
            }

            AuditoriaEtiqueta::create([
                'auditoria_id' => $this->auditoriaId,
                'etiqueta_id' => $etiquetaProcesado->id,
                'apartado_id' => $this->apartadoPadreId,
                'respuesta_ia' => $motivoError,
                'razon_asignacion' => $razonFallback,
                'comentario_fuente' => $comentarioFallback,
                'confianza_ia' => 0.1,
                'procesado_en' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            Log::info("💾 Etiqueta fallback creada para apartado {$this->apartadoPadreId}");
            
        } catch (Exception $e) {
            Log::error("❌ Error creando etiqueta fallback para apartado {$this->apartadoPadreId}: {$e->getMessage()}");
            // No re-lanzar el error para evitar loops infinitos
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Throwable $exception): void
    {
        Log::error("❌ ProcesarApartadoIndividualJob falló para apartado {$this->apartadoPadreId}", [
            'auditoria_id' => $this->auditoriaId,
            'apartado_padre_id' => $this->apartadoPadreId,
            'nombre_etiqueta' => $this->nombreEtiqueta,
            'intentos_duplicado' => $this->intentosDuplicado,
            'error' => $exception->getMessage(),
            'procesado_por' => $this->procesadoPor,
            'trace' => $exception->getTraceAsString()
        ]);
        
        // Intentar crear una etiqueta de fallback como último recurso
        try {
            $auditoria = Auditorias::find($this->auditoriaId);
            if ($auditoria) {
                $this->crearEtiquetaFallbackSeguro($auditoria, "Job falló: " . $exception->getMessage());
            }
        } catch (Exception $e) {
            Log::error("❌ Error creando fallback final para apartado {$this->apartadoPadreId}: {$e->getMessage()}");
        }
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return [
            'apartado:' . $this->apartadoPadreId,
            'auditoria:' . $this->auditoriaId,
            'etiqueta:' . $this->nombreEtiqueta
        ];
    }
} 