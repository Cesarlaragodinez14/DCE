<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Collection;
use App\Livewire\Dashboard\CatUaas\Forms\CreateForm;

class CatUaaCreate extends Component
{
    use WithFileUploads;

    public CreateForm $form;

    public function mount()
    {
    }

    public function save()
    {
        $this->authorize('create', CatUaa::class);

        $this->validate();

        $catUaa = $this->form->save();

        return redirect()->route('dashboard.cat-uaas.edit', $catUaa);
    }

    public function render()
    {
        return view('livewire.dashboard.cat-uaas.create', []);
    }
}
