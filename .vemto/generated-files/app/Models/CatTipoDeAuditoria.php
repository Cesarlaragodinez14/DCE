<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CatTipoDeAuditoria extends Model
{
    use HasFactory;

    protected $table = 'cat_tipo_de_auditoria';

    protected $guarded = [];

    public function allAuditorias()
    {
        return $this->hasMany(Auditorias::class, 'tipo_de_auditoria');
    }
}
