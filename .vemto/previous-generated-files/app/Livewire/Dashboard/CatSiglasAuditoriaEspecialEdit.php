<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Collection;
use App\Models\CatSiglasAuditoriaEspecial;
use App\Livewire\Dashboard\CatSiglasAuditoriaEspecials\Forms\UpdateForm;

class CatSiglasAuditoriaEspecialEdit extends Component
{
    public ?CatSiglasAuditoriaEspecial $catSiglasAuditoriaEspecial = null;

    public UpdateForm $form;

    public function mount(
        CatSiglasAuditoriaEspecial $catSiglasAuditoriaEspecial
    ) {
        $this->authorize('view-any', CatSiglasAuditoriaEspecial::class);

        $this->catSiglasAuditoriaEspecial = $catSiglasAuditoriaEspecial;

        $this->form->setCatSiglasAuditoriaEspecial($catSiglasAuditoriaEspecial);
    }

    public function save()
    {
        $this->authorize('update', $this->catSiglasAuditoriaEspecial);

        $this->validate();

        $this->form->save();

        $this->dispatch('saved');
    }

    public function render()
    {
        return view(
            'livewire.dashboard.cat-siglas-auditoria-especials.edit',
            []
        );
    }
}
