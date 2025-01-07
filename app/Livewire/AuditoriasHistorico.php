<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Auditorias;
use App\Models\AuditoriasHistory;
use App\Models\ChecklistApartadoHistory;
use Illuminate\Support\Facades\Log;

class AuditoriasHistorico extends Component
{
    use WithPagination;

    public $perPage = 10; // Número de auditorías por página
    public $auditoriaFields = [
        'clave_de_accion' => 'Clave de Acción',
        'estatus_checklist' => 'Estatus Checklist',
        'auditor_nombre' => 'Auditor Nombre',
        'auditor_puesto' => 'Auditor Puesto',
        'seguimiento_nombre' => 'Seguimiento Nombre',
        'seguimiento_puesto' => 'Seguimiento Puesto',
        'comentarios' => 'Comentarios',
        'estatus_firmas' => 'Estatus Firmas',
        // Agrega más campos si es necesario
    ];

    public $apartadoFields = [
        'se_aplica' => 'Se Aplica',
        'es_obligatorio' => 'Es Obligatorio',
        'se_integra' => 'Se Integra',
        'observaciones' => 'Observaciones',
        'comentarios_uaa' => 'Comentarios UAA',
        // Agrega más campos si es necesario
    ];

    public $selectedAuditoriaId;
    public $historialAuditoria;
    public $historialChecklistApartados;
    public $dataForAuditoria = []; // Datos para Auditoría
    public $dataForChecklistApartados = []; // Datos para Checklist Apartados
    public $isLoading = false; // Indicador de carga
    public $search = ''; // Parámetro de búsqueda

    protected $paginationTheme = 'tailwind'; // Tema de paginación de Tailwind

    // Escuchar eventos de búsqueda para reiniciar la paginación
    protected $updatesQueryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Validar que los campos existen en la tabla
        $validFields = array_keys($this->auditoriaFields);

        // Construir la consulta con los campos válidos
        $auditorias = Auditorias::with(['catEntrega', 'catUaa', 'catDgsegEf'])
            ->when($this->search, function($query) use ($validFields) {
                $query->where(function($q) use ($validFields) {
                    foreach ($validFields as $field) {
                        $q->orWhere($field, 'like', '%' . $this->search . '%');
                    }
                });
            })
            ->orderBy('id', 'desc') // Ordenar por ID descendente
            ->paginate($this->perPage);

        return view('livewire.auditorias-historico', [
            'auditorias' => $auditorias,
            'auditoriaFields' => $this->auditoriaFields,
        ]);
    }

    public function loadHistorial($auditoriaId)
    {
        $this->isLoading = true;
        $this->selectedAuditoriaId = $auditoriaId;

        // Cargar el historial de la auditoría seleccionada
        $this->historialAuditoria = AuditoriasHistory::where('auditoria_id', $auditoriaId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        // Cargar el historial de los checklist apartados
        $this->historialChecklistApartados = ChecklistApartadoHistory::whereIn('checklist_apartado_id', function($query) use ($auditoriaId) {
            $query->select('id')
                  ->from('checklist_apartados')
                  ->where('auditoria_id', $auditoriaId);
        })->with('user', 'checklistApartado.apartado')
          ->orderBy('created_at', 'desc')
          ->get();

        // Limpiar las variables antes de poblarlas
        $this->dataForAuditoria = [];
        $this->dataForChecklistApartados = [];

        // Procesar los cambios de la auditoría
        foreach ($this->historialAuditoria as $history) {
            $changes = json_decode($history->changes, true);

            // Log para verificar la estructura de $changes
            Log::info("Procesando AuditoriasHistory ID {$history->id}: ", ['changes' => $changes]);

            if (!is_array($changes)) {
                // Manejar JSON malformado o no estructurado
                Log::error("Historial Auditoria ID {$history->id} tiene 'changes' malformado o no es un arreglo.");
                continue;
            }

            // Verificar que 'before' y 'after' existen y son arreglos
            if (!isset($changes['before']) || !is_array($changes['before']) ||
                !isset($changes['after']) || !is_array($changes['after'])) {
                Log::error("Historial Auditoria ID {$history->id} no contiene 'before' y 'after' como arreglos.");
                continue;
            }

            // Iterar sobre las claves de 'before' para obtener los cambios
            foreach ($changes['before'] as $field => $beforeValue) {
                // Verificar si el campo está en auditoriaFields
                if (array_key_exists($field, $this->auditoriaFields)) {
                    $afterValue = isset($changes['after'][$field]) ? $changes['after'][$field] : 'N/A';

                    // Convertir valores booleanos a 'Sí' o 'No'
                    $before = is_bool($beforeValue) ? ($beforeValue ? 'Sí' : 'No') : $beforeValue;
                    $after = is_bool($afterValue) ? ($afterValue ? 'Sí' : 'No') : $afterValue;

                    $this->dataForAuditoria[] = [
                        'date' => $history->created_at->format('d/m/Y H:i'),
                        'user' => $history->user->name,
                        'field' => $this->auditoriaFields[$field],
                        'before' => $before,
                        'after' => $after,
                    ];
                }
            }
        }

        // Procesar los cambios de los apartados del checklist
        foreach ($this->historialChecklistApartados as $history) {
            $changes = json_decode($history->changes, true);

            // Log para verificar la estructura de $changes
            Log::info("Procesando ChecklistApartadoHistory ID {$history->id}: ", ['changes' => $changes]);

            if (!is_array($changes)) {
                // Manejar JSON malformado o no estructurado
                Log::error("Historial ChecklistApartado ID {$history->id} tiene 'changes' malformado o no es un arreglo.");
                continue;
            }

            // Verificar que 'before' y 'after' existen y son arreglos
            if (!isset($changes['before']) || !is_array($changes['before']) ||
                !isset($changes['after']) || !is_array($changes['after'])) {
                Log::error("Historial ChecklistApartado ID {$history->id} no contiene 'before' y 'after' como arreglos.");
                continue;
            }

            // Iterar sobre las claves de 'before' para obtener los cambios
            foreach ($changes['before'] as $field => $beforeValue) {
                // Verificar si el campo está en apartadoFields
                if (array_key_exists($field, $this->apartadoFields)) {
                    $afterValue = isset($changes['after'][$field]) ? $changes['after'][$field] : 'N/A';

                    // Convertir valores booleanos a 'Sí' o 'No'
                    $before = is_bool($beforeValue) ? ($beforeValue ? 'Sí' : 'No') : $beforeValue;
                    $after = is_bool($afterValue) ? ($afterValue ? 'Sí' : 'No') : $afterValue;

                    $this->dataForChecklistApartados[] = [
                        'date' => $history->created_at->format('d/m/Y H:i'),
                        'user' => $history->user->name,
                        'field' => $this->apartadoFields[$field],
                        'apartado_nombre' => $history->checklistApartado->apartado->nombre ?? 'N/A',
                        'before' => $before,
                        'after' => $after,
                    ];
                }
            }
        }

        $this->isLoading = false;
    }

    public function closeModal()
    {
        $this->selectedAuditoriaId = null;
        $this->historialAuditoria = null;
        $this->historialChecklistApartados = null;
        $this->dataForAuditoria = [];
        $this->dataForChecklistApartados = [];
        $this->isLoading = false;
    }
}
