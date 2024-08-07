<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CatEnteFiscalizado extends Model
{
    use HasFactory;

    protected $table = 'cat_ente_fiscalizado';

    protected $guarded = [];

    public function allAuditorias()
    {
        return $this->hasMany(Auditorias::class, 'ente_fiscalizado');
    }
}
