<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Collection;
use App\Livewire\Dashboard\CatEnteDeLaAccions\Forms\CreateForm;

class CatEnteDeLaAccionCreate extends Component
{
    use WithFileUploads;

    public CreateForm $form;

    public function mount()
    {
    }

    public function save()
    {
        $this->authorize('create', CatEnteDeLaAccion::class);

        $this->validate();

        $catEnteDeLaAccion = $this->form->save();

        return redirect()->route(
            'dashboard.cat-ente-de-la-accions.edit',
            $catEnteDeLaAccion
        );
    }

    public function render()
    {
        return view('livewire.dashboard.cat-ente-de-la-accions.create', []);
    }
}
