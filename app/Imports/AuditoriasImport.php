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
            Log::info("Procesando fila: " . json_encode($row));
            $cuentaPublica = CatCuentaPublica::firstOrCreate(['valor' => $row['cuenta_publica']]);
            $entrega = CatEntrega::firstOrCreate(['valor' => $row['entrega']]);
            $auditoriaEspecial = CatAuditoriaEspecial::firstOrCreate(['valor' => $row['auditoria']]);
            $tipoDeAuditoria = CatTipoDeAuditoria::firstOrCreate(['valor' => $row['modalidad']]);
            $siglasAuditoriaEspecial = CatSiglasAuditoriaEspecial::firstOrCreate(['valor' => $row['siglas_ae']]);
            $uaa = CatUaa::firstOrCreate(['valor' => $row['siglas_dg_uaa']]);
            $enteFiscalizado = CatEnteFiscalizado::firstOrCreate(['valor' => $row['ente_fiscalizado']]);
            $enteDeLaAccion = CatEnteDeLaAccion::firstOrCreate(['valor' => $row['ente_de_la_accion']]);
            $claveAccion = CatClaveAccion::firstOrCreate(['valor' => $row['clave_de_accion']]);
            $siglasTipoAccion = CatSiglasTipoAccion::firstOrCreate(['valor' => $row['siglas_tipo_accion']]);
            $dgsegEf = CatDgsegEf::firstOrCreate(['valor' => $row['dgseg_x_ef']]);

            // Crear la auditoría
            $auditoria = Auditorias::create([
                'clave_de_accion' => $row['clave_de_accion'],
                'cuenta_publica' => $cuentaPublica->id,
                'entrega' => $entrega->id,
                'auditoria_especial' => $auditoriaEspecial->id,
                'tipo_de_auditoria' => $tipoDeAuditoria->id,
                'siglas_auditoria_especial' => $siglasAuditoriaEspecial->id,
                'uaa' => $uaa->id,
                'ente_fiscalizado' => $enteFiscalizado->id,
                'ente_de_la_accion' => $enteDeLaAccion->id,
                'clave_accion' => $claveAccion->id,
                'siglas_tipo_accion' => $siglasTipoAccion->id,
                'dgseg_ef' => $dgsegEf->id,
                'titulo' => $row['titulo'],
                'numero_de_auditoria' => $auditoriaEspecial->id,
                'nombre_director_general' => $row['nombre_director_general'],
                'direccion_de_area' => $row['da'],
                'nombre_director_de_area' => $row['director_de_area'],
                'sub_direccion_de_area' => $row['sub'],
                'nombre_sub_director_de_area' => $row['nombre_subdirector'],
                'jefe_de_departamento' => $row['jd'],
            ]);

            // Incrementar el número de filas procesadas
            Log::info("Fila procesada exitosamente: " . $auditoria->id);
            $this->import->increment('processed_rows');
        } catch (\Exception $e) {
            Log::error("Error al procesar la fila: " . $e->getMessage());
        }
    }
}
