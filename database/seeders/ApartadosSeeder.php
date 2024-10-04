<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Apartado;

class ApartadosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $apartados = [
            [
                'nombre' => 'Carátula del Expediente',
                'nivel' => 1,
            ],
            [
                'nombre' => 'Índice del Expediente Técnico',
                'nivel' => 1,
            ],
            [
                'nombre' => 'Orden de Auditoría',
                'nivel' => 1,
                'subapartados' => [
                    ['nombre' => 'Oficio de Orden de Auditoría', 'nivel' => 2],
                    ['nombre' => 'Acuse del Oficio de Orden de Auditoría con anexos', 'nivel' => 2],
                    ['nombre' => 'Anexo de la solicitud de información que se adjunta a la Orden de Auditoría (en su caso)', 'nivel' => 2],
                    ['nombre' => 'Oficio u oficios y acuse del o de los oficios de respuesta por parte de la Entidad Fiscalizada (en su caso)', 'nivel' => 2],
                ],
            ],
            [
                'nombre' => 'Oficio de Solicitud de Información Preliminar con anexos (en su caso)',
                'nivel' => 1,
                'subapartados' => [
                    ['nombre' => 'Oficio de Solicitud de Información Preliminar', 'nivel' => 2],
                    ['nombre' => 'Anexos de la Solicitud de Información Preliminar', 'nivel' => 2],
                    ['nombre' => 'Acuse del Oficio de Solicitud de Información Preliminar con anexos', 'nivel' => 2],
                    ['nombre' => 'Oficio u oficios y acuse del o de los oficios de respuesta por parte de la Entidad Fiscalizada', 'nivel' => 2],
                ],
            ],
            [
                'nombre' => 'Acta de Formalización e Inicio de los Trabajos de Auditoría',
                'nivel' => 1,
                'subapartados' => [
                    ['nombre' => 'Acta de Formalización e Inicio de los Trabajos de Auditoría', 'nivel' => 2],
                    ['nombre' => 'Anexos del acta con las identificaciones del personal designado por parte de la ASF y de la Entidad Fiscalizada', 'nivel' => 2],
                    ['nombre' => 'Anexo de la solicitud de información (en su caso)', 'nivel' => 2],
                    ['nombre' => 'Oficio u oficios y acuse del o de los oficios de respuesta por parte de la Entidad Fiscalizada (en su caso)', 'nivel' => 2],
                    ['nombre' => 'Oficio de solicitud de designación del enlace de la Entidad Fiscalizada', 'nivel' => 2],
                    ['nombre' => 'Acuse del oficio de designación del enlace de la Entidad Fiscalizada', 'nivel' => 2],
                ],
            ],
            [
                'nombre' => 'Oficio para dar Aviso a la Entidad Fiscalizada del Aumento, Disminución o Sustitución del Personal Actuante (en su caso)',
                'nivel' => 1,
                'subapartados' => [
                    ['nombre' => 'Oficio para dar Aviso a la Entidad Fiscalizada del Aumento, Disminución o Sustitución del Personal Actuante', 'nivel' => 2],
                    ['nombre' => 'Acuse del Oficio para dar Aviso a la Entidad Fiscalizada del Aumento, Disminución o Sustitución del Personal Actuante', 'nivel' => 2],
                    ['nombre' => 'En el caso del Oficio para dar Aviso a la Entidad Fiscalizada del Aumento, Disminución o Sustitución del Personal Actuante, incluir las identificaciones correspondientes', 'nivel' => 2],
                ],
            ],
            [
                'nombre' => 'Solicitud de Información Complementaria (en su caso)',
                'nivel' => 1,
                'subapartados' => [
                    ['nombre' => 'Oficio de Solicitud de Información Complementaria', 'nivel' => 2],
                    ['nombre' => 'Acuse del Oficio de Solicitud de Información Complementaria', 'nivel' => 2],
                    ['nombre' => 'Anexos del Oficio de la Solicitud de Información Complementaria', 'nivel' => 2],
                    ['nombre' => 'Oficio u oficios y acuse del o de los oficios de respuesta por parte de la Entidad Fiscalizada', 'nivel' => 2],
                ],
            ],
            [
                'nombre' => 'Acta de Presentación de Resultados Finales y Observaciones Preliminares',
                'nivel' => 1,
                'subapartados' => [
                    ['nombre' => 'Acta de Presentación de Resultados Finales y Observaciones Preliminares', 'nivel' => 2],
                    ['nombre' => 'Anexos del acta integrando las identificaciones del personal designado por parte de la ASF y de la Entidad Fiscalizada', 'nivel' => 2],
                    ['nombre' => 'Cédula de Resultados Finales (primera hoja de la cédula, las hojas del resultado correspondiente completo y la hoja de las firmas)', 'nivel' => 2],
                    ['nombre' => 'Anexo de los mecanismos de atención de la recomendación de que se trate', 'nivel' => 2],
                ],
            ],
            [
                'nombre' => 'Oficio de Notificación de Conclusión de los Trabajos de Auditoría (en su caso)',
                'nivel' => 1,
                'subapartados' => [
                    ['nombre' => 'Oficio de Notificación de Conclusión de los Trabajos de Auditoría', 'nivel' => 2],
                    ['nombre' => 'Acuse del Oficio de Notificación de Conclusión de los Trabajos de Auditoría', 'nivel' => 2],
                ],
            ],
            [
                'nombre' => 'Informe de Auditoría publicado (Carátula y hojas del alcance, del resultado y de la irregularidad completa)',
                'nivel' => 1,
            ],
            [
                'nombre' => 'Cédulas de Trabajo de la Irregularidad',
                'nivel' => 1,
                'subapartados' => [
                    ['nombre' => 'Sumarias', 'nivel' => 2],
                    ['nombre' => 'Analíticas identificando la integración del monto observado', 'nivel' => 2],
                    ['nombre' => 'Subanalíticas', 'nivel' => 2],
                    ['nombre' => 'Reportes fotográficos (en su caso)', 'nivel' => 2],
                    ['nombre' => 'Actas circunstanciadas, actas de visita de verificación física y, reportes fotográficos o cualquier otra evidencia, según sea el caso.', 'nivel' => 2],
                    [
                        'nombre' => '*Documentación soporte con la que se acredita la observación o irregularidad',
                        'nivel' => 2,
                        'subapartados' => [
                            ['nombre' => 'Documentación soporte con la que se acredita la observación o irregularidad', 'nivel' => 3],
                            ['nombre' => 'Presupuesto de Egresos de la Federación, así como Acuerdos, Decretos, Convenios y Cuentas por Liquidar Certificadas en los que conste que los recursos federales se asignaron y ministraron a la entidad fiscalizada, a efecto de determinar la federalidad y asignación de los recursos.', 'nivel' => 3],
                            ['nombre' => 'Contrato de apertura de la cuenta bancaria, estados de cuenta bancarios de todo el ejercicio fiscal, contratos, convenios, facturas, estimaciones, generadores, bitácoras, finiquitos, actas de entrega-recepción, actas circunstanciadas y toda aquella que acredite la irregularidad, para demostrar el origen federal de los recursos y que los mismos fueron transferidos a la Entidad Fiscalizada, así como para acreditar la erogación de los mismos.', 'nivel' => 3],
                            ['nombre' => 'Documentos que acrediten la forma en que la Entidad Fiscalizada asignó, destinó o aplicó los recursos públicos federales a fines distintos a los establecidos en la normatividad aplicable.', 'nivel' => 3],
                        ],
                    ],
                ],
            ],
            [
                'nombre' => 'Cédulas de trabajo para soportar el incumplimiento normativo del servidor público de la Entidad Fiscalizada que ejerció el recurso, considerando el periodo de gestión',
                'nivel' => 1,
            ],
            [
                'nombre' => 'Cédulas de trabajo para soportar el importe del daño o perjuicio del servidor público de la Entidad Fiscalizada que ejerció el recurso, considerando el periodo de gestión, (en el supuesto de considerarse a más de un servidor público, deberá señalarse el monto parcial observado que le corresponde a cada uno de ellos, en su caso)',
                'nivel' => 1,
            ],
            [
                'nombre' => 'Documentación de servidores públicos o particulares que ejercieron recursos federales o que se encuentran relacionados con irregularidades',
                'nivel' => 1,
                'subapartados' => [
                    ['nombre' => 'Datos de inicio y conclusión del periodo del cargo', 'nivel' => 2],
                    ['nombre' => 'Nombramientos', 'nivel' => 2],
                    ['nombre' => 'Formato Único de Movimiento - altas y bajas', 'nivel' => 2],
                    ['nombre' => 'Otros (INE, Constancia de Situación Fiscal, RFC con homoclave, CURP, comprobante de domicilio particular)', 'nivel' => 2],
                ],
            ],
            [
                'nombre' => 'Normativa infringida vigente en la temporalidad de la comisión de los hechos que motivaron la irregularidad atendiendo a la jerarquía de las disposiciones legales',
                'nivel' => 1,
            ],
            [
                'nombre' => 'Cédula Resumen',
                'nivel' => 1,
                'subapartados' => [
                    ['nombre' => 'Cédula Resumen', 'nivel' => 2],
                    ['nombre' => 'Oficio Justificatorio en caso de no haber sido acordadas las recomendaciones', 'nivel' => 2],
                ],
            ],
            [
                'nombre' => 'Certificación del Expediente',
                'nivel' => 1,
            ],
        ];

        
        foreach ($apartados as $apartadoData) {
            $this->createApartado($apartadoData, null);
        }
    }
    private function createApartado(array $apartadoData, $parentId)
    {
        // Crear el apartado
        $apartado = Apartado::create([
            'nombre' => $apartadoData['nombre'],
            'parent_id' => $parentId,
            'nivel' => $apartadoData['nivel'],
        ]);

        // Si tiene subapartados, los insertamos recursivamente
        if (isset($apartadoData['subapartados'])) {
            foreach ($apartadoData['subapartados'] as $subapartadoData) {
                $this->createApartado($subapartadoData, $apartado->id);
            }
        }
    }
}
