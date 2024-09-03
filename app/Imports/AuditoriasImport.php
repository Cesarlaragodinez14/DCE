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

class AuditoriasImport implements ToModel, WithHeadingRow
{
    protected $import;

    public function __construct(Import $import)
    {
        $this->import = $import;
    }

    public function model(array $row)
    {
        try {
            // Iniciar transacción
            \DB::beginTransaction();

            // Desactivar eventos de Eloquent
            Auditorias::unsetEventDispatcher();

            Log::info("Procesando fila: " . json_encode($row));

            // Cargar todos los valores existentes de los catálogos en un solo paso
            static $cuentaPublicaIds = null;
            static $entregaIds = null;
            static $auditoriaEspecialIds = null;
            static $tipoDeAuditoriaIds = null;
            static $siglasAuditoriaEspecialIds = null;
            static $uaaIds = null;
            static $enteFiscalizadoIds = null;
            static $enteDeLaAccionIds = null;
            static $claveAccionIds = null;
            static $siglasTipoAccionIds = null;
            static $dgsegEfIds = null;

            // Si los valores no están cargados en memoria, cargarlos
            if ($cuentaPublicaIds === null) {
                $cuentaPublicaIds = CatCuentaPublica::pluck('id', 'valor');
                $entregaIds = CatEntrega::pluck('id', 'valor');
                $auditoriaEspecialIds = CatAuditoriaEspecial::pluck('id', 'valor');
                $tipoDeAuditoriaIds = CatTipoDeAuditoria::pluck('id', 'valor');
                $siglasAuditoriaEspecialIds = CatSiglasAuditoriaEspecial::pluck('id', 'valor');
                $uaaIds = CatUaa::pluck('id', 'valor');
                $enteFiscalizadoIds = CatEnteFiscalizado::pluck('id', 'valor');
                $enteDeLaAccionIds = CatEnteDeLaAccion::pluck('id', 'valor');
                $claveAccionIds = CatClaveAccion::pluck('id', 'valor');
                $siglasTipoAccionIds = CatSiglasTipoAccion::pluck('id', 'valor');
                $dgsegEfIds = CatDgsegEf::pluck('id', 'valor');
            }

            // Obtener o crear nuevos registros en la memoria antes de insertarlos
            $cuentaPublicaId = $cuentaPublicaIds[$row['cuenta_publica']] ?? 
                $cuentaPublicaIds[$row['cuenta_publica']] = CatCuentaPublica::create(['valor' => $row['cuenta_publica']])->id;
            $entregaId = $entregaIds[$row['entrega']] ?? 
                $entregaIds[$row['entrega']] = CatEntrega::create(['valor' => $row['entrega']])->id;
            $auditoriaEspecialId = $auditoriaEspecialIds[$row['auditoria']] ?? 
                $auditoriaEspecialIds[$row['auditoria']] = CatAuditoriaEspecial::create(['valor' => $row['auditoria']])->id;
            $tipoDeAuditoriaId = $tipoDeAuditoriaIds[$row['modalidad']] ?? 
                $tipoDeAuditoriaIds[$row['modalidad']] = CatTipoDeAuditoria::create(['valor' => $row['modalidad']])->id;
            $siglasAuditoriaEspecialId = $siglasAuditoriaEspecialIds[$row['siglas_ae']] ?? 
                $siglasAuditoriaEspecialIds[$row['siglas_ae']] = CatSiglasAuditoriaEspecial::create(['valor' => $row['siglas_ae']])->id;
            $uaaId = $uaaIds[$row['siglas_dg_uaa']] ?? 
                $uaaIds[$row['siglas_dg_uaa']] = CatUaa::create(['valor' => $row['siglas_dg_uaa']])->id;
            $enteFiscalizadoId = $enteFiscalizadoIds[$row['ente_fiscalizado']] ?? 
                $enteFiscalizadoIds[$row['ente_fiscalizado']] = CatEnteFiscalizado::create(['valor' => $row['ente_fiscalizado']])->id;
            $enteDeLaAccionId = $enteDeLaAccionIds[$row['ente_de_la_accion']] ?? 
                $enteDeLaAccionIds[$row['ente_de_la_accion']] = CatEnteDeLaAccion::create(['valor' => $row['ente_de_la_accion']])->id;
            $claveAccionId = $claveAccionIds[$row['clave_de_accion']] ?? 
                $claveAccionIds[$row['clave_de_accion']] = CatClaveAccion::create(['valor' => $row['clave_de_accion']])->id;
            $siglasTipoAccionId = $siglasTipoAccionIds[$row['siglas_tipo_accion']] ?? 
                $siglasTipoAccionIds[$row['siglas_tipo_accion']] = CatSiglasTipoAccion::create(['valor' => $row['siglas_tipo_accion']])->id;
            $dgsegEfId = $dgsegEfIds[$row['dgseg_x_ef']] ?? 
                $dgsegEfIds[$row['dgseg_x_ef']] = CatDgsegEf::create(['valor' => $row['dgseg_x_ef']])->id;

            // Crear la auditoría
            $auditoria = Auditorias::create([
                'clave_de_accion' => $row['clave_de_accion'],
                'cuenta_publica' => $cuentaPublicaId,
                'entrega' => $entregaId,
                'auditoria_especial' => $auditoriaEspecialId,
                'tipo_de_auditoria' => $tipoDeAuditoriaId,
                'siglas_auditoria_especial' => $siglasAuditoriaEspecialId,
                'uaa' => $uaaId,
                'ente_fiscalizado' => $enteFiscalizadoId,
                'ente_de_la_accion' => $enteDeLaAccionId,
                'clave_accion' => $claveAccionId,
                'siglas_tipo_accion' => $siglasTipoAccionId,
                'dgseg_ef' => $dgsegEfId,
                'titulo' => $row['titulo'],
                'numero_de_auditoria' => $auditoriaEspecialId,
                'nombre_director_general' => $row['nombre_director_general'],
                'direccion_de_area' => $row['da'],
                'nombre_director_de_area' => $row['director_de_area'],
                'sub_direccion_de_area' => $row['sub'],
                'nombre_sub_director_de_area' => $row['nombre_subdirector'],
                'jd' => $row['jd'],
                'jefe_de_departamento' => $row['nombre_jefe_de_departamento'],
            ]);

            Log::info("Fila procesada exitosamente: " . $auditoria->id);
            $this->import->increment('processed_rows');

            // Confirmar la transacción
            \DB::commit();
        } catch (\Exception $e) {
            // Revertir la transacción si ocurre un error
            \DB::rollBack();
            Log::error("Error al procesar la fila: " . $e->getMessage());
        } finally {
            // Volver a activar los eventos de Eloquent
            Auditorias::setEventDispatcher(new \Illuminate\Events\Dispatcher());
        }
    }

}
