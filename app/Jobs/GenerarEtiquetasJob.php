<?php

namespace App\Jobs;

use App\Models\Auditorias;
use App\Models\AuditoriaEtiqueta;
use App\Models\CatEtiqueta;
use App\Models\ChecklistApartado;
use App\Models\ChecklistApartadoHistory;
use App\Http\Controllers\AIController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Exception;
use Throwable;

class GenerarEtiquetasJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 1 hora de timeout (reducido de 3 horas)
    public $tries = 3; // 3 intentos (mantenido)
    public $backoff = [30, 60]; // Backoff más rápido: 30s, 1min (antes era 2min, 5min)
    
    protected $auditoriaId;
    protected $procesadoPor;
    protected $esManual;
    protected $modoRapido; // NUEVO: Modo rápido para optimizar velocidad
    protected $ultraRapido; // NUEVO: Modo ultra rápido sin pausas
    
    // NUEVO: Array para trackear apartados ya procesados en esta ejecución
    protected $apartadosProcesados = [];

    /**
     * Create a new job instance.
     */
    public function __construct(?int $auditoriaId = null, ?int $procesadoPor = null, bool $esManual = false, bool $modoRapido = false, bool $ultraRapido = false)
    {
        $this->auditoriaId = $auditoriaId;
        $this->procesadoPor = $procesadoPor;
        $this->esManual = $esManual;
        $this->modoRapido = $modoRapido; // NUEVO: Permitir modo rápido
        $this->ultraRapido = $ultraRapido; // NUEVO: Permitir modo ultra rápido
        $this->apartadosProcesados = []; // Inicializar array
        
        // Configurar memoria según el modo
        if (is_null($auditoriaId)) {
            $memoriaRequerida = $this->ultraRapido ? '2048M' : ($this->modoRapido ? '1024M' : '512M');
            ini_set('memory_limit', $memoriaRequerida); // Más memoria según el modo
        } else {
            ini_set('memory_limit', $this->modoRapido || $this->ultraRapido ? '512M' : '256M');
        }
        
        // Ajustar timeout según el modo
        if ($this->ultraRapido) {
            $this->timeout = 1800; // 30 minutos para ultra rápido
        } elseif ($this->modoRapido) {
            $this->timeout = 2700; // 45 minutos para modo rápido
        }
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $memoryInicial = round(memory_get_usage(true) / 1024 / 1024, 2);
        $memoryLimit = ini_get('memory_limit');
        
        Log::info('🚀 Iniciando GenerarEtiquetasJob (v2.0 - Optimizado por Lotes)', [
            'auditoria_id' => $this->auditoriaId,
            'es_manual' => $this->esManual,
            'procesado_por' => $this->procesadoPor,
            'memoria_inicial' => $memoryInicial . ' MB',
            'limite_memoria' => $memoryLimit
        ]);

        try {
            if ($this->auditoriaId) {
                // Procesar una auditoría específica
                $this->procesarAuditoriaEspecificaOptimizada($this->auditoriaId);
            } else {
                // Procesar todas las auditorías pendientes en lotes
                $this->procesarAuditoriasPendientesOptimizadas();
            }

            $memoryFinal = round(memory_get_usage(true) / 1024 / 1024, 2);
            $memoriaUsada = $memoryFinal - $memoryInicial;
            
            Log::info('✅ GenerarEtiquetasJob (v2.0) completado exitosamente', [
                'memoria_final' => $memoryFinal . ' MB',
                'memoria_usada' => $memoriaUsada . ' MB'
            ]);

        } catch (Exception $e) {
            $memoryError = round(memory_get_usage(true) / 1024 / 1024, 2);
            
            Log::error('❌ Error en GenerarEtiquetasJob (v2.0): ' . $e->getMessage(), [
                'memoria_en_error' => $memoryError . ' MB',
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e; // Re-lanzar para que el sistema de colas lo maneje
        } finally {
            // Limpieza final de memoria
            gc_collect_cycles();
        }
    }

    /**
     * Procesar una auditoría específica (v2.0 optimizada)
     */
    private function procesarAuditoriaEspecificaOptimizada(int $auditoriaId): void
    {
        $auditoria = Auditorias::with(['checklistApartados.apartado'])->find($auditoriaId);

        if (!$auditoria) {
            Log::warning("⚠️ Auditoría no encontrada: {$auditoriaId}");
            return;
        }

        Log::info("🔍 Procesando auditoría específica optimizada: {$auditoria->clave_de_accion}");
        
        // NUEVO: Limpiar duplicados existentes si es procesamiento manual
        if ($this->esManual) {
            $this->limpiarDuplicadosAuditoria($auditoria);
        }
        
        $this->procesarAuditoriaOptimizada($auditoria);
    }

    /**
     * Limpiar etiquetas duplicadas de una auditoría
     */
    private function limpiarDuplicadosAuditoria(Auditorias $auditoria): void
    {
        Log::info("🧹 Iniciando limpieza de duplicados para auditoría: {$auditoria->clave_de_accion}");
        
        // Encontrar duplicados por apartado y etiqueta
        $duplicados = AuditoriaEtiqueta::where('auditoria_id', $auditoria->id)
            ->select('checklist_apartado_id', 'etiqueta_id', \DB::raw('COUNT(*) as cantidad'))
            ->groupBy('checklist_apartado_id', 'etiqueta_id')
            ->having('cantidad', '>', 1)
            ->get();
        
        $totalLimpiados = 0;
        
        foreach ($duplicados as $duplicado) {
            // Obtener todas las instancias duplicadas
            $instanciasDuplicadas = AuditoriaEtiqueta::where('auditoria_id', $auditoria->id)
                ->where('checklist_apartado_id', $duplicado->checklist_apartado_id)
                ->where('etiqueta_id', $duplicado->etiqueta_id)
                ->orderBy('created_at', 'desc') // Mantener la más reciente
                ->get();
            
            // Eliminar todas excepto la primera (más reciente)
            for ($i = 1; $i < $instanciasDuplicadas->count(); $i++) {
                $instanciasDuplicadas[$i]->delete();
                $totalLimpiados++;
            }
        }
        
        if ($totalLimpiados > 0) {
            Log::info("🧹 Eliminados {$totalLimpiados} duplicados de la auditoría {$auditoria->clave_de_accion}");
        } else {
            Log::info("✅ No se encontraron duplicados en auditoría {$auditoria->clave_de_accion}");
        }
    }

    /**
     * Procesar auditorías pendientes (v2.0 optimizada)
     */
    private function procesarAuditoriasPendientesOptimizadas(): void
    {
        // Contar total de auditorías pendientes
        $totalAuditorias = Auditorias::whereHas('checklistApartados', function($query) {
                $query->where(function($subQuery) {
                    $subQuery->whereNotNull('observaciones')->where('observaciones', '!=', '');
                })->orWhere(function($subQuery) {
                    $subQuery->whereNotNull('comentarios_uaa')->where('comentarios_uaa', '!=', '');
                });
            })->count();

        Log::info("📊 Total de auditorías con comentarios: {$totalAuditorias}");

        // Configurar parámetros según el modo
        if ($this->ultraRapido) {
            $loteSize = 50; // Lotes muy grandes
            $pausaEntreAuditorias = 0; // Sin pausas
            $pausaEntreLotes = 0; // Sin pausas entre lotes
        } elseif ($this->modoRapido) {
            $loteSize = 20; // Lotes grandes
            $pausaEntreAuditorias = 2; // Pausas muy cortas
            $pausaEntreLotes = 5; // Pausas cortas entre lotes
        } else {
            $loteSize = 10; // Lotes normales
            $pausaEntreAuditorias = 5; // Pausas normales
            $pausaEntreLotes = 15; // Pausas normales entre lotes
        }
        
        Log::info("⚡ Configuración de velocidad:", [
            'modo_rapido' => $this->modoRapido,
            'ultra_rapido' => $this->ultraRapido,
            'lote_size' => $loteSize,
            'pausa_auditorias' => $pausaEntreAuditorias,
            'pausa_lotes' => $pausaEntreLotes
        ]);

        $loteActual = 0;
        $totalProcesadas = 0;

        // Procesar en lotes optimizados
        Auditorias::whereHas('checklistApartados', function($query) {
            $query->where(function($subQuery) {
                $subQuery->whereNotNull('observaciones')->where('observaciones', '!=', '');
            })->orWhere(function($subQuery) {
                $subQuery->whereNotNull('comentarios_uaa')->where('comentarios_uaa', '!=', '');
            });
        })
        ->with(['checklistApartados.apartado'])
        ->chunk($loteSize, function($auditorias) use (&$loteActual, &$totalProcesadas, $totalAuditorias, $pausaEntreAuditorias, $pausaEntreLotes) {
            $loteActual++;
            $tiempoInicioLote = microtime(true);
            Log::info("🚀 Procesando lote {$loteActual} ({$auditorias->count()} auditorías)");

            foreach ($auditorias as $auditoria) {
                $totalProcesadas++;
                
                // Verificar si tiene etiquetas pendientes
                if (!$auditoria->tieneEtiquetasPendientes()) {
                    Log::info("⏭️ [{$totalProcesadas}/{$totalAuditorias}] Auditoría {$auditoria->clave_de_accion} ya procesada, saltando...");
                    continue;
                }

                try {
                    $tiempoInicio = microtime(true);
                    Log::info("🎯 [{$totalProcesadas}/{$totalAuditorias}] Procesando auditoría: {$auditoria->clave_de_accion}");
                    $this->procesarAuditoriaOptimizada($auditoria);
                    
                    // Liberar memoria explícitamente
                    unset($auditoria->checklistApartados);
                    
                    $tiempoTranscurrido = round(microtime(true) - $tiempoInicio, 2);
                    Log::info("✅ [{$totalProcesadas}/{$totalAuditorias}] Auditoría {$auditoria->clave_de_accion} completada en {$tiempoTranscurrido}s");
                    
                    // Pausa optimizada entre auditorías
                    if ($pausaEntreAuditorias > 0) {
                        sleep($pausaEntreAuditorias);
                    }
                    
                } catch (Exception $e) {
                    Log::error("❌ [{$totalProcesadas}/{$totalAuditorias}] Error procesando auditoría {$auditoria->clave_de_accion}: {$e->getMessage()}");
                    continue;
                }
            }

            // Liberar memoria al final de cada lote
            gc_collect_cycles();
            $memoryUsage = round(memory_get_usage(true) / 1024 / 1024, 2);
            $tiempoLote = round(microtime(true) - $tiempoInicioLote, 2);
            Log::info("🧹 Lote {$loteActual} completado en {$tiempoLote}s. Memoria: {$memoryUsage} MB");
            
            // Pausa optimizada entre lotes
            if ($loteActual > 1 && $pausaEntreLotes > 0) {
                Log::info("⏳ Pausa entre lotes ({$pausaEntreLotes} segundos)...");
                sleep($pausaEntreLotes);
            }
        });

        Log::info("🎉 Procesamiento masivo completado. Total procesadas: {$totalProcesadas}/{$totalAuditorias}");
    }

    /**
     * Procesar una auditoría optimizada (v3.0 - DIRECTAMENTE por apartado padre)
     */
    private function procesarAuditoriaOptimizada(Auditorias $auditoria): void
    {
        // NUEVA LÓGICA SIMPLIFICADA: Obtener apartados padre únicos con comentarios
        $apartadosConComentarios = $auditoria->checklistApartados
            ->filter(function($apartado) {
                return (!empty($apartado->observaciones) && trim($apartado->observaciones) !== '') ||
                       (!empty($apartado->comentarios_uaa) && trim($apartado->comentarios_uaa) !== '');
            });

        if ($apartadosConComentarios->isEmpty()) {
            Log::info("ℹ️ No hay comentarios para procesar en la auditoría: {$auditoria->clave_de_accion}");
            return;
        }

        // CLAVE: Agrupar directamente por apartado padre (apartado_id)
        $apartadosPorPadre = $apartadosConComentarios->groupBy(function($apartado) {
            return $apartado->apartado_id ?? 0;
        });

        Log::info("📂 Apartados padre únicos encontrados: " . $apartadosPorPadre->count() . " (de " . $apartadosConComentarios->count() . " instancias individuales)");

        // Procesar cada apartado padre individual
        $contador = 0;
        $total = $apartadosPorPadre->count();
        
        foreach ($apartadosPorPadre as $apartadoPadreId => $apartadosHijos) {
            $contador++;
            Log::info("🔄 Procesando apartado {$contador}/{$total} (ID: {$apartadoPadreId})");
            try {
                $this->procesarApartadoPadreUnico($auditoria, $apartadoPadreId, $apartadosHijos);
                
                // Pausa optimizada entre apartados
                if ($this->ultraRapido) {
                    $pausaApartados = 0; // Sin pausas en ultra rápido
                } elseif ($this->modoRapido) {
                    $pausaApartados = 1; // Pausa mínima en modo rápido
                } else {
                    $pausaApartados = 3; // Pausa normal
                }
                
                if ($pausaApartados > 0) {
                    sleep($pausaApartados);
                }
                
            } catch (Exception $e) {
                // Manejo optimizado de rate limits
                if (strpos($e->getMessage(), '429') !== false || strpos($e->getMessage(), 'rate limit') !== false) {
                    Log::warning("⏳ Rate limit detectado para apartado {$apartadoPadreId}, aplicando backoff optimizado...");
                    
                    // Backoff optimizado según el modo
                    if ($this->ultraRapido) {
                        $waitTimes = [5, 10]; // Backoff ultra agresivo
                    } elseif ($this->modoRapido) {
                        $waitTimes = [15, 30]; // Backoff rápido
                    } else {
                        $waitTimes = [30, 60]; // Backoff normal
                    }
                    
                    foreach ($waitTimes as $index => $waitTime) {
                        Log::info("⏰ Esperando {$waitTime} segundos (intento " . ($index + 1) . "/2)...");
                        sleep($waitTime);
                        
                        try {
                            $this->procesarApartadoPadreUnico($auditoria, $apartadoPadreId, $apartadosHijos);
                            Log::info("✅ Apartado {$apartadoPadreId} procesado exitosamente después de rate limit");
                            
                            // Pausa extra optimizada después de rate limit
                            if ($this->ultraRapido) {
                                $pausaPostRateLimit = 0; // Sin pausa extra en ultra rápido
                            } elseif ($this->modoRapido) {
                                $pausaPostRateLimit = 3; // Pausa mínima en modo rápido
                            } else {
                                $pausaPostRateLimit = 10; // Pausa normal
                            }
                            
                            if ($pausaPostRateLimit > 0) {
                                sleep($pausaPostRateLimit);
                            }
                            break; // Salir del bucle si fue exitoso
                        } catch (Exception $retryException) {
                            if ($index === count($waitTimes) - 1) {
                                // Último intento fallido
                                Log::error("❌ Apartado {$apartadoPadreId} falló después de todos los reintentos: {$retryException->getMessage()}");
                                continue 2; // Continuar con el siguiente apartado
                            }
                            Log::warning("⚠️ Reintento " . ($index + 1) . " falló, esperando más tiempo...");
                        }
                    }
                } else {
                    Log::error("❌ Error no relacionado con rate limit en apartado {$apartadoPadreId}: {$e->getMessage()}");
                    continue;
                }
            }
            
            // Liberar memoria periódicamente
            if ($contador % 3 === 0) {
                gc_collect_cycles();
                $memoryUsage = round(memory_get_usage(true) / 1024 / 1024, 2);
                Log::info("🧠 Memoria utilizada: {$memoryUsage} MB");
            }
        }
    }

    /**
     * Procesar un apartado padre único (nueva función simplificada)
     */
    private function procesarApartadoPadreUnico(Auditorias $auditoria, int $apartadoPadreId, $apartadosHijos): void
    {
        $primerApartado = $apartadosHijos->first();
        $nombreApartado = $primerApartado->apartado->nombre ?? "Apartado {$apartadoPadreId}";
        
        Log::info("🏷️ Procesando apartado padre ID {$apartadoPadreId}: " . substr($nombreApartado, 0, 100) . "... ({$apartadosHijos->count()} instancias)");

        // NUEVA LÓGICA OPTIMIZADA: Verificar si ya existe etiqueta Y si está actualizada
        $etiquetaExistente = AuditoriaEtiqueta::where('auditoria_id', $auditoria->id)
            ->where('apartado_id', $apartadoPadreId)
            ->first();

        if ($etiquetaExistente && !$this->esManual) {
            // Verificar si los comentarios han sido modificados después de la última etiqueta
            $ultimaModificacionComentarios = $apartadosHijos->max('updated_at');
            $fechaEtiqueta = $etiquetaExistente->procesado_en ?? $etiquetaExistente->created_at;

            if ($ultimaModificacionComentarios && $fechaEtiqueta && $ultimaModificacionComentarios <= $fechaEtiqueta) {
                // Los comentarios NO han sido modificados después de la etiqueta, saltar
                Log::info("ℹ️ Apartado padre {$apartadoPadreId} ya tiene etiqueta actualizada: {$etiquetaExistente->etiqueta->nombre} (última modificación: {$ultimaModificacionComentarios}, etiqueta: {$fechaEtiqueta})");
                return;
            } else {
                // Los comentarios SÍ han sido modificados, necesita reprocesamiento
                Log::info("🔄 Apartado padre {$apartadoPadreId} tiene etiqueta pero comentarios modificados. Reprocesando... (última modificación: {$ultimaModificacionComentarios}, etiqueta: {$fechaEtiqueta})");
            }
        }

        // Recopilar TODOS los comentarios de todas las instancias del apartado padre
        $todosLosComentarios = [];
        foreach ($apartadosHijos as $apartadoHijo) {
            $historialApartado = $this->recopilarHistorialApartado($auditoria, $apartadoHijo);
            $todosLosComentarios = array_merge($todosLosComentarios, $historialApartado);
        }

        // Remover duplicados
        $comentariosUnicos = collect($todosLosComentarios)
            ->unique(function($item) {
                return md5($item['contenido']);
            })
            ->sortBy('fecha')
            ->values()
            ->toArray();

        if (empty($comentariosUnicos)) {
            Log::info("ℹ️ No hay comentarios únicos para apartado padre {$apartadoPadreId}");
            return;
        }

        // Generar etiqueta para este apartado padre
        $this->generarEtiquetaParaApartadoPadre($auditoria, $apartadoPadreId, $nombreApartado, $comentariosUnicos);
    }

    /**
     * Recopilar todo el historial de comentarios de un apartado
     */
    private function recopilarHistorialApartado(Auditorias $auditoria, ChecklistApartado $apartado): array
    {
        $historial = [];
        
        // 1. Comentarios actuales del apartado
        if (!empty($apartado->observaciones) && trim($apartado->observaciones) !== '') {
            $historial[] = [
                'fecha' => $apartado->updated_at->format('Y-m-d H:i'),
                'tipo' => 'Observaciones actuales',
                'contenido' => trim($apartado->observaciones),
                'usuario' => 'Sistema'
            ];
        }
        
        if (!empty($apartado->comentarios_uaa) && trim($apartado->comentarios_uaa) !== '') {
            $historial[] = [
                'fecha' => $apartado->updated_at->format('Y-m-d H:i'),
                'tipo' => 'Comentarios UAA actuales',
                'contenido' => trim($apartado->comentarios_uaa),
                'usuario' => 'Sistema'
            ];
        }

        // 2. Historial de cambios del apartado
        $historialCambios = ChecklistApartadoHistory::where('checklist_apartado_id', $apartado->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($historialCambios as $cambio) {
            $changes = json_decode($cambio->changes, true);
            
            // Historial de observaciones
            if (isset($changes['after']['observaciones']) && !empty(trim($changes['after']['observaciones']))) {
                $historial[] = [
                    'fecha' => $cambio->created_at->format('Y-m-d H:i'),
                    'tipo' => 'Observaciones (historial)',
                    'contenido' => trim($changes['after']['observaciones']),
                    'usuario' => $cambio->user->name ?? 'Usuario desconocido'
                ];
            }
            
            // Historial de comentarios UAA
            if (isset($changes['after']['comentarios_uaa']) && !empty(trim($changes['after']['comentarios_uaa']))) {
                $historial[] = [
                    'fecha' => $cambio->created_at->format('Y-m-d H:i'),
                    'tipo' => 'Comentarios UAA (historial)',
                    'contenido' => trim($changes['after']['comentarios_uaa']),
                    'usuario' => $cambio->user->name ?? 'Usuario desconocido'
                ];
            }
        }

        // Remover duplicados y ordenar por fecha
        $historial = collect($historial)
            ->unique(function($item) {
                return md5($item['contenido']);
            })
            ->sortBy('fecha')
            ->values()
            ->toArray();

        return $historial;
    }

    /**
     * Generar etiqueta para un apartado padre único (v3.0 simplificado)
     */
    private function generarEtiquetaParaApartadoPadre(Auditorias $auditoria, int $apartadoPadreId, string $nombreApartado, array $comentarios): void
    {
        Log::info("🎯 Generando etiqueta para apartado padre {$apartadoPadreId}: " . substr($nombreApartado, 0, 100));
        Log::info("📝 Total de comentarios únicos: " . count($comentarios));

        // Crear contenido mejorado para IA con más contexto
        $contenidoIA = "📋 **AUDITORÍA:** {$auditoria->clave_de_accion}\n";
        $contenidoIA .= "🏢 **ENTE FISCALIZADO:** " . ($auditoria->enteFiscalizado->nombre ?? 'No especificado') . "\n";
        $contenidoIA .= "📂 **APARTADO:** {$nombreApartado}\n\n";
        
        $contenidoIA .= "💬 **COMENTARIOS Y OBSERVACIONES CONSOLIDADOS:**\n\n";
        
        // Análisis de patrones comunes
        $patronesEncontrados = [];
        $palabrasClave = ['documento', 'falta', 'incompleto', 'error', 'fecha', 'monto', 'normativa', 'irregular', 'vencido'];
        
        foreach ($comentarios as $index => $comentario) {
            $numero = $index + 1;
            $fecha = $comentario['fecha'] ?? 'Sin fecha';
            $tipo = $comentario['tipo'] ?? 'Sin tipo';
            $contenido = trim($comentario['contenido']);
            
            // Detectar patrones en el contenido
            foreach ($palabrasClave as $palabra) {
                if (stripos($contenido, $palabra) !== false) {
                    $patronesEncontrados[$palabra] = true;
                }
            }
            
            $contenidoIA .= "**{$numero}.** [{$fecha}] *{$tipo}*\n";
            $contenidoIA .= "📝 {$contenido}\n\n";
        }
        
        // Agregar contexto de patrones detectados
        if (!empty($patronesEncontrados)) {
            $contenidoIA .= "🔍 **PATRONES DETECTADOS:** " . implode(', ', array_keys($patronesEncontrados)) . "\n\n";
        }
        
        $contenidoIA .= "📊 **ESTADÍSTICAS:**\n";
        $contenidoIA .= "- Total de comentarios: " . count($comentarios) . "\n";
        $contenidoIA .= "- Tipos de comentarios: " . implode(', ', array_unique(array_column($comentarios, 'tipo'))) . "\n";

        try {
            // Generar etiqueta con IA
            $respuestaIA = $this->llamarClaudeApi($contenidoIA);
            
            if (!$respuestaIA || trim($respuestaIA) === '') {
                throw new Exception("Respuesta vacía de la API");
            }

            Log::info("🤖 Respuesta IA: " . substr($respuestaIA, 0, 200) . "...");

            // Procesar respuesta
            $etiquetasParsear = $this->parseearRespuestaIARobusta($respuestaIA);
            
            if (empty($etiquetasParsear)) {
                Log::warning("⚠️ No se pudo parsear la respuesta de IA para apartado {$apartadoPadreId}");
                
                // Crear etiqueta "Procesado" como fallback
                $this->crearEtiquetaFallback($auditoria, $apartadoPadreId, $respuestaIA);
                return;
            }

            // Tomar la primera etiqueta parseada
            $etiquetaSeleccionada = $etiquetasParsear[0];
            Log::info("✅ Etiqueta seleccionada: {$etiquetaSeleccionada}");

            // Crear etiqueta usando la nueva estructura
            $this->crearEtiquetaOptimizada($auditoria, $apartadoPadreId, $etiquetaSeleccionada, $respuestaIA);

        } catch (Exception $e) {
            Log::error("❌ Error generando etiqueta para apartado {$apartadoPadreId}: {$e->getMessage()}");
            
            // Crear etiqueta fallback en caso de error
            $this->crearEtiquetaFallback($auditoria, $apartadoPadreId, "Error: " . $e->getMessage());
        }
    }

    /**
     * Crear etiqueta optimizada usando nueva estructura (v3.0)
     */
    private function crearEtiquetaOptimizada(Auditorias $auditoria, int $apartadoPadreId, string $nombreEtiqueta, string $respuestaIA): void
    {
        // Buscar etiqueta en catálogo
        $etiqueta = \App\Models\CatEtiqueta::where('nombre', $nombreEtiqueta)->first();
        
        if (!$etiqueta) {
            Log::error("❌ Etiqueta '{$nombreEtiqueta}' no encontrada en catálogo");
            $this->crearEtiquetaFallback($auditoria, $apartadoPadreId, $respuestaIA);
            return;
        }

        // Obtener información del apartado para comentario fuente y razón
        $apartado = \App\Models\Apartado::find($apartadoPadreId);
        $nombreApartado = $apartado ? $apartado->nombre : "Apartado {$apartadoPadreId}";
        
        // Recopilar comentarios del apartado para crear fuente y razón detalladas
        $comentariosApartado = \App\Models\ChecklistApartado::where('apartado_id', $apartadoPadreId)
            ->where('auditoria_id', $auditoria->id)
            ->where(function($query) {
                $query->where(function($subQuery) {
                    $subQuery->whereNotNull('observaciones')
                             ->where('observaciones', '!=', '');
                })->orWhere(function($subQuery) {
                    $subQuery->whereNotNull('comentarios_uaa')
                             ->where('comentarios_uaa', '!=', '');
                });
            })
            ->get();

        // Crear comentario fuente consolidado
        $comentariosFuente = [];
        foreach ($comentariosApartado as $comentario) {
            if (!empty(trim($comentario->observaciones ?? ''))) {
                $comentariosFuente[] = "Observación: " . trim($comentario->observaciones ?? '');
            }
            if (!empty(trim($comentario->comentarios_uaa ?? ''))) {
                $comentariosFuente[] = "Comentario UAA: " . trim($comentario->comentarios_uaa ?? '');
            }
        }
        
        $comentarioFuente = !empty($comentariosFuente) ? 
            implode(' | ', array_slice($comentariosFuente, 0, 3)) : // Máximo 3 comentarios
            "Apartado: {$nombreApartado}";

        // Crear razón de asignación específica
        $razonAsignacion = "IA analizó {$comentariosApartado->count()} comentario(s) del apartado '{$nombreApartado}' y determinó la categoría '{$nombreEtiqueta}' basándose en patrones identificados en el contenido";

        try {
            // Verificar si ya existe
            $etiquetaExistente = \App\Models\AuditoriaEtiqueta::where('auditoria_id', $auditoria->id)
                ->where('apartado_id', $apartadoPadreId)
                ->first();

            if ($etiquetaExistente) {
                Log::info("ℹ️ Ya existe etiqueta para apartado {$apartadoPadreId}, actualizando...");
                
                $etiquetaExistente->update([
                    'etiqueta_id' => $etiqueta->id,
                    'respuesta_ia' => $respuestaIA,
                    'razon_asignacion' => $razonAsignacion,
                    'comentario_fuente' => $comentarioFuente,
                    'procesado_en' => now(), // IMPORTANTE: Actualizar fecha de procesamiento
                    'updated_at' => now()
                ]);
                
                $accion = "actualizada";
            } else {
                // Crear nueva usando la nueva estructura
                \App\Models\AuditoriaEtiqueta::create([
                    'auditoria_id' => $auditoria->id,
                    'etiqueta_id' => $etiqueta->id,
                    'apartado_id' => $apartadoPadreId, // NUEVA ESTRUCTURA
                    'respuesta_ia' => $respuestaIA,
                    'razon_asignacion' => $razonAsignacion,
                    'comentario_fuente' => $comentarioFuente,
                    'confianza_ia' => 0.85, // Confianza alta para etiquetas del catálogo
                    'procesado_en' => now(), // IMPORTANTE: Establecer fecha de procesamiento
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                $accion = "creada";
            }

            Log::info("✅ Etiqueta {$accion} para apartado {$apartadoPadreId}: {$nombreEtiqueta}");
            Log::info("📝 Razón: " . substr($razonAsignacion, 0, 100) . "...");
            
        } catch (\Illuminate\Database\QueryException $e) {
            // Manejar errores de constraint de integridad específicamente
            if ($e->getCode() == 23000 && strpos($e->getMessage(), '1062') !== false) {
                Log::warning("⚠️ Error de duplicado detectado para apartado {$apartadoPadreId}: {$e->getMessage()}");
                $this->manejarErrorDuplicado($auditoria, $apartadoPadreId, $nombreEtiqueta, $respuestaIA, $razonAsignacion, $comentarioFuente);
            } else {
                // Re-lanzar otros errores de base de datos
                Log::error("❌ Error de base de datos para apartado {$apartadoPadreId}: {$e->getMessage()}");
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error("❌ Error general creando etiqueta para apartado {$apartadoPadreId}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Manejar errores de duplicado reencolando el apartado
     */
    private function manejarErrorDuplicado(Auditorias $auditoria, int $apartadoPadreId, string $nombreEtiqueta, string $respuestaIA, string $razonAsignacion, string $comentarioFuente): void
    {
        // Obtener información del apartado
        $apartado = \App\Models\Apartado::find($apartadoPadreId);
        $nombreApartado = $apartado ? $apartado->nombre : "Apartado {$apartadoPadreId}";
        
        Log::info("🔄 Reencolando apartado {$apartadoPadreId} ({$nombreApartado}) debido a error de duplicado");
        
        // Crear job específico para este apartado con delay
        \App\Jobs\ProcesarApartadoIndividualJob::dispatch(
            $auditoria->id,
            $apartadoPadreId,
            $nombreEtiqueta,
            $respuestaIA,
            $razonAsignacion,
            $comentarioFuente,
            $this->procesadoPor
        )->delay(now()->addSeconds(rand(30, 120))); // Delay aleatorio entre 30 segundos y 2 minutos
        
        Log::info("✅ Apartado {$apartadoPadreId} reencolado exitosamente con delay aleatorio");
    }

    /**
     * Crear etiqueta fallback usando la nueva estructura
     */
    private function crearEtiquetaFallback(Auditorias $auditoria, int $apartadoPadreId, string $respuestaIA): void
    {
        $etiquetaProcesado = \App\Models\CatEtiqueta::where('nombre', 'Procesado')->first();
        
        if (!$etiquetaProcesado) {
            Log::error("❌ No se encontró la etiqueta 'Procesado' en el catálogo");
            return;
        }

        // Obtener información del apartado para contexto
        $apartado = \App\Models\Apartado::find($apartadoPadreId);
        $nombreApartado = $apartado ? $apartado->nombre : "Apartado {$apartadoPadreId}";
        
        // Recopilar comentarios para el comentario fuente
        $comentariosApartado = \App\Models\ChecklistApartado::where('apartado_id', $apartadoPadreId)
            ->where('auditoria_id', $auditoria->id)
            ->where(function($query) {
                $query->where(function($subQuery) {
                    $subQuery->whereNotNull('observaciones')
                             ->where('observaciones', '!=', '');
                })->orWhere(function($subQuery) {
                    $subQuery->whereNotNull('comentarios_uaa')
                             ->where('comentarios_uaa', '!=', '');
                });
            })
            ->get();

        $comentarioFuente = "Apartado procesado automáticamente: {$nombreApartado} ({$comentariosApartado->count()} comentario(s) analizados)";
        
        // Determinar el tipo de error para la razón
        $tipoError = "error de procesamiento";
        if (strpos($respuestaIA, "rate limit") !== false || strpos($respuestaIA, "429") !== false) {
            $tipoError = "límite de API alcanzado";
        } elseif (strpos($respuestaIA, "Error:") !== false) {
            $tipoError = "error de API";
        } elseif (strpos($respuestaIA, "No se encontró coincidencia") !== false) {
            $tipoError = "respuesta no categorizable";
        }
        
        $razonAsignacion = "Apartado '{$nombreApartado}' marcado como procesado debido a {$tipoError}. Requiere revisión manual para asignación de etiqueta específica";

        try {
            $etiquetaCreada = \App\Models\AuditoriaEtiqueta::create([
                'auditoria_id' => $auditoria->id,
                'etiqueta_id' => $etiquetaProcesado->id,
                'apartado_id' => $apartadoPadreId, // NUEVA ESTRUCTURA
                'respuesta_ia' => $respuestaIA,
                'razon_asignacion' => $razonAsignacion,
                'comentario_fuente' => $comentarioFuente,
                'confianza_ia' => 0.1, // Baja confianza para fallbacks
                'procesado_en' => now(), // IMPORTANTE: Establecer fecha de procesamiento
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info("💾 Etiqueta fallback creada para apartado {$apartadoPadreId}: Procesado (ID: {$etiquetaCreada->id})");
            Log::info("📝 Tipo de error: {$tipoError}");
            
        } catch (\Illuminate\Database\QueryException $e) {
            // Manejar errores de constraint de integridad en fallback también
            if ($e->getCode() == 23000 && strpos($e->getMessage(), '1062') !== false) {
                Log::warning("⚠️ Error de duplicado en fallback para apartado {$apartadoPadreId}, reencolando...");
                $this->manejarErrorDuplicado($auditoria, $apartadoPadreId, 'Procesado', $respuestaIA, $razonAsignacion, $comentarioFuente);
            } else {
                Log::error("❌ Error de base de datos creando fallback para apartado {$apartadoPadreId}: {$e->getMessage()}");
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error("❌ Error general creando fallback para apartado {$apartadoPadreId}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Construir el prompt para la IA (método legacy - mantener para compatibilidad)
     */
    private function construirPromptParaIA(
        string $comentario,
        string $nombreApartado,
        string $claveAccion,
        string $tipoComentario,
        array $etiquetasExistentes
    ): string {
        $etiquetasContext = empty($etiquetasExistentes) ? 
            "No hay etiquetas existentes aún." : 
            "Etiquetas existentes: " . implode(', ', $etiquetasExistentes);

        return "Categoriza este comentario de auditoría con máximo 2 etiquetas.

COMENTARIO: {$comentario}
APARTADO: {$nombreApartado}

ETIQUETAS DISPONIBLES: {$etiquetasContext}

Responde JSON:
{\"etiquetas\":[{\"nombre\":\"etiqueta\",\"razon\":\"motivo breve\",\"confianza\":0.8}]}";
    }

    /**
     * Llamar a la API de IA directamente (v3.0)
     */
    private function llamarClaudeApi(string $contenido): string
    {
        $aiController = new \App\Http\Controllers\AIController();
        
        // Crear prompt optimizado para IA
        $prompt = $this->construirPromptClaudeV3($contenido);
        
        // Simular request
        $request = new \Illuminate\Http\Request();
        $request->merge([
            'message' => $prompt,
            'provider' => 'groq',
            'model' => env('GROQ_DEF_MODEL', 'llama3-8b-8192'),
            'includeContext' => false
        ]);

        // Usar reflexión para acceder al método privado
        $reflection = new \ReflectionClass($aiController);
        $method = $reflection->getMethod('getAIResponse');
        $method->setAccessible(true);

        return $method->invoke(
            $aiController,
            $prompt,
            'groq',
            env('GROQ_DEF_MODEL', 'llama3-8b-8192'),
            false,
            [],
            null
        );
    }

    /**
     * Construir prompt optimizado para IA v3.0
     */
    private function construirPromptClaudeV3(string $contenido): string
    {
        // Obtener todas las etiquetas del catálogo
        $etiquetasCatalogo = \App\Models\CatEtiqueta::all()->pluck('nombre')->toArray();
        $etiquetasTexto = implode('", "', $etiquetasCatalogo);

        return "Eres un auditor experto analizando comentarios de auditoría gubernamental. ANALIZA DETALLADAMENTE el contenido y asigna la etiqueta MÁS ESPECÍFICA del catálogo oficial.

ETIQUETAS OFICIALES DISPONIBLES:
\"{$etiquetasTexto}\"

CONTENIDO A ANALIZAR:
{$contenido}

METODOLOGÍA DE ANÁLISIS:
1. **IDENTIFICA PROBLEMAS ESPECÍFICOS**: Busca issues concretos en el contenido
2. **PRIORIZA ETIQUETAS ESPECÍFICAS**: Usa \"Documentación faltante\", \"Error de cálculo\", etc. cuando aplique
3. **USA \"Procesado\" SOLO SI**: No hay problemas detectados o comentarios vacíos

PATRONES A DETECTAR:
📋 **DOCUMENTACIÓN**:
- \"falta\", \"no se adjunta\", \"no presenta\" → \"Documentación faltante\"
- \"incompleto\", \"parcial\", \"faltan datos\" → \"Documentación incompleta\"
- \"erróneo\", \"incorrecto\", \"no corresponde\" → \"Documentación incorrecta\"

💰 **MONTOS Y CÁLCULOS**:
- \"error de cálculo\", \"mal calculado\", \"suma incorrecta\" → \"Error de cálculo\"
- \"monto diferente\", \"no coincide\" → \"Monto inconsistente\"

📅 **FECHAS Y PLAZOS**:
- \"fuera de tiempo\", \"extemporáneo\", \"tardío\" → \"Plazo incumplido\"
- \"fecha vencida\", \"caducado\" → \"Fecha vencida\"

⚖️ **CUMPLIMIENTO**:
- \"no cumple normativa\", \"viola reglamento\" → \"Incumplimiento normativo\"
- \"proceso irregular\", \"procedimiento incorrecto\" → \"Proceso irregular\"

EJEMPLOS ESPECÍFICOS:
- \"No se incluye información\" → \"Documentación faltante\"
- \"Se requiere justificación\" → \"Documentación incompleta\"
- \"Monto no justificado\" → \"Monto inconsistente\"
- \"Sin observaciones\" → \"Procesado\"

REGLA CRÍTICA: USA \"Procesado\" ÚNICAMENTE cuando NO hay problemas identificados. Si detectas CUALQUIER problema, usa la etiqueta específica correspondiente.

Responde ÚNICAMENTE con el nombre exacto de la etiqueta.";
    }

    /**
     * Parsear respuesta de IA robusta (v3.0)
     */
    private function parseearRespuestaIARobusta(string $respuestaIA): array
    {
        $respuesta = trim($respuestaIA);
        
        // Obtener etiquetas del catálogo para validación
        $etiquetasCatalogo = \App\Models\CatEtiqueta::all()->pluck('nombre')->map(function($nombre) {
            return strtolower(trim($nombre));
        })->toArray();

        // Limpiar respuesta
        $respuesta = preg_replace('/["""\'\']/', '', $respuesta);
        $respuesta = preg_replace('/\s+/', ' ', $respuesta);
        $respuesta = trim($respuesta);

        Log::info("🔍 Parseando respuesta: '{$respuesta}'");
        Log::info("📋 Etiquetas catálogo: " . implode(', ', $etiquetasCatalogo));

        // Mapeo SOLO para respuestas claramente inadecuadas
        $respuestasEspeciales = [
            'no aplica', 'n/a', 'na'
        ];
        
        $respuestaLower = strtolower($respuesta);
        
        // SOLO mapear respuestas claramente problemáticas
        foreach ($respuestasEspeciales as $especial) {
            if ($respuestaLower === $especial) { // Coincidencia EXACTA, no parcial
                Log::info("🔄 Mapeando respuesta problemática '{$respuesta}' a 'Procesado'");
                $etiquetaProcesado = \App\Models\CatEtiqueta::where('nombre', 'Procesado')->first();
                if ($etiquetaProcesado) {
                    return [$etiquetaProcesado->nombre];
                }
            }
        }

        // Buscar coincidencia exacta en catálogo
        
        foreach ($etiquetasCatalogo as $etiquetaCatalogo) {
            if ($respuestaLower === $etiquetaCatalogo) {
                $etiquetaReal = \App\Models\CatEtiqueta::whereRaw('LOWER(nombre) = ?', [$etiquetaCatalogo])->first();
                if ($etiquetaReal) {
                    Log::info("✅ Coincidencia exacta encontrada: {$etiquetaReal->nombre}");
                    return [$etiquetaReal->nombre];
                }
            }
        }

        // Buscar coincidencia parcial (palabras clave)
        $palabrasRespuesta = explode(' ', $respuestaLower);
        
        foreach ($etiquetasCatalogo as $etiquetaCatalogo) {
            $palabrasEtiqueta = explode(' ', $etiquetaCatalogo);
            
            $coincidencias = 0;
            foreach ($palabrasEtiqueta as $palabraEtiqueta) {
                if (in_array($palabraEtiqueta, $palabrasRespuesta)) {
                    $coincidencias++;
                }
            }
            
            // Si coincide al menos 50% de las palabras
            if ($coincidencias > 0 && $coincidencias >= (count($palabrasEtiqueta) * 0.5)) {
                $etiquetaReal = \App\Models\CatEtiqueta::whereRaw('LOWER(nombre) = ?', [$etiquetaCatalogo])->first();
                if ($etiquetaReal) {
                    Log::info("✅ Coincidencia parcial encontrada: {$etiquetaReal->nombre} (palabras: {$coincidencias}/" . count($palabrasEtiqueta) . ")");
                    return [$etiquetaReal->nombre];
                }
            }
        }

        Log::warning("⚠️ No se encontró coincidencia en catálogo para: '{$respuesta}'");
        return [];
    }

    /**
     * Handle a job failure.
     */
    public function failed(Throwable $exception): void
    {
        $errorType = 'error_general';
        $solucion = 'Revisar logs y reintentarlo manualmente';
        
        // Identificar tipo de error específico
        if (strpos($exception->getMessage(), 'attempted too many times') !== false) {
            $errorType = 'max_attempts_exceeded';
            $solucion = 'Job falló después de todos los reintentos. Esperar antes de reintentarlo o procesar auditoría específica.';
        } elseif (strpos($exception->getMessage(), '429') !== false || strpos($exception->getMessage(), 'rate limit') !== false) {
            $errorType = 'rate_limit';
            $solucion = 'Rate limit de API excedido. Esperar al menos 1 hora antes de reintentarlo.';
        } elseif (strpos($exception->getMessage(), 'timeout') !== false) {
            $errorType = 'timeout';
            $solucion = 'Job tomó demasiado tiempo. Considerar procesar auditorías más pequeñas o aumentar timeout.';
        }
        
        Log::error('❌ GenerarEtiquetasJob falló completamente', [
            'auditoria_id' => $this->auditoriaId,
            'error_type' => $errorType,
            'error' => $exception->getMessage(),
            'solucion_sugerida' => $solucion,
            'procesado_por' => $this->procesadoPor,
            'es_manual' => $this->esManual,
            'trace' => $exception->getTraceAsString()
        ]);
        
        // Si es una auditoría específica que falló, podríamos notificar al usuario
        if ($this->auditoriaId && $this->esManual) {
            Log::info("📧 Notificar al usuario {$this->procesadoPor} sobre fallo en auditoría {$this->auditoriaId}");
        }
    }
} 