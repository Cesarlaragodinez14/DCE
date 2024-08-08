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

    public function catSiglasTipoAccion()
    {
        return $this->belongsTo(CatEntrega::class, 'entrega');
    }

    public function catAuditoriaEspecial()
    {
        return $this->belongsTo(
            CatAuditoriaEspecial::class,
            'auditoria_especial'
        );
    }

    public function catUaa()
    {
        return $this->belongsTo(CatUaa::class, 'siglas_dg_uaa');
    }

    public function catTipoDeAuditoria()
    {
        return $this->belongsTo(CatTipoDeAuditoria::class, 'tipo_de_auditoria');
    }

    public function catSiglasTipoAccion2()
    {
        return $this->belongsTo(
            CatSiglasAuditoriaEspecial::class,
            'siglas_auditoria_especial'
        );
    }

    public function catEnteFiscalizado()
    {
        return $this->belongsTo(CatEnteFiscalizado::class, 'ente_fiscalizado');
    }

    public function catEnteDeLaAccion()
    {
        return $this->belongsTo(CatEnteDeLaAccion::class, 'ente_de_la_accion');
    }

    public function auditorias()
    {
        return $this->belongsTo(CatClaveAccion::class, 'clave_accion');
    }

    public function catSiglasTipoAccion3()
    {
        return $this->belongsTo(
            CatSiglasTipoAccion::class,
            'siglas_tipo_accion'
        );
    }

    public function catDgsegEf()
    {
        return $this->belongsTo(CatDgsegEf::class, 'dgseg_ef');
    }

    public function catCuentaPublica()
    {
        return $this->belongsTo(CatCuentaPublica::class, 'cuenta_publica');
    }
}
