<?php

namespace App\Livewire\Dashboard\AllAuditorias\Forms;

use Livewire\Form;
use App\Models\Auditorias;
use Livewire\Attributes\Rule;

class CreateDetailForm extends Form
{
    public $siglas_tipo_accion = null;

    #[Rule('required|string|unique:aditorias,clave_de_accion')]
    public $clave_de_accion = '';

    #[Rule('required')]
    public $entrega = '';

    #[Rule('required')]
    public $auditoria_especial = '';

    #[Rule('required')]
    public $tipo_de_auditoria = '';

    #[Rule('required')]
    public $siglas_auditoria_especial = '';

    #[Rule('required')]
    public $uaa = '';

    #[Rule('required|string')]
    public $titulo = '';

    #[Rule('required')]
    public $ente_fiscalizado = '';

    #[Rule('required')]
    public $numero_de_auditoria = '';

    #[Rule('required')]
    public $ente_de_la_accion = '';

    #[Rule('required')]
    public $clave_accion = '';

    #[Rule('required')]
    public $dgseg_ef = '';

    #[Rule('required|string')]
    public $nombre_director_general = '';

    #[Rule('required|string')]
    public $direccion_de_area = '';

    #[Rule('required|string')]
    public $nombre_director_de_area = '';

    #[Rule('required|string')]
    public $sub_direccion_de_area = '';

    #[Rule('required|string')]
    public $nombre_sub_director_de_area = '';

    #[Rule('required|string')]
    public $jefe_de_departamento = '';
    

    public function save()
    {
        $this->validate();

        $auditorias = Auditorias::create($this->except([]));

        $this->reset();

        return $auditorias;
    }
}
