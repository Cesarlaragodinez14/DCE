<?php

namespace App\Livewire\Dashboard\CatUaas\Forms;

use Livewire\Form;
use App\Models\CatUaa;
use Livewire\Attributes\Rule;

class CreateForm extends Form
{
    #[Rule('required|string|unique:cat_uaa,valor')]
    public $valor = '';

    #[Rule('nullable|string')]
    public $descripcion = '';

    #[Rule('nullable|string')]
    public $nombre = '';

    #[Rule('required|boolean')]
    public $activo = '';

    public function save()
    {
        $this->validate();

        $catUaa = CatUaa::create($this->except([]));

        $this->reset();

        return $catUaa;
    }
}
