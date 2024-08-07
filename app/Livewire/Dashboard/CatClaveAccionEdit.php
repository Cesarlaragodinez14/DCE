<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\CatClaveAccion;
use Illuminate\Support\Collection;
use App\Livewire\Dashboard\CatClaveAccions\Forms\UpdateForm;

class CatClaveAccionEdit extends Component
{
    public ?CatClaveAccion $catClaveAccion = null;

    public UpdateForm $form;

    public function mount(CatClaveAccion $catClaveAccion)
    {
        $this->authorize('view-any', CatClaveAccion::class);

        $this->catClaveAccion = $catClaveAccion;

        $this->form->setCatClaveAccion($catClaveAccion);
    }

    public function save()
    {
        $this->authorize('update', $this->catClaveAccion);

        $this->validate();

        $this->form->save();

        $this->dispatch('saved');
    }

    public function render()
    {
        return view('livewire.dashboard.cat-clave-accions.edit', []);
    }
}
