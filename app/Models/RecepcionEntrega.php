<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecepcionEntrega extends Model
{
    use HasFactory;

    protected $fillable = [
        'entrega_id',
        'nombre_servidor_uaa',
        'puesto_servidor_uaa',
        'firma_servidor_uaa',
        'nombre_servidor_dce',
        'puesto_servidor_dce',
        'firma_servidor_dce',
    ];

    // Relación con el modelo Entrega
    public function entrega()
    {
        return $this->belongsTo(Entrega::class);
    }
}
