<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CatSiglasAuditoriaEspecial;

class CatSiglasAuditoriaEspecialIndex extends Component
{
    use WithPagination;

    public $search;
    public $sortField = 'updated_at';
    public $sortDirection = 'desc';

    public $queryString = ['search', 'sortField', 'sortDirection'];

    public $confirmingDeletion = false;
    public $deletingCatSiglasAuditoriaEspecial;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function confirmDeletion(string $id)
    {
        $this->deletingCatSiglasAuditoriaEspecial = $id;

        $this->confirmingDeletion = true;
    }

    public function delete(
        CatSiglasAuditoriaEspecial $catSiglasAuditoriaEspecial
    ) {
        $catSiglasAuditoriaEspecial->delete();

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
        return CatSiglasAuditoriaEspecial::query()
            ->orderBy($this->sortField, $this->sortDirection)
            ->where('valor', 'like', "%{$this->search}%");
    }

    public function render()
    {
        return view('livewire.dashboard.cat-siglas-auditoria-especials.index', [
            'catSiglasAuditoriaEspecials' => $this->rows,
        ]);
    }
}
