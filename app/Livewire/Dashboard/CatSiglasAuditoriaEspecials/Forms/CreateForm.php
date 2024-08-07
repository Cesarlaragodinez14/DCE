<?php

namespace App\Livewire\Dashboard\CatSiglasAuditoriaEspecials\Forms;

use Livewire\Form;
use Livewire\Attributes\Rule;
use App\Models\CatSiglasAuditoriaEspecial;

class CreateForm extends Form
{
    #[Rule('required|string|unique:cat_siglas_auditoria_especial,valor')]
    public $valor = '';

    #[Rule('nullable|string')]
    public $descripcion = '';

    #[Rule('required|boolean')]
    public $activo = '';

    public function save()
    {
        $this->validate();

        $catSiglasAuditoriaEspecial = CatSiglasAuditoriaEspecial::create(
            $this->except([])
        );

        $this->reset();

        return $catSiglasAuditoriaEspecial;
    }
}
