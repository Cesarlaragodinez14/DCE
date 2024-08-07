<?php

namespace App\Livewire\Dashboard\CatEnteDeLaAccions\Forms;

use Livewire\Form;
use Illuminate\Validation\Rule;
use App\Models\CatEnteDeLaAccion;

class UpdateForm extends Form
{
    public ?CatEnteDeLaAccion $catEnteDeLaAccion;

    public $valor = '';

    public $descripcion = '';

    public $activo = '';

    public function rules(): array
    {
        return [
            'valor' => [
                'required',
                'string',
                Rule::unique('cat_ente_de_la_accion', 'valor')->ignore(
                    $this->catEnteDeLaAccion
                ),
            ],
            'descripcion' => ['nullable', 'string'],
            'activo' => ['required', 'boolean'],
        ];
    }

    public function setCatEnteDeLaAccion(CatEnteDeLaAccion $catEnteDeLaAccion)
    {
        $this->catEnteDeLaAccion = $catEnteDeLaAccion;

        $this->valor = $catEnteDeLaAccion->valor;
        $this->descripcion = $catEnteDeLaAccion->descripcion;
        $this->activo = $catEnteDeLaAccion->activo;
    }

    public function save()
    {
        $this->validate();

        $this->catEnteDeLaAccion->update($this->except(['catEnteDeLaAccion']));
    }
}
