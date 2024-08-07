<?php

namespace App\Livewire\Dashboard\CatTipoDeAuditorias\Forms;

use Livewire\Form;
use Livewire\Attributes\Rule;
use App\Models\CatTipoDeAuditoria;

class CreateForm extends Form
{
    #[Rule('required|string|unique:cat_tipo_de_auditoria,valor')]
    public $valor = '';

    #[Rule('nullable|string')]
    public $descripcion = '';

    #[Rule('required|boolean')]
    public $activo = '';

    public function save()
    {
        $this->validate();

        $catTipoDeAuditoria = CatTipoDeAuditoria::create($this->except([]));

        $this->reset();

        return $catTipoDeAuditoria;
    }
}
