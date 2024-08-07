<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\CatEntrega;
use Illuminate\Support\Collection;
use App\Livewire\Dashboard\CatEntregas\Forms\UpdateForm;

class CatEntregaEdit extends Component
{
    public ?CatEntrega $catEntrega = null;

    public UpdateForm $form;

    public function mount(CatEntrega $catEntrega)
    {
        $this->authorize('view-any', CatEntrega::class);

        $this->catEntrega = $catEntrega;

        $this->form->setCatEntrega($catEntrega);
    }

    public function save()
    {
        $this->authorize('update', $this->catEntrega);

        $this->validate();

        $this->form->save();

        $this->dispatch('saved');
    }

    public function render()
    {
        return view('livewire.dashboard.cat-entregas.edit', []);
    }
}
