<?php

namespace App\Livewire\Dashboard;

use App\Models\CatUaa;
use Livewire\Component;
use App\Models\Auditorias;
use App\Models\CatEntrega;
use App\Models\CatDgsegEf;
use App\Models\CatClaveAccion;
use App\Models\CatCuentaPublica;
use App\Models\CatEnteDeLaAccion;
use Illuminate\Support\Collection;
use App\Models\CatTipoDeAuditoria;
use App\Models\CatEnteFiscalizado;
use App\Models\CatSiglasTipoAccion;
use App\Models\CatAuditoriaEspecial;
use App\Models\CatSiglasAuditoriaEspecial;
use App\Livewire\Dashboard\AllAuditorias\Forms\UpdateForm;

class AuditoriasEdit extends Component
{
    public ?Auditorias $auditorias = null;

    public UpdateForm $form;
    public Collection $catEntregas;
    public Collection $catAuditoriaEspecials;
    public Collection $catTipoDeAuditorias;
    public Collection $catSiglasAuditoriaEspecials;
    public Collection $catUaas;
    public Collection $catEnteFiscalizados;
    public Collection $catEnteDeLaAccions;
    public Collection $catClaveAccions;
    public Collection $catSiglasTipoAcciones;
    public Collection $catDgsegEfs;
    public Collection $catCuentaPublicas;

    public function mount(Auditorias $auditorias)
    {
        $this->authorize('view-any', Auditorias::class);

        $this->auditorias = $auditorias;

        $this->form->setAuditorias($auditorias);
        $this->catEntregas = CatEntrega::pluck('valor', 'id');
        $this->catAuditoriaEspecials = CatAuditoriaEspecial::pluck(
            'valor',
            'id'
        );
        $this->catTipoDeAuditorias = CatTipoDeAuditoria::pluck('valor', 'id');
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
        $this->catCuentaPublicas = CatCuentaPublica::pluck('valor', 'id');
    }

    public function save()
    {
        $this->authorize('update', $this->auditorias);

        $this->validate();

        $this->form->save();

        $this->dispatch('saved');
    }

    public function render()
    {
        return view('livewire.dashboard.all-auditorias.edit', []);
    }
}
