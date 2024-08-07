<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CatSiglasTipoAccion extends Model
{
    use HasFactory;

    protected $table = 'cat_siglas_tipo_accion';

    protected $guarded = [];

    public function allAuditorias()
    {
        return $this->hasMany(Auditorias::class, 'siglas_tipo_accion');
    }
}
