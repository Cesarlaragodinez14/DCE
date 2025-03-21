<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Auditorias;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AuditoriasExport;
use App\Models\CatCuentaPublica;
use App\Models\CatEntrega;

class AuditoriasIndex extends Component
{
    use WithPagination;

    protected $listeners = ['resetClaveAccion'];

    public $search;
    public $sortField = 'updated_at';
    public $sortDirection = 'desc';

    // Propiedades para los filtros
    public $entregaId;
    public $cuentaPublicaId;

    public $confirmingDeletion = false;
    public $deletingAuditorias;

    // Incluir las nuevas propiedades en $queryString
    public $queryString = ['search', 'sortField', 'sortDirection', 'entregaId', 'cuentaPublicaId'];

    public $cuentaPublica;
    public $entrega;

    public function mount()
    {
        $this->entregaId = request()->get('entrega');
        $this->cuentaPublicaId = request()->get('cuenta_publica');

        $this->cuentaPublica = CatCuentaPublica::all();
        $this->entrega = CatEntrega::all();
    }

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
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    public function exportExcel()
    {
        // Verificar permisos
        if (!Auth::user()->hasAnyRole(['admin', 'Jefe de departamento', 'Director General', 'Auditor habilitado UAA'])) {
            abort(403, 'No tienes permiso para exportar datos.');
        }

        $fecha = now()->format('dmY-His'); // Formato: ddmmyyyy-his
        $nombreArchivo = "auditorias_{$fecha}.xlsx";
        
        try {
            return Excel::download(new AuditoriasExport($this->rowsQuery), $nombreArchivo);
        } catch (\Exception $e) {
            session()->flash('error', 'Hubo un problema al generar el archivo Excel.');
            return redirect()->back();
        }
    } 

    public function getRowsProperty()
    {
        return $this->rowsQuery->paginate(50);
    }

    public function getRowsQueryProperty()
    {
        $user = Auth::user();

        $query = Auditorias::query()
            ->with([
                'catCuentaPublica',
                'catEntrega',
                'catSiglasAuditoriaEspecial',
                'catUaa',
                'catEnteDeLaAccion',
                'catDgsegEf',
                'catAuditoriaEspecial',
                'catTipoDeAuditoria',
                'catEnteFiscalizado',
                'catClaveAccion',
                'catSiglasTipoAccion',
            ])
            ->orderBy($this->sortField, $this->sortDirection)
            ->where('clave_de_accion', 'like', "%{$this->search}%");

        // Aplicar filtros si están definidos, especificando la tabla de "entrega"
        if ($this->entregaId) {
            $query->where('aditorias.entrega', $this->entregaId); // Evita ambigüedad
        }

        if ($this->cuentaPublicaId) {
            $query->where('aditorias.cuenta_publica', $this->cuentaPublicaId); // Evita ambigüedad
        }


        // Si el usuario tiene el rol "Auditor habilitado UAA", hacemos el join con "entregas"
        if ($user->hasRole('Auditor habilitado UAA')) {
            return $query->select('aditorias.*') // Especificamos explícitamente que tomamos las columnas de aditorias
                ->leftJoin('entregas', 'aditorias.id', '=', 'entregas.auditoria_id')
                ->where('entregas.responsable', 'LIKE', "%{$user->name}%");
        }

        // Verificar roles y ajustar la consulta
        if (
            !$user->hasRole(['admin', 'Director General', 'Auditor habilitado UAA', 'Auditor habilitado'])
            && $user->email != "uaapruebas@asf.gob.mx"
        ) {
            $query->where('aditorias.jefe_de_departamento', $user->name); // Evita ambigüedad
        }

        if (($user->hasRole('Director General') || $user->hasRole('Auditor habilitado UAA')) && $user->email != "uaapruebas@asf.gob.mx") {
            $userUAA = $user->uaa_id;
            $query->where('aditorias.uaa', $userUAA); // Evita ambigüedad
        }

        if ($user->hasRole('Auditor habilitado')) {
            $query->where('aditorias.seguimiento_nombre', $user->name); // Evita ambigüedad
        }

        return $query;
    }

    public function render()
    {
        return view('livewire.dashboard.all-auditorias.index', [
            'allAuditorias' => $this->rows,
            'cuentaPublica' => $this->cuentaPublica,
            'entrega' => $this->entrega,
        ]);
    }

}
