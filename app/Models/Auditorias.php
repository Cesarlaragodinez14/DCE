<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Auditorias extends Model
{
    use HasFactory;

    protected $table = 'aditorias';

    protected $guarded = [];

    protected $hidden = ['id'];

    // Relationship for the catalog of "Siglas Tipo Acción"
    public function catSiglasTipoAccion()
    {
        return $this->belongsTo(CatSiglasTipoAccion::class, 'siglas_tipo_accion');
    }

    // Relationship for "Auditoria Especial"
    public function catAuditoriaEspecial()
    {
        return $this->belongsTo(CatAuditoriaEspecial::class, 'auditoria_especial');
    }

    // Relationship for "UAA"
    public function catUaa()
    {
        return $this->belongsTo(CatUaa::class, 'uaa');
    }

    // Relationship for "Tipo de Auditoría"
    public function catTipoDeAuditoria()
    {
        return $this->belongsTo(CatTipoDeAuditoria::class, 'tipo_de_auditoria');
    }

    // Relationship for "Ente Fiscalizado"
    public function catEnteFiscalizado()
    {
        return $this->belongsTo(CatEnteFiscalizado::class, 'ente_fiscalizado');
    }

    // Relationship for "Ente de la Acción"
    public function catEnteDeLaAccion()
    {
        return $this->belongsTo(CatEnteDeLaAccion::class, 'ente_de_la_accion');
    }

    // Relationship for "Clave de Acción"
    public function catClaveAccion()
    {
        return $this->belongsTo(CatClaveAccion::class, 'clave_accion');
    }

    // Relationship for "Dgseg Ef"
    public function catDgsegEf()
    {
        return $this->belongsTo(CatDgsegEf::class, 'dgseg_ef');
    }

    // Relationship for "Cuenta Pública"
    public function catCuentaPublica()
    {
        return $this->belongsTo(CatCuentaPublica::class, 'cuenta_publica');
    }
}
