<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PdfHistory;
use App\Models\Auditorias;
use Illuminate\Support\Facades\Auth;

class PdfHistoryIndex extends Component
{
    use WithPagination;

    public $search;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    public $perPage = 10;

    protected $queryString = ['search', 'sortField', 'sortDirection'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        // Definir los campos permitidos para la ordenación
        $allowedSorts = [
            'clave_de_accion',
            'pdf_path',
            'generated_by',
            'created_at',
            // Agregar aquí los campos relacionados que deseas permitir ordenar
            'auditoria.catEntrega.valor',
            'auditoria.estatus_checklist',
            'auditoria.catUaa.valor',
            'auditoria.catDgsegEf.valor',
            'auditoria.titulo',
        ];

        if(!in_array($field, $allowedSorts)){
            return;
        }

        if($this->sortField === $field){
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        }
        else{
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }


    public function render()
    {
        $query = PdfHistory::query()
            ->select('pdf_histories.*')
            ->join('aditorias', 'pdf_histories.auditoria_id', '=', 'aditorias.id')
            ->leftJoin('cat_entrega', 'aditorias.entrega', '=', 'cat_entrega.id')
            ->leftJoin('cat_uaa', 'aditorias.uaa', '=', 'cat_uaa.id')
            ->leftJoin('cat_dgseg_ef', 'aditorias.dgseg_ef', '=', 'cat_dgseg_ef.id')
            ->with(['auditoria.catEntrega', 'auditoria.catUaa', 'auditoria.catDgsegEf', 'user']);

        // Filtrado
        if ($this->search) {
            $query->where(function($q) {
                $q->where('aditorias.clave_de_accion', 'like', '%' . $this->search . '%')
                ->orWhere('cat_entrega.valor', 'like', '%' . $this->search . '%')
                ->orWhere('cat_uaa.valor', 'like', '%' . $this->search . '%')
                ->orWhere('cat_dgseg_ef.valor', 'like', '%' . $this->search . '%')
                ->orWhere('aditorias.estatus_checklist', 'like', '%' . $this->search . '%')
                ->orWhere('aditorias.titulo', 'like', '%' . $this->search . '%');
            });
        }

        if(Auth::user()->hasRole('Director General')){
            $query->where('aditorias.uaa', Auth::user()->uaa_id);
        }

        // Filtrado adicional basado en el rol del usuario
        if (!Auth::user()->hasRole('admin') AND !Auth::user()->hasRole('Director General')) {
            $query->where('aditorias.jefe_de_departamento', Auth::user()->name);
        }

        // Ordenación
        if(in_array($this->sortField, [
            'clave_de_accion',
            'pdf_path',
            'generated_by',
            'created_at',
            'cat_entrega.id',
            'aditorias.estatus_checklist',
            'cat_uaa.id',
            'cat_dgseg_ef.id',
            'aditorias.titulo',
        ])){
            $query->orderBy($this->sortField, $this->sortDirection);
        }
        else{
            $query->orderBy('pdf_histories.created_at', 'desc');
        }

        $pdfHistories = $query->paginate($this->perPage);

        return view('livewire.dashboard.pdf-histories.index', [
            'pdfHistories' => $pdfHistories,
        ]);
    }

}
