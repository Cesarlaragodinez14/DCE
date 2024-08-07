<?php

namespace App\Livewire\Dashboard;

use Livewire\Form;
use App\Models\CatUaa;
use Livewire\Component;
use App\Models\Auditorias;
use App\Models\CatEntrega;
use App\Models\CatDgsegEf;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\CatClaveAccion;
use App\Models\CatEnteDeLaAccion;
use Illuminate\Support\Collection;
use App\Models\CatTipoDeAuditoria;
use App\Models\CatEnteFiscalizado;
use App\Models\CatSiglasTipoAccion;
use App\Models\CatAuditoriaEspecial;
use Illuminate\Support\Facades\Storage;
use App\Models\CatSiglasAuditoriaEspecial;
use App\Livewire\Dashboard\AllAuditorias\Forms\CreateDetailForm;
use App\Livewire\Dashboard\AllAuditorias\Forms\UpdateDetailForm;

class CatTipoDeAuditoriaAllAuditoriasDetail extends Component
{
    use WithFileUploads, WithPagination;

    public CreateDetailForm|UpdateDetailForm $form;

    public ?Auditorias $auditorias;
    public CatTipoDeAuditoria $catTipoDeAuditoria;

    public Collection $catEntregas;
    public Collection $catAuditoriaEspecials;
    public Collection $catSiglasAuditoriaEspecials;
    public Collection $catUaas;
    public Collection $catEnteFiscalizados;
    public Collection $catEnteDeLaAccions;
    public Collection $catClaveAccions;
    public Collection $catSiglasTipoAcciones;
    public Collection $catDgsegEfs;

    public $showingModal = false;

    public $detailAllAuditoriasSearch = '';
    public $detailAllAuditoriasSortField = 'updated_at';
    public $detailAllAuditoriasSortDirection = 'desc';

    public $queryString = [
        'detailAllAuditoriasSearch',
        'detailAllAuditoriasSortField',
        'detailAllAuditoriasSortDirection',
    ];

    public $confirmingAuditoriasDeletion = false;
    public string $deletingAuditorias;

    public function mount()
    {
        $this->form = new CreateDetailForm($this, 'form');

        $this->catEntregas = CatEntrega::pluck('valor', 'id');
        $this->catAuditoriaEspecials = CatAuditoriaEspecial::pluck(
            'valor',
            'id'
        );
        $this->catSiglasAuditoriaEspecials = CatSiglasAuditoriaEspecial::pluck(
            'valor',
            'id'
        );
        $this->catUaas = CatUaa::pluck('valor', 'id');
        $this->catEnteFiscalizados = CatEnteFiscalizado::pluck('valor', 'id');
        $this->catEnteDeLaAccions = CatEnteDeLaAccion::pluck('valor', 'id');
        $this->catClaveAccions = CatClaveAccion::pluck('valor', 'id');
        $this->catSiglasTipoAcciones = CatSiglasTipoAccion::pluck(
            'valor',
            'id'
        );
        $this->catDgsegEfs = CatDgsegEf::pluck('valor', 'id');
    }

    public function newAuditorias()
    {
        $this->form = new CreateDetailForm($this, 'form');
        $this->auditorias = null;

        $this->showModal();
    }

    public function editAuditorias(Auditorias $auditorias)
    {
        $this->form = new UpdateDetailForm($this, 'form');
        $this->form->setAuditorias($auditorias);
        $this->auditorias = $auditorias;

        $this->showModal();
    }

    public function showModal()
    {
        $this->showingModal = true;
    }

    public function closeModal()
    {
        $this->showingModal = false;
    }

    public function confirmAuditoriasDeletion(string $id)
    {
        $this->deletingAuditorias = $id;

        $this->confirmingAuditoriasDeletion = true;
    }

    public function deleteAuditorias(Auditorias $auditorias)
    {
        $this->authorize('delete', $auditorias);

        $auditorias->delete();

        $this->confirmingAuditoriasDeletion = false;
    }

    public function save()
    {
        if (empty($this->auditorias)) {
            $this->authorize('create', Auditorias::class);
        } else {
            $this->authorize('update', $this->auditorias);
        }

        $this->form->tipo_de_auditoria = $this->catTipoDeAuditoria->id;
        $this->form->save();

        $this->closeModal();
    }

    public function sortBy($field)
    {
        if ($this->detailAllAuditoriasSortField === $field) {
            $this->detailAllAuditoriasSortDirection =
                $this->detailAllAuditoriasSortDirection === 'asc'
                    ? 'desc'
                    : 'asc';
        } else {
            $this->detailAllAuditoriasSortDirection = 'asc';
        }

        $this->detailAllAuditoriasSortField = $field;
    }

    public function getRowsProperty()
    {
        return $this->rowsQuery->paginate(5);
    }

    public function getRowsQueryProperty()
    {
        return $this->catTipoDeAuditoria
            ->allAuditorias()
            ->orderBy(
                $this->detailAllAuditoriasSortField,
                $this->detailAllAuditoriasSortDirection
            )
            ->where(
                'clave_de_accion',
                'like',
                "%{$this->detailAllAuditoriasSearch}%"
            );
    }

    public function render()
    {
        return view(
            'livewire.dashboard.cat-tipo-de-auditorias.all-auditorias-detail',
            [
                'detailAllAuditorias' => $this->rows,
            ]
        );
    }
}
