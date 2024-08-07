<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Collection;
use App\Models\CatSiglasTipoAccion;
use App\Livewire\Dashboard\CatSiglasTipoAcciones\Forms\UpdateForm;

class CatSiglasTipoAccionEdit extends Component
{
    public ?CatSiglasTipoAccion $catSiglasTipoAccion = null;

    public UpdateForm $form;

    public function mount(CatSiglasTipoAccion $catSiglasTipoAccion)
    {
        $this->authorize('view-any', CatSiglasTipoAccion::class);

        $this->catSiglasTipoAccion = $catSiglasTipoAccion;

        $this->form->setCatSiglasTipoAccion($catSiglasTipoAccion);
    }

    public function save()
    {
        $this->authorize('update', $this->catSiglasTipoAccion);

        $this->validate();

        $this->form->save();

        $this->dispatch('saved');
    }

    public function render()
    {
        return view('livewire.dashboard.cat-siglas-tipo-acciones.edit', []);
    }
}
