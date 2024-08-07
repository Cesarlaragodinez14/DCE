<?php

namespace App\Livewire\Dashboard\CatEnteFiscalizados\Forms;

use Livewire\Form;
use Illuminate\Validation\Rule;
use App\Models\CatEnteFiscalizado;

class UpdateForm extends Form
{
    public ?CatEnteFiscalizado $catEnteFiscalizado;

    public $valor = '';

    public $descripcion = '';

    public $activo = '';

    public function rules(): array
    {
        return [
            'valor' => [
                'required',
                'string',
                Rule::unique('cat_ente_fiscalizado', 'valor')->ignore(
                    $this->catEnteFiscalizado
                ),
            ],
            'descripcion' => ['nullable', 'string'],
            'activo' => ['required', 'boolean'],
        ];
    }

    public function setCatEnteFiscalizado(
        CatEnteFiscalizado $catEnteFiscalizado
    ) {
        $this->catEnteFiscalizado = $catEnteFiscalizado;

        $this->valor = $catEnteFiscalizado->valor;
        $this->descripcion = $catEnteFiscalizado->descripcion;
        $this->activo = $catEnteFiscalizado->activo;
    }

    public function save()
    {
        $this->validate();

        $this->catEnteFiscalizado->update(
            $this->except(['catEnteFiscalizado'])
        );
    }
}
