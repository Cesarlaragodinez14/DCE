<?php

namespace App\Livewire\Dashboard\CatSiglasTipoAcciones\Forms;

use Livewire\Form;
use Livewire\Attributes\Rule;
use App\Models\CatSiglasTipoAccion;

class CreateForm extends Form
{
    #[Rule('required|string|unique:cat_siglas_tipo_accion,valor')]
    public $valor = '';

    #[Rule('nullable|string')]
    public $description = '';

    #[Rule('required|boolean')]
    public $activo = '';

    public function save()
    {
        $this->validate();

        $catSiglasTipoAccion = CatSiglasTipoAccion::create($this->except([]));

        $this->reset();

        return $catSiglasTipoAccion;
    }
}
