<?php

namespace App\Console\Commands;

use App\Jobs\GenerarEtiquetasJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerarEtiquetasCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'etiquetas:generar 
                            {--auditoria-id= : ID específico de auditoría a procesar}
                            {--usuario-id= : ID del usuario que ejecuta el comando}
                            {--sync : Ejecutar de forma síncrona en lugar de usar colas}
                            {--masivo : Procesamiento masivo con límite de memoria aumentado}
                            {--mostrar-ahorro : Mostrar estimación de ahorro en costos de IA}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generar etiquetas automáticas OPTIMIZADAS (v2.0 - procesamiento en lotes para evitar problemas de memoria)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $auditoriaId = $this->option('auditoria-id');
        $usuarioId = $this->option('usuario-id');
        $esSync = $this->option('sync');
        $esMasivo = $this->option('masivo');
        $mostrarAhorro = $this->option('mostrar-ahorro');

        // Configurar límite de memoria para procesamiento masivo
        if ($esMasivo || (!$auditoriaId && $esSync)) {
            ini_set('memory_limit', '1G');
            $this->info('💾 Límite de memoria aumentado a 1GB para procesamiento masivo');
        }

        $this->info('🚀 Iniciando generación de etiquetas OPTIMIZADA (v2.0)...');
        $this->info('⚡ Procesamiento en lotes para evitar problemas de memoria');
        
        if ($mostrarAhorro) {
            $this->mostrarEstimacionAhorro($auditoriaId);
        }

        // Mostrar información de memoria
        $memoryLimit = ini_get('memory_limit');
        $memoryUsage = round(memory_get_usage(true) / 1024 / 1024, 2);
        $this->info("🧠 Memoria disponible: {$memoryLimit} | Memoria actual: {$memoryUsage} MB");

        try {
            if ($auditoriaId) {
                $this->info("🔍 Procesando auditoría específica OPTIMIZADA: {$auditoriaId}");
                
                if ($esSync) {
                    // Ejecutar sincrónicamente
                    $job = new GenerarEtiquetasJob($auditoriaId, $usuarioId, true);
                    $job->handle();
                    $this->info('✅ Procesamiento optimizado síncrono completado');
                } else {
                    // Despachar al queue
                    GenerarEtiquetasJob::dispatch($auditoriaId, $usuarioId, true);
                    $this->info('✅ Job optimizado despachado a la cola');
                }
            } else {
                $this->info('📊 Procesando todas las auditorías pendientes OPTIMIZADAS en lotes...');
                
                if ($esSync || $esMasivo) {
                    // Ejecutar sincrónicamente con mayor límite
                    $this->warn('⚠️ Procesamiento masivo síncrono puede tomar HORAS');
                    $this->warn('💡 Recomendación: usar colas con `php artisan queue:work` en segundo plano');
                    
                    if ($this->confirm('¿Continuar con procesamiento síncrono masivo?')) {
                        $job = new GenerarEtiquetasJob(null, $usuarioId, false);
                        $job->handle();
                        $this->info('✅ Procesamiento masivo síncrono completado');
                    } else {
                        $this->info('❌ Procesamiento cancelado');
                        return;
                    }
                } else {
                    // Despachar al queue
                    GenerarEtiquetasJob::dispatch(null, $usuarioId, false);
                    $this->info('✅ Job masivo optimizado despachado a la cola');
                    $this->info('💡 Monitorear progreso con: tail -f storage/logs/laravel.log');
                }
            }

            // Mostrar información final de memoria
            $memoryFinal = round(memory_get_usage(true) / 1024 / 1024, 2);
            $this->info("🏁 Memoria final utilizada: {$memoryFinal} MB");

            Log::info('✅ Comando etiquetas:generar (v2.0) ejecutado exitosamente', [
                'auditoria_id' => $auditoriaId,
                'usuario_id' => $usuarioId,
                'sincrono' => $esSync
            ]);

        } catch (\Exception $e) {
            $this->error('❌ Error durante la ejecución: ' . $e->getMessage());
            $memoryError = round(memory_get_usage(true) / 1024 / 1024, 2);
            $this->error("💥 Memoria en error: {$memoryError} MB");
            
            if (strpos($e->getMessage(), 'memory') !== false) {
                $this->warn('💡 Sugerencia: usar --masivo para aumentar límite de memoria');
                $this->warn('💡 O procesar por lotes más pequeños con colas: php artisan queue:work');
            }
            
            Log::error('❌ Error en comando etiquetas:generar (v2.0)', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return 1; // Código de error
        }

        return 0; // Éxito
    }

    /**
     * Mostrar estimación de ahorro en costos
     */
    private function mostrarEstimacionAhorro(?int $auditoriaId): void
    {
        $this->info('💰 ESTIMACIÓN DE AHORRO EN COSTOS DE IA');
        $this->info('=========================================');

        // Import de Auditorias aquí para evitar problemas
        $auditorias = \App\Models\Auditorias::with(['checklistApartados.apartado'])
            ->whereHas('checklistApartados', function($query) {
                $query->where(function($subQuery) {
                    $subQuery->whereNotNull('observaciones')->where('observaciones', '!=', '');
                })->orWhere(function($subQuery) {
                    $subQuery->whereNotNull('comentarios_uaa')->where('comentarios_uaa', '!=', '');
                });
            })
            ->when($auditoriaId, function($query, $id) {
                return $query->where('id', $id);
            })
            ->take($auditoriaId ? 1 : 3)
            ->get();

        $totalOriginal = 0;
        $totalOptimizado = 0;

        foreach ($auditorias as $auditoria) {
            $apartados = $auditoria->checklistApartados->filter(function($apartado) {
                return (!empty($apartado->observaciones) && trim($apartado->observaciones) !== '') ||
                       (!empty($apartado->comentarios_uaa) && trim($apartado->comentarios_uaa) !== '');
            });

            if ($apartados->isEmpty()) continue;

            $llamadasOriginal = $apartados->count();
            $tiposUnicos = $apartados->groupBy(function($apartado) {
                return $apartado->apartado->nombre ?? 'Tipo desconocido';
            })->count();

            $totalOriginal += $llamadasOriginal;
            $totalOptimizado += $tiposUnicos;

            $ahorro = round((1 - $tiposUnicos / $llamadasOriginal) * 100, 1);
            $this->info("📋 {$auditoria->clave_de_accion}: {$llamadasOriginal} → {$tiposUnicos} llamadas ({$ahorro}% ahorro)");
        }

        $ahorroTotal = round((1 - $totalOptimizado / max($totalOriginal, 1)) * 100, 1);
        $costoOriginal = $totalOriginal * 0.05;
        $costoOptimizado = $totalOptimizado * 0.05;
        $ahorroMonetario = $costoOriginal - $costoOptimizado;

        $this->info('');
        $this->info("🔴 Versión anterior: {$totalOriginal} llamadas IA (\${$costoOriginal} USD)");
        $this->info("🟢 Versión optimizada: {$totalOptimizado} llamadas IA (\${$costoOptimizado} USD)");
        $this->info("💚 AHORRO: {$ahorroTotal}% (-\${$ahorroMonetario} USD)");
        $this->info('=========================================');
        $this->info('');
    }
} 