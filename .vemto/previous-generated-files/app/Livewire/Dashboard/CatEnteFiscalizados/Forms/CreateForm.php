<?php

namespace App\Livewire\Dashboard\CatEnteFiscalizados\Forms;

use Livewire\Form;
use Livewire\Attributes\Rule;
use App\Models\CatEnteFiscalizado;

class CreateForm extends Form
{
    #[Rule('required|string|unique:cat_ente_fiscalizado,valor')]
    public $valor = '';

    #[Rule('nullable|string')]
    public $descripcion = '';

    #[Rule('required|boolean')]
    public $activo = '';

    public function save()
    {
        $this->validate();

        $catEnteFiscalizado = CatEnteFiscalizado::create($this->except([]));

        $this->reset();

        return $catEnteFiscalizado;
    }
}
