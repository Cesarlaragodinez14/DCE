<?php

namespace App\Livewire\Dashboard\CatEnteDeLaAccions\Forms;

use Livewire\Form;
use Livewire\Attributes\Rule;
use App\Models\CatEnteDeLaAccion;

class CreateForm extends Form
{
    #[Rule('required|string|unique:cat_ente_de_la_accion,valor')]
    public $valor = '';

    #[Rule('nullable|string')]
    public $descripcion = '';

    #[Rule('required|boolean')]
    public $activo = '';

    public function save()
    {
        $this->validate();

        $catEnteDeLaAccion = CatEnteDeLaAccion::create($this->except([]));

        $this->reset();

        return $catEnteDeLaAccion;
    }
}
