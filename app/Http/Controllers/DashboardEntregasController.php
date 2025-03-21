<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\CatUaa;
use App\Models\Apartado;
use App\Models\Auditorias;
use App\Models\AuditoriasHistory;
use App\Models\ChecklistApartadoHistory;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardEntregasController extends Controller
{ 
    // Constantes para mejorar legibilidad
    const STATUS_SIN_REVISAR = "Sin Revisar";
    const STATUS_ACEPTADO = "Aceptado";
    const STATUS_DEVUELTO = "Devuelto";
    const STATUS_EN_PROCESO = "En Proceso de Revisión del Checklist";
    
    const ESTADO_RECIBIDO_DCE = "Recibido en el DCE para resguardo (DGSEG – DCE)%";
    const ESTADO_RECIBIDO_DGSEG = "Recibido por la DGSEG para revisión (DCE - DGSEG) - Firmado%";
    const ESTADO_RECIBIDO_CORRECCIONES = "Recibido por la DGSEG para revisión de correcciones (DCE - DGSEG) - Firmado%";
    const ESTADO_PROGRAMADO = "Programado%";

    /**
     * Normaliza el estatus del checklist a las 4 categorías originales
     *
     * @param string $estatus
     * @return string
     */
    private function normalizeChecklistStatus($estatus)
    {
        // Si el estatus es null o vacío, devolvemos "Sin Revisar"
        if (empty($estatus)) {
            return self::STATUS_SIN_REVISAR;
        }
        
        // Mapeamos los sinónimos y nuevas categorías a las 4 originales
        if (str_contains($estatus, 'Sin Revisar')) {
            return self::STATUS_SIN_REVISAR;
        } elseif ($estatus === self::STATUS_ACEPTADO) {
            return self::STATUS_ACEPTADO;
        } elseif ($estatus === self::STATUS_DEVUELTO) {
            return self::STATUS_DEVUELTO;
        } else {
            // Todos los demás son sinónimos de "En Proceso"
            return self::STATUS_EN_PROCESO;
        }
    }
    
    /**
     * Aplica la normalización del estatus a una colección de resultados
     *
     * @param \Illuminate\Support\Collection $collection
     * @param string $estatusField
     * @return \Illuminate\Support\Collection
     */
    private function normalizeCollection($collection, $estatusField = 'estatus_checklist')
    {
        return $collection->map(function($item) use ($estatusField) {
            if (is_object($item) && property_exists($item, $estatusField)) {
                $item->$estatusField = $this->normalizeChecklistStatus($item->$estatusField);
            } elseif (is_array($item) && isset($item[$estatusField])) {
                $item[$estatusField] = $this->normalizeChecklistStatus($item[$estatusField]);
            }
            return $item;
        });
    }
    
    /**
     * Maneja las redirecciones basadas en el rol del usuario
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|null
     */
    private function handleRoleBasedRedirects(Request $request)
    {
        if (Auth::user()->hasRole(['Director General SEG', 'AECF', 'AED', 'AEGF'])) {
            $dgReal = CatUaa::find(Auth::user()->uaa_id)->dgseg_ef_id;
            
            if ($request->dg_id != $dgReal) {
                return redirect()->route('dashboard.charts.index', array_merge($request->query(), [
                    'dg_id' => $dgReal
                ]));
            }
        } else if (Auth::user()->hasRole(['DGUAA'])) {
            if ($request->uaa_id != Auth::user()->uaa_id) {
                return redirect()->route('dashboard.charts.index', array_merge($request->query(), [
                    'uaa_id' => Auth::user()->uaa_id
                ]));
            }
        }
        
        return null;
    }
    
    /**
     * Obtiene los catálogos para los filtros
     *
     * @param Request $request
     * @return array
     */
    private function getCatalogos()
    {
        // Obtener solo las UAA que tienen registros en aditorias
        $uaas = DB::table('cat_uaa')
            ->join('aditorias', 'cat_uaa.id', '=', 'aditorias.uaa')
            ->select('cat_uaa.id', 'cat_uaa.valor')
            ->distinct()
            ->get();

        // Obtener solo las DGSEG EF que tienen registros en aditorias
        $dgsegs = DB::table('cat_dgseg_ef')
            ->join('aditorias', 'cat_dgseg_ef.id', '=', 'aditorias.dgseg_ef')
            ->select('cat_dgseg_ef.id', 'cat_dgseg_ef.valor')
            ->distinct()
            ->get();

        // Obtener solo las Entregas que tienen registros en aditorias
        $entregas = DB::table('cat_entrega')
            ->join('aditorias', 'cat_entrega.id', '=', 'aditorias.entrega')
            ->select('cat_entrega.id', 'cat_entrega.valor')
            ->distinct()
            ->get();

        // Obtener solo las Cuentas Públicas que tienen registros en aditorias
        $cuentasPublicas = DB::table('cat_cuenta_publica')
            ->join('aditorias', 'cat_cuenta_publica.id', '=', 'aditorias.cuenta_publica')
            ->select('cat_cuenta_publica.id', 'cat_cuenta_publica.valor')
            ->distinct()
            ->get();
            
        return [
            'uaas' => $uaas,
            'dgsegs' => $dgsegs,
            'entregas' => $entregas,
            'cuentasPublicas' => $cuentasPublicas
        ];
    }
    
    /**
     * Aplica los filtros a una consulta
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param Request $request
     * @return \Illuminate\Database\Query\Builder
     */
    private function applyFilters($query, Request $request)
    {
        return $query->when($request->filled('uaa_id'), function($q) use ($request) {
                $q->where('aditorias.uaa', $request->uaa_id);
            })
            ->when($request->filled('dg_id'), function($q) use ($request) {
                $q->where('aditorias.dgseg_ef', $request->dg_id);
            })
            ->when($request->filled('entrega'), function($q) use ($request) {
                $q->where('aditorias.entrega', $request->entrega);
            })
            ->when($request->filled('cuenta_publica'), function($q) use ($request) {
                $q->where('aditorias.cuenta_publica', $request->cuenta_publica);
            });
    }
    
    /**
     * Construye la condición SQL que determina si una entrega está completa
     *
     * @return string
     */
    private function buildDeliveredCondition()
    {
        return "
            CASE WHEN entregas.estado LIKE '" . self::ESTADO_RECIBIDO_DCE . "'
                OR (
                    entregas.estado LIKE '" . self::ESTADO_RECIBIDO_DGSEG . "'
                    AND aditorias.estatus_checklist = '" . self::STATUS_ACEPTADO . "'
                )
                OR (
                    entregas.estado LIKE '" . self::ESTADO_RECIBIDO_CORRECCIONES . "'
                    AND aditorias.estatus_checklist = '" . self::STATUS_ACEPTADO . "'
                )
                THEN 1 ELSE 0 END
        ";
    }
    
    /**
     * Construye la condición SQL que determina si una entrega está en proceso
     *
     * @return string
     */
    private function buildInProcessCondition()
    {
        return "
            CASE WHEN 
                entregas.id IS NOT NULL
                AND NOT (
                    entregas.estado LIKE '" . self::ESTADO_RECIBIDO_DCE . "'
                    OR (
                        entregas.estado LIKE '" . self::ESTADO_RECIBIDO_DGSEG . "' 
                        AND aditorias.estatus_checklist = '" . self::STATUS_ACEPTADO . "'
                    )
                    OR (
                        entregas.estado LIKE '" . self::ESTADO_RECIBIDO_CORRECCIONES . "' 
                        AND aditorias.estatus_checklist = '" . self::STATUS_ACEPTADO . "'
                    )
                )
                AND entregas.estado NOT LIKE '" . self::ESTADO_PROGRAMADO . "'
            THEN 1 
            ELSE 0 
            END
        ";
    }
    
    /**
     * Construye la condición SQL que determina si una entrega no está programada
     *
     * @return string
     */
    private function buildUnscheduledCondition()
    {
        return "
            CASE WHEN entregas.id IS NULL 
            OR entregas.estado LIKE '" . self::ESTADO_PROGRAMADO . "' 
                THEN 1 ELSE 0 END
        ";
    }
    
    /**
     * Obtiene el resumen general del estado de entregas
     *
     * @param Request $request
     * @return object
     */
    private function getDeliveryStatusSummary(Request $request)
    {
        $query = DB::table('aditorias')
            ->leftJoin('entregas', 'entregas.auditoria_id', '=', 'aditorias.id')
            ->select(
                DB::raw("SUM(" . $this->buildDeliveredCondition() . ") as delivered"),
                DB::raw("SUM(" . $this->buildInProcessCondition() . ") as in_process"),
                DB::raw("SUM(" . $this->buildUnscheduledCondition() . ") as unscheduled")
            );
            
        $query = $this->applyFilters($query, $request);
        $rawDeliveryStatus = $query->first();

        // Convertir a enteros
        $delivered   = (int) $rawDeliveryStatus->delivered;
        $inProcess   = (int) $rawDeliveryStatus->in_process;
        $unscheduled = (int) $rawDeliveryStatus->unscheduled;
        $total       = $delivered + $inProcess + $unscheduled;

        return [
            'delivered'   => $delivered,
            'in_process'  => $inProcess,
            'unscheduled' => $unscheduled,
            'total'       => $total,
        ];
    }
    
    /**
     * Obtiene el estado de entregas agrupado por siglas
     *
     * @param Request $request
     * @return \Illuminate\Support\Collection
     */
    private function getDeliveryStatusBySigla(Request $request)
    {
        $query = DB::table('aditorias')
            ->leftJoin('entregas', 'entregas.auditoria_id', '=', 'aditorias.id')
            ->leftJoin('cat_siglas_auditoria_especial as csae', 'aditorias.siglas_auditoria_especial', '=', 'csae.id')
            ->select(
                'csae.valor as sigla_name',
                DB::raw("SUM(" . $this->buildDeliveredCondition() . ") as delivered"),
                DB::raw("SUM(" . $this->buildInProcessCondition() . ") as in_process"),
                DB::raw("SUM(" . $this->buildUnscheduledCondition() . ") as unscheduled")
            )
            ->groupBy('csae.valor');
            
        $query = $this->applyFilters($query, $request);
        return $query->get();
    }
    
    /**
     * Obtiene el estado de entregas agrupado por auditoría especial y UAA
     *
     * @param Request $request
     * @return \Illuminate\Support\Collection
     */
    private function getDeliveryStatusByAeUaa(Request $request)
    {
        $query = DB::table('aditorias')
            ->join('cat_siglas_auditoria_especial as ae', 'ae.id', '=', 'aditorias.siglas_auditoria_especial')
            ->join('cat_uaa as cu', 'cu.id', '=', 'aditorias.uaa')
            ->leftJoin('entregas', 'entregas.auditoria_id', '=', 'aditorias.id')
            ->select(
                'ae.valor as ae_valor',
                'cu.nombre as uaa_valor',
                DB::raw("SUM(" . $this->buildDeliveredCondition() . ") as delivered"),
                DB::raw("SUM(" . $this->buildInProcessCondition() . ") as in_process"),
                DB::raw("SUM(" . $this->buildUnscheduledCondition() . ") as unscheduled")
            )
            ->groupBy('ae.valor', 'cu.nombre')
            ->orderBy('ae.valor');
            
        $query = $this->applyFilters($query, $request);
        $rawDeliveryByAeUaa = $query->get();

        // Estructura los datos para la gráfica
        $grouped = [];
        foreach ($rawDeliveryByAeUaa as $item) {
            $ae = $item->ae_valor ?: 'Sin Datos';
            $uaa = $item->uaa_valor ?: 'Sin Datos';
            $key = $ae . '|' . $uaa;
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'ae_valor' => $ae,
                    'uaa_valor' => $uaa,
                    'delivered' => 0,
                    'in_process' => 0,
                    'unscheduled' => 0,
                    'total' => 0,
                ];
            }
            $grouped[$key]['delivered'] = (int)$item->delivered;
            $grouped[$key]['in_process'] = (int)$item->in_process;
            $grouped[$key]['unscheduled'] = (int)$item->unscheduled;
            $grouped[$key]['total'] = $grouped[$key]['delivered'] + $grouped[$key]['in_process'] + $grouped[$key]['unscheduled'];
        }

        // Convertir a array de valores y ordenar
        return collect($grouped)->sortByDesc('total')->values();
    }
    
    /**
     * Prepara los datos para la gráfica de barras
     *
     * @param \Illuminate\Support\Collection $grouped
     * @return array
     */
    private function prepareChartData($grouped)
    {
        $labels = $grouped->map(function($item) {
            return $item['ae_valor'] . ' / ' . $item['uaa_valor'];
        })->toArray();

        $datasetDelivered = $grouped->pluck('delivered')->toArray();
        $datasetInProcess = $grouped->pluck('in_process')->toArray();
        $datasetUnscheduled = $grouped->pluck('unscheduled')->toArray();
        
        return [
            'labels' => $labels,
            'datasetDelivered' => $datasetDelivered,
            'datasetInProcess' => $datasetInProcess,
            'datasetUnscheduled' => $datasetUnscheduled,
        ];
    }
    
    /**
     * Acción principal del dashboard de entregas
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function dashboardEntregasIndex(Request $request)
    {
        $validated = $request->validate([
            'entrega' => 'nullable|exists:cat_entrega,id',
            'cuenta_publica' => 'nullable|exists:cat_cuenta_publica,id',
            'uaa_id' => 'nullable|exists:cat_uaa,id',
            'dg_id' => 'nullable|exists:cat_dgseg_ef,id',
        ]);

        // Verificar si necesitamos redirigir basado en el rol del usuario
        $redirect = $this->handleRoleBasedRedirects($request);
        if ($redirect) {
            return $redirect;
        }
        
        // Obtener los catálogos para los filtros
        $catalogos = $this->getCatalogos();
        
        // Obtener datos para los gráficos
        $deliveryStatus = $this->getDeliveryStatusSummary($request);
        $deliveryStatusBySigla = $this->getDeliveryStatusBySigla($request);
        $deliveryStatusByAeUaa = $this->getDeliveryStatusByAeUaa($request);
        
        // Preparar datos para el gráfico de barras
        $chartData = $this->prepareChartData($deliveryStatusByAeUaa);
        
        // Agrupar todos los datos para el dashboard
        $dashboardData = array_merge(
            [
                'deliveryStatus' => $deliveryStatus,
                'deliveryStatusBySigla' => $deliveryStatusBySigla,
                'deliveryStatusByAeUaa' => $deliveryStatusByAeUaa
            ],
            $chartData
        );

        return view('admin.stats.entregas', array_merge(
            ['dashboardData' => $dashboardData],
            $catalogos
        ));
    }
}