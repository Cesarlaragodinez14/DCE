<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Collection;
use App\Models\CatAuditoriaEspecial;
use App\Livewire\Dashboard\CatAuditoriaEspecials\Forms\UpdateForm;

class CatAuditoriaEspecialEdit extends Component
{
    public ?CatAuditoriaEspecial $catAuditoriaEspecial = null;

    public UpdateForm $form;

    public function mount(CatAuditoriaEspecial $catAuditoriaEspecial)
    {
        $this->authorize('view-any', CatAuditoriaEspecial::class);

        $this->catAuditoriaEspecial = $catAuditoriaEspecial;

        $this->form->setCatAuditoriaEspecial($catAuditoriaEspecial);
    }

    public function save()
    {
        $this->authorize('update', $this->catAuditoriaEspecial);

        $this->validate();

        $this->form->save();

        $this->dispatch('saved');
    }

    public function render()
    {
        return view('livewire.dashboard.cat-auditoria-especials.edit', []);
    }
}
