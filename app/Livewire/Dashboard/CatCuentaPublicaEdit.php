<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\CatCuentaPublica;
use Illuminate\Support\Collection;
use App\Livewire\Dashboard\CatCuentaPublicas\Forms\UpdateForm;

class CatCuentaPublicaEdit extends Component
{
    public ?CatCuentaPublica $catCuentaPublica = null;

    public UpdateForm $form;

    public function mount(CatCuentaPublica $catCuentaPublica)
    {
        $this->authorize('view-any', CatCuentaPublica::class);

        $this->catCuentaPublica = $catCuentaPublica;

        $this->form->setCatCuentaPublica($catCuentaPublica);
    }

    public function save()
    {
        $this->authorize('update', $this->catCuentaPublica);

        $this->validate();

        $this->form->save();

        $this->dispatch('saved');
    }

    public function render()
    {
        return view('livewire.dashboard.cat-cuenta-publicas.edit', []);
    }
}
