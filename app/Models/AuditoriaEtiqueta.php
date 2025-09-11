<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Apartado;
use App\Models\ChecklistApartado;

class AuditoriaEtiqueta extends Model
{
    use HasFactory;

    protected $table = 'auditoria_etiquetas';

    protected $fillable = [
        'auditoria_id',
        'etiqueta_id',
        'apartado_id',
        'checklist_apartado_id',
        'razon_asignacion',
        'comentario_fuente',
        'respuesta_ia',
        'confianza_ia',
        'validado_manualmente',
        'procesado_por',
        'procesado_en'
    ];

    protected $casts = [
        'confianza_ia' => 'decimal:2',
        'validado_manualmente' => 'boolean',
        'procesado_en' => 'datetime'
    ];

    /**
     * Relación con auditoría
     */
    public function auditoria(): BelongsTo
    {
        return $this->belongsTo(Auditorias::class, 'auditoria_id');
    }

    /**
     * Relación con etiqueta
     */
    public function etiqueta(): BelongsTo
    {
        return $this->belongsTo(CatEtiqueta::class, 'etiqueta_id');
    }

    /**
     * Relación con apartado padre (NUEVA)
     */
    public function apartado(): BelongsTo
    {
        return $this->belongsTo(Apartado::class, 'apartado_id');
    }

    /**
     * Relación con apartado del checklist (LEGACY)
     */
    public function checklistApartado(): BelongsTo
    {
        return $this->belongsTo(ChecklistApartado::class, 'checklist_apartado_id');
    }

    /**
     * Relación con usuario que procesó
     */
    public function procesadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'procesado_por');
    }

    /**
     * Scope para etiquetas de una auditoría específica
     */
    public function scopeParaAuditoria($query, $auditoriaId)
    {
        return $query->where('auditoria_id', $auditoriaId);
    }

    /**
     * Scope para etiquetas validadas manualmente
     */
    public function scopeValidadas($query)
    {
        return $query->where('validado_manualmente', true);
    }

    /**
     * Scope para etiquetas con alta confianza
     */
    public function scopeAltaConfianza($query, $minimoConfianza = 0.7)
    {
        return $query->where('confianza_ia', '>=', $minimoConfianza);
    }

    /**
     * Crear relación etiqueta-auditoría con apartado padre (NUEVA ESTRUCTURA)
     */
    public static function crearRelacionConApartado(
        int $auditoriaId,
        int $etiquetaId,
        int $apartadoId, // ID del apartado padre
        string $razonAsignacion,
        ?string $comentarioFuente = null,
        float $confianzaIA = 0.0,
        ?int $procesadoPor = null
    ): AuditoriaEtiqueta {
        return static::create([
            'auditoria_id' => $auditoriaId,
            'etiqueta_id' => $etiquetaId,
            'apartado_id' => $apartadoId, // NUEVA: relación directa con apartado padre
            'checklist_apartado_id' => null, // Ya no necesario
            'razon_asignacion' => $razonAsignacion,
            'comentario_fuente' => $comentarioFuente,
            'confianza_ia' => $confianzaIA,
            'validado_manualmente' => false,
            'procesado_por' => $procesadoPor,
            'procesado_en' => now()
        ]);
    }

    /**
     * Crear relación etiqueta-auditoría (LEGACY: mantener para compatibilidad)
     */
    public static function crearRelacion(
        int $auditoriaId,
        int $etiquetaId,
        string $razonAsignacion,
        int $checklistApartadoId, // Ahora opcional para transición
        ?string $comentarioFuente = null,
        float $confianzaIA = 0.0,
        ?int $procesadoPor = null
    ): AuditoriaEtiqueta {
        // Obtener apartado_id del checklist_apartado para migración
        $apartadoId = null;
        if ($checklistApartadoId) {
            $checklistApartado = ChecklistApartado::find($checklistApartadoId);
            $apartadoId = $checklistApartado?->apartado_id;
        }

        return static::create([
            'auditoria_id' => $auditoriaId,
            'etiqueta_id' => $etiquetaId,
            'apartado_id' => $apartadoId, // NUEVO: obtener desde checklist_apartado
            'checklist_apartado_id' => $checklistApartadoId, // LEGACY
            'razon_asignacion' => $razonAsignacion,
            'comentario_fuente' => $comentarioFuente,
            'confianza_ia' => $confianzaIA,
            'validado_manualmente' => false,
            'procesado_por' => $procesadoPor,
            'procesado_en' => now()
        ]);
    }

    /**
     * Obtener descripción resumida
     */
    public function getDescripcionResumidaAttribute(): string
    {
        $apartado = $this->checklistApartado ? 
                   ' (Apartado: ' . ($this->checklistApartado->apartado->nombre ?? 'Desconocido') . ')' : 
                   '';
        
        return $this->etiqueta->nombre . $apartado;
    }

    /**
     * Obtener todos los comentarios del apartado para esta auditoría
     */
    public function getComentariosApartadoAttribute(): Collection
    {
        if (!$this->apartado_id || !$this->auditoria_id) {
            return collect();
        }

        return ChecklistApartado::where('apartado_id', $this->apartado_id)
            ->where('auditoria_id', $this->auditoria_id)
            ->where(function($query) {
                $query->whereNotNull('observaciones')
                      ->where('observaciones', '!=', '')
                      ->orWhere(function($subQuery) {
                          $subQuery->whereNotNull('comentarios_uaa')
                                   ->where('comentarios_uaa', '!=', '');
                      });
            })
            ->get();
    }
} 