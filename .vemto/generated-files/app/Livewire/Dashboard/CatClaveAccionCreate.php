<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Collection;
use App\Livewire\Dashboard\CatClaveAccions\Forms\CreateForm;

class CatClaveAccionCreate extends Component
{
    use WithFileUploads;

    public CreateForm $form;

    public function mount()
    {
    }

    public function save()
    {
        $this->authorize('create', CatClaveAccion::class);

        $this->validate();

        $catClaveAccion = $this->form->save();

        return redirect()->route(
            'dashboard.cat-clave-accions.edit',
            $catClaveAccion
        );
    }

    public function render()
    {
        return view('livewire.dashboard.cat-clave-accions.create', []);
    }
}
