<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CatEntrega extends Model
{
    use HasFactory;

    protected $table = 'cat_entrega';

    protected $guarded = [];

    public function allAuditorias()
    {
        return $this->hasMany(Auditorias::class, 'entrega');
    }
}
