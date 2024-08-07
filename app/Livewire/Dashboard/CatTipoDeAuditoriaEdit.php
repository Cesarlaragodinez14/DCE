<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\CatTipoDeAuditoria;
use Illuminate\Support\Collection;
use App\Livewire\Dashboard\CatTipoDeAuditorias\Forms\UpdateForm;

class CatTipoDeAuditoriaEdit extends Component
{
    public ?CatTipoDeAuditoria $catTipoDeAuditoria = null;

    public UpdateForm $form;

    public function mount(CatTipoDeAuditoria $catTipoDeAuditoria)
    {
        $this->authorize('view-any', CatTipoDeAuditoria::class);

        $this->catTipoDeAuditoria = $catTipoDeAuditoria;

        $this->form->setCatTipoDeAuditoria($catTipoDeAuditoria);
    }

    public function save()
    {
        $this->authorize('update', $this->catTipoDeAuditoria);

        $this->validate();

        $this->form->save();

        $this->dispatch('saved');
    }

    public function render()
    {
        return view('livewire.dashboard.cat-tipo-de-auditorias.edit', []);
    }
}
