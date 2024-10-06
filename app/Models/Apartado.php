<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apartado extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion', 'parent_id', 'nivel', 'auditoria_id']; // Make sure 'auditoria_id' is fillable

    // Relación para subapartados
    public function subapartados()
    {
        return $this->hasMany(Apartado::class, 'parent_id')->with('subapartados');
    }

    // Relación para el apartado padre
    public function parent()
    {
        return $this->belongsTo(Apartado::class, 'parent_id');
    }

    // Relación para la auditoría
    public function auditoria()
    {
        return $this->belongsTo(Auditorias::class, 'auditoria_id');
    }

    // Relación con las plantillas para este apartado
    public function plantillas()
    {
        return $this->hasMany(ApartadoPlantilla::class, 'apartado_id');
    }
    

    // Cálculo de la profundidad (nivel)
    public function getDepthAttribute()
    {
        $depth = 0;
        $parent = $this->parent;

        while ($parent) {
            $depth++;
            $parent = $parent->parent;
        }

        return $depth;
    }
}
