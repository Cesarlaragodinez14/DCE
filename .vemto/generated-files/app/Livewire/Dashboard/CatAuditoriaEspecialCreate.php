<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Collection;
use App\Livewire\Dashboard\CatAuditoriaEspecials\Forms\CreateForm;

class CatAuditoriaEspecialCreate extends Component
{
    use WithFileUploads;

    public CreateForm $form;

    public function mount()
    {
    }

    public function save()
    {
        $this->authorize('create', CatAuditoriaEspecial::class);

        $this->validate();

        $catAuditoriaEspecial = $this->form->save();

        return redirect()->route(
            'dashboard.cat-auditoria-especials.edit',
            $catAuditoriaEspecial
        );
    }

    public function render()
    {
        return view('livewire.dashboard.cat-auditoria-especials.create', []);
    }
}
