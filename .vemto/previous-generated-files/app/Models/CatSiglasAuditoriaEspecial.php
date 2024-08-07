<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CatSiglasAuditoriaEspecial extends Model
{
    use HasFactory;

    protected $table = 'cat_siglas_auditoria_especial';

    protected $guarded = [];

    public function allAuditorias()
    {
        return $this->hasMany(Auditorias::class, 'siglas_auditoria_especial');
    }
}
