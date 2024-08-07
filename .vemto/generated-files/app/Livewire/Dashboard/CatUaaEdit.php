<?php

namespace App\Livewire\Dashboard;

use App\Models\CatUaa;
use Livewire\Component;
use Illuminate\Support\Collection;
use App\Livewire\Dashboard\CatUaas\Forms\UpdateForm;

class CatUaaEdit extends Component
{
    public ?CatUaa $catUaa = null;

    public UpdateForm $form;

    public function mount(CatUaa $catUaa)
    {
        $this->authorize('view-any', CatUaa::class);

        $this->catUaa = $catUaa;

        $this->form->setCatUaa($catUaa);
    }

    public function save()
    {
        $this->authorize('update', $this->catUaa);

        $this->validate();

        $this->form->save();

        $this->dispatch('saved');
    }

    public function render()
    {
        return view('livewire.dashboard.cat-uaas.edit', []);
    }
}
