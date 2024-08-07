<?php

namespace App\Livewire\Dashboard\CatTipoDeAuditorias\Forms;

use Livewire\Form;
use Illuminate\Validation\Rule;
use App\Models\CatTipoDeAuditoria;

class UpdateForm extends Form
{
    public ?CatTipoDeAuditoria $catTipoDeAuditoria;

    public $valor = '';

    public $descripcion = '';

    public $activo = '';

    public function rules(): array
    {
        return [
            'valor' => [
                'required',
                'string',
                Rule::unique('cat_tipo_de_auditoria', 'valor')->ignore(
                    $this->catTipoDeAuditoria
                ),
            ],
            'descripcion' => ['nullable', 'string'],
            'activo' => ['required', 'boolean'],
        ];
    }

    public function setCatTipoDeAuditoria(
        CatTipoDeAuditoria $catTipoDeAuditoria
    ) {
        $this->catTipoDeAuditoria = $catTipoDeAuditoria;

        $this->valor = $catTipoDeAuditoria->valor;
        $this->descripcion = $catTipoDeAuditoria->descripcion;
        $this->activo = $catTipoDeAuditoria->activo;
    }

    public function save()
    {
        $this->validate();

        $this->catTipoDeAuditoria->update(
            $this->except(['catTipoDeAuditoria'])
        );
    }
}
