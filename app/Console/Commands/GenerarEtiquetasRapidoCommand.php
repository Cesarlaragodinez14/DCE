<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\GenerarEtiquetasJob;
use App\Models\Auditorias;
use App\Models\AuditoriaEtiqueta;
use App\Models\CatEtiqueta;
use App\Models\ChecklistApartado;
use Illuminate\Http\Request;
use ReflectionClass;

class GenerarEtiquetasRapidoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'etiquetas:generar-rapido 
                            {--auditoria-id= : ID específico de auditoría a procesar}
                            {--ultra-rapido : Usar configuración ultra rápida (sin pausas)}
                            {--stats : Mostrar estadísticas antes de procesar}
                            {--dry-run : Solo mostrar qué se va a procesar sin ejecutar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generar etiquetas de manera optimizada y rápida para todas las auditorías o una específica';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Iniciando generación de etiquetas optimizada');
        
        $auditoriaId = $this->option('auditoria-id');
        $ultraRapido = $this->option('ultra-rapido');
        $mostrarStats = $this->option('stats');
        $dryRun = $this->option('dry-run');

        // Mostrar estadísticas si se solicita
        if ($mostrarStats) {
            $this->mostrarEstadisticas($auditoriaId);
        }

        if ($dryRun) {
            $this->info('🔍 Modo dry-run activado - Solo mostrando qué se procesaría');
            $this->simularProcesamiento($auditoriaId);
            return;
        }

        // Configurar el job según las opciones
        $configuracion = [
            'modo_rapido' => true,
            'ultra_rapido' => $ultraRapido,
            'auditoria_especifica' => $auditoriaId
        ];

        $this->info('⚙️ Configuración del job:');
        $this->table(['Parámetro', 'Valor'], [
            ['Modo rápido', $configuracion['modo_rapido'] ? '✅ Activado' : '❌ Desactivado'],
            ['Ultra rápido', $configuracion['ultra_rapido'] ? '✅ Activado' : '❌ Desactivado'],
            ['Auditoría específica', $auditoriaId ?? 'Todas las auditorías'],
            ['Timeout estimado', $ultraRapido ? '30 minutos' : '1 hora'],
            ['Velocidad estimada', $ultraRapido ? '~3x más rápido' : '~2x más rápido']
        ]);

        if (!$this->confirm('¿Proceder con la generación de etiquetas?')) {
            $this->info('❌ Operación cancelada por el usuario');
            return;
        }

        // Ejecutar el job
        $this->info('🔥 Ejecutando job optimizado...');
        
        try {
            if ($ultraRapido) {
                // Crear job ultra rápido personalizado
                $this->ejecutarJobUltraRapido($auditoriaId);
            } else {
                // Usar job normal en modo rápido
                GenerarEtiquetasJob::dispatch($auditoriaId, null, true, true);
                $this->info('✅ Job despachado exitosamente en modo rápido');
            }
            
            $this->info('📊 Puedes monitorear el progreso en los logs: tail -f storage/logs/laravel.log | grep GenerarEtiquetas');
            
        } catch (\Exception $e) {
            $this->error('❌ Error ejecutando el job: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Mostrar estadísticas antes del procesamiento
     */
    private function mostrarEstadisticas(?string $auditoriaId): void
    {
        $this->info('📊 Estadísticas del sistema:');

        if ($auditoriaId) {
            $auditoria = Auditorias::find($auditoriaId);
            if (!$auditoria) {
                $this->error("❌ Auditoría ID {$auditoriaId} no encontrada");
                return;
            }

            $apartadosConComentarios = $auditoria->checklistApartados()
                ->where(function($query) {
                    $query->where(function($subQuery) {
                        $subQuery->whereNotNull('observaciones')->where('observaciones', '!=', '');
                    })->orWhere(function($subQuery) {
                        $subQuery->whereNotNull('comentarios_uaa')->where('comentarios_uaa', '!=', '');
                    });
                })->count();

            $etiquetasExistentes = AuditoriaEtiqueta::where('auditoria_id', $auditoriaId)->count();

            $this->table(['Métrica', 'Valor'], [
                ['Auditoría', $auditoria->clave_de_accion],
                ['Ente fiscalizado', $auditoria->enteFiscalizado->nombre ?? 'N/A'],
                ['Apartados con comentarios', $apartadosConComentarios],
                ['Etiquetas existentes', $etiquetasExistentes],
                ['Apartados pendientes', $apartadosConComentarios - $etiquetasExistentes]
            ]);

        } else {
            $totalAuditorias = Auditorias::count();
            $auditoriasConComentarios = Auditorias::whereHas('checklistApartados', function($query) {
                $query->where(function($subQuery) {
                    $subQuery->whereNotNull('observaciones')->where('observaciones', '!=', '');
                })->orWhere(function($subQuery) {
                    $subQuery->whereNotNull('comentarios_uaa')->where('comentarios_uaa', '!=', '');
                });
            })->count();

            $totalEtiquetas = AuditoriaEtiqueta::count();
            $auditoriasConEtiquetas = AuditoriaEtiqueta::distinct('auditoria_id')->count();

            $this->table(['Métrica', 'Valor'], [
                ['Total auditorías', number_format($totalAuditorias)],
                ['Auditorías con comentarios', number_format($auditoriasConComentarios)],
                ['Total etiquetas existentes', number_format($totalEtiquetas)],
                ['Auditorías con etiquetas', number_format($auditoriasConEtiquetas)],
                ['Auditorías pendientes', number_format($auditoriasConComentarios - $auditoriasConEtiquetas)]
            ]);
        }
    }

    /**
     * Simular procesamiento (dry run)
     */
    private function simularProcesamiento(?string $auditoriaId): void
    {
        if ($auditoriaId) {
            $auditoria = Auditorias::find($auditoriaId);
            if (!$auditoria) {
                $this->error("❌ Auditoría ID {$auditoriaId} no encontrada");
                return;
            }

            $this->info("🔍 Simulando procesamiento de auditoría: {$auditoria->clave_de_accion}");
            
            $apartados = $auditoria->checklistApartados()
                ->where(function($query) {
                    $query->where(function($subQuery) {
                        $subQuery->whereNotNull('observaciones')->where('observaciones', '!=', '');
                    })->orWhere(function($subQuery) {
                        $subQuery->whereNotNull('comentarios_uaa')->where('comentarios_uaa', '!=', '');
                    });
                })
                ->with('apartado')
                ->get();

            $apartadosPorPadre = $apartados->groupBy('apartado_id');

            $this->info("📂 Se procesarían {$apartadosPorPadre->count()} apartados padre únicos:");
            
            foreach ($apartadosPorPadre as $apartadoPadreId => $apartadosHijos) {
                $nombreApartado = $apartadosHijos->first()->apartado->nombre ?? "Apartado {$apartadoPadreId}";
                $this->line("  - ID {$apartadoPadreId}: " . substr($nombreApartado, 0, 80) . "... ({$apartadosHijos->count()} instancias)");
            }

        } else {
            $this->info("🔍 Simulando procesamiento masivo de todas las auditorías");
            
            $auditorias = Auditorias::whereHas('checklistApartados', function($query) {
                $query->where(function($subQuery) {
                    $subQuery->whereNotNull('observaciones')->where('observaciones', '!=', '');
                })->orWhere(function($subQuery) {
                    $subQuery->whereNotNull('comentarios_uaa')->where('comentarios_uaa', '!=', '');
                });
            })->take(10)->get();

            $this->info("📋 Primeras 10 auditorías que se procesarían:");
            
            foreach ($auditorias as $auditoria) {
                $apartadosConComentarios = $auditoria->checklistApartados()
                    ->where(function($query) {
                        $query->where(function($subQuery) {
                            $subQuery->whereNotNull('observaciones')->where('observaciones', '!=', '');
                        })->orWhere(function($subQuery) {
                            $subQuery->whereNotNull('comentarios_uaa')->where('comentarios_uaa', '!=', '');
                        });
                    })->count();

                $this->line("  - {$auditoria->clave_de_accion}: {$apartadosConComentarios} apartados con comentarios");
            }
        }
    }

    /**
     * Ejecutar job en modo ultra rápido con progreso visible
     */
    private function ejecutarJobUltraRapido(?string $auditoriaId): void
    {
        $this->info('🔥 Ejecutando en modo ULTRA RÁPIDO (sin pausas)');
        
        if ($auditoriaId) {
            $this->procesarAuditoriaEspecificaConProgreso($auditoriaId);
        } else {
            $this->procesarTodasLasAuditoriasConProgreso();
        }
        
        $this->info('✅ Job ultra rápido ejecutado exitosamente');
    }

    /**
     * Procesar una auditoría específica mostrando progreso
     */
    private function procesarAuditoriaEspecificaConProgreso(string $auditoriaId): void
    {
        $auditoria = Auditorias::with(['checklistApartados.apartado'])->find($auditoriaId);

        if (!$auditoria) {
            $this->error("❌ Auditoría no encontrada: {$auditoriaId}");
            return;
        }

        $this->info("🔍 Procesando auditoría: {$auditoria->clave_de_accion}");
        
        // Obtener apartados con comentarios
        $apartadosConComentarios = $auditoria->checklistApartados
            ->filter(function($apartado) {
                return (!empty($apartado->observaciones) && trim($apartado->observaciones) !== '') ||
                       (!empty($apartado->comentarios_uaa) && trim($apartado->comentarios_uaa) !== '');
            });

        if ($apartadosConComentarios->isEmpty()) {
            $this->warn("⚠️ No hay comentarios para procesar en esta auditoría");
            return;
        }

        // Agrupar por apartado padre
        $apartadosPorPadre = $apartadosConComentarios->groupBy(function($apartado) {
            return $apartado->apartado_id ?? 0;
        });

        $this->info("📂 Encontrados {$apartadosPorPadre->count()} apartados padre únicos");
        
        // Crear barra de progreso
        $progressBar = $this->output->createProgressBar($apartadosPorPadre->count());
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s% -- %message%');
        $progressBar->start();

        $contador = 0;
        foreach ($apartadosPorPadre as $apartadoPadreId => $apartadosHijos) {
            $contador++;
            $primerApartado = $apartadosHijos->first();
            $nombreApartado = $primerApartado->apartado->nombre ?? "Apartado {$apartadoPadreId}";
            
            $progressBar->setMessage("Procesando: " . substr($nombreApartado, 0, 50) . "...");
            
            try {
                $this->procesarApartadoPadreDirecto($auditoria, $apartadoPadreId, $apartadosHijos, $nombreApartado);
                $progressBar->advance();
            } catch (\Exception $e) {
                $progressBar->setMessage("❌ Error: " . substr($e->getMessage(), 0, 50) . "...");
                $progressBar->advance();
                $this->warn("\n❌ Error procesando apartado {$nombreApartado}: {$e->getMessage()}");
            }
        }

        $progressBar->finish();
        $this->newLine();
        $this->info("✅ Auditoría {$auditoria->clave_de_accion} procesada completamente");
    }

    /**
     * Procesar todas las auditorías mostrando progreso
     */
    private function procesarTodasLasAuditoriasConProgreso(): void
    {
        // Obtener todas las auditorías con comentarios
        $auditoriasQuery = Auditorias::whereHas('checklistApartados', function($query) {
            $query->where(function($subQuery) {
                $subQuery->whereNotNull('observaciones')->where('observaciones', '!=', '');
            })->orWhere(function($subQuery) {
                $subQuery->whereNotNull('comentarios_uaa')->where('comentarios_uaa', '!=', '');
            });
        });

        $totalAuditorias = $auditoriasQuery->count();
        $this->info("📊 Total de auditorías con comentarios: {$totalAuditorias}");

        if ($totalAuditorias === 0) {
            $this->warn("⚠️ No hay auditorías con comentarios para procesar");
            return;
        }

        // Crear barra de progreso principal
        $progressBar = $this->output->createProgressBar($totalAuditorias);
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s% -- %message%');
        $progressBar->start();

        $procesadas = 0;
        $loteSize = 50; // Ultra rápido: lotes grandes

        $auditoriasQuery->with(['checklistApartados.apartado'])
            ->chunk($loteSize, function($auditorias) use (&$procesadas, &$progressBar, $totalAuditorias) {
                
                foreach ($auditorias as $auditoria) {
                    $procesadas++;
                    
                    $progressBar->setMessage("Procesando: {$auditoria->clave_de_accion}");
                    
                    try {
                        // Verificar si tiene etiquetas pendientes
                        if (!$auditoria->tieneEtiquetasPendientes()) {
                            $progressBar->setMessage("⏭️ Ya procesada: {$auditoria->clave_de_accion}");
                            $progressBar->advance();
                            continue;
                        }

                        $this->procesarAuditoriaCompleta($auditoria, $progressBar);
                        $progressBar->advance();
                        
                    } catch (\Exception $e) {
                        $progressBar->setMessage("❌ Error: {$auditoria->clave_de_accion}");
                        $progressBar->advance();
                        $this->warn("\n❌ Error en auditoría {$auditoria->clave_de_accion}: {$e->getMessage()}");
                    }
                }
            });

        $progressBar->finish();
        $this->newLine();
        $this->info("🎉 Procesamiento masivo completado. Total procesadas: {$procesadas}");
    }

    /**
     * Procesar una auditoría completa (para uso en lotes)
     */
    private function procesarAuditoriaCompleta(Auditorias $auditoria, $progressBar): void
    {
        // Obtener apartados con comentarios
        $apartadosConComentarios = $auditoria->checklistApartados
            ->filter(function($apartado) {
                return (!empty($apartado->observaciones) && trim($apartado->observaciones) !== '') ||
                       (!empty($apartado->comentarios_uaa) && trim($apartado->comentarios_uaa) !== '');
            });

        if ($apartadosConComentarios->isEmpty()) {
            return;
        }

        // Agrupar por apartado padre
        $apartadosPorPadre = $apartadosConComentarios->groupBy(function($apartado) {
            return $apartado->apartado_id ?? 0;
        });

        foreach ($apartadosPorPadre as $apartadoPadreId => $apartadosHijos) {
            $primerApartado = $apartadosHijos->first();
            $nombreApartado = $primerApartado->apartado->nombre ?? "Apartado {$apartadoPadreId}";
            
            $progressBar->setMessage("├─ Apartado: " . substr($nombreApartado, 0, 40) . "...");
            
            $this->procesarApartadoPadreDirecto($auditoria, $apartadoPadreId, $apartadosHijos, $nombreApartado);
        }
    }

    /**
     * Procesar apartado padre directamente (lógica del job pero simplificada)
     */
    private function procesarApartadoPadreDirecto(Auditorias $auditoria, int $apartadoPadreId, $apartadosHijos, string $nombreApartado): void
    {
        // Verificar si ya existe etiqueta actualizada
        $etiquetaExistente = AuditoriaEtiqueta::where('auditoria_id', $auditoria->id)
            ->where('apartado_id', $apartadoPadreId)
            ->first();

        if ($etiquetaExistente) {
            // Verificar si los comentarios han sido modificados después de la última etiqueta
            $ultimaModificacionComentarios = $apartadosHijos->max('updated_at');
            $fechaEtiqueta = $etiquetaExistente->procesado_en ?? $etiquetaExistente->created_at;

            if ($ultimaModificacionComentarios && $fechaEtiqueta && $ultimaModificacionComentarios <= $fechaEtiqueta) {
                // Ya está actualizado, saltar
                return;
            }
        }

        // Recopilar comentarios
        $todosLosComentarios = [];
        foreach ($apartadosHijos as $apartadoHijo) {
            if (!empty($apartadoHijo->observaciones) && trim($apartadoHijo->observaciones) !== '') {
                $todosLosComentarios[] = [
                    'fecha' => $apartadoHijo->updated_at->format('Y-m-d H:i'),
                    'tipo' => 'Observaciones',
                    'contenido' => trim($apartadoHijo->observaciones),
                ];
            }
            
            if (!empty($apartadoHijo->comentarios_uaa) && trim($apartadoHijo->comentarios_uaa) !== '') {
                $todosLosComentarios[] = [
                    'fecha' => $apartadoHijo->updated_at->format('Y-m-d H:i'),
                    'tipo' => 'Comentarios UAA',
                    'contenido' => trim($apartadoHijo->comentarios_uaa),
                ];
            }
        }

        // Remover duplicados
        $comentariosUnicos = collect($todosLosComentarios)
            ->unique(function($item) {
                return md5($item['contenido']);
            })
            ->sortBy('fecha')
            ->values()
            ->toArray();

        if (empty($comentariosUnicos)) {
            return;
        }

        // Generar etiqueta con IA
        $this->generarEtiquetaConIA($auditoria, $apartadoPadreId, $nombreApartado, $comentariosUnicos);
    }

    /**
     * Generar etiqueta usando IA (simplificado)
     */
    private function generarEtiquetaConIA(Auditorias $auditoria, int $apartadoPadreId, string $nombreApartado, array $comentarios): void
    {
        // Crear contenido para IA
        $contenidoIA = "📋 AUDITORÍA: {$auditoria->clave_de_accion}\n";
        $contenidoIA .= "📂 APARTADO: {$nombreApartado}\n\n";
        $contenidoIA .= "COMENTARIOS:\n";
        
        foreach ($comentarios as $index => $comentario) {
            $numero = $index + 1;
            $contenido = trim($comentario['contenido']);
            $contenidoIA .= "{$numero}. {$contenido}\n";
        }

        try {
            // Llamar IA usando el AIController existente
            $aiController = new \App\Http\Controllers\AIController();
            
            $prompt = $this->construirPromptSimplificado($contenidoIA);
            
            $request = new \Illuminate\Http\Request();
            $request->merge([
                'message' => $prompt,
                'provider' => 'groq',
                'model' => env('GROQ_DEF_MODEL', 'llama3-8b-8192'),
                'includeContext' => false
            ]);

            $reflection = new \ReflectionClass($aiController);
            $method = $reflection->getMethod('getAIResponse');
            $method->setAccessible(true);

            $respuestaIA = $method->invoke(
                $aiController,
                $prompt,
                'groq',
                env('GROQ_DEF_MODEL', 'llama3-8b-8192'),
                false,
                [],
                null
            );

            if (!$respuestaIA || trim($respuestaIA) === '') {
                throw new \Exception("Respuesta vacía de la API");
            }

            // Parsear respuesta
            $etiquetaNombre = $this->parseearRespuestaSimple($respuestaIA);
            
            // Crear o actualizar etiqueta
            $this->crearOActualizarEtiqueta($auditoria, $apartadoPadreId, $etiquetaNombre, $respuestaIA, $nombreApartado, $comentarios);

        } catch (\Exception $e) {
            // Crear etiqueta fallback
            $this->crearEtiquetaFallback($auditoria, $apartadoPadreId, $nombreApartado, "Error: " . $e->getMessage());
        }
    }

    /**
     * Construir prompt simplificado para IA
     */
    private function construirPromptSimplificado(string $contenido): string
    {
        $etiquetasCatalogo = \App\Models\CatEtiqueta::all()->pluck('nombre')->toArray();
        $etiquetasTexto = implode('", "', $etiquetasCatalogo);

        return "Eres un auditor experto. Analiza estos comentarios y asigna la etiqueta MÁS ESPECÍFICA del catálogo.\n\nETIQUETAS DISPONIBLES: \"{$etiquetasTexto}\"\n\nCONTENIDO:\n{$contenido}\n\nResponde ÚNICAMENTE con el nombre exacto de la etiqueta.";
    }

    /**
     * Parsear respuesta simple de IA
     */
    private function parseearRespuestaSimple(string $respuesta): string
    {
        $respuesta = trim($respuesta);
        $respuesta = preg_replace('/["""\'\']/', '', $respuesta);
        $respuesta = preg_replace('/\s+/', ' ', $respuesta);
        
        // Buscar coincidencia exacta en catálogo
        $etiquetasCatalogo = \App\Models\CatEtiqueta::all();
        
        foreach ($etiquetasCatalogo as $etiqueta) {
            if (strtolower(trim($etiqueta->nombre)) === strtolower($respuesta)) {
                return $etiqueta->nombre;
            }
        }

        // Si no se encuentra, usar "Procesado"
        return 'Procesado';
    }

    /**
     * Crear o actualizar etiqueta
     */
    private function crearOActualizarEtiqueta(Auditorias $auditoria, int $apartadoPadreId, string $nombreEtiqueta, string $respuestaIA, string $nombreApartado, array $comentarios): void
    {
        $etiqueta = \App\Models\CatEtiqueta::where('nombre', $nombreEtiqueta)->first();
        
        if (!$etiqueta) {
            $etiqueta = \App\Models\CatEtiqueta::where('nombre', 'Procesado')->first();
            $nombreEtiqueta = 'Procesado';
        }

        $comentarioFuente = implode(' | ', array_slice(array_column($comentarios, 'contenido'), 0, 2));
        $razonAsignacion = "IA analizó " . count($comentarios) . " comentario(s) del apartado '{$nombreApartado}' y determinó la categoría '{$nombreEtiqueta}'";

        AuditoriaEtiqueta::updateOrCreate(
            [
                'auditoria_id' => $auditoria->id,
                'apartado_id' => $apartadoPadreId,
            ],
            [
                'etiqueta_id' => $etiqueta->id,
                'respuesta_ia' => $respuestaIA,
                'razon_asignacion' => $razonAsignacion,
                'comentario_fuente' => $comentarioFuente,
                'confianza_ia' => 0.85,
                'procesado_en' => now(),
            ]
        );
    }

    /**
     * Crear etiqueta fallback
     */
    private function crearEtiquetaFallback(Auditorias $auditoria, int $apartadoPadreId, string $nombreApartado, string $error): void
    {
        $etiquetaProcesado = \App\Models\CatEtiqueta::where('nombre', 'Procesado')->first();
        
        if ($etiquetaProcesado) {
            AuditoriaEtiqueta::updateOrCreate(
                [
                    'auditoria_id' => $auditoria->id,
                    'apartado_id' => $apartadoPadreId,
                ],
                [
                    'etiqueta_id' => $etiquetaProcesado->id,
                    'respuesta_ia' => $error,
                    'razon_asignacion' => "Apartado '{$nombreApartado}' marcado como procesado debido a error de procesamiento",
                    'comentario_fuente' => "Error procesando apartado: {$nombreApartado}",
                    'confianza_ia' => 0.1,
                    'procesado_en' => now(),
                ]
            );
        }
    }
} 