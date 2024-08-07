<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\CatDgsegEf;
use Livewire\WithPagination;

class CatDgsegEfIndex extends Component
{
    use WithPagination;

    public $search;
    public $sortField = 'updated_at';
    public $sortDirection = 'desc';

    public $queryString = ['search', 'sortField', 'sortDirection'];

    public $confirmingDeletion = false;
    public $deletingCatDgsegEf;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function confirmDeletion(string $id)
    {
        $this->deletingCatDgsegEf = $id;

        $this->confirmingDeletion = true;
    }

    public function delete(CatDgsegEf $catDgsegEf)
    {
        $catDgsegEf->delete();

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
        return CatDgsegEf::query()
            ->orderBy($this->sortField, $this->sortDirection)
            ->where('valor', 'like', "%{$this->search}%");
    }

    public function render()
    {
        return view('livewire.dashboard.cat-dgseg-efs.index', [
            'catDgsegEfs' => $this->rows,
        ]);
    }
}
