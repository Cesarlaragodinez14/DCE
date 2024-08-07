<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Collection;
use App\Livewire\Dashboard\CatSiglasAuditoriaEspecials\Forms\CreateForm;

class CatSiglasAuditoriaEspecialCreate extends Component
{
    use WithFileUploads;

    public CreateForm $form;

    public function mount()
    {
    }

    public function save()
    {
        $this->authorize('create', CatSiglasAuditoriaEspecial::class);

        $this->validate();

        $catSiglasAuditoriaEspecial = $this->form->save();

        return redirect()->route(
            'dashboard.cat-siglas-auditoria-especials.edit',
            $catSiglasAuditoriaEspecial
        );
    }

    public function render()
    {
        return view(
            'livewire.dashboard.cat-siglas-auditoria-especials.create',
            []
        );
    }
}
