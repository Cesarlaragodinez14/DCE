<?php

namespace App\Livewire\Dashboard\CatAuditoriaEspecials\Forms;

use Livewire\Form;
use Livewire\Attributes\Rule;
use App\Models\CatAuditoriaEspecial;

class CreateForm extends Form
{
    #[Rule('required|string|unique:cat_auditoria_especial,valor')]
    public $valor = '';

    #[Rule('nullable|string')]
    public $descripcion = '';

    #[Rule('required|boolean')]
    public $activo = '';

    public function save()
    {
        $this->validate();

        $catAuditoriaEspecial = CatAuditoriaEspecial::create($this->except([]));

        $this->reset();

        return $catAuditoriaEspecial;
    }
}
