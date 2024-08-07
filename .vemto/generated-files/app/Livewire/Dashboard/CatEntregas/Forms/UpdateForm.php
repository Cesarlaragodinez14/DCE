<?php

namespace App\Livewire\Dashboard\CatEntregas\Forms;

use Livewire\Form;
use App\Models\CatEntrega;
use Illuminate\Validation\Rule;

class UpdateForm extends Form
{
    public ?CatEntrega $catEntrega;

    public $valor = '';

    public $descripcion = '';

    public $activo = '';

    public function rules(): array
    {
        return [
            'valor' => [
                'required',
                'string',
                Rule::unique('cat_entrega', 'valor')->ignore($this->catEntrega),
            ],
            'descripcion' => ['nullable', 'string'],
            'activo' => ['required', 'boolean'],
        ];
    }

    public function setCatEntrega(CatEntrega $catEntrega)
    {
        $this->catEntrega = $catEntrega;

        $this->valor = $catEntrega->valor;
        $this->descripcion = $catEntrega->descripcion;
        $this->activo = $catEntrega->activo;
    }

    public function save()
    {
        $this->validate();

        $this->catEntrega->update($this->except(['catEntrega']));
    }
}
