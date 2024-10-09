<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;

class UserTable extends Component
{
    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $userIdBeingDeleted = null; // ID del usuario a eliminar
    public $showModal = false; // Controla la visibilidad del modal

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    // Método para confirmar la eliminación del usuario
    public function confirmDeletion($userId)
    {
        $this->userIdBeingDeleted = $userId; // Guardar el ID del usuario
        $this->showModal = true; // Mostrar el modal
    }

    // Método para eliminar el usuario
    public function deleteUser($userId)
    {
        User::findOrFail($userId)->delete(); // Eliminar el usuario
        session()->flash('message', 'Usuario eliminado exitosamente.');
    }

    public function search()
    {
        $this->resetPage(); // Resetea la paginación cuando se realiza la búsqueda
    }

    public function render()
    {
        return view('livewire.user-table', [
            'users' => User::query()
                ->with('roles', 'uaa') // Cargar las relaciones de roles y UAA
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('email', 'like', '%' . $this->search . '%')
                          ->orWhereHas('roles', function ($roleQuery) {
                              $roleQuery->where('name', 'like', '%' . $this->search . '%');
                          })
                          ->orWhereHas('uaa', function ($uaaQuery) {
                              $uaaQuery->where('valor', 'like', '%' . $this->search . '%');
                          });
                    });
                })
                ->when($this->sortField === 'uaa', function ($query) {
                    // Ordenar por UAA cuando se seleccione la columna UAA
                    return $query->leftJoin('cat_uaa', 'users.uaa_id', '=', 'cat_uaa.id')
                                 ->select('users.*', 'cat_uaa.valor as uaa_nombre')
                                 ->orderBy('uaa_nombre', $this->sortDirection);
                }, function ($query) {
                    if ($this->sortField === 'roles') {
                        // Ordenar por la cantidad de roles
                        return $query->withCount('roles')->orderBy('roles_count', $this->sortDirection);
                    }
                    // Ordenar por el campo seleccionado
                    return $query->orderBy($this->sortField, $this->sortDirection);
                })
                ->paginate(10),
        ]);
    }
}
