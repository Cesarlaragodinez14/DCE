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
        'estatus_checklist' => 'Estatus de la revisión',
        'auditor_nombre' => 'Responsable de la UAA',
        'auditor_puesto' => 'Puesto del responsable de la UAA',
        'seguimiento_nombre' => 'Responsable de Seguimiento',
        'seguimiento_puesto' => 'Puesto del responsable de Seguimiento',
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
    public $apartadosWithChanges = []; // Datos agrupados por apartados
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

        // Cargar el historial de los checklist apartados con apartado información
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
        $this->apartadosWithChanges = [];

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
                    $afterValue = isset($changes['after'][$field]) ? $changes['after'][$field] : null;

                    // Formatear valores
                    $before = $this->formatValue($beforeValue);
                    $after = $this->formatValue($afterValue);

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

        // Procesar los cambios de los apartados del checklist y agrupar por apartado
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

            $apartadoNombre = $history->checklistApartado->apartado->nombre ?? 'Apartado Desconocido';
            $apartadoId = $history->checklistApartado->apartado->id ?? 'unknown';

            // Inicializar el array del apartado si no existe
            if (!isset($this->apartadosWithChanges[$apartadoId])) {
                $this->apartadosWithChanges[$apartadoId] = [
                    'nombre' => $apartadoNombre,
                    'cambios' => []
                ];
            }

            // Iterar sobre las claves de 'before' para obtener los cambios
            foreach ($changes['before'] as $field => $beforeValue) {
                // Verificar si el campo está en apartadoFields (excluir updated_at ya que no es relevante para el usuario)
                if (array_key_exists($field, $this->apartadoFields) && $field !== 'updated_at') {
                    $afterValue = isset($changes['after'][$field]) ? $changes['after'][$field] : null;

                    // Formatear valores
                    $before = $this->formatValue($beforeValue);
                    $after = $this->formatValue($afterValue);

                    // Solo agregar si realmente hay un cambio visible
                    if ($before !== $after) {
                        // Agregar al apartado correspondiente
                        $this->apartadosWithChanges[$apartadoId]['cambios'][] = [
                            'date' => $history->created_at->format('d/m/Y H:i'),
                            'user' => $history->user->name,
                            'field' => $this->apartadoFields[$field],
                            'apartado_nombre' => $apartadoNombre,
                            'before' => $before,
                            'after' => $after,
                        ];

                        // También mantener la lista global para compatibilidad
                        $this->dataForChecklistApartados[] = [
                            'date' => $history->created_at->format('d/m/Y H:i'),
                            'user' => $history->user->name,
                            'field' => $this->apartadoFields[$field],
                            'apartado_nombre' => $apartadoNombre,
                            'before' => $before,
                            'after' => $after,
                        ];
                    }
                }
            }
        }

        $this->isLoading = false;
    }

    /**
     * Formatear valores para mostrar en el historial
     */
    private function formatValue($value)
    {
        if (is_null($value)) {
            return 'Sin valor';
        }
        
        if (is_bool($value)) {
            return $value ? 'Sí' : 'No';
        }
        
        if (is_string($value) && trim($value) === '') {
            return 'Vacío';
        }
        
        if (is_numeric($value)) {
            // Si es 0 o 1, probablemente sea un booleano representado como número
            if ($value === 0 || $value === '0') {
                return 'No';
            }
            if ($value === 1 || $value === '1') {
                return 'Sí';
            }
        }
        
        return (string) $value;
    }

    public function closeModal()
    {
        $this->selectedAuditoriaId = null;
        $this->historialAuditoria = null;
        $this->historialChecklistApartados = null;
        $this->dataForAuditoria = [];
        $this->dataForChecklistApartados = [];
        $this->apartadosWithChanges = [];
        $this->isLoading = false;
    }
}
