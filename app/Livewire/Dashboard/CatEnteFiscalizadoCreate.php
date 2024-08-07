<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Collection;
use App\Livewire\Dashboard\CatEnteFiscalizados\Forms\CreateForm;

class CatEnteFiscalizadoCreate extends Component
{
    use WithFileUploads;

    public CreateForm $form;

    public function mount()
    {
    }

    public function save()
    {
        $this->authorize('create', CatEnteFiscalizado::class);

        $this->validate();

        $catEnteFiscalizado = $this->form->save();

        return redirect()->route(
            'dashboard.cat-ente-fiscalizados.edit',
            $catEnteFiscalizado
        );
    }

    public function render()
    {
        return view('livewire.dashboard.cat-ente-fiscalizados.create', []);
    }
}
