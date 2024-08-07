<?php

namespace App\Livewire\Dashboard\CatAuditoriaEspecials\Forms;

use Livewire\Form;
use Illuminate\Validation\Rule;
use App\Models\CatAuditoriaEspecial;

class UpdateForm extends Form
{
    public ?CatAuditoriaEspecial $catAuditoriaEspecial;

    public $valor = '';

    public $descripcion = '';

    public $activo = '';

    public function rules(): array
    {
        return [
            'valor' => [
                'required',
                'string',
                Rule::unique('cat_auditoria_especial', 'valor')->ignore(
                    $this->catAuditoriaEspecial
                ),
            ],
            'descripcion' => ['nullable', 'string'],
            'activo' => ['required', 'boolean'],
        ];
    }

    public function setCatAuditoriaEspecial(
        CatAuditoriaEspecial $catAuditoriaEspecial
    ) {
        $this->catAuditoriaEspecial = $catAuditoriaEspecial;

        $this->valor = $catAuditoriaEspecial->valor;
        $this->descripcion = $catAuditoriaEspecial->descripcion;
        $this->activo = $catAuditoriaEspecial->activo;
    }

    public function save()
    {
        $this->validate();

        $this->catAuditoriaEspecial->update(
            $this->except(['catAuditoriaEspecial'])
        );
    }
}
