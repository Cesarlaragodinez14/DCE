<?php

namespace App\Livewire\Dashboard\CatCuentaPublicas\Forms;

use Livewire\Form;
use Illuminate\Validation\Rule;
use App\Models\CatCuentaPublica;

class UpdateForm extends Form
{
    public ?CatCuentaPublica $catCuentaPublica;

    public $valor = '';

    public $descripcion = '';

    public $activo = '';

    public function rules(): array
    {
        return [
            'valor' => [
                'required',
                'string',
                Rule::unique('cat_cuenta_publica', 'valor')->ignore(
                    $this->catCuentaPublica
                ),
            ],
            'descripcion' => ['nullable', 'string'],
            'activo' => ['required', 'boolean'],
        ];
    }

    public function setCatCuentaPublica(CatCuentaPublica $catCuentaPublica)
    {
        $this->catCuentaPublica = $catCuentaPublica;

        $this->valor = $catCuentaPublica->valor;
        $this->descripcion = $catCuentaPublica->descripcion;
        $this->activo = $catCuentaPublica->activo;
    }

    public function save()
    {
        $this->validate();

        $this->catCuentaPublica->update($this->except(['catCuentaPublica']));
    }
}
