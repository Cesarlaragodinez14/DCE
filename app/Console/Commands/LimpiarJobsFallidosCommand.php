<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Jobs\GenerarEtiquetasJob;
use App\Jobs\ProcesarApartadoIndividualJob;

class LimpiarJobsFallidosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jobs:limpiar-fallidos
                            {--tipo=etiquetas : Tipo de jobs a limpiar (etiquetas, apartados, todos)}
                            {--reintentar : Reintentar jobs fallidos en lugar de solo eliminarlos}
                            {--auditoria-id= : ID específico de auditoría para reintentar}
                            {--apartado-id= : ID específico de apartado para reintentar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpiar jobs fallidos y opcionalmente reintentarlos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tipo = $this->option('tipo');
        $reintentar = $this->option('reintentar');
        $auditoriaId = $this->option('auditoria-id');
        $apartadoId = $this->option('apartado-id');

        $this->info('🧹 Iniciando limpieza de jobs fallidos...');

        // Obtener jobs fallidos
        $query = DB::table('failed_jobs');
        
        if ($tipo === 'etiquetas') {
            $query->where('payload', 'like', '%GenerarEtiquetasJob%');
        } elseif ($tipo === 'apartados') {
            $query->where('payload', 'like', '%ProcesarApartadoIndividualJob%');
        }

        if ($auditoriaId) {
            $query->where('payload', 'like', "%auditoria_id.*{$auditoriaId}%");
        }

        if ($apartadoId) {
            $query->where('payload', 'like', "%apartadoPadreId.*{$apartadoId}%");
        }

        $jobsFallidos = $query->get();
        
        $this->info("📊 Jobs fallidos encontrados: " . $jobsFallidos->count());

        if ($jobsFallidos->isEmpty()) {
            $this->info("✅ No hay jobs fallidos para limpiar");
            return;
        }

        foreach ($jobsFallidos as $job) {
            try {
                // Extraer información del payload
                $payload = json_decode($job->payload, true);
                $jobClass = $payload['displayName'] ?? 'Desconocido';
                
                $this->line("🔍 Procesando job: {$jobClass} (ID: {$job->id})");

                if ($reintentar && $jobClass === 'App\\Jobs\\GenerarEtiquetasJob') {
                    // Intentar extraer auditoriaId del job
                    $jobData = unserialize($payload['data']['command']);
                    $auditoriaJobId = $jobData->auditoriaId ?? null;
                    $procesadoPor = $jobData->procesadoPor ?? 1;

                    if ($auditoriaJobId) {
                        $this->info("🔄 Reintentando GenerarEtiquetasJob para auditoría: {$auditoriaJobId}");
                        
                        // Despachar nuevo job
                        GenerarEtiquetasJob::dispatch($auditoriaJobId, $procesadoPor, true);
                        
                        $this->info("✅ Nuevo job despachado para auditoría: {$auditoriaJobId}");
                    } else {
                        $this->warn("⚠️ No se pudo extraer auditoriaId del job fallido");
                    }
                } elseif ($reintentar && $jobClass === 'App\\Jobs\\ProcesarApartadoIndividualJob') {
                    // Intentar extraer datos del job de apartado individual
                    $jobData = unserialize($payload['data']['command']);
                    $auditoriaJobId = $jobData->auditoriaId ?? null;
                    $apartadoPadreId = $jobData->apartadoPadreId ?? null;
                    $nombreEtiqueta = $jobData->nombreEtiqueta ?? 'Procesado';
                    $respuestaIA = $jobData->respuestaIA ?? 'Reintento de job fallido';
                    $razonAsignacion = $jobData->razonAsignacion ?? 'Reintento automático';
                    $comentarioFuente = $jobData->comentarioFuente ?? 'Job reencolado';
                    $procesadoPor = $jobData->procesadoPor ?? 1;

                    if ($auditoriaJobId && $apartadoPadreId) {
                        $this->info("🔄 Reintentando ProcesarApartadoIndividualJob para apartado: {$apartadoPadreId}");
                        
                        // Despachar nuevo job con delay
                        ProcesarApartadoIndividualJob::dispatch(
                            $auditoriaJobId,
                            $apartadoPadreId,
                            $nombreEtiqueta,
                            $respuestaIA,
                            $razonAsignacion,
                            $comentarioFuente,
                            $procesadoPor,
                            0 // Resetear contador de intentos de duplicado
                        )->delay(now()->addSeconds(30));
                        
                        $this->info("✅ Nuevo job de apartado despachado: {$apartadoPadreId}");
                    } else {
                        $this->warn("⚠️ No se pudo extraer datos del job de apartado fallido");
                    }
                }

                // Eliminar job fallido
                DB::table('failed_jobs')->where('id', $job->id)->delete();
                $this->info("🗑️ Job fallido eliminado");

            } catch (\Exception $e) {
                $this->error("❌ Error procesando job {$job->id}: {$e->getMessage()}");
                continue;
            }
        }

        $this->info("✅ Limpieza de jobs fallidos completada");

        if ($reintentar) {
            $this->warn("⏰ Recuerda: Los nuevos jobs pueden tardar en procesarse para evitar rate limits");
        }
    }
} 