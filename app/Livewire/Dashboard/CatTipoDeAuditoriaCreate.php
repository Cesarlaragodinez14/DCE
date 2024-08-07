<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Collection;
use App\Livewire\Dashboard\CatTipoDeAuditorias\Forms\CreateForm;

class CatTipoDeAuditoriaCreate extends Component
{
    use WithFileUploads;

    public CreateForm $form;

    public function mount()
    {
    }

    public function save()
    {
        $this->authorize('create', CatTipoDeAuditoria::class);

        $this->validate();

        $catTipoDeAuditoria = $this->form->save();

        return redirect()->route(
            'dashboard.cat-tipo-de-auditorias.edit',
            $catTipoDeAuditoria
        );
    }

    public function render()
    {
        return view('livewire.dashboard.cat-tipo-de-auditorias.create', []);
    }
}
