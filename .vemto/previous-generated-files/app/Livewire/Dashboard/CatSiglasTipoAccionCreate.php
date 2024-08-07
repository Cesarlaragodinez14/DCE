<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Collection;
use App\Livewire\Dashboard\CatSiglasTipoAcciones\Forms\CreateForm;

class CatSiglasTipoAccionCreate extends Component
{
    use WithFileUploads;

    public CreateForm $form;

    public function mount()
    {
    }

    public function save()
    {
        $this->authorize('create', CatSiglasTipoAccion::class);

        $this->validate();

        $catSiglasTipoAccion = $this->form->save();

        return redirect()->route(
            'dashboard.cat-siglas-tipo-acciones.edit',
            $catSiglasTipoAccion
        );
    }

    public function render()
    {
        return view('livewire.dashboard.cat-siglas-tipo-acciones.create', []);
    }
}
