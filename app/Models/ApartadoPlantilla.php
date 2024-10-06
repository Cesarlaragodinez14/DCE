<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApartadoPlantilla extends Model
{
    use HasFactory;

    protected $table = 'apartado_plantillas'; // Definir la tabla explícitamente si es necesario

    protected $fillable = [
        'apartado_id',
        'plantilla',
        'es_obligatorio',
        'se_integra',
        'es_aplicable',
    ];

}
