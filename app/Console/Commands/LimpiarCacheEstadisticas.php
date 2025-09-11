<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class LimpiarCacheEstadisticas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:limpiar-estadisticas-auditorias {--force : Limpiar sin confirmación}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpiar el caché de estadísticas de auditorías para forzar el recálculo';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🗑️ Iniciando limpieza de caché de estadísticas de auditorías...');
        
        try {
            // Obtener todas las claves de cache registradas
            $cacheKeys = Cache::get('resumen_auditorias_cache_keys', []);
            $cantidadLimpiada = 0;
            
            if (!empty($cacheKeys)) {
                foreach ($cacheKeys as $key) {
                    if (Cache::has($key)) {
                        Cache::forget($key);
                        $cantidadLimpiada++;
                        $this->line("   ✓ Limpiado: {$key}");
                    }
                }
            }
            
            // Limpiar también las claves de registro
            Cache::forget('resumen_auditorias_cache_keys');
            
            // Limpiar cualquier clave que coincida con el patrón
            $this->limpiarCachesPorPatron();
            
            Log::info("🧹 Caché de estadísticas limpiado automáticamente. Claves limpiadas: {$cantidadLimpiada}");
            
            $this->info("✅ Proceso completado exitosamente.");
            $this->info("📊 Total de claves de caché limpiadas: {$cantidadLimpiada}");
            $this->info("🔄 Las estadísticas se recalcularán en la próxima consulta.");
            
            return 0;
            
        } catch (\Exception $e) {
            Log::error("❌ Error limpiando caché de estadísticas: {$e->getMessage()}");
            $this->error("Error: {$e->getMessage()}");
            return 1;
        }
    }
    
    /**
     * Limpiar cachés que coincidan con el patrón de resumen_auditorias
     */
    private function limpiarCachesPorPatron()
    {
        // Si estás usando Redis, puedes usar patrones
        if (config('cache.default') === 'redis') {
            try {
                $redis = Cache::getRedis();
                $keys = $redis->keys('*resumen_auditorias_stats_*');
                
                foreach ($keys as $key) {
                    // Remover el prefijo de Redis si existe
                    $cleanKey = str_replace(config('cache.prefix') . ':', '', $key);
                    Cache::forget($cleanKey);
                    $this->line("   ✓ Limpiado por patrón: {$cleanKey}");
                }
                
                $this->info("🔍 Limpiadas " . count($keys) . " claves adicionales por patrón.");
                
            } catch (\Exception $e) {
                $this->warn("No se pudieron limpiar claves por patrón: {$e->getMessage()}");
            }
        }
    }
} 