<?php

namespace App\Livewire\Dashboard\CatClaveAccions\Forms;

use Livewire\Form;
use App\Models\CatClaveAccion;
use Illuminate\Validation\Rule;

class UpdateForm extends Form
{
    public ?CatClaveAccion $catClaveAccion;

    public $valor = '';

    public $descripcion = '';

    public $activo = '';

    public function rules(): array
    {
        return [
            'valor' => [
                'required',
                'string',
                Rule::unique('cat_clave_accion', 'valor')->ignore(
                    $this->catClaveAccion
                ),
            ],
            'descripcion' => ['nullable', 'string'],
            'activo' => ['required', 'boolean'],
        ];
    }

    public function setCatClaveAccion(CatClaveAccion $catClaveAccion)
    {
        $this->catClaveAccion = $catClaveAccion;

        $this->valor = $catClaveAccion->valor;
        $this->descripcion = $catClaveAccion->descripcion;
        $this->activo = $catClaveAccion->activo;
    }

    public function save()
    {
        $this->validate();

        $this->catClaveAccion->update($this->except(['catClaveAccion']));
    }
}
