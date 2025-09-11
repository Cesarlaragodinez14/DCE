<?php

// app/Models/AuditoriasHistory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class AuditoriasHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'auditoria_id',
        'changed_by',
        'changes',
    ];

    /**
     * Boot del modelo para agregar eventos que limpien el caché
     */
    protected static function boot()
    {
        parent::boot();

        // Eventos que limpian el caché cuando se modifican registros de historial
        static::created(function ($history) {
            static::limpiarCacheEstadisticas();
        });
    }

    /**
     * Limpiar caché de estadísticas de auditorías
     */
    private static function limpiarCacheEstadisticas()
    {
        try {
            Log::info('🔄 Limpiando caché por modificación en modelo AuditoriasHistory...');
            
            // Obtener todas las claves de cache registradas
            $cacheKeys = Cache::get('resumen_auditorias_cache_keys', []);
            
            if (!empty($cacheKeys)) {
                foreach ($cacheKeys as $key) {
                    Cache::forget($key);
                }
            }
            
            // Limpiar también las claves de registro
            Cache::forget('resumen_auditorias_cache_keys');
            
            // Limpiar cachés por patrón si es Redis
            if (config('cache.default') === 'redis') {
                try {
                    $redis = Cache::getRedis();
                    $keys = $redis->keys('*resumen_auditorias_stats_*');
                    
                    foreach ($keys as $key) {
                        $cleanKey = str_replace(config('cache.prefix') . ':', '', $key);
                        Cache::forget($cleanKey);
                    }
                } catch (\Exception $e) {
                    // Silently fail if Redis is not available
                }
            }
            
            Log::info('✅ Caché de estadísticas limpiado por modificación de historial de auditoría');
            
        } catch (\Exception $e) {
            Log::error("❌ Error limpiando caché por modificación de historial de auditoría: {$e->getMessage()}");
        }
    }

    /**
     * Relación con el usuario que realizó el cambio.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Relación con la auditoría.
     */
    public function auditoria()
    {
        return $this->belongsTo(Auditorias::class, 'auditoria_id');
    }
}
