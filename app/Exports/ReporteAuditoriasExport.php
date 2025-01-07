<?php

namespace App\Exports;

use App\Models\Auditorias;
use App\Models\Apartado;
use App\Models\ChecklistApartado;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize; // Para ajustar automáticamente el tamaño de las columnas
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents; // Para manejar eventos
use Maatwebsite\Excel\Events\AfterSheet; // Evento después de crear la hoja

class ReporteAuditoriasExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    protected $apartadosList;
    protected $apartadoFields = ['se_aplica', 'es_obligatorio', 'se_integra', 'observaciones', 'comentarios_uaa'];

    public function __construct()
    {
        $this->apartadosList = $this->getAllApartados();
    }

    /**
     * Recupera la colección de auditorías con las relaciones necesarias.
     */
    public function collection()
    {
        return Auditorias::with([
            'catSiglasAuditoriaEspecial',
            'catUaa',
            'catEnteFiscalizado',
            'catEnteDeLaAccion',
            'catSiglasTipoAccion',
            'catDgsegEf',
            'checklistApartados.apartado',
        ])->get();
    }

    /**
     * Define los encabezados del archivo Excel.
     */
    public function headings(): array
    {
        // Encabezados de los datos generales de la auditoría (primera fila)
        $generalHeadings = [
            'Siglas AE',
            'Siglas DG UAA',
            'Ente Fiscalizado',
            'Ente de la Acción',
            'Clave de Acción',
            'Siglas Tipo Acción',
            'DGSEG x EF',
            'Nombre Director de Área',
            'Nombre Subdirector',
            'JD',
            'Nombre Jefe de Departamento',
            'Estatus',
            'Fecha del último estatus',
            // Agrega otros campos generales si es necesario
        ];

        // Encabezados de los apartados (primera fila)
        $apartadosHeadings = [];
        foreach ($this->apartadosList as $apartadoName) {
            // Cada apartado tendrá 5 columnas (una por cada campo)
            for ($i = 0; $i < count($this->apartadoFields); $i++) {
                $apartadosHeadings[] = $apartadoName;
            }
        }

        // Combinar los encabezados generales y los de los apartados para la primera fila
        $firstRowHeadings = array_merge($generalHeadings, $apartadosHeadings);

        // Encabezados de los campos de los apartados (segunda fila)
        $apartadosFieldsHeadings = [];
        foreach ($this->apartadosList as $apartadoName) {
            foreach ($this->apartadoFields as $field) {
                $apartadosFieldsHeadings[] = $field;
            }
        }

        // Para los encabezados generales, dejamos las celdas vacías en la segunda fila
        $generalFieldsHeadings = array_fill(0, count($generalHeadings), '');

        // Combinar los encabezados de la segunda fila
        $secondRowHeadings = array_merge($generalFieldsHeadings, $apartadosFieldsHeadings);

        // Retornar un array que contiene ambas filas de encabezados
        return [$firstRowHeadings, $secondRowHeadings];
    }

    /**
     * Mapea los datos para cada fila.
     */
    public function map($auditoria): array
    {
        // Datos generales de la auditoría
        $data = [
            $auditoria->catSiglasAuditoriaEspecial->valor ?? 'N/A',
            $auditoria->catUaa->valor ?? 'N/A',
            $auditoria->catEnteFiscalizado->valor ?? 'N/A',
            $auditoria->catEnteDeLaAccion->valor ?? 'N/A',
            $auditoria->clave_de_accion,
            $auditoria->catSiglasTipoAccion->valor ?? 'N/A',
            $auditoria->catDgsegEf->valor ?? 'N/A',
            $auditoria->nombre_director_de_area,
            $auditoria->nombre_sub_director_de_area,
            $auditoria->jd,
            $auditoria->jefe_de_departamento,
            $auditoria->estatus_checklist,
            optional($auditoria->updated_at)->format('Y-m-d H:i:s') ?? 'N/A',
            // Agrega otros campos generales si es necesario
        ];

        // Obtener los valores de los apartados
        $apartadosValues = $this->getApartadosValues($auditoria);

        // Agregar los valores de los apartados en el orden de los encabezados
        foreach ($this->apartadosList as $apartadoName) {
            $values = $apartadosValues[$apartadoName] ?? [];
            foreach ($this->apartadoFields as $field) {
                $data[] = $values[$field] ?? 'N/A';
            }
        }

        return $data;
    }

    /**
     * Obtiene la lista completa de nombres de apartados.
     */
    protected function getAllApartados()
    {
        // Obtener todos los nombres de los apartados únicos ordenados
        $apartados = Apartado::orderBy('id')->pluck('nombre')->unique()->toArray();
        return $apartados;
    }

    /**
     * Obtiene los valores de los apartados para una auditoría específica.
     */
    private function getApartadosValues($auditoria)
    {
        $apartadosValues = [];

        // Obtener los checklist_apartados relacionados con la auditoría
        $checklistApartados = $auditoria->checklistApartados;

        foreach ($checklistApartados as $checklist) {
            $apartadoName = $checklist->apartado->nombre ?? 'N/A';
            $apartadosValues[$apartadoName] = [
                'se_aplica' => $this->formatValue($checklist->se_aplica),
                'es_obligatorio' => $this->formatValue($checklist->es_obligatorio),
                'se_integra' => $this->formatValue($checklist->se_integra),
                'observaciones' => $checklist->observaciones ?? 'N/A',
                'comentarios_uaa' => $checklist->comentarios_uaa ?? 'N/A',
            ];
        }

        return $apartadosValues;
    }

    /**
     * Formatea valores booleanos y otros.
     */
    private function formatValue($value)
    {
        if (is_bool($value)) {
            return $value ? 'Sí' : 'No';
        } elseif ($value === null) {
            return 'N/A';
        } else {
            return $value;
        }
    }

    /**
     * Registra los eventos para manipular el libro de Excel.
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Obtenemos la hoja activa
                $sheet = $event->sheet->getDelegate();

                // Número de columnas de datos generales
                $generalColumns = count($this->headings()[0]) - count($this->apartadosList) * count($this->apartadoFields);

                // Número total de columnas
                $totalColumns = count($this->headings()[0]);

                // Convertir el número de columna a letra
                $lastColumnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalColumns);

                // Aplicar estilos a las dos primeras filas
                $sheet->getStyle("A1:{$lastColumnLetter}2")->getFont()->setBold(true);

                // Combinar celdas para los nombres de los apartados en la primera fila
                $currentColumn = $generalColumns + 1; // Iniciar después de las columnas generales
                foreach ($this->apartadosList as $apartadoName) {
                    $startColumnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentColumn);
                    $endColumn = $currentColumn + count($this->apartadoFields) - 1;
                    $endColumnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($endColumn);

                    // Combinar las celdas para el nombre del apartado
                    $sheet->mergeCells("{$startColumnLetter}1:{$endColumnLetter}1");

                    // Centrar el texto
                    $sheet->getStyle("{$startColumnLetter}1:{$endColumnLetter}1")->getAlignment()->setHorizontal('center');

                    $currentColumn = $endColumn + 1;
                }

                // Centrar y ajustar texto de los encabezados
                $sheet->getStyle("A1:{$lastColumnLetter}2")->getAlignment()->setVertical('center');
                $sheet->getStyle("A1:{$lastColumnLetter}2")->getAlignment()->setHorizontal('center');
                $sheet->getStyle("A1:{$lastColumnLetter}2")->getAlignment()->setWrapText(true);

                // Ajustar el alto de las filas de encabezado
                $sheet->getRowDimension(1)->setRowHeight(40);
                $sheet->getRowDimension(2)->setRowHeight(20);

                // Congelar las primeras dos filas
                $sheet->freezePane('A3');
            },
        ];
    }
}
