<?php

namespace App\Livewire\Dashboard\CatUaas\Forms;

use Livewire\Form;
use App\Models\CatUaa;
use Illuminate\Validation\Rule;

class UpdateForm extends Form
{
    public ?CatUaa $catUaa;

    public $valor = '';

    public $descripcion = '';

    public $activo = '';

    public function rules(): array
    {
        return [
            'valor' => [
                'required',
                'string',
                Rule::unique('cat_uaa', 'valor')->ignore($this->catUaa),
            ],
            'descripcion' => ['nullable', 'string'],
            'activo' => ['required', 'boolean'],
        ];
    }

    public function setCatUaa(CatUaa $catUaa)
    {
        $this->catUaa = $catUaa;

        $this->valor = $catUaa->valor;
        $this->descripcion = $catUaa->descripcion;
        $this->activo = $catUaa->activo;
    }

    public function save()
    {
        $this->validate();

        $this->catUaa->update($this->except(['catUaa']));
    }
}
