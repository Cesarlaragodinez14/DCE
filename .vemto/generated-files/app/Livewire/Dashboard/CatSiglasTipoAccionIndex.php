<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CatSiglasTipoAccion;

class CatSiglasTipoAccionIndex extends Component
{
    use WithPagination;

    public $search;
    public $sortField = 'updated_at';
    public $sortDirection = 'desc';

    public $queryString = ['search', 'sortField', 'sortDirection'];

    public $confirmingDeletion = false;
    public $deletingCatSiglasTipoAccion;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function confirmDeletion(string $id)
    {
        $this->deletingCatSiglasTipoAccion = $id;

        $this->confirmingDeletion = true;
    }

    public function delete(CatSiglasTipoAccion $catSiglasTipoAccion)
    {
        $catSiglasTipoAccion->delete();

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
        return CatSiglasTipoAccion::query()
            ->orderBy($this->sortField, $this->sortDirection)
            ->where('valor', 'like', "%{$this->search}%");
    }

    public function render()
    {
        return view('livewire.dashboard.cat-siglas-tipo-acciones.index', [
            'catSiglasTipoAcciones' => $this->rows,
        ]);
    }
}
