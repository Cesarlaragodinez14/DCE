<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CatEtiqueta extends Model
{
    use HasFactory;

    protected $table = 'cat_etiquetas';

    protected $fillable = [
        'nombre',
        'descripcion',
        'color',
        'activo',
        'veces_usada'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'veces_usada' => 'integer'
    ];

    /**
     * Relación con auditoría etiquetas
     */
    public function auditoriaEtiquetas(): HasMany
    {
        return $this->hasMany(AuditoriaEtiqueta::class, 'etiqueta_id');
    }

    /**
     * Incrementar contador de uso
     */
    public function incrementarUso(): void
    {
        $this->increment('veces_usada');
    }

    /**
     * Scope para etiquetas activas
     */
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para buscar por nombre
     */
    public function scopeBuscarPorNombre($query, $nombre)
    {
        return $query->where('nombre', 'like', '%' . $nombre . '%');
    }

    /**
     * Obtener color CSS para la etiqueta
     */
    public function getColorCssAttribute(): string
    {
        $colores = [
            'red' => 'bg-red-100 text-red-800',
            'blue' => 'bg-blue-100 text-blue-800',
            'green' => 'bg-green-100 text-green-800',
            'yellow' => 'bg-yellow-100 text-yellow-800',
            'purple' => 'bg-purple-100 text-purple-800',
            'pink' => 'bg-pink-100 text-pink-800',
            'gray' => 'bg-gray-100 text-gray-800',
            'indigo' => 'bg-indigo-100 text-indigo-800',
            'orange' => 'bg-orange-100 text-orange-800',
            'teal' => 'bg-teal-100 text-teal-800',
        ];

        return $colores[$this->color] ?? $colores['gray'];
    }

    /**
     * Crear o obtener etiqueta por nombre
     */
    public static function crearOObtener(string $nombre, string $descripcion = null, string $color = 'gray'): CatEtiqueta
    {
        return static::firstOrCreate(
            ['nombre' => trim($nombre)],
            [
                'descripcion' => $descripcion,
                'color' => $color,
                'activo' => true,
                'veces_usada' => 0
            ]
        );
    }
} 