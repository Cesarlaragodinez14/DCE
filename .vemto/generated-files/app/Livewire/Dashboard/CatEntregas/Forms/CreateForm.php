<?php

namespace App\Livewire\Dashboard\CatEntregas\Forms;

use Livewire\Form;
use App\Models\CatEntrega;
use Livewire\Attributes\Rule;

class CreateForm extends Form
{
    #[Rule('required|string|unique:cat_entrega,valor')]
    public $valor = '';

    #[Rule('nullable|string')]
    public $descripcion = '';

    #[Rule('required|boolean')]
    public $activo = '';

    public function save()
    {
        $this->validate();

        $catEntrega = CatEntrega::create($this->except([]));

        $this->reset();

        return $catEntrega;
    }
}
