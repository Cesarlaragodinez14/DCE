<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CatEnteDeLaAccion extends Model
{
    use HasFactory;

    protected $table = 'cat_ente_de_la_accion';

    protected $guarded = [];

    public function allAuditorias()
    {
        return $this->hasMany(Auditorias::class, 'ente_de_la_accion');
    }
}
