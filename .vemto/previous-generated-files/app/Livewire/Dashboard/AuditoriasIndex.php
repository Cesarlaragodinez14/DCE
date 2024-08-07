<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Auditorias;
use Livewire\WithPagination;

class AuditoriasIndex extends Component
{
    use WithPagination;

    public $search;
    public $sortField = 'updated_at';
    public $sortDirection = 'desc';

    public $queryString = ['search', 'sortField', 'sortDirection'];

    public $confirmingDeletion = false;
    public $deletingAuditorias;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function confirmDeletion(string $id)
    {
        $this->deletingAuditorias = $id;

        $this->confirmingDeletion = true;
    }

    public function delete(Auditorias $auditorias)
    {
        $auditorias->delete();

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
        return Auditorias::query()
            ->orderBy($this->sortField, $this->sortDirection)
            ->where('clave_de_accion', 'like', "%{$this->search}%");
    }

    public function render()
    {
        return view('livewire.dashboard.all-auditorias.index', [
            'allAuditorias' => $this->rows,
        ]);
    }
}
