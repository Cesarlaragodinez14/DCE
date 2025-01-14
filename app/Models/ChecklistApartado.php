<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistApartado extends Model
{
    use HasFactory;

    protected $fillable = ['apartado_id', 'auditoria_id', 'se_aplica', 'es_obligatorio', 'se_integra', 'observaciones'];

    // Relación con el apartado
    public function apartado()
    {
        return $this->belongsTo(Apartado::class, 'apartado_id', 'id');
    }


    // Relación con la auditoría
    public function auditoria()
    {
        return $this->belongsTo(Auditorias::class);
    }

    public function histories()
    {
        return $this->hasMany(ChecklistApartadoHistory::class, 'checklist_apartado_id');
    }
}
