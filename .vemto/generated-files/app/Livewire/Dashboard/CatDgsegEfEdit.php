<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\CatDgsegEf;
use Illuminate\Support\Collection;
use App\Livewire\Dashboard\CatDgsegEfs\Forms\UpdateForm;

class CatDgsegEfEdit extends Component
{
    public ?CatDgsegEf $catDgsegEf = null;

    public UpdateForm $form;

    public function mount(CatDgsegEf $catDgsegEf)
    {
        $this->authorize('view-any', CatDgsegEf::class);

        $this->catDgsegEf = $catDgsegEf;

        $this->form->setCatDgsegEf($catDgsegEf);
    }

    public function save()
    {
        $this->authorize('update', $this->catDgsegEf);

        $this->validate();

        $this->form->save();

        $this->dispatch('saved');
    }

    public function render()
    {
        return view('livewire.dashboard.cat-dgseg-efs.edit', []);
    }
}
