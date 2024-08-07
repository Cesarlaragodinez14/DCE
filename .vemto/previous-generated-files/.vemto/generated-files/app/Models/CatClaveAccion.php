<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CatClaveAccion extends Model
{
    use HasFactory;

    protected $table = 'cat_clave_accion';

    protected $guarded = [];

    public function auditorias()
    {
        return $this->hasMany(Auditorias::class, 'clave_accion');
    }
}
