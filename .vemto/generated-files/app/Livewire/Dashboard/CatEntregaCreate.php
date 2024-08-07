<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Collection;
use App\Livewire\Dashboard\CatEntregas\Forms\CreateForm;

class CatEntregaCreate extends Component
{
    use WithFileUploads;

    public CreateForm $form;

    public function mount()
    {
    }

    public function save()
    {
        $this->authorize('create', CatEntrega::class);

        $this->validate();

        $catEntrega = $this->form->save();

        return redirect()->route('dashboard.cat-entregas.edit', $catEntrega);
    }

    public function render()
    {
        return view('livewire.dashboard.cat-entregas.create', []);
    }
}
