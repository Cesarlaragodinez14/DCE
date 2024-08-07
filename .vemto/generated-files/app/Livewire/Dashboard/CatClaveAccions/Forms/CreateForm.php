<?php

namespace App\Livewire\Dashboard\CatClaveAccions\Forms;

use Livewire\Form;
use Livewire\Attributes\Rule;
use App\Models\CatClaveAccion;

class CreateForm extends Form
{
    #[Rule('required|string|unique:cat_clave_accion,valor')]
    public $valor = '';

    #[Rule('nullable|string')]
    public $descripcion = '';

    #[Rule('required|boolean')]
    public $activo = '';

    public function save()
    {
        $this->validate();

        $catClaveAccion = CatClaveAccion::create($this->except([]));

        $this->reset();

        return $catClaveAccion;
    }
}
