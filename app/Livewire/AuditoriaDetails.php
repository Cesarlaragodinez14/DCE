<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Auditorias;
use App\Models\ChecklistApartado;
use App\Models\AuditoriasHistory;
use App\Models\ChecklistApartadoHistory;

class AuditoriaDetails extends Component
{
    public $auditoria_id;
    public $auditoria;
    public $dataForTable = [];

    public function mount($auditoria_id)
    {
        $this->auditoria_id = $auditoria_id;
        $this->loadData();
    }

    public function loadData()
    {
        // Obtener la auditoría con sus relaciones
        $this->auditoria = Auditorias::with(['catEntrega', 'catUaa', 'catDgsegEf'])
                                     ->findOrFail($this->auditoria_id);

        // Obtener el historial de cambios de la auditoría
        $auditoriasHistories = AuditoriasHistory::where('auditoria_id', $this->auditoria_id)
                                                ->with('user')
                                                ->orderBy('created_at', 'desc')
                                                ->get();

        // Procesar los cambios de la auditoría
        $auditoriaFields = [
            'estatus_checklist' => 'Estatus de la revisión',
            'auditor_nombre' => 'Responsable de la UAA',
            'auditor_puesto' => 'Puesto del responsable de la UAA',
            'seguimiento_nombre' => 'Responsable de Seguimiento',
            'seguimiento_puesto' => 'Puesto del responsable de Seguimiento',
            'comentarios' => 'Comentarios',
            'estatus_firmas' => 'Estatus Firmas',
            // Agrega más campos si es necesario
        ];

        foreach ($auditoriaFields as $field => $fieldName) {
            $fieldData = [
                'type' => 'auditoria',
                'field' => $fieldName,
                'current_value' => $this->auditoria->$field,
                'histories' => [],
            ];

            // Agregar los cambios históricos para este campo
            foreach ($auditoriasHistories as $history) {
                $changes = json_decode($history->changes, true);
                if (isset($changes['before'][$field]) || isset($changes['after'][$field])) {
                    $fieldData['histories'][] = [
                        'date' => $history->created_at->format('d/m/Y H:i'),
                        'user' => $history->user->name,
                        'before' => $changes['before'][$field] ?? null,
                        'after' => $changes['after'][$field] ?? null,
                    ];
                }
            }

            $this->dataForTable[] = $fieldData;
        }

        // Obtener los apartados del checklist con sus relaciones
        $checklistApartados = ChecklistApartado::where('auditoria_id', $this->auditoria_id)
                                                ->with('apartado')
                                                ->get();

        // Obtener el historial de cambios de los apartados del checklist
        $checklistApartadoHistories = ChecklistApartadoHistory::whereIn('checklist_apartado_id', $checklistApartados->pluck('id'))
                                                              ->with('user', 'checklistApartado.apartado')
                                                              ->orderBy('created_at', 'desc')
                                                              ->get();

        // Procesar los cambios de los apartados del checklist
        $apartadoFields = [
            'se_aplica' => 'Se Aplica',
            'es_obligatorio' => 'Es Obligatorio',
            'se_integra' => 'Se Integra',
            'observaciones' => 'Observaciones',
            'comentarios_uaa' => 'Comentarios UAA',
            // Agrega más campos si es necesario
        ];

        foreach ($checklistApartados as $apartado) {
            foreach ($apartadoFields as $field => $fieldName) {
                $fieldData = [
                    'type' => 'apartado',
                    'apartado_nombre' => $apartado->apartado->nombre,
                    'field' => $fieldName,
                    'current_value' => $apartado->$field,
                    'histories' => [],
                ];

                // Filtrar los historiales para este apartado y campo
                foreach ($checklistApartadoHistories as $history) {
                    if ($history->checklist_apartado_id == $apartado->id) {
                        $changes = json_decode($history->changes, true);
                        if (isset($changes['before'][$field]) || isset($changes['after'][$field])) {
                            $fieldData['histories'][] = [
                                'date' => $history->created_at->format('d/m/Y H:i'),
                                'user' => $history->user->name,
                                'before' => $changes['before'][$field] ?? null,
                                'after' => $changes['after'][$field] ?? null,
                            ];
                        }
                    }
                }

                $this->dataForTable[] = $fieldData;
            }
        }
    }

    public function render()
    {
        return view('livewire.auditoria-details');
    }
}
