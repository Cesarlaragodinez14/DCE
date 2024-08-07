<?php

namespace App\Livewire\Dashboard\CatDgsegEfs\Forms;

use Livewire\Form;
use App\Models\CatDgsegEf;
use Illuminate\Validation\Rule;

class UpdateForm extends Form
{
    public ?CatDgsegEf $catDgsegEf;

    public $valor = '';

    public $descripcion = '';

    public $activo = '';

    public function rules(): array
    {
        return [
            'valor' => [
                'required',
                'string',
                Rule::unique('cat_dgseg_ef', 'valor')->ignore(
                    $this->catDgsegEf
                ),
            ],
            'descripcion' => ['nullable', 'string'],
            'activo' => ['required', 'boolean'],
        ];
    }

    public function setCatDgsegEf(CatDgsegEf $catDgsegEf)
    {
        $this->catDgsegEf = $catDgsegEf;

        $this->valor = $catDgsegEf->valor;
        $this->descripcion = $catDgsegEf->descripcion;
        $this->activo = $catDgsegEf->activo;
    }

    public function save()
    {
        $this->validate();

        $this->catDgsegEf->update($this->except(['catDgsegEf']));
    }
}
