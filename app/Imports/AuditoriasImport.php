<?php

namespace App\Imports;

use App\Models\Auditorias;
use App\Models\CatCuentaPublica;
use App\Models\CatEntrega;
use App\Models\CatDgsegEf;
use App\Models\CatClaveAccion;
use App\Models\CatEnteDeLaAccion;
use App\Models\CatTipoDeAuditoria;
use App\Models\CatEnteFiscalizado;
use App\Models\CatSiglasTipoAccion;
use App\Models\CatAuditoriaEspecial;
use App\Models\CatSiglasAuditoriaEspecial;
use App\Models\CatUaa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Import;
use Log;
use Illuminate\Support\Facades\DB;

class AuditoriasImport implements ToModel, WithHeadingRow
{
    protected $import;

    // Almacén de IDs de catálogos
    protected $catalogs = [
        'cuenta_publica' => CatCuentaPublica::class,
        'entrega' => CatEntrega::class,
        'auditoria' => CatAuditoriaEspecial::class,
        'modalidad' => CatTipoDeAuditoria::class,
        'siglas_ae' => CatSiglasAuditoriaEspecial::class,
        'siglas_dg_uaa' => CatUaa::class,
        'ente_fiscalizado' => CatEnteFiscalizado::class,
        'ente_de_la_accion' => CatEnteDeLaAccion::class,
        'clave_de_accion' => CatClaveAccion::class,
        'siglas_tipo_accion' => CatSiglasTipoAccion::class,
        'dgseg_x_ef' => CatDgsegEf::class,
    ];

    // Almacena los mapas de valores a IDs
    protected $catalogMaps = [];

    public function __construct(Import $import)
    {
        $this->import = $import;
        $this->initializeCatalogMaps();
    }

    /**
     * Inicializa los mapas de los catálogos para evitar múltiples consultas
     */
    protected function initializeCatalogMaps()
    {
        foreach ($this->catalogs as $key => $model) {
            $this->catalogMaps[$key] = $model::pluck('id', 'valor')->toArray();
        }
    }

    /**
     * Maneja la creación o actualización de registros en los catálogos
     *
     * @param string $catalogKey
     * @param string $value
     * @return int
     */
    protected function getCatalogId(string $catalogKey, string $value): int
    {
        if (isset($this->catalogMaps[$catalogKey][$value])) {
            return $this->catalogMaps[$catalogKey][$value];
        }

        // Crear nuevo registro en el catálogo
        $modelClass = $this->catalogs[$catalogKey];
        $newRecord = $modelClass::create(['valor' => $value]);
        $this->catalogMaps[$catalogKey][$value] = $newRecord->id;

        return $newRecord->id;
    }

    /**
     * Crea o actualiza una auditoría basada en la fila de datos
     *
     * @param array $row
     * @return void
     */
    protected function processRow(array $row)
    {
        // Obtener o crear IDs de los catálogos
        $data = [
            'clave_de_accion' => $this->getCatalogId('clave_de_accion', $row['clave_de_accion']),
            'cuenta_publica' => $this->getCatalogId('cuenta_publica', $row['cuenta_publica']),
            'entrega' => $this->getCatalogId('entrega', $row['entrega']),
            'auditoria_especial' => $this->getCatalogId('auditoria', $row['auditoria']),
            'tipo_de_auditoria' => $this->getCatalogId('modalidad', $row['modalidad']),
            'siglas_auditoria_especial' => $this->getCatalogId('siglas_ae', $row['siglas_ae']),
            'uaa' => $this->getCatalogId('siglas_dg_uaa', $row['siglas_dg_uaa']),
            'ente_fiscalizado' => $this->getCatalogId('ente_fiscalizado', $row['ente_fiscalizado']),
            'ente_de_la_accion' => $this->getCatalogId('ente_de_la_accion', $row['ente_de_la_accion']),
            'clave_accion' => $this->getCatalogId('clave_de_accion', $row['clave_de_accion']),
            'siglas_tipo_accion' => $this->getCatalogId('siglas_tipo_accion', $row['siglas_tipo_accion']),
            'dgseg_ef' => $this->getCatalogId('dgseg_x_ef', $row['dgseg_x_ef']),
        ];

        // Determinar si se debe actualizar una auditoría existente
        $estatusChecklist = $row['estatus_checklist'] ?? null;
        $debeActualizar = empty($estatusChecklist) || strtolower($estatusChecklist) === 'sin revisar';

        if ($debeActualizar) {
            $auditoriaExistente = Auditorias::where('clave_de_accion', $row['clave_de_accion'])->first();
            if ($auditoriaExistente) {
                $this->updateAuditoria($auditoriaExistente, $data, $row);
                Log::info("Fila actualizada exitosamente: " . $auditoriaExistente->id);
                $this->import->increment('processed_rows');
                return;
            }
        }

        // Crear una nueva auditoría
        $auditoria = $this->createAuditoria($data, $row);
        Log::info("Fila procesada exitosamente: " . $auditoria->id);
        $this->import->increment('processed_rows');
    }

