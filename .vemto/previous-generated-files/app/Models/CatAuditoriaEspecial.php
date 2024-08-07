<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CatAuditoriaEspecial extends Model
{
    use HasFactory;

    protected $table = 'cat_auditoria_especial';

    protected $guarded = [];

    public function allAuditorias()
    {
        return $this->hasMany(Auditorias::class, 'auditoria_especial');
    }
}
