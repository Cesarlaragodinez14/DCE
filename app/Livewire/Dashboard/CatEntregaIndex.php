<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\CatEntrega;
use Livewire\WithPagination;

class CatEntregaIndex extends Component
{
    use WithPagination;

    public $search;
    public $sortField = 'updated_at';
    public $sortDirection = 'desc';

    public $queryString = ['search', 'sortField', 'sortDirection'];

    public $confirmingDeletion = false;
    public $deletingCatEntrega;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function confirmDeletion(string $id)
    {
        $this->deletingCatEntrega = $id;

        $this->confirmingDeletion = true;
    }

    public function delete(CatEntrega $catEntrega)
    {
        $catEntrega->delete();

        $this->confirmingDeletion = false;
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection =
                $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    public function getRowsProperty()
    {
        return $this->rowsQuery->paginate(5);
    }

    public function getRowsQueryProperty()
    {
        return CatEntrega::query()
            ->orderBy($this->sortField, $this->sortDirection)
            ->where('valor', 'like', "%{$this->search}%");
    }

    public function render()
    {
        return view('livewire.dashboard.cat-entregas.index', [
            'catEntregas' => $this->rows,
        ]);
    }
}