    /**
     * Crea una nueva auditoría
     *
     * @param array $data
     * @param array $row
     * @return Auditorias
     */
    protected function createAuditoria(array $data, array $row): Auditorias
    {
        return Auditorias::create([
            'clave_de_accion' => $row['clave_de_accion'],
            'cuenta_publica' => $data['cuenta_publica'],
            'entrega' => $data['entrega'],
            'auditoria_especial' => $data['auditoria_especial'],
            'tipo_de_auditoria' => $data['tipo_de_auditoria'],
            'siglas_auditoria_especial' => $data['siglas_auditoria_especial'],
            'uaa' => $data['uaa'],
            'ente_fiscalizado' => $data['ente_fiscalizado'],
            'ente_de_la_accion' => $data['ente_de_la_accion'],
            'clave_accion' => $data['clave_accion'],
            'siglas_tipo_accion' => $data['siglas_tipo_accion'],
            'dgseg_ef' => $data['dgseg_ef'],
            'titulo' => $row['titulo'],
            'numero_de_auditoria' => $data['auditoria_especial'], // Verifica si este campo es correcto
            'nombre_director_general' => $row['nombre_director_general'],
            'direccion_de_area' => $row['da'],
            'nombre_director_de_area' => $row['director_de_area'],
            'sub_direccion_de_area' => $row['sub'],
            'nombre_sub_director_de_area' => $row['nombre_subdirector'],
            'jd' => $row['jd'],
            'jefe_de_departamento' => $row['nombre_jefe_de_departamento'],
            // Si deseas establecer un valor por defecto para estatus_checklist al crear una nueva auditoría, puedes agregarlo aquí
            'estatus_checklist' => $row['estatus_checklist'] ?? 'Sin Revisar',
        ]);
    }

    /**
     * Actualiza una auditoría existente
     *
     * @param Auditorias $auditoria
     * @param array $data
     * @param array $row
     * @return void
     */
    protected function updateAuditoria(Auditorias $auditoria, array $data, array $row): void
    {
        $auditoria->update([
            'clave_de_accion' => $row['clave_de_accion'],
            'cuenta_publica' => $data['cuenta_publica'],
            'entrega' => $data['entrega'],
            'auditoria_especial' => $data['auditoria_especial'],
            'tipo_de_auditoria' => $data['tipo_de_auditoria'],
            'siglas_auditoria_especial' => $data['siglas_auditoria_especial'],
            'uaa' => $data['uaa'],
            'ente_fiscalizado' => $data['ente_fiscalizado'],
            'ente_de_la_accion' => $data['ente_de_la_accion'],
            'clave_accion' => $data['clave_accion'],
            'siglas_tipo_accion' => $data['siglas_tipo_accion'],
            'dgseg_ef' => $data['dgseg_ef'],
            'titulo' => $row['titulo'],
            'numero_de_auditoria' => $data['auditoria_especial'], // Verifica si este campo es correcto
            'nombre_director_general' => $row['nombre_director_general'],
            'direccion_de_area' => $row['da'],
            'nombre_director_de_area' => $row['director_de_area'],
            'sub_direccion_de_area' => $row['sub'],
            'nombre_sub_director_de_area' => $row['nombre_subdirector'],
            'jd' => $row['jd'],
            'jefe_de_departamento' => $row['nombre_jefe_de_departamento'],
            // Actualizar estatus_checklist a "Sin Revisar - Actualizado"
            'estatus_checklist' => 'Sin Revisar - Actualizado',
        ]);
    }

    /**
     * Implementación de ToModel para cada fila
     *
     * @param array $row
     * @return Auditorias|null
     */
    public function model(array $row)
    {
        try {
            // Iniciar transacción
            DB::beginTransaction();

            // Desactivar eventos de Eloquent
            Auditorias::unsetEventDispatcher();

            Log::info("Procesando fila: " . json_encode($row));

            // Procesar la fila
            $this->processRow($row);

            // Confirmar la transacción
            DB::commit();
        } catch (\Exception $e) {
            // Revertir la transacción si ocurre un error
            DB::rollBack();
            Log::error("Error al procesar la fila: " . $e->getMessage());
        } finally {
            // Volver a activar los eventos de Eloquent
            Auditorias::setEventDispatcher(new \Illuminate\Events\Dispatcher());
        }

        // Retornar null ya que manejamos la creación directamente
        return null;
    }
}
