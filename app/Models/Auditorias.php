<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class Auditorias extends Model
{
    use HasFactory;

    protected $table = 'aditorias';

    protected $guarded = [];

    protected $hidden = ['id'];

    /**
     * Boot del modelo para agregar eventos que limpien el caché
     */
    protected static function boot()
    {
        parent::boot();

        // Eventos que limpian el caché cuando se modifican auditorías
        static::updated(function ($auditoria) {
            static::limpiarCacheEstadisticas();
        });

        static::created(function ($auditoria) {
            static::limpiarCacheEstadisticas();
        });

        static::deleted(function ($auditoria) {
            static::limpiarCacheEstadisticas();
        });
    }

    /**
     * Limpiar caché de estadísticas de auditorías
     */
    private static function limpiarCacheEstadisticas()
    {
        try {
            Log::info('🔄 Limpiando caché por modificación en modelo Auditorias...');
            
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
            
            Log::info('✅ Caché de estadísticas limpiado por modificación de auditoría');
            
        } catch (\Exception $e) {
            Log::error("❌ Error limpiando caché por modificación de auditoría: {$e->getMessage()}");
        }
    }

    // Relationship for the catalog of "Siglas Tipo Acción"
    public function catSiglasTipoAccion()
    {
        return $this->belongsTo(CatSiglasTipoAccion::class, 'siglas_tipo_accion');
    }

    // Relationship for the catalog of "Siglas Tipo Acción"
    public function catSiglasAuditoriaEspecial()
    {
        return $this->belongsTo(CatSiglasAuditoriaEspecial::class, 'siglas_auditoria_especial');
    }

    // Relationship for "Auditoria Especial"
    public function catAuditoriaEspecial()
    {
        return $this->belongsTo(CatAuditoriaEspecial::class, 'auditoria_especial');
    }

    // Relationship for "Entrega"
    public function catEntrega()
    {
        return $this->belongsTo(CatEntrega::class, 'entrega');
    }

    // Relationship for "UAA"
    public function catUaa()
    {
        return $this->belongsTo(CatUaa::class, 'uaa');
    }

    // Relationship for "Tipo de Auditoría"
    public function catTipoDeAuditoria()
    {
        return $this->belongsTo(CatTipoDeAuditoria::class, 'tipo_de_auditoria');
    }

    // Relationship for "Ente Fiscalizado"
    public function catEnteFiscalizado()
    {
        return $this->belongsTo(CatEnteFiscalizado::class, 'ente_fiscalizado');
    }

    // Relationship for "Ente de la Acción"
    public function catEnteDeLaAccion()
    {
        return $this->belongsTo(CatEnteDeLaAccion::class, 'ente_de_la_accion');
    }

    // Relationship for "Clave de Acción"
    public function catClaveAccion()
    {
        return $this->belongsTo(CatClaveAccion::class, 'clave_accion');
    }

    // Relationship for "Dgseg Ef"
    public function catDgsegEf()
    {
        return $this->belongsTo(CatDgsegEf::class, 'dgseg_ef');
    }

    // Relationship for "Cuenta Pública"
    public function catCuentaPublica()
    {
        return $this->belongsTo(CatCuentaPublica::class, 'cuenta_publica');
    }

    public function entregas()
    {
        return $this->hasMany(Entrega::class, 'auditoria_id');
    }

    public function apartados()
    {
        return $this->hasMany(Apartado::class, 'auditoria_id');
    }

    public function checklistApartados()
    {
        return $this->hasMany(ChecklistApartado::class, 'auditoria_id', 'id');
    }

    /**
     * Relación con PdfHistory.
     */
    public function pdfHistories()
    {
        return $this->hasMany(PdfHistory::class, 'auditoria_id');
    }

    /**
     * Relación con etiquetas de auditoría.
     */
    public function auditoriaEtiquetas()
    {
        return $this->hasMany(AuditoriaEtiqueta::class, 'auditoria_id');
    }

    /**
     * Relación many-to-many con etiquetas.
     */
    public function etiquetas()
    {
        return $this->belongsToMany(CatEtiqueta::class, 'auditoria_etiquetas', 'auditoria_id', 'etiqueta_id')
                    ->withPivot('razon_asignacion', 'comentario_fuente', 'confianza_ia', 'validado_manualmente', 'procesado_en')
                    ->withTimestamps();
    }

    /**
     * Verificar si tiene etiquetas pendientes de procesar.
     */
    public function tieneEtiquetasPendientes(): bool
    {
        // Verificar si hay comentarios en apartados que no han sido procesados para etiquetas
        $ultimaActualizacionComentarios = $this->checklistApartados()
            ->whereNotNull('observaciones')
            ->orWhereNotNull('comentarios_uaa')
            ->max('updated_at');

        if (!$ultimaActualizacionComentarios) {
            return false;
        }

        $ultimoProcesamientoEtiquetas = $this->auditoriaEtiquetas()
            ->max('procesado_en');

        return !$ultimoProcesamientoEtiquetas || 
               $ultimaActualizacionComentarios > $ultimoProcesamientoEtiquetas;
    }

    /**
     * Obtener etiquetas únicas de la auditoría.
     */
    public function getEtiquetasUnicasAttribute()
    {
        return $this->etiquetas()
                    ->distinct()
                    ->get()
                    ->groupBy('nombre');
    }
    
}
