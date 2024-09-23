<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrega extends Model
{
    use HasFactory;

    protected $fillable = [
        'auditoria_id',
        'clave_accion',
        'tipo_accion',
        'CP',
        'entrega',
        'fecha_entrega',
        'responsable',
        'numero_legajos',
        'confirmado_por',
    ];

    // Relación con el modelo Auditoria (expediente)
    public function expediente()
    {
        return $this->belongsTo(Auditorias::class, 'auditoria_id');
    }

    // Relación con el modelo User (usuario que confirmó la entrega)
    public function confirmadoPor()
    {
        return $this->belongsTo(User::class, 'confirmado_por');
    }
    public function auditoria()
    {
        return $this->belongsTo(Auditorias::class, 'auditoria_id');
    }

}
