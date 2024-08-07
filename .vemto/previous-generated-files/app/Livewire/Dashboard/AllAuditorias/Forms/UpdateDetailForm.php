<?php

namespace App\Livewire\Dashboard\AllAuditorias\Forms;

use Livewire\Form;
use App\Models\Auditorias;
use Livewire\Attributes\Rule;

class UpdateDetailForm extends Form
{
    public ?Auditorias $auditorias;

    public $clave_de_accion = '';

    public $entrega = '';

    public $auditoria_especial = '';

    public $tipo_de_auditoria = '';

    public $siglas_auditoria_especial = '';

    public $siglas_dg_uaa = '';

    public $titulo = '';

    public $ente_fiscalizado = '';

    public $numero_de_auditoria = '';

    public $ente_de_la_accion = '';

    public $clave_accion = '';

    public $dgseg_ef = '';

    public $nombre_director_general = '';

    public $direccion_de_area = '';

    public $nombre_director_de_area = '';

    public $sub_direccion_de_area = '';

    public $nombre_sub_director_de_area = '';

    public $jefe_de_departamento = '';

    public function rules(): array
    {
        return [
            'clave_de_accion' => [
                'required',
                'string',
                Rule::unique('aditorias', 'clave_de_accion')->ignore(
                    $this->auditorias
                ),
            ],
            'entrega' => ['required'],
            'auditoria_especial' => ['required'],
            'tipo_de_auditoria' => ['required'],
            'siglas_auditoria_especial' => ['required'],
            'siglas_dg_uaa' => ['required'],
            'titulo' => ['required', 'string'],
            'ente_fiscalizado' => ['required'],
            'numero_de_auditoria' => ['required'],
            'ente_de_la_accion' => ['required'],
            'clave_accion' => ['required'],
            'dgseg_ef' => ['required'],
            'nombre_director_general' => ['required', 'string'],
            'direccion_de_area' => ['required', 'string'],
            'nombre_director_de_area' => ['required', 'string'],
            'sub_direccion_de_area' => ['required', 'string'],
            'nombre_sub_director_de_area' => ['required', 'string'],
            'jefe_de_departamento' => ['required', 'string'],
        ];
    }

    public function setAuditorias(Auditorias $auditorias)
    {
        $this->auditorias = $auditorias;

        $this->clave_de_accion = $auditorias->clave_de_accion;
        $this->entrega = $auditorias->entrega;
        $this->auditoria_especial = $auditorias->auditoria_especial;
        $this->tipo_de_auditoria = $auditorias->tipo_de_auditoria;
        $this->siglas_auditoria_especial =
            $auditorias->siglas_auditoria_especial;
        $this->siglas_dg_uaa = $auditorias->siglas_dg_uaa;
        $this->titulo = $auditorias->titulo;
        $this->ente_fiscalizado = $auditorias->ente_fiscalizado;
        $this->numero_de_auditoria = $auditorias->numero_de_auditoria;
        $this->ente_de_la_accion = $auditorias->ente_de_la_accion;
        $this->clave_accion = $auditorias->clave_accion;
        $this->dgseg_ef = $auditorias->dgseg_ef;
        $this->nombre_director_general = $auditorias->nombre_director_general;
        $this->direccion_de_area = $auditorias->direccion_de_area;
        $this->nombre_director_de_area = $auditorias->nombre_director_de_area;
        $this->sub_direccion_de_area = $auditorias->sub_direccion_de_area;
        $this->nombre_sub_director_de_area =
            $auditorias->nombre_sub_director_de_area;
        $this->jefe_de_departamento = $auditorias->jefe_de_departamento;
    }

    public function save()
    {
        $this->validate();

        $this->auditorias->update(
            $this->except([
                'auditorias',
                'entrega',
                'auditoria_especial',
                'tipo_de_auditoria',
                'siglas_auditoria_especial',
                'siglas_dg_uaa',
                'ente_fiscalizado',
                'ente_de_la_accion',
                'clave_accion',
                'dgseg_ef',
            ])
        );
    }
}
