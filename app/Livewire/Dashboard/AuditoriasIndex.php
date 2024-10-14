<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Auditorias;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

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
        $user = Auth::user();

        $query = Auditorias::query()
            ->orderBy($this->sortField, $this->sortDirection)
            ->where('clave_de_accion', 'like', "%{$this->search}%");

        // Verificar si el usuario no es admin
        if (!$user->hasRole('admin')) {
            $userName = $user->name;
            $query->where('jefe_de_departamento', $userName);
        }

        return $query;
    }

    public function render()
    {
        return view('livewire.dashboard.all-auditorias.index', [
            'allAuditorias' => $this->rows,
        ]);
    }
}
