<?php

namespace App\Livewire\Dashboard\CatCuentaPublicas\Forms;

use Livewire\Form;
use Livewire\Attributes\Rule;
use App\Models\CatCuentaPublica;

class CreateForm extends Form
{
    #[Rule('required|string|unique:cat_cuenta_publica,valor')]
    public $valor = '';

    #[Rule('nullable|string')]
    public $descripcion = '';

    #[Rule('required|boolean')]
    public $activo = '';

    public function save()
    {
        $this->validate();

        $catCuentaPublica = CatCuentaPublica::create($this->except([]));

        $this->reset();

        return $catCuentaPublica;
    }
}
