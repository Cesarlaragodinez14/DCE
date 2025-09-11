<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Auditorias;
use App\Models\CatDgsegEf;
use App\Models\CatEntrega;
use App\Models\CatCuentaPublica;
use App\Models\CatSiglasTipoAccion;
use App\Models\CatEnteDeLaAccion;
use App\Models\AuditoriasHistory;
use App\Models\ChecklistApartadoHistory;
use App\Models\CatEtiqueta;
use App\Models\AuditoriaEtiqueta;
use App\Models\Apartado;
use App\Jobs\GenerarEtiquetasJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ResumenAuditoriasExport;

class ResumenAuditorias extends Component
{
    use WithPagination;

    public $perPage = 15;
    public $search = '';
    public $filtroClaveAccion = '';
    public $filtroDireccionGeneral = '';
    public $filtroEntrega = '';
    public $filtroCuentaPublica = '';
    public $filtroTipoAccion = '';
    public $filtroEnteDeLaAccion = '';
    public $filtroEstatusChecklist = '';
    
    // Variables para el modal de etiquetas
    public $modalEtiquetasAbierto = false;
    public $etiquetasSeleccionadas = [];
    public $auditoriaSeleccionada = null;
    
    // Variables para el modal de generación masiva de etiquetas
    public $modalGenerarTodasEtiquetas = false;
    
    protected $paginationTheme = 'tailwind';
    protected $queryString = ['search', 'filtroClaveAccion', 'filtroDireccionGeneral', 'filtroEntrega', 'filtroCuentaPublica', 'filtroTipoAccion', 'filtroEnteDeLaAccion', 'filtroEstatusChecklist'];
    
    protected $listeners = [
        'etiquetasGeneradas' => 'actualizarVista',
        'cerrarModalEtiquetas' => 'cerrarModalEtiquetas'
    ];

