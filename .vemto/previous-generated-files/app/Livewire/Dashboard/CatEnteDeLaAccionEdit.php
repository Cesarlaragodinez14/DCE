<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\CatEnteDeLaAccion;
use Illuminate\Support\Collection;
use App\Livewire\Dashboard\CatEnteDeLaAccions\Forms\UpdateForm;

class CatEnteDeLaAccionEdit extends Component
{
    public ?CatEnteDeLaAccion $catEnteDeLaAccion = null;

    public UpdateForm $form;

    public function mount(CatEnteDeLaAccion $catEnteDeLaAccion)
    {
        $this->authorize('view-any', CatEnteDeLaAccion::class);

        $this->catEnteDeLaAccion = $catEnteDeLaAccion;

        $this->form->setCatEnteDeLaAccion($catEnteDeLaAccion);
    }

    public function save()
    {
        $this->authorize('update', $this->catEnteDeLaAccion);

        $this->validate();

        $this->form->save();

        $this->dispatch('saved');
    }

    public function render()
    {
        return view('livewire.dashboard.cat-ente-de-la-accions.edit', []);
    }
}
