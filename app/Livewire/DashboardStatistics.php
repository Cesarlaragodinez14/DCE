<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Auditorias;
use Illuminate\Support\Facades\DB;

class DashboardStatistics extends Component
{
    public $countsByStatus;
    public $countsByUaaAndStatus;
    public $withCommentsBeforeAccepted;

    public $search = ''; // Ejemplo de campo de búsqueda, si quisieras filtrar por algo

    public function mount()
    {
        // Obtener estadísticas
        $this->countsByStatus = Auditorias::select('estatus_checklist', DB::raw('count(*) as total'))
            ->groupBy('estatus_checklist')
            ->get();

        $this->countsByUaaAndStatus = Auditorias::with('catUaa')
            ->select('uaa', 'estatus_checklist', DB::raw('count(*) as total'))
            ->groupBy('uaa', 'estatus_checklist')
            ->get();

        $this->withCommentsBeforeAccepted = Auditorias::whereNotNull('comentarios')
            ->where('estatus_checklist', '!=', 'Aceptado')
            ->count();
    }

    public function render()
    {
        return view('livewire.dashboard.statistics.index');
    }
}
