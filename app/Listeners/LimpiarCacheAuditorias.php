<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class LimpiarCacheAuditorias
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        try {
            Log::info('🔄 Evento detectado, limpiando caché de estadísticas de auditorías...');
            
            // Obtener todas las claves de cache registradas
            $cacheKeys = Cache::get('resumen_auditorias_cache_keys', []);
            $cantidadLimpiada = 0;
            
            if (!empty($cacheKeys)) {
                foreach ($cacheKeys as $key) {
                    if (Cache::has($key)) {
                        Cache::forget($key);
                        $cantidadLimpiada++;
                    }
                }
            }
            
            // Limpiar también las claves de registro
            Cache::forget('resumen_auditorias_cache_keys');
            
            // Limpiar cualquier clave que coincida con el patrón
            $this->limpiarCachesPorPatron();
            
            Log::info("✅ Caché de estadísticas limpiado por evento. Claves limpiadas: {$cantidadLimpiada}");
            
        } catch (\Exception $e) {
            Log::error("❌ Error limpiando caché por evento: {$e->getMessage()}");
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
                }
                
            } catch (\Exception $e) {
                Log::warning("No se pudieron limpiar claves por patrón: {$e->getMessage()}");
            }
        }
    }
} 