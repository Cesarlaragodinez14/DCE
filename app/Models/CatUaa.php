<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CatUaa extends Model
{
    use HasFactory;

    protected $table = 'cat_uaa';

    protected $guarded = [];

    public function allAuditorias()
    {
        return $this->hasMany(Auditorias::class, 'siglas_dg_uaa');
    }
}
