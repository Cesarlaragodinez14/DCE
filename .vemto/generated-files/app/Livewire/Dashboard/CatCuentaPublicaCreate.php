<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Collection;
use App\Livewire\Dashboard\CatCuentaPublicas\Forms\CreateForm;

class CatCuentaPublicaCreate extends Component
{
    use WithFileUploads;

    public CreateForm $form;

    public function mount()
    {
    }

    public function save()
    {
        $this->authorize('create', CatCuentaPublica::class);

        $this->validate();

        $catCuentaPublica = $this->form->save();

        return redirect()->route(
            'dashboard.cat-cuenta-publicas.edit',
            $catCuentaPublica
        );
    }

    public function render()
    {
        return view('livewire.dashboard.cat-cuenta-publicas.create', []);
    }
}
