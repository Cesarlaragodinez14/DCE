<?php

namespace App\Livewire\Dashboard\CatSiglasAuditoriaEspecials\Forms;

use Livewire\Form;
use Illuminate\Validation\Rule;
use App\Models\CatSiglasAuditoriaEspecial;

class UpdateForm extends Form
{
    public ?CatSiglasAuditoriaEspecial $catSiglasAuditoriaEspecial;

    public $valor = '';

    public $descripcion = '';

    public $activo = '';

    public function rules(): array
    {
        return [
            'valor' => [
                'required',
                'string',
                Rule::unique('cat_siglas_auditoria_especial', 'valor')->ignore(
                    $this->catSiglasAuditoriaEspecial
                ),
            ],
            'descripcion' => ['nullable', 'string'],
            'activo' => ['required', 'boolean'],
        ];
    }

    public function setCatSiglasAuditoriaEspecial(
        CatSiglasAuditoriaEspecial $catSiglasAuditoriaEspecial
    ) {
        $this->catSiglasAuditoriaEspecial = $catSiglasAuditoriaEspecial;

        $this->valor = $catSiglasAuditoriaEspecial->valor;
        $this->descripcion = $catSiglasAuditoriaEspecial->descripcion;
        $this->activo = $catSiglasAuditoriaEspecial->activo;
    }

    public function save()
    {
        $this->validate();

        $this->catSiglasAuditoriaEspecial->update(
            $this->except(['catSiglasAuditoriaEspecial'])
        );
    }
}
