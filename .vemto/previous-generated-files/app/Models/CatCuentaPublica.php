<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CatCuentaPublica extends Model
{
    use HasFactory;

    protected $table = 'cat_cuenta_publica';

    protected $guarded = [];

    public function allAuditorias()
    {
        return $this->hasMany(Auditorias::class, 'cuenta_publica');
    }
}
