<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\CatEnteFiscalizado;
use Illuminate\Support\Collection;
use App\Livewire\Dashboard\CatEnteFiscalizados\Forms\UpdateForm;

class CatEnteFiscalizadoEdit extends Component
{
    public ?CatEnteFiscalizado $catEnteFiscalizado = null;

    public UpdateForm $form;

    public function mount(CatEnteFiscalizado $catEnteFiscalizado)
    {
        $this->authorize('view-any', CatEnteFiscalizado::class);

        $this->catEnteFiscalizado = $catEnteFiscalizado;

        $this->form->setCatEnteFiscalizado($catEnteFiscalizado);
    }

    public function save()
    {
        $this->authorize('update', $this->catEnteFiscalizado);

        $this->validate();

        $this->form->save();

        $this->dispatch('saved');
    }

    public function render()
    {
        return view('livewire.dashboard.cat-ente-fiscalizados.edit', []);
    }
}
