<?php

namespace App\Livewire\Dashboard\CatDgsegEfs\Forms;

use Livewire\Form;
use App\Models\CatDgsegEf;
use Livewire\Attributes\Rule;

class CreateForm extends Form
{
    #[Rule('required|string|unique:cat_dgseg_ef,valor')]
    public $valor = '';

    #[Rule('nullable|string')]
    public $descripcion = '';

    #[Rule('required|boolean')]
    public $activo = '';

    public function save()
    {
        $this->validate();

        $catDgsegEf = CatDgsegEf::create($this->except([]));

        $this->reset();

        return $catDgsegEf;
    }
}
