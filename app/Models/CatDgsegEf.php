<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CatDgsegEf extends Model
{
    use HasFactory;

    protected $table = 'cat_dgseg_ef';

    protected $guarded = [];

    public function allAuditorias()
    {
        return $this->hasMany(Auditorias::class, 'dgseg_ef');
    }
}
