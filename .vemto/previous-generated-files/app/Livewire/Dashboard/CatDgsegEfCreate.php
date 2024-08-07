<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Collection;
use App\Livewire\Dashboard\CatDgsegEfs\Forms\CreateForm;

class CatDgsegEfCreate extends Component
{
    use WithFileUploads;

    public CreateForm $form;

    public function mount()
    {
    }

    public function save()
    {
        $this->authorize('create', CatDgsegEf::class);

        $this->validate();

        $catDgsegEf = $this->form->save();

        return redirect()->route('dashboard.cat-dgseg-efs.edit', $catDgsegEf);
    }

    public function render()
    {
        return view('livewire.dashboard.cat-dgseg-efs.create', []);
    }
}