    public function mount()
    {
        // Inicializar valores desde query string si existen
        $this->search = request()->get('search', '');
        $this->filtroClaveAccion = request()->get('filtroClaveAccion', '');
        $this->filtroDireccionGeneral = request()->get('filtroDireccionGeneral', '');
        $this->filtroEntrega = request()->get('filtroEntrega', '');
        $this->filtroCuentaPublica = request()->get('filtroCuentaPublica', '');
        $this->filtroTipoAccion = request()->get('filtroTipoAccion', '');
        $this->filtroEnteDeLaAccion = request()->get('filtroEnteDeLaAccion', '');
        $this->filtroEstatusChecklist = request()->get('filtroEstatusChecklist', '');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFiltroClaveAccion()
    {
        $this->resetPage();
    }

    public function updatingFiltroDireccionGeneral()
    {
        $this->resetPage();
    }

    public function updatingFiltroEntrega()
    {
        $this->resetPage();
    }

    public function updatingFiltroCuentaPublica()
    {
        $this->resetPage();
    }

    public function updatingFiltroTipoAccion()
    {
        $this->resetPage();
    }

    public function updatingFiltroEnteDeLaAccion()
    {
        $this->resetPage();
    }

    public function updatingFiltroEstatusChecklist()
    {
        $this->resetPage();
    }

    public function limpiarFiltros()
    {
        $this->search = '';
        $this->filtroClaveAccion = '';
        $this->filtroDireccionGeneral = '';
        $this->filtroEntrega = '';
        $this->filtroCuentaPublica = '';
        $this->filtroTipoAccion = '';
        $this->filtroEnteDeLaAccion = '';
        $this->filtroEstatusChecklist = '';
        $this->resetPage();
    }

    public function hasFiltrosActivos()
    {
        return !empty($this->search) || 
               !empty($this->filtroClaveAccion) || 
               !empty($this->filtroDireccionGeneral) ||
               !empty($this->filtroEntrega) ||
               !empty($this->filtroCuentaPublica) ||
               !empty($this->filtroTipoAccion) ||
               !empty($this->filtroEnteDeLaAccion) ||
               !empty($this->filtroEstatusChecklist);
    }

    /**
     * Generar etiquetas para una auditoría específica
     */
    public function generarEtiquetas($auditoriaId)
    {
        // Verificar permisos de usuario (solo usuarios 1, 2, 3)
        if (!in_array(Auth::id(), [1, 2, 3])) {
            $this->dispatch('mostrarError', [
                'titulo' => 'Sin permisos',
                'mensaje' => 'No tienes permisos para generar etiquetas.'
            ]);
            return;
        }

        try {
            Log::info("🏷️ Iniciando generación manual de etiquetas para auditoría: {$auditoriaId}");
            
            // Despachar el job para generar etiquetas de esta auditoría específica
            GenerarEtiquetasJob::dispatch($auditoriaId, Auth::id(), true);
            
            $this->dispatch('mostrarExito', [
                'titulo' => 'Etiquetas en proceso',
                'mensaje' => 'Se ha iniciado la generación de etiquetas. El proceso puede tomar unos minutos.'
            ]);

            Log::info("✅ Job de etiquetas despachado exitosamente para auditoría: {$auditoriaId}");

        } catch (\Exception $e) {
            Log::error("❌ Error despachando job de etiquetas: {$e->getMessage()}");
            
            $this->dispatch('mostrarError', [
                'titulo' => 'Error',
                'mensaje' => 'Ocurrió un error al iniciar la generación de etiquetas.'
            ]);
        }
    }

    /**
     * Abrir modal con etiquetas de una auditoría
     */
    public function verEtiquetas($auditoriaId)
    {
        try {
            $this->auditoriaSeleccionada = Auditorias::with([
                'auditoriaEtiquetas.etiqueta',
                'auditoriaEtiquetas.apartado', // Nueva estructura - apartado padre
                'auditoriaEtiquetas.checklistApartado.apartado' // Legacy - apartado individual
            ])->find($auditoriaId);

            if (!$this->auditoriaSeleccionada) {
                $this->dispatch('mostrarError', [
                    'titulo' => 'Error',
                    'mensaje' => 'Auditoría no encontrada.'
                ]);
                return;
            }

            $this->etiquetasSeleccionadas = $this->auditoriaSeleccionada->auditoriaEtiquetas;
            $this->modalEtiquetasAbierto = true;

        } catch (\Exception $e) {
            Log::error("❌ Error al cargar etiquetas: {$e->getMessage()}");
            
            $this->dispatch('mostrarError', [
                'titulo' => 'Error',
                'mensaje' => 'Error al cargar las etiquetas.'
            ]);
        }
    }

    /**
     * Cerrar modal de etiquetas
     */
    public function cerrarModalEtiquetas()
    {
        $this->modalEtiquetasAbierto = false;
        $this->etiquetasSeleccionadas = [];
        $this->auditoriaSeleccionada = null;
    }

    /**
     * Actualizar vista después de generar etiquetas
     */
    public function actualizarVista()
    {
        // Refrescar los datos para mostrar las nuevas etiquetas
        $this->render();
    }

    /**
     * Verificar si el usuario puede generar etiquetas
     */
    public function puedeGenerarEtiquetas(): bool
    {
        return in_array(Auth::id(), [1, 2, 3]);
    }

    /**
     * Abrir modal de confirmación para generar todas las etiquetas
     */
    public function abrirModalGenerarTodasEtiquetas()
    {
        // Verificar permisos de usuario (solo usuarios 1, 2, 3)
        if (!in_array(Auth::id(), [1, 2, 3])) {
            $this->dispatch('mostrarError', [
                'titulo' => 'Sin permisos',
                'mensaje' => 'No tienes permisos para generar etiquetas.'
            ]);
            return;
        }

        // Precalcular el total en background para que el modal abra rápido
        $this->calcularTotalEtiquetasEnBackground();
        
        $this->modalGenerarTodasEtiquetas = true;
    }

    /**
     * Calcular total de etiquetas a procesar en background
     */
    private function calcularTotalEtiquetasEnBackground()
    {
        try {
            // Usar cache temporal de 5 minutos para el cálculo
            $cacheKey = 'total_etiquetas_procesar_' . md5(serialize([
                'search' => $this->search,
                'filtroClaveAccion' => $this->filtroClaveAccion,
                'filtroDireccionGeneral' => $this->filtroDireccionGeneral,
                'filtroEntrega' => $this->filtroEntrega,
                'filtroCuentaPublica' => $this->filtroCuentaPublica,
                'filtroTipoAccion' => $this->filtroTipoAccion,
                'filtroEnteDeLaAccion' => $this->filtroEnteDeLaAccion,
                'filtroEstatusChecklist' => $this->filtroEstatusChecklist,
            ]));

            // Si ya está en caché, usar ese valor
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            // Si no está en caché, calcular y cachear por 5 minutos
            $total = $this->getTotalEtiquetasAProcesarProperty();
            Cache::put($cacheKey, $total, 300); // 5 minutos

            return $total;
        } catch (\Exception $e) {
            Log::error("❌ Error en cálculo background: {$e->getMessage()}");
            return 0;
        }
    }

    /**
     * Cerrar modal de generación masiva de etiquetas
     */
    public function cerrarModalGenerarTodasEtiquetas()
    {
        $this->modalGenerarTodasEtiquetas = false;
    }

    /**
     * Generar etiquetas para todas las auditorías de la selección actual
     */
    public function generarTodasLasEtiquetas()
    {
        // Verificar permisos de usuario (solo usuarios 1, 2, 3)
        if (!in_array(Auth::id(), [1, 2, 3])) {
            $this->dispatch('mostrarError', [
                'titulo' => 'Sin permisos',
                'mensaje' => 'No tienes permisos para generar etiquetas.'
            ]);
            return;
        }

        try {
            Log::info("🚀 Iniciando generación masiva de etiquetas por usuario: " . Auth::id());
            
            // Obtener todas las auditorías que coinciden con los filtros actuales
            $auditoriasIds = $this->buildBaseQuery()->pluck('id')->toArray();
            
            if (empty($auditoriasIds)) {
                $this->dispatch('mostrarError', [
                    'titulo' => 'Sin auditorías',
                    'mensaje' => 'No hay auditorías en la selección actual para procesar.'
                ]);
                return;
            }

            // Despachar un job para procesar todas las auditorías pendientes
            // Esto procesará TODAS las auditorías que tengan apartados con comentarios
            GenerarEtiquetasJob::dispatch(null, Auth::id(), true);
            
            $this->dispatch('mostrarExito', [
                'titulo' => 'Procesamiento iniciado',
                'mensaje' => "Se ha iniciado la generación masiva de etiquetas para todas las auditorías con comentarios. Este proceso puede tomar varios minutos."
            ]);

            // Cerrar el modal
            $this->modalGenerarTodasEtiquetas = false;

            Log::info("✅ Job de generación masiva de etiquetas despachado exitosamente. Total de auditorías en filtro: " . count($auditoriasIds));

        } catch (\Exception $e) {
            Log::error("❌ Error despachando job de generación masiva: {$e->getMessage()}");
            
            $this->dispatch('mostrarError', [
                'titulo' => 'Error',
                'mensaje' => 'Ocurrió un error al iniciar la generación masiva de etiquetas.'
            ]);
        }
    }

    /**
     * Obtener el total de etiquetas que se van a procesar (ULTRA OPTIMIZADO)
     * Usa caché para respuesta instantánea
     */
    public function getTotalEtiquetasAProcesarProperty()
    {
        try {
            // Usar cache para respuesta instantánea
            $cacheKey = 'total_etiquetas_procesar_' . md5(serialize([
                'search' => $this->search,
                'filtroClaveAccion' => $this->filtroClaveAccion,
                'filtroDireccionGeneral' => $this->filtroDireccionGeneral,
                'filtroEntrega' => $this->filtroEntrega,
                'filtroCuentaPublica' => $this->filtroCuentaPublica,
                'filtroTipoAccion' => $this->filtroTipoAccion,
                'filtroEnteDeLaAccion' => $this->filtroEnteDeLaAccion,
                'filtroEstatusChecklist' => $this->filtroEstatusChecklist,
            ]));

            // Si está en caché, devolver inmediatamente
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            // Si no está en caché, hacer cálculo rápido aproximado primero
            $auditoriasIds = $this->buildBaseQuery()->pluck('id')->toArray();
            
            if (empty($auditoriasIds)) {
                Cache::put($cacheKey, 0, 300);
                return 0;
            }

            // Cálculo optimizado con timeout
            set_time_limit(30); // Máximo 30 segundos
            
            // Usar solo una muestra para el cálculo rápido
            $auditoriasMuestra = array_slice($auditoriasIds, 0, 500);
            
            $apartadosNecesitanProcesamiento = DB::select("
                SELECT COUNT(DISTINCT apartado_padre.apartado_padre_id) as total
                FROM (
                    SELECT 
                        ca.auditoria_id,
                        CASE 
                            WHEN a.parent_id IS NULL THEN a.id 
                            ELSE a.parent_id 
                        END as apartado_padre_id,
                        MAX(ca.updated_at) as ultima_modificacion_comentarios
                    FROM checklist_apartados ca
                    JOIN apartados a ON ca.apartado_id = a.id
                    WHERE ca.auditoria_id IN (" . implode(',', $auditoriasMuestra) . ")
                    AND (
                        (ca.observaciones IS NOT NULL AND ca.observaciones != '') 
                        OR (ca.comentarios_uaa IS NOT NULL AND ca.comentarios_uaa != '')
                    )
                    GROUP BY ca.auditoria_id, CASE WHEN a.parent_id IS NULL THEN a.id ELSE a.parent_id END
                    LIMIT 10000
                ) apartado_padre
                LEFT JOIN auditoria_etiquetas ae ON (
                    ae.auditoria_id = apartado_padre.auditoria_id 
                    AND ae.apartado_id = apartado_padre.apartado_padre_id
                )
                WHERE (
                    ae.id IS NULL
                    OR 
                    apartado_padre.ultima_modificacion_comentarios > COALESCE(ae.procesado_en, ae.created_at)
                )
            ");

            $total = $apartadosNecesitanProcesamiento[0]->total ?? 0;
            
            // Si tenemos más de 500 auditorías, estimar el total
            if (count($auditoriasIds) > 500) {
                $total = round($total * (count($auditoriasIds) / 500));
            }

            Cache::put($cacheKey, $total, 300); // Cache por 5 minutos
            return $total;

        } catch (\Exception $e) {
            Log::error("❌ Error calculando apartados a procesar: {$e->getMessage()}");
            
            // Fallback ultra simple: solo contar apartados con comentarios
            try {
                $auditoriasIds = $this->buildBaseQuery()->pluck('id')->toArray();
                if (empty($auditoriasIds)) return 0;
                
                $total = DB::table('checklist_apartados as ca')
                    ->join('apartados as a', 'ca.apartado_id', '=', 'a.id')
                    ->whereIn('ca.auditoria_id', array_slice($auditoriasIds, 0, 100)) // Solo primeras 100
                    ->where(function($query) {
                        $query->whereNotNull('ca.observaciones')->where('ca.observaciones', '!=', '')
                              ->orWhereNotNull('ca.comentarios_uaa')->where('ca.comentarios_uaa', '!=', '');
                    })
                    ->distinct('a.parent_id')
                    ->count();
                    
                // Estimar el total
                if (count($auditoriasIds) > 100) {
                    $total = round($total * (count($auditoriasIds) / 100));
                }
                
                return $total;
            } catch (\Exception $fallbackException) {
                Log::error("❌ Error en fallback: {$fallbackException->getMessage()}");
                return 0; // Último recurso
            }
        }
    }

    /**
     * Propiedades computadas para estadísticas de etiquetas IA
     * Excluye la etiqueta "Procesado" de todos los conteos
     */
    public function getTotalEtiquetasProperty()
    {
        $auditorias = $this->buildBaseQuery()->pluck('id');
        return AuditoriaEtiqueta::whereIn('auditoria_id', $auditorias)
                                ->whereHas('etiqueta', function($query) {
                                    $query->where('nombre', '!=', 'Procesado');
                                })
                                ->count();
    }

    public function getAuditoriasConEtiquetasProperty()
    {
        $auditorias = $this->buildBaseQuery()->pluck('id');
        return AuditoriaEtiqueta::whereIn('auditoria_id', $auditorias)
                                ->whereHas('etiqueta', function($query) {
                                    $query->where('nombre', '!=', 'Procesado');
                                })
                                ->distinct('auditoria_id')
                                ->count('auditoria_id');
    }

    public function getApartadosUnicosProperty()
    {
        $auditorias = $this->buildBaseQuery()->pluck('id');
        return AuditoriaEtiqueta::whereIn('auditoria_id', $auditorias)
                                ->whereNotNull('apartado_id')
                                ->whereHas('etiqueta', function($query) {
                                    $query->where('nombre', '!=', 'Procesado');
                                })
                                ->distinct('apartado_id')
                                ->count('apartado_id');
    }

    public function getConfianzaPromedioProperty()
    {
        $auditorias = $this->buildBaseQuery()->pluck('id');
        $confianzaPromedio = AuditoriaEtiqueta::whereIn('auditoria_id', $auditorias)
                                             ->whereHas('etiqueta', function($query) {
                                                 $query->where('nombre', '!=', 'Procesado');
                                             })
                                             ->where('confianza_ia', '>', 0)
                                             ->avg('confianza_ia');
        return $confianzaPromedio ? ($confianzaPromedio * 100) : 0;
    }

    public function getTopApartadosProperty()
    {
        $auditorias = $this->buildBaseQuery()->pluck('id');
        
        // Obtener todas las etiquetas agrupadas por apartado con detalles de cada etiqueta
        // Excluir la etiqueta "Procesado"
        $apartadosConEtiquetas = AuditoriaEtiqueta::whereIn('auditoria_id', $auditorias)
                                ->whereNotNull('apartado_id')
                                ->with(['apartado', 'etiqueta'])
                                ->whereHas('etiqueta', function($query) {
                                    $query->where('nombre', '!=', 'Procesado');
                                })
                                ->get()
                                ->groupBy('apartado_id')
                                ->map(function($etiquetasPorApartado, $apartadoId) {
                                    $apartado = $etiquetasPorApartado->first()->apartado;
                                    
                                    // Contar etiquetas por tipo (excluyendo "Procesado")
                                    $etiquetasContadas = $etiquetasPorApartado
                                        ->filter(function($auditoriaEtiqueta) {
                                            return $auditoriaEtiqueta->etiqueta->nombre !== 'Procesado';
                                        })
                                        ->groupBy('etiqueta_id')
                                        ->map(function($etiquetasDelMismoTipo) {
                                            $etiqueta = $etiquetasDelMismoTipo->first()->etiqueta;
                                            return [
                                                'nombre' => $etiqueta->nombre,
                                                'cantidad' => $etiquetasDelMismoTipo->count(),
                                                'color_css' => $etiqueta->color_css ?? 'bg-gray-100 text-gray-800'
                                            ];
                                        })
                                        ->sortByDesc('cantidad')
                                        ->values();
                                    
                                    $totalEtiquetas = $etiquetasPorApartado->count();
                                    
                                    return [
                                        'apartado_id' => $apartadoId,
                                        'nombre' => $apartado ? $apartado->nombre : "Apartado {$apartadoId}",
                                        'total_etiquetas' => $totalEtiquetas,
                                        'etiquetas_detalle' => $etiquetasContadas->toArray()
                                    ];
                                })
                                ->filter(function($apartado) {
                                    // Solo incluir apartados que tengan etiquetas después de filtrar "Procesado"
                                    return $apartado['total_etiquetas'] > 0;
                                })
                                ->sortByDesc('total_etiquetas')
                                ->values()
                                ->toArray();
        
        return $apartadosConEtiquetas;
    }

    /**
     * Generar clave de caché basada en los filtros activos
     */
    private function generarClaveCache()
    {
        $filtros = [
            'search' => trim($this->search ?? ''),
            'filtroClaveAccion' => trim($this->filtroClaveAccion ?? ''),
            'filtroDireccionGeneral' => $this->filtroDireccionGeneral ?? '',
            'filtroEntrega' => $this->filtroEntrega ?? '',
            'filtroCuentaPublica' => $this->filtroCuentaPublica ?? '',
            'filtroTipoAccion' => $this->filtroTipoAccion ?? '',
            'filtroEnteDeLaAccion' => $this->filtroEnteDeLaAccion ?? '',
            'filtroEstatusChecklist' => trim($this->filtroEstatusChecklist ?? ''),
        ];
        
        // Normalizar filtros para generar una clave consistente
        $filtrosNormalizados = array_map(function($value) {
            return is_string($value) ? trim($value) : $value;
        }, $filtros);
        
        // Crear un hash único basado en los filtros
        // Ordenar array para generar hash consistente
        ksort($filtrosNormalizados);
        $filtrosJson = json_encode($filtrosNormalizados);
        $filtrosHash = md5($filtrosJson);
        
        $claveCache = "resumen_auditorias_stats_{$filtrosHash}";
        
        Log::info("🔑 Clave de caché generada: {$claveCache}", [
            'filtros' => $filtrosNormalizados,
            'json' => $filtrosJson
        ]);
        
        return $claveCache;
    }

    /**
     * Obtener estadísticas desde caché o calcularlas si no existen
     */
    private function obtenerEstadisticasConCache()
    {
        $claveCache = $this->generarClaveCache();
        
        Log::info("🔍 Buscando estadísticas en caché con clave: {$claveCache}");
        
        try {
            // Verificar si la clave existe en caché
            if (Cache::has($claveCache)) {
                Log::info("✅ Estadísticas encontradas en caché");
                $resultado = Cache::get($claveCache);
                if (is_array($resultado) && isset($resultado['totalAuditorias'])) {
                    return $resultado;
                } else {
                    Log::warning("⚠️ Datos de caché corruptos, recalculando...");
                    Cache::forget($claveCache);
                }
            } else {
                Log::info("❌ Estadísticas no encontradas en caché");
            }
            
            // Calcular estadísticas si no están en caché o están corruptas
            Log::info('🔄 Calculando estadísticas totales (no encontradas en caché)');
            $estadisticas = $this->calcularEstadisticasTotales();
            
            // Guardar en caché con manejo de errores
            try {
                Cache::put($claveCache, $estadisticas, 3600); // 1 hora
                Log::info("💾 Estadísticas guardadas en caché exitosamente");
                
                // Registrar la clave para limpieza posterior
                $this->registrarClaveCache($claveCache);
                
            } catch (\Exception $e) {
                Log::error("❌ Error guardando en caché: {$e->getMessage()}");
                // Continuar sin caché si hay error
            }
            
            return $estadisticas;
            
        } catch (\Exception $e) {
            Log::error("❌ Error general en caché: {$e->getMessage()}");
            // Si hay cualquier error con el caché, calcular directamente
            return $this->calcularEstadisticasTotales();
        }
    }

    /**
     * Limpiar caché de estadísticas (útil cuando se actualicen datos)
     */
    public function limpiarCacheEstadisticas()
    {
        try {
            Log::info('🧹 Iniciando limpieza manual de caché...');
            
            $claveCache = $this->generarClaveCache();
            $cacheLimpiado = false;
            
            // Limpiar la clave específica actual
            if (Cache::has($claveCache)) {
                Cache::forget($claveCache);
                $cacheLimpiado = true;
                Log::info("🗑️ Clave específica limpiada: {$claveCache}");
            }
            
            // Limpiar todas las claves registradas
            $cacheKeys = Cache::get('resumen_auditorias_cache_keys', []);
            $cantidadLimpiada = 0;
            
            if (!empty($cacheKeys)) {
                foreach ($cacheKeys as $key) {
                    if (Cache::has($key)) {
                        Cache::forget($key);
                        $cantidadLimpiada++;
                        Log::info("🗑️ Clave registrada limpiada: {$key}");
                    }
                }
            }
            
            // Limpiar el registro de claves
            Cache::forget('resumen_auditorias_cache_keys');
            
            // Limpiar cachés por patrón si es posible
            $this->limpiarCachesPorPatron();
            
            Log::info("✅ Limpieza manual completada. Claves limpiadas: {$cantidadLimpiada}");
            
            $this->dispatch('mostrarExito', [
                'titulo' => 'Caché actualizado',
                'mensaje' => "Las estadísticas se han recalculado correctamente. Se limpiaron {$cantidadLimpiada} claves de caché."
            ]);
            
        } catch (\Exception $e) {
            Log::error("❌ Error en limpieza manual de caché: {$e->getMessage()}");
            
            $this->dispatch('mostrarError', [
                'titulo' => 'Error al limpiar caché',
                'mensaje' => 'Ocurrió un error al limpiar el caché. Se calculará directamente.'
            ]);
        }
    }

    /**
     * Limpiar cachés por patrón (solo para Redis)
     */
    private function limpiarCachesPorPatron()
    {
        if (config('cache.default') === 'redis') {
            try {
                $redis = Cache::getRedis();
                $keys = $redis->keys('*resumen_auditorias_stats_*');
                
                foreach ($keys as $key) {
                    $cleanKey = str_replace(config('cache.prefix') . ':', '', $key);
                    Cache::forget($cleanKey);
                    Log::info("🔍 Clave por patrón limpiada: {$cleanKey}");
                }
                
                if (count($keys) > 0) {
                    Log::info("🔍 Limpiadas " . count($keys) . " claves adicionales por patrón.");
                }
                
            } catch (\Exception $e) {
                Log::warning("⚠️ No se pudieron limpiar claves por patrón: {$e->getMessage()}");
            }
        }
    }

    /**
     * Registrar clave de caché para poder limpiarla después
     */
    private function registrarClaveCache($clave)
    {
        try {
            $cacheKeys = Cache::get('resumen_auditorias_cache_keys', []);
            if (!in_array($clave, $cacheKeys)) {
                $cacheKeys[] = $clave;
                Cache::put('resumen_auditorias_cache_keys', $cacheKeys, 7200); // 2 horas
                Log::info("📝 Clave registrada para limpieza: {$clave}");
            }
        } catch (\Exception $e) {
            Log::error("❌ Error registrando clave de caché: {$e->getMessage()}");
        }
    }

    /**
     * Método para diagnosticar el estado del caché
     */
    public function diagnosticarCache()
    {
        try {
            $claveCache = $this->generarClaveCache();
            
            $diagnostico = [
                'driver_cache' => config('cache.default'),
                'clave_generada' => $claveCache,
                'existe_en_cache' => Cache::has($claveCache),
                'filtros_actuales' => [
                    'search' => $this->search,
                    'filtroClaveAccion' => $this->filtroClaveAccion,
                    'filtroDireccionGeneral' => $this->filtroDireccionGeneral,
                    'filtroEntrega' => $this->filtroEntrega,
                    'filtroCuentaPublica' => $this->filtroCuentaPublica,
                    'filtroTipoAccion' => $this->filtroTipoAccion,
                    'filtroEnteDeLaAccion' => $this->filtroEnteDeLaAccion,
                    'filtroEstatusChecklist' => $this->filtroEstatusChecklist,
                ]
            ];
            
            if (Cache::has($claveCache)) {
                $cachedData = Cache::get($claveCache);
                $diagnostico['datos_cache'] = [
                    'tipo' => gettype($cachedData),
                    'es_array' => is_array($cachedData),
                    'claves' => is_array($cachedData) ? array_keys($cachedData) : 'N/A'
                ];
            }
            
            Log::info("🔍 Diagnóstico de caché:", $diagnostico);
            
            $this->dispatch('mostrarExito', [
                'titulo' => 'Diagnóstico de caché',
                'mensaje' => 'Revisa los logs para ver el diagnóstico completo. Driver: ' . config('cache.default')
            ]);
            
        } catch (\Exception $e) {
            Log::error("❌ Error en diagnóstico: {$e->getMessage()}");
            
            $this->dispatch('mostrarError', [
                'titulo' => 'Error en diagnóstico',
                'mensaje' => $e->getMessage()
            ]);
        }
    }

    /**
     * Diagnóstico detallado del cálculo de etiquetas a procesar
     */
    public function diagnosticarCalculoEtiquetas()
    {
        try {
            // Primero limpiar el caché para forzar un cálculo fresco
            $cacheKey = 'total_etiquetas_procesar_' . md5(serialize([
                'search' => $this->search,
                'filtroClaveAccion' => $this->filtroClaveAccion,
                'filtroDireccionGeneral' => $this->filtroDireccionGeneral,
                'filtroEntrega' => $this->filtroEntrega,
                'filtroCuentaPublica' => $this->filtroCuentaPublica,
                'filtroTipoAccion' => $this->filtroTipoAccion,
                'filtroEnteDeLaAccion' => $this->filtroEnteDeLaAccion,
                'filtroEstatusChecklist' => $this->filtroEstatusChecklist,
            ]));
            Cache::forget($cacheKey);
            Log::info("🧹 Caché limpiado para cálculo fresco");
            
            Log::info("🔍 INICIANDO DIAGNÓSTICO DETALLADO DEL CÁLCULO DE ETIQUETAS");
            
            // Paso 1: Obtener auditorías del filtro actual
            $auditoriasIds = $this->buildBaseQuery()->pluck('id')->toArray();
            $totalAuditorias = count($auditoriasIds);
            Log::info("📊 Paso 1: Total de auditorías en filtro: {$totalAuditorias}");
            
            // Paso 2: Verificar cuántas auditorías tienen comentarios
            $auditoriasConComentarios = DB::table('checklist_apartados')
                ->whereIn('auditoria_id', $auditoriasIds)
                ->where(function($query) {
                    $query->where(function($subQuery) {
                        $subQuery->whereNotNull('observaciones')->where('observaciones', '!=', '');
                    })->orWhere(function($subQuery) {
                        $subQuery->whereNotNull('comentarios_uaa')->where('comentarios_uaa', '!=', '');
                    });
                })
                ->distinct('auditoria_id')
                ->count();
            Log::info("💬 Paso 2: Auditorías con comentarios: {$auditoriasConComentarios}");
            
            // Paso 3: Contar apartados padre únicos con comentarios
            $apartadosPadreConComentarios = DB::select("
                SELECT COUNT(DISTINCT 
                    CASE 
                        WHEN a.parent_id IS NULL THEN a.id 
                        ELSE a.parent_id 
                    END
                ) as total
                FROM checklist_apartados ca
                JOIN apartados a ON ca.apartado_id = a.id
                WHERE ca.auditoria_id IN (" . implode(',', $auditoriasIds) . ")
                AND (
                    (ca.observaciones IS NOT NULL AND ca.observaciones != '') 
                    OR (ca.comentarios_uaa IS NOT NULL AND ca.comentarios_uaa != '')
                )
            ");
            $totalApartadosPadre = $apartadosPadreConComentarios[0]->total ?? 0;
            Log::info("📂 Paso 3: Apartados padre únicos con comentarios: {$totalApartadosPadre}");
            
            // Paso 4: Verificar cuántos ya tienen etiquetas
            $apartadosConEtiquetas = DB::table('auditoria_etiquetas')
                ->whereIn('auditoria_id', $auditoriasIds)
                ->whereNotNull('apartado_id')
                ->distinct('apartado_id')
                ->count();
            Log::info("🏷️ Paso 4: Apartados que ya tienen etiquetas: {$apartadosConEtiquetas}");
            
            // Paso 5: Apartados sin etiquetas
            $apartadosSinEtiquetas = DB::select("
                SELECT COUNT(DISTINCT apartado_padre_id) as total
                FROM (
                    SELECT 
                        ca.auditoria_id,
                        CASE 
                            WHEN a.parent_id IS NULL THEN a.id 
                            ELSE a.parent_id 
                        END as apartado_padre_id
                    FROM checklist_apartados ca
                    JOIN apartados a ON ca.apartado_id = a.id
                    WHERE ca.auditoria_id IN (" . implode(',', $auditoriasIds) . ")
                    AND (
                        (ca.observaciones IS NOT NULL AND ca.observaciones != '') 
                        OR (ca.comentarios_uaa IS NOT NULL AND ca.comentarios_uaa != '')
                    )
                ) apartados_con_comentarios
                LEFT JOIN auditoria_etiquetas ae ON (
                    ae.auditoria_id = apartados_con_comentarios.auditoria_id 
                    AND ae.apartado_id = apartados_con_comentarios.apartado_padre_id
                )
                WHERE ae.id IS NULL
            ");
            $totalSinEtiquetas = $apartadosSinEtiquetas[0]->total ?? 0;
            Log::info("🆕 Paso 5: Apartados SIN etiquetas: {$totalSinEtiquetas}");
            
            // Paso 6: Apartados con comentarios modificados después de la etiqueta
            $apartadosConModificaciones = DB::select("
                SELECT COUNT(*) as total
                FROM (
                    SELECT 
                        apartado_padre.auditoria_id,
                        apartado_padre.apartado_padre_id,
                        apartado_padre.ultima_modificacion_comentarios,
                        ae.procesado_en,
                        ae.created_at,
                        COALESCE(ae.procesado_en, ae.created_at) as fecha_etiqueta
                    FROM (
                        SELECT 
                            ca.auditoria_id,
                            CASE 
                                WHEN a.parent_id IS NULL THEN a.id 
                                ELSE a.parent_id 
                            END as apartado_padre_id,
                            MAX(ca.updated_at) as ultima_modificacion_comentarios
                        FROM checklist_apartados ca
                        JOIN apartados a ON ca.apartado_id = a.id
                                                 WHERE ca.auditoria_id IN (" . implode(',', $auditoriasIds) . ")
                        AND (
                            (ca.observaciones IS NOT NULL AND ca.observaciones != '') 
                            OR (ca.comentarios_uaa IS NOT NULL AND ca.comentarios_uaa != '')
                        )
                        GROUP BY ca.auditoria_id, CASE WHEN a.parent_id IS NULL THEN a.id ELSE a.parent_id END
                    ) apartado_padre
                    JOIN auditoria_etiquetas ae ON (
                        ae.auditoria_id = apartado_padre.auditoria_id 
                        AND ae.apartado_id = apartado_padre.apartado_padre_id
                    )
                    WHERE apartado_padre.ultima_modificacion_comentarios > COALESCE(ae.procesado_en, ae.created_at)
                ) modificados
            ");
            $totalConModificaciones = $apartadosConModificaciones[0]->total ?? 0;
            Log::info("🔄 Paso 6: Apartados con comentarios modificados DESPUÉS de etiqueta: {$totalConModificaciones}");
            
            // Resultado final
            $totalNecesitanProcesamiento = $totalSinEtiquetas + $totalConModificaciones;
            Log::info("🎯 RESULTADO FINAL: {$totalNecesitanProcesamiento} apartados necesitan procesamiento");
            
            // Resumen del diagnóstico
            $resumen = [
                'total_auditorias_filtro' => $totalAuditorias,
                'auditorias_con_comentarios' => $auditoriasConComentarios,
                'apartados_padre_con_comentarios' => $totalApartadosPadre,
                'apartados_con_etiquetas_existentes' => $apartadosConEtiquetas,
                'apartados_sin_etiquetas' => $totalSinEtiquetas,
                'apartados_con_modificaciones_posteriores' => $totalConModificaciones,
                'total_necesitan_procesamiento' => $totalNecesitanProcesamiento,
                'porcentaje_necesita_procesamiento' => $totalApartadosPadre > 0 ? round(($totalNecesitanProcesamiento / $totalApartadosPadre) * 100, 2) : 0
            ];
            
            Log::info("📋 RESUMEN COMPLETO DEL DIAGNÓSTICO:", $resumen);
            
            $this->dispatch('mostrarExito', [
                'titulo' => 'Diagnóstico completado',
                'mensaje' => "Diagnóstico detallado completado. {$totalNecesitanProcesamiento} apartados necesitan procesamiento de {$totalApartadosPadre} totales con comentarios. Ver logs para detalles completos."
            ]);
            
        } catch (\Exception $e) {
            Log::error("❌ Error en diagnóstico de cálculo: {$e->getMessage()}");
            
            $this->dispatch('mostrarError', [
                'titulo' => 'Error en diagnóstico',
                'mensaje' => 'Error durante el diagnóstico: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        // Obtener los catálogos para los filtros
        $direccionesGenerales = CatDgsegEf::orderBy('valor')->get();
        $entregas = CatEntrega::orderBy('valor')->get();
        $cuentasPublicas = CatCuentaPublica::orderBy('valor')->get();
        $tiposAccion = CatSiglasTipoAccion::orderBy('valor')->get();
        $entesAccion = CatEnteDeLaAccion::orderBy('valor')->get();
        
        // Obtener los estatus únicos de checklist
        $estatusChecklist = Auditorias::select('estatus_checklist')
                                     ->whereNotNull('estatus_checklist')
                                     ->where('estatus_checklist', '!=', '')
                                     ->distinct()
                                     ->orderBy('estatus_checklist')
                                     ->get()
                                     ->pluck('estatus_checklist')
                                     ->filter()
                                     ->values();

        // Construir la consulta base
        $query = $this->buildBaseQuery();

        // Debug: descomentar estas líneas si necesitas verificar los filtros en el log
        // \Log::info('Filtros aplicados:', [
        //     'search' => $this->search,
        //     'filtroClaveAccion' => $this->filtroClaveAccion,
        //     'filtroDireccionGeneral' => $this->filtroDireccionGeneral,
        //     'sql' => $query->toSql(),
        //     'bindings' => $query->getBindings()
        // ]);

        // Obtener los resultados paginados
        $auditorias = $query->orderBy('updated_at', 'desc')
                           ->paginate($this->perPage);

        // Procesar cada auditoría para obtener el historial
        $auditoriasConHistorial = $auditorias->getCollection()->map(function ($auditoria) {
            return $this->procesarHistorialAuditoria($auditoria);
        });

        // Reemplazar la colección original con la procesada
        $auditorias->setCollection($auditoriasConHistorial);

        // Obtener estadísticas totales usando caché
        $estadisticasTotales = $this->obtenerEstadisticasConCache();
        
        // Registrar la clave de caché para poder limpiarla después
        $this->registrarClaveCache($this->generarClaveCache());

        return view('livewire.resumen-auditorias', [
            'auditorias' => $auditorias,
            'direccionesGenerales' => $direccionesGenerales,
            'entregas' => $entregas,
            'cuentasPublicas' => $cuentasPublicas,
            'tiposAccion' => $tiposAccion,
            'entesAccion' => $entesAccion,
            'estatusChecklist' => $estatusChecklist,
            'totalCambiosComentarios' => $estadisticasTotales['totalCambiosComentarios'],
            'totalCambiosObservaciones' => $estadisticasTotales['totalCambiosObservaciones'],
            'auditoriasConComentarios' => $estadisticasTotales['auditoriasConComentarios'],
            'auditoriasConObservaciones' => $estadisticasTotales['auditoriasConObservaciones'],
            'totalAuditorias' => $estadisticasTotales['totalAuditorias'],
        ]);
    }

    private function procesarHistorialAuditoria($auditoria)
    {
        // Obtener historial de comentarios de la auditoría principal
        $historialComentarios = AuditoriasHistory::where('auditoria_id', $auditoria->id)
            ->with('user')
            ->get()
            ->filter(function ($history) {
                $changes = json_decode($history->changes, true);
                return isset($changes['before']['comentarios']) || isset($changes['after']['comentarios']);
            })
            ->map(function ($history) {
                $changes = json_decode($history->changes, true);
                return [
                    'fecha' => $history->created_at->format('d/m/Y H:i'),
                    'usuario' => $history->user->name ?? 'Usuario desconocido',
                    'antes' => $changes['before']['comentarios'] ?? 'Sin valor',
                    'despues' => $changes['after']['comentarios'] ?? 'Sin valor',
                ];
            });

        // Obtener historial de observaciones de checklist apartados
        $historialObservaciones = ChecklistApartadoHistory::whereIn('checklist_apartado_id', function($query) use ($auditoria) {
                $query->select('id')
                      ->from('checklist_apartados')
                      ->where('auditoria_id', $auditoria->id);
            })
            ->with('user', 'checklistApartado.apartado')
            ->get()
            ->filter(function ($history) {
                $changes = json_decode($history->changes, true);
                return isset($changes['before']['observaciones']) || 
                       isset($changes['after']['observaciones']) ||
                       isset($changes['before']['comentarios_uaa']) || 
                       isset($changes['after']['comentarios_uaa']);
            })
            ->map(function ($history) {
                $changes = json_decode($history->changes, true);
                $apartadoNombre = $history->checklistApartado->apartado->nombre ?? 'Apartado desconocido';
                
                $items = [];
                
                // Agregar cambios de observaciones
                if (isset($changes['before']['observaciones']) || isset($changes['after']['observaciones'])) {
                    $items[] = [
                        'fecha' => $history->created_at->format('d/m/Y H:i'),
                        'usuario' => $history->user->name ?? 'Usuario desconocido',
                        'apartado' => $apartadoNombre,
                        'tipo' => 'Observaciones',
                        'antes' => $this->formatValue($changes['before']['observaciones'] ?? null),
                        'despues' => $this->formatValue($changes['after']['observaciones'] ?? null),
                    ];
                }
                
                // Agregar cambios de comentarios UAA
                if (isset($changes['before']['comentarios_uaa']) || isset($changes['after']['comentarios_uaa'])) {
                    $items[] = [
                        'fecha' => $history->created_at->format('d/m/Y H:i'),
                        'usuario' => $history->user->name ?? 'Usuario desconocido',
                        'apartado' => $apartadoNombre,
                        'tipo' => 'Comentarios UAA',
                        'antes' => $this->formatValue($changes['before']['comentarios_uaa'] ?? null),
                        'despues' => $this->formatValue($changes['after']['comentarios_uaa'] ?? null),
                    ];
                }
                
                return $items;
            })
            ->flatten(1);

        // Agregar los datos del historial al objeto auditoría
        $auditoria->historial_comentarios = $historialComentarios;
        $auditoria->historial_observaciones = $historialObservaciones;
        $auditoria->total_cambios_comentarios = $historialComentarios->count();
        $auditoria->total_cambios_observaciones = $historialObservaciones->count();

        return $auditoria;
    }

    private function buildBaseQuery()
    {
        $query = Auditorias::with([
            'catDgsegEf',
            'catClaveAccion',
            'catEntrega',
            'catCuentaPublica',
            'catSiglasTipoAccion',
            'catEnteDeLaAccion',
            'checklistApartados.apartado',
            'auditoriaEtiquetas.etiqueta',
            'auditoriaEtiquetas.apartado', // Nueva estructura - apartado padre
            'auditoriaEtiquetas.checklistApartado.apartado' // Legacy - apartado individual
        ]);

        // Aplicar filtros
        if (!empty($this->search)) {
            $searchTerm = '%' . trim($this->search) . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('clave_de_accion', 'like', $searchTerm)
                  ->orWhere('comentarios', 'like', $searchTerm)
                  ->orWhereHas('catDgsegEf', function($subQuery) use ($searchTerm) {
                      $subQuery->where('valor', 'like', $searchTerm);
                  })
                  ->orWhereHas('catEntrega', function($subQuery) use ($searchTerm) {
                      $subQuery->where('valor', 'like', $searchTerm);
                  })
                  ->orWhereHas('catCuentaPublica', function($subQuery) use ($searchTerm) {
                      $subQuery->where('valor', 'like', $searchTerm);
                  })
                  ->orWhereHas('catSiglasTipoAccion', function($subQuery) use ($searchTerm) {
                      $subQuery->where('valor', 'like', $searchTerm);
                  })
                  ->orWhereHas('catEnteDeLaAccion', function($subQuery) use ($searchTerm) {
                      $subQuery->where('valor', 'like', $searchTerm);
                  });
            });
        }

        if (!empty($this->filtroClaveAccion)) {
            $claveAccionTerm = '%' . trim($this->filtroClaveAccion) . '%';
            $query->where('clave_de_accion', 'like', $claveAccionTerm);
        }

        if (!empty($this->filtroDireccionGeneral)) {
            $query->where('dgseg_ef', $this->filtroDireccionGeneral);
        }

        if (!empty($this->filtroEntrega)) {
            $query->where('entrega', $this->filtroEntrega);
        }

        if (!empty($this->filtroCuentaPublica)) {
            $query->where('cuenta_publica', $this->filtroCuentaPublica);
        }

        if (!empty($this->filtroTipoAccion)) {
            $query->where('siglas_tipo_accion', $this->filtroTipoAccion);
        }

        if (!empty($this->filtroEnteDeLaAccion)) {
            $query->where('ente_de_la_accion', $this->filtroEnteDeLaAccion);
        }

        if (!empty($this->filtroEstatusChecklist)) {
            $query->where('estatus_checklist', $this->filtroEstatusChecklist);
        }

        return $query;
    }

    private function calcularEstadisticasTotales()
    {
        // Obtener TODAS las auditorías que coinciden con los filtros (sin paginación)
        $todasLasAuditorias = $this->buildBaseQuery()
                                   ->orderBy('updated_at', 'desc')
                                   ->get();

        // Procesar cada auditoría para obtener el historial
        $todasLasAuditoriasConHistorial = $todasLasAuditorias->map(function ($auditoria) {
            return $this->procesarHistorialAuditoria($auditoria);
        });

        // Calcular estadísticas totales
        $totalCambiosComentarios = $todasLasAuditoriasConHistorial->sum('total_cambios_comentarios');
        $totalCambiosObservaciones = $todasLasAuditoriasConHistorial->sum('total_cambios_observaciones');
        $auditoriasConComentarios = $todasLasAuditoriasConHistorial->where('total_cambios_comentarios', '>', 0)->count();
        $auditoriasConObservaciones = $todasLasAuditoriasConHistorial->where('total_cambios_observaciones', '>', 0)->count();
        $totalAuditorias = $todasLasAuditoriasConHistorial->count();

        return [
            'totalCambiosComentarios' => $totalCambiosComentarios,
            'totalCambiosObservaciones' => $totalCambiosObservaciones,
            'auditoriasConComentarios' => $auditoriasConComentarios,
            'auditoriasConObservaciones' => $auditoriasConObservaciones,
            'totalAuditorias' => $totalAuditorias,
        ];
    }

    private function formatValue($value)
    {
        if (is_null($value)) {
            return 'Sin valor';
        }
        
        if (is_string($value) && trim($value) === '') {
            return 'Vacío';
        }
        
        return (string) $value;
    }

    public function exportarExcel()
    {
        try {
            // Obtener TODAS las auditorías que coinciden con los filtros actuales (sin paginación)
            $todasLasAuditorias = $this->buildBaseQuery()
                                       ->orderBy('updated_at', 'desc')
                                       ->get();

            // Procesar cada auditoría para obtener el historial
            $auditoriasConHistorial = $todasLasAuditorias->map(function ($auditoria) {
                return $this->procesarHistorialAuditoria($auditoria);
            });

            // Preparar los datos para el Excel
            $datosParaExcel = $auditoriasConHistorial->map(function ($auditoria) {
                $comentarios = $auditoria->historial_comentarios->map(function($comentario) {
                    return "[{$comentario['fecha']}] {$comentario['usuario']}: {$comentario['despues']}";
                })->implode("\n---\n");

                $observaciones = $auditoria->historial_observaciones->map(function($observacion) {
                    return "[{$observacion['fecha']}] {$observacion['usuario']} ({$observacion['apartado']} - {$observacion['tipo']}): {$observacion['despues']}";
                })->implode("\n---\n");

                return [
                    'clave_de_accion' => $auditoria->clave_de_accion,
                    'direccion_general' => $auditoria->catDgsegEf->valor ?? 'No asignada',
                    'entrega' => $auditoria->catEntrega->valor ?? '',
                    'cuenta_publica' => $auditoria->catCuentaPublica->valor ?? '',
                    'tipo_accion' => $auditoria->catSiglasTipoAccion->valor ?? '',
                    'ente_accion' => $auditoria->catEnteDeLaAccion->valor ?? '',
                    'total_cambios_comentarios' => $auditoria->total_cambios_comentarios,
                    'total_cambios_observaciones' => $auditoria->total_cambios_observaciones,
                    'historial_comentarios' => $comentarios ?: 'Sin cambios registrados',
                    'historial_observaciones' => $observaciones ?: 'Sin cambios registrados',
                    'comentarios_actuales' => $auditoria->comentarios ?? '',
                    'fecha_actualizacion' => $auditoria->updated_at->format('d/m/Y H:i'),
                ];
            });

            // Generar nombre del archivo con timestamp y filtros aplicados
            $timestamp = now()->format('Y-m-d_H-i-s');
            $filtrosTexto = $this->hasFiltrosActivos() ? '_filtrado' : '_completo';
            $nombreArchivo = "resumen_auditorias_{$timestamp}{$filtrosTexto}.xlsx";

            // Crear la exportación usando una clase Export
            return Excel::download(new ResumenAuditoriasExport($datosParaExcel, $this->obtenerInformacionFiltros()), $nombreArchivo);

        } catch (\Exception $e) {
            Log::error('Error al exportar Excel: ' . $e->getMessage());
            
            // Mostrar mensaje de error al usuario
            $this->dispatch('mostrarError', [
                'titulo' => 'Error en exportación',
                'mensaje' => 'Ocurrió un error al generar el archivo Excel. Por favor, inténtelo nuevamente.'
            ]);
        }
    }

    private function obtenerInformacionFiltros()
    {
        $filtros = [];
        
        if (!empty($this->search)) {
            $filtros[] = "Búsqueda: {$this->search}";
        }
        
        if (!empty($this->filtroClaveAccion)) {
            $filtros[] = "Clave de Acción: {$this->filtroClaveAccion}";
        }
        
        if (!empty($this->filtroDireccionGeneral)) {
            $direccion = CatDgsegEf::find($this->filtroDireccionGeneral);
            $filtros[] = "Dirección General: " . ($direccion->valor ?? $this->filtroDireccionGeneral);
        }
        
        if (!empty($this->filtroEntrega)) {
            $entrega = CatEntrega::find($this->filtroEntrega);
            $filtros[] = "Entrega: " . ($entrega->valor ?? $this->filtroEntrega);
        }
        
        if (!empty($this->filtroCuentaPublica)) {
            $cuenta = CatCuentaPublica::find($this->filtroCuentaPublica);
            $filtros[] = "Cuenta Pública: " . ($cuenta->valor ?? $this->filtroCuentaPublica);
        }
        
        if (!empty($this->filtroTipoAccion)) {
            $tipo = CatSiglasTipoAccion::find($this->filtroTipoAccion);
            $filtros[] = "Tipo de Acción: " . ($tipo->valor ?? $this->filtroTipoAccion);
        }
        
        if (!empty($this->filtroEnteDeLaAccion)) {
            $ente = CatEnteDeLaAccion::find($this->filtroEnteDeLaAccion);
            $filtros[] = "Ente de la Acción: " . ($ente->valor ?? $this->filtroEnteDeLaAccion);
        }
        
        if (!empty($this->filtroEstatusChecklist)) {
            $filtros[] = "Estatus del Checklist: " . $this->filtroEstatusChecklist;
        }
        
        return empty($filtros) ? ['Sin filtros aplicados'] : $filtros;
    }
} 