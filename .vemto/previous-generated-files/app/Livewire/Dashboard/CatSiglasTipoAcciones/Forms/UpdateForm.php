<?php

namespace App\Livewire\Dashboard\CatSiglasTipoAcciones\Forms;

use Livewire\Form;
use Illuminate\Validation\Rule;
use App\Models\CatSiglasTipoAccion;

class UpdateForm extends Form
{
    public ?CatSiglasTipoAccion $catSiglasTipoAccion;

    public $valor = '';

    public $description = '';

    public $activo = '';

    public function rules(): array
    {
        return [
            'valor' => [
                'required',
                'string',
                Rule::unique('cat_siglas_tipo_accion', 'valor')->ignore(
                    $this->catSiglasTipoAccion
                ),
            ],
            'description' => ['nullable', 'string'],
            'activo' => ['required', 'boolean'],
        ];
    }

    public function setCatSiglasTipoAccion(
        CatSiglasTipoAccion $catSiglasTipoAccion
    ) {
        $this->catSiglasTipoAccion = $catSiglasTipoAccion;

        $this->valor = $catSiglasTipoAccion->valor;
        $this->description = $catSiglasTipoAccion->description;
        $this->activo = $catSiglasTipoAccion->activo;
    }

    public function save()
    {
        $this->validate();

        $this->catSiglasTipoAccion->update(
            $this->except(['catSiglasTipoAccion'])
        );
    }
}
