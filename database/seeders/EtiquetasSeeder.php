<?php

namespace Database\Seeders;

use App\Models\CatEtiqueta;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EtiquetasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $etiquetasIniciales = [
            // Documentación
            [
                'nombre' => 'Documentación faltante',
                'descripcion' => 'Falta documentación requerida para la auditoría',
                'color' => 'red'
            ],
            [
                'nombre' => 'Documentación incompleta',
                'descripcion' => 'Documentación presentada está incompleta',
                'color' => 'orange'
            ],
            [
                'nombre' => 'Documentación incorrecta',
                'descripcion' => 'Documentación no corresponde a lo solicitado',
                'color' => 'yellow'
            ],
            
            // Fechas y Plazos
            [
                'nombre' => 'Fecha vencida',
                'descripcion' => 'Documentos o procesos con fechas vencidas',
                'color' => 'red'
            ],
            [
                'nombre' => 'Plazo incumplido',
                'descripcion' => 'Incumplimiento de plazos establecidos',
                'color' => 'red'
            ],
            
            // Montos y Cálculos
            [
                'nombre' => 'Error de cálculo',
                'descripcion' => 'Errores en cálculos aritméticos o aplicación de fórmulas',
                'color' => 'purple'
            ],
            [
                'nombre' => 'Monto inconsistente',
                'descripcion' => 'Montos que no coinciden entre documentos',
                'color' => 'purple'
            ],
            [
                'nombre' => 'Diferencia monetaria',
                'descripcion' => 'Diferencias no justificadas en importes',
                'color' => 'green'
            ],
            
            // Cumplimiento Normativo
            [
                'nombre' => 'Incumplimiento normativo',
                'descripcion' => 'Falta de cumplimiento de normas o reglamentos',
                'color' => 'red'
            ],
            [
                'nombre' => 'Proceso irregular',
                'descripcion' => 'Procesos que no siguen los procedimientos establecidos',
                'color' => 'orange'
            ],
            [
                'nombre' => 'Falta de autorización',
                'descripcion' => 'Operaciones sin las autorizaciones correspondientes',
                'color' => 'red'
            ],
            
            // Registros Contables
            [
                'nombre' => 'Error contable',
                'descripcion' => 'Errores en los registros contables',
                'color' => 'blue'
            ],
            [
                'nombre' => 'Registro incompleto',
                'descripcion' => 'Registros contables incompletos o faltantes',
                'color' => 'blue'
            ],
            [
                'nombre' => 'Clasificación incorrecta',
                'descripcion' => 'Clasificación contable incorrecta de operaciones',
                'color' => 'blue'
            ],
            
            // Transparencia y Acceso
            [
                'nombre' => 'Falta de transparencia',
                'descripcion' => 'Información no disponible o no accesible',
                'color' => 'gray'
            ],
            [
                'nombre' => 'Información inconsistente',
                'descripcion' => 'Información que presenta inconsistencias',
                'color' => 'yellow'
            ],
            
            // Controles Internos
            [
                'nombre' => 'Control interno deficiente',
                'descripcion' => 'Controles internos inadecuados o inexistentes',
                'color' => 'indigo'
            ],
            [
                'nombre' => 'Segregación de funciones',
                'descripcion' => 'Problemas en la segregación de funciones',
                'color' => 'indigo'
            ],
            
            // Procesos y Procedimientos
            [
                'nombre' => 'Procedimiento inadecuado',
                'descripcion' => 'Procedimientos que no cumplen con estándares',
                'color' => 'teal'
            ],
            [
                'nombre' => 'Proceso pendiente',
                'descripcion' => 'Procesos que no han sido completados',
                'color' => 'orange'
            ],
            
            // Recursos Humanos
            [
                'nombre' => 'Personal no autorizado',
                'descripcion' => 'Personal que actúa sin autorización adecuada',
                'color' => 'pink'
            ],
            [
                'nombre' => 'Responsabilidad unclear',
                'descripcion' => 'Responsabilidades no claramente definidas',
                'color' => 'gray'
            ]
        ];

        foreach ($etiquetasIniciales as $etiqueta) {
            CatEtiqueta::updateOrCreate(
                ['nombre' => $etiqueta['nombre']],
                [
                    'descripcion' => $etiqueta['descripcion'],
                    'color' => $etiqueta['color'],
                    'activo' => true,
                    'veces_usada' => 0
                ]
            );
        }
    }
} 