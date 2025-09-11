<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\CatEtiqueta;
use App\Models\Auditorias;
use App\Models\AuditoriaEtiqueta;

class DiagnosticoEtiquetasCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'etiquetas:diagnostico 
                            {--auditoria-id= : ID específico de auditoría para diagnosticar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Diagnosticar estado del sistema de etiquetas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $auditoriaId = $this->option('auditoria-id');

        $this->info('🔍 Iniciando diagnóstico del sistema de etiquetas...');
        $this->newLine();

        // 1. Verificar catálogo de etiquetas
        $this->line('📋 <fg=blue>CATÁLOGO DE ETIQUETAS</fg=blue>');
        $etiquetas = CatEtiqueta::where('activo', true)->get();
        $this->info("✅ Etiquetas activas: {$etiquetas->count()}");
        
        $etiquetaProcesado = CatEtiqueta::where('nombre', 'Procesado')->first();
        if ($etiquetaProcesado) {
            $this->info("✅ Etiqueta 'Procesado' encontrada (ID: {$etiquetaProcesado->id})");
        } else {
            $this->error("❌ Etiqueta 'Procesado' NO encontrada");
        }
        $this->newLine();

        // 2. Verificar jobs fallidos
        $this->line('💼 <fg=blue>JOBS FALLIDOS</fg=blue>');
        $jobsFallidos = DB::table('failed_jobs')
            ->where('payload', 'like', '%GenerarEtiquetasJob%')
            ->count();
        
        if ($jobsFallidos > 0) {
            $this->warn("⚠️ Jobs de etiquetas fallidos: {$jobsFallidos}");
            $this->line("   💡 Ejecuta: php artisan jobs:limpiar-fallidos --tipo=etiquetas");
        } else {
            $this->info("✅ No hay jobs de etiquetas fallidos");
        }
        $this->newLine();

        // 3. Verificar jobs pendientes
        $this->line('⏳ <fg=blue>JOBS PENDIENTES</fg=blue>');
        $jobsPendientes = DB::table('jobs')
            ->where('payload', 'like', '%GenerarEtiquetasJob%')
            ->count();
        
        if ($jobsPendientes > 0) {
            $this->warn("⚠️ Jobs de etiquetas pendientes: {$jobsPendientes}");
            $this->line("   💡 Los jobs están en cola, procesar con: php artisan queue:work");
        } else {
            $this->info("✅ No hay jobs de etiquetas pendientes");
        }
        $this->newLine();

        // 4. Estadísticas generales
        if (!$auditoriaId) {
            $this->line('📊 <fg=blue>ESTADÍSTICAS GENERALES</fg=blue>');
            
            $totalAuditorias = Auditorias::count();
            $auditoriasConEtiquetas = Auditorias::whereHas('auditoriaEtiquetas')->count();
            $auditoriasSinEtiquetas = $totalAuditorias - $auditoriasConEtiquetas;
            
            $this->info("📋 Total auditorías: {$totalAuditorias}");
            $this->info("🏷️ Con etiquetas: {$auditoriasConEtiquetas}");
            $this->info("❓ Sin etiquetas: {$auditoriasSinEtiquetas}");
            
            $totalEtiquetasAsignadas = AuditoriaEtiqueta::count();
            $this->info("🎯 Total etiquetas asignadas: {$totalEtiquetasAsignadas}");
            $this->newLine();
        }

        // 5. Análisis de auditoría específica
        if ($auditoriaId) {
            $this->line("🔍 <fg=blue>ANÁLISIS AUDITORÍA {$auditoriaId}</fg=blue>");
            
            $auditoria = Auditorias::with(['auditoriaEtiquetas.etiqueta', 'checklistApartados.apartado'])
                ->find($auditoriaId);
            
            if (!$auditoria) {
                $this->error("❌ Auditoría {$auditoriaId} no encontrada");
                return;
            }

            $this->info("📂 Clave: {$auditoria->clave_de_accion}");
            
            $apartadosConComentarios = $auditoria->checklistApartados
                ->filter(function($apartado) {
                    return (!empty($apartado->observaciones) && trim($apartado->observaciones) !== '') ||
                           (!empty($apartado->comentarios_uaa) && trim($apartado->comentarios_uaa) !== '');
                });
            
            $this->info("💬 Apartados con comentarios: {$apartadosConComentarios->count()}");
            
            $etiquetasExistentes = $auditoria->auditoriaEtiquetas;
            $this->info("🏷️ Etiquetas asignadas: {$etiquetasExistentes->count()}");
            
            if ($etiquetasExistentes->count() > 0) {
                $this->line("   Etiquetas:");
                foreach ($etiquetasExistentes as $rel) {
                    $this->line("   - {$rel->etiqueta->nombre}");
                }
            }
            
            if ($apartadosConComentarios->count() > 0 && $etiquetasExistentes->count() === 0) {
                $this->warn("⚠️ Esta auditoría tiene comentarios pero no tiene etiquetas");
                $this->line("   💡 Ejecuta: php artisan etiquetas:generar --auditoria-id={$auditoriaId} --sync");
            }
            $this->newLine();
        }

        // 6. Recomendaciones
        $this->line('💡 <fg=blue>RECOMENDACIONES</fg=blue>');
        
        if ($jobsFallidos > 0) {
            $this->line("1. Limpiar jobs fallidos antes de continuar");
            $this->line("   php artisan jobs:limpiar-fallidos --tipo=etiquetas");
        }
        
        if ($jobsPendientes > 0) {
            $this->line("2. Procesar jobs pendientes:");
            $this->line("   php artisan queue:work --once");
        } else {
            $this->line("2. Para procesar etiquetas de forma segura:");
            $this->line("   php artisan etiquetas:generar --sync");
        }
        
        $this->line("3. Para monitorear rate limits:");
        $this->line("   tail -f storage/logs/laravel.log | grep -E '(rate limit|429|ERROR)'");
        
        $this->newLine();
        $this->info("✅ Diagnóstico completado");
    }
} 