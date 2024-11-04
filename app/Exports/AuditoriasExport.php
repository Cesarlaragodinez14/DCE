<?php

namespace App\Exports;

use App\Models\Auditorias;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AuditoriasExport implements FromCollection, WithHeadings, WithMapping
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    /**
     * Retorna la colección de auditorías con relaciones cargadas.
     */
    public function collection()
    {
        return $this->query->get();
    }

    /**
     * Define cómo se mapea cada fila en el Excel.
     */
    public function map($auditorias): array
    {
        return [
            $auditorias->id,
            $auditorias->clave_de_accion,
            $auditorias->catCuentaPublica->valor ?? 'N/A',
            $auditorias->catEntrega->valor ?? 'N/A',
            $auditorias->catAuditoriaEspecial->valor ?? 'N/A', 
            $auditorias->catTipoDeAuditoria->valor ?? 'N/A',
            $auditorias->catSiglasAuditoriaEspecial->valor ?? 'N/A',
            $auditorias->catUaa->valor ?? 'N/A',
            $auditorias->titulo,
            $auditorias->catEnteFiscalizado->valor ?? 'N/A',
            $auditorias->catEnteDeLaAccion->valor ?? 'N/A',
            $auditorias->catSiglasTipoAccion->valor ?? 'N/A',
            $auditorias->catDgsegEf->valor ?? 'N/A',
            $auditorias->nombre_director_general,
            $auditorias->direccion_de_area,
            $auditorias->nombre_director_de_area,
            $auditorias->sub_direccion_de_area,
            $auditorias->nombre_sub_director_de_area,
            $auditorias->jd,
            $auditorias->jefe_de_departamento,
            $auditorias->estatus_checklist,
            $auditorias->auditor_nombre,
            $auditorias->auditor_puesto,
            $auditorias->seguimiento_nombre,
            $auditorias->seguimiento_puesto,
            $auditorias->comentarios,
            $auditorias->estatus_firmas,
            $auditorias->archivo_uua,
            $auditorias->created_at?->format('Y-m-d H:i:s') ?? 'N/A',
            $auditorias->updated_at?->format('Y-m-d H:i:s') ?? 'N/A',
        ];
    }

    /**
     * Define los encabezados del Excel.
     */
    public function headings(): array
    {
        return [
            'ID',
            'Clave de Acción',
            'Cuenta Pública',
            'Entrega',
            'Número de Auditoría',
            'Tipo de Auditoría',
            'Siglas Auditoría Especial',
            'UAA',
            'Título',
            'Ente Fiscalizado',
            'Ente de la Acción',
            'Siglas Tipo Acción',
            'DGSEG EF',
            'Nombre Director General',
            'Dirección de Área',
            'Nombre Director de Área',
            'Sub Dirección de Área',
            'Nombre Sub Director de Área',
            'Jefatura de Departamento',
            'Nombre Jefe de Departamento',
            'Estatus Checklist',
            'Nombre Auditor',
            'Puesto Auditor',
            'Nombre Seguimiento',
            'Puesto Seguimiento',
            'Comentarios',
            'Estatus Firmas',
            'Archivo UUA',
            'Creado En',
            'Actualizado En',
        ];
    }
}
