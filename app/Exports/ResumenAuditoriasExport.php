<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Collection;

class ResumenAuditoriasExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithEvents
{
    protected $auditorias;
    protected $filtrosAplicados;

    public function __construct(Collection $auditorias, array $filtrosAplicados = [])
    {
        $this->auditorias = $auditorias;
        $this->filtrosAplicados = $filtrosAplicados;
    }

    /**
     * Retorna la colección de auditorías a exportar.
     */
    public function collection()
    {
        return $this->auditorias;
    }

    /**
     * Define cómo se mapea cada fila en el Excel.
     */
    public function map($auditoria): array
    {
        return [
            $auditoria['clave_de_accion'],
            $auditoria['direccion_general'],
            $auditoria['entrega'],
            $auditoria['cuenta_publica'],
            $auditoria['tipo_accion'],
            $auditoria['ente_accion'],
            $auditoria['total_cambios_comentarios'],
            $auditoria['total_cambios_observaciones'],
            $auditoria['comentarios_actuales'],
            $auditoria['historial_comentarios'],
            $auditoria['historial_observaciones'],
            $auditoria['fecha_actualizacion'],
        ];
    }

    /**
     * Define los encabezados del Excel.
     */
    public function headings(): array
    {
        return [
            'Clave de Acción',
            'Dirección General',
            'Entrega',
            'Cuenta Pública',
            'Tipo de Acción',
            'Ente de la Acción',
            'Total Cambios Comentarios',
            'Total Cambios Observaciones',
            'Comentarios Actuales',
            'Historial de Comentarios',
            'Historial de Observaciones',
            'Fecha de Actualización',
        ];
    }

    /**
     * Define los anchos de las columnas.
     */
    public function columnWidths(): array
    {
        return [
            'A' => 20, // Clave de Acción
            'B' => 25, // Dirección General
            'C' => 15, // Entrega
            'D' => 15, // Cuenta Pública
            'E' => 15, // Tipo de Acción
            'F' => 20, // Ente de la Acción
            'G' => 12, // Total Cambios Comentarios
            'H' => 12, // Total Cambios Observaciones
            'I' => 30, // Comentarios Actuales
            'J' => 40, // Historial de Comentarios
            'K' => 40, // Historial de Observaciones
            'L' => 15, // Fecha de Actualización
        ];
    }

    /**
     * Define los estilos del Excel.
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo para los encabezados
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'], // Color púrpura
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Define eventos adicionales para el archivo.
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Agregar información de filtros en las primeras filas si existen filtros
                if (!empty($this->filtrosAplicados)) {
                    $this->agregarInformacionFiltros($sheet);
                }
                
                // Aplicar estilos a todas las celdas con datos
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                
                // Aplicar bordes a todas las celdas con datos
                $dataRange = 'A1:' . $highestColumn . $highestRow;
                $sheet->getStyle($dataRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC'],
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_TOP,
                        'wrapText' => true,
                    ],
                ]);
                
                // Hacer que las columnas de historial tengan texto envolvente
                $sheet->getStyle('I:K')->getAlignment()->setWrapText(true);
                
                // Aplicar color alternado a las filas de datos
                for ($row = 2; $row <= $highestRow; $row++) {
                    if ($row % 2 == 0) {
                        $sheet->getStyle("A{$row}:{$highestColumn}{$row}")->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F9FAFB'], // Gris muy claro
                            ],
                        ]);
                    }
                }
                
                // Ajustar altura de filas automáticamente
                for ($row = 1; $row <= $highestRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(-1);
                }
            },
        ];
    }

    /**
     * Agrega información de filtros aplicados al inicio del archivo.
     */
    private function agregarInformacionFiltros(Worksheet $sheet)
    {
        // Insertar filas al inicio para la información de filtros
        $cantidadFiltros = count($this->filtrosAplicados);
        $sheet->insertNewRowBefore(1, $cantidadFiltros + 3);
        
        // Título
        $sheet->setCellValue('A1', 'RESUMEN DE AUDITORÍAS - EXPORTACIÓN');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => '1F2937'],
            ],
        ]);
        
        // Fecha de exportación
        $sheet->setCellValue('A2', 'Exportado el: ' . now()->format('d/m/Y H:i:s'));
        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'size' => 10,
                'color' => ['rgb' => '6B7280'],
            ],
        ]);
        
        // Filtros aplicados
        $filaActual = 3;
        foreach ($this->filtrosAplicados as $filtro) {
            $sheet->setCellValue("A{$filaActual}", "• {$filtro}");
            $sheet->getStyle("A{$filaActual}")->applyFromArray([
                'font' => [
                    'size' => 9,
                    'color' => ['rgb' => '4B5563'],
                ],
            ]);
            $filaActual++;
        }
        
        // Línea en blanco antes de los datos
        $filaActual++;
    }
} 