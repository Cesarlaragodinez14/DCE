<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TarjetaAuditorEspController extends Controller
{
    public function index(Request $request)
    {
        $entregaId = $request->get('entrega');
        $cuentaPublicaId = $request->get('cuenta_publica');

        // Obtener listas para los filtros
        $entregas = DB::table('cat_entrega')->select('id', 'valor')->get();
        $cuentasPublicas = DB::table('cat_cuenta_publica')->select('id', 'valor')->get();

        // Si no hay filtros seleccionados, mostrar vista sin datos
        if (empty($entregaId) || empty($cuentaPublicaId)) {
            return view('dashboard.reporte-responsables', [
                'reporte' => collect(),
                'reporteDevueltos' => collect(),
                'reporteEstatusResponsables' => collect(),
                'entregas' => $entregas,
                'cuentasPublicas' => $cuentasPublicas,
            ]);
        }

        // Generar reporte de responsables
        $reporte = $this->generarReporteResponsables($entregaId, $cuentaPublicaId);
        
        // Generar reporte de expedientes devueltos a la UAA
        $reporteDevueltos = $this->generarReporteExpedientesDevueltos($entregaId, $cuentaPublicaId);

        // Generar reporte de estatus por responsables
        $reporteEstatusResponsables = $this->generarReporteEstatusResponsables($entregaId, $cuentaPublicaId);

        return view('dashboard.reporte-responsables', [
            'reporte' => $reporte,
            'reporteDevueltos' => $reporteDevueltos,
            'reporteEstatusResponsables' => $reporteEstatusResponsables,
            'entregas' => $entregas,
            'cuentasPublicas' => $cuentasPublicas,
        ]);
    }

    private function generarReporteResponsables($entregaId, $cuentaPublicaId)
    {
        $query = DB::table('aditorias as a')
            ->join('cat_dgseg_ef as dg', 'a.dgseg_ef', '=', 'dg.id')
            ->leftJoin('entregas as e', function($join) {
                $join->on('a.id', '=', 'e.auditoria_id')
                     ->whereRaw('e.id = (SELECT MAX(e2.id) FROM entregas e2 WHERE e2.auditoria_id = a.id)');
            })
            ->select(
                'dg.valor as responsable',
                // A Recibir - total de auditorías que pertenecen a este responsable
                DB::raw('COUNT(a.id) as a_recibir'),
                // Entregados - auditorías que tienen al menos una entrega
                DB::raw('SUM(CASE WHEN e.id IS NOT NULL THEN 1 ELSE 0 END) as entregados'),
                // Aceptados - estado de checklist = "Aceptado"
                DB::raw('SUM(CASE WHEN a.estatus_checklist = "Aceptado" THEN 1 ELSE 0 END) as aceptados'),
                // Devueltos - estado de checklist = "Devuelto"
                DB::raw('SUM(CASE WHEN a.estatus_checklist = "Devuelto" THEN 1 ELSE 0 END) as devueltos'),
                // En Revisión - estado de checklist contiene "En Proceso" o "En Revisión"
                DB::raw('SUM(CASE WHEN (a.estatus_checklist LIKE "%En Proceso%" OR a.estatus_checklist LIKE "%Auditor%") THEN 1 ELSE 0 END) as en_revision'),
                // Sin Revisar - estado de checklist = "Sin Revisar" o NULL
                DB::raw('SUM(CASE WHEN (a.estatus_checklist like "%Sin Revisar%" OR a.estatus_checklist IS NULL OR a.estatus_checklist = "") THEN 1 ELSE 0 END) as sin_revisar')
            )
            ->where('a.entrega', $entregaId)
            ->where('a.cuenta_publica', $cuentaPublicaId)
            ->groupBy('dg.id', 'dg.valor')
            ->orderBy('dg.valor');

        // Aplicar exclusiones RIASF
        $query = $this->aplicarExclusionesRIASF($query, $entregaId, $cuentaPublicaId);

        $resultados = $query->get();

        // Calcular campos derivados
        $reporte = $resultados->map(function ($row) {
            $pendientesEntregar = max(0, $row->a_recibir - $row->entregados);
            $porcentajeAvance = $row->a_recibir > 0 ? round(($row->aceptados / $row->a_recibir) * 100, 2) : 0;

            return (object) [
                'responsable' => $row->responsable,
                'a_recibir' => $row->a_recibir,
                'entregados' => $row->entregados,
                'pendientes_entregar' => $pendientesEntregar,
                'aceptados' => $row->aceptados,
                'devueltos' => $row->devueltos,
                'en_revision' => $row->en_revision,
                'sin_revisar' => $row->sin_revisar,
                'porcentaje_avance' => $porcentajeAvance,
            ];
        });

        return $reporte;
    }

    private function generarReporteExpedientesDevueltos($entregaId, $cuentaPublicaId)
    {
        $query = DB::table('aditorias as a')
            ->join('cat_siglas_auditoria_especial as sae', 'a.siglas_auditoria_especial', '=', 'sae.id')
            ->select(
                'sae.valor as responsable',
                // R = siglas_tipo_accion = 34
                DB::raw('SUM(CASE WHEN a.siglas_tipo_accion = 34 THEN 1 ELSE 0 END) as r'),
                // PO = siglas_tipo_accion = 35  
                DB::raw('SUM(CASE WHEN a.siglas_tipo_accion = 35 THEN 1 ELSE 0 END) as po'),
                // SA = siglas_tipo_accion = 38
                DB::raw('SUM(CASE WHEN a.siglas_tipo_accion = 38 THEN 1 ELSE 0 END) as sa')
            )
            ->where('a.entrega', $entregaId)
            ->where('a.cuenta_publica', $cuentaPublicaId)
            ->where('a.estatus_checklist', 'Devuelto')
            ->groupBy('sae.id', 'sae.valor')
            ->orderBy('sae.valor');

        // Aplicar exclusiones RIASF
        $query = $this->aplicarExclusionesRIASF($query, $entregaId, $cuentaPublicaId);

        $resultados = $query->get();

        // Calcular totales y agregar campo total_general
        $reporte = $resultados->map(function ($row) {
            $totalGeneral = $row->r + $row->po + $row->sa;

            return (object) [
                'responsable' => $row->responsable,
                'r' => $row->r,
                'po' => $row->po,
                'sa' => $row->sa,
                'total_general' => $totalGeneral,
            ];
        });

        return $reporte;
    }

    /**
     * Genera el reporte de estatus por responsables (segunda tabla)
     */
    private function generarReporteEstatusResponsables($entregaId, $cuentaPublicaId)
    {
        // Mapeo de UAA a sus grupos principales
        $uaaToGroup = [
            // AECF
            'DGAFCF' => 'AECF',
            'DGAFFA' => 'AECF', 
            'DGAFFB' => 'AECF',
            'DGAFFC' => 'AECF',
            'DGAIFF' => 'AECF',
            'DGATIC' => 'AECF',
            // AEGF
            'DGAFGF' => 'AEGF',
            'DGAGFA' => 'AEGF',
            'DGAGFB' => 'AEGF', 
            'DGAGFC' => 'AEGF',
            'DGAGFD' => 'AEGF',
            'DGEGF' => 'AEGF'
        ];

        // Orden específico para mostrar las UAA
        $ordenUAA = [
            'AECF' => ['DGAFCF', 'DGAFFA', 'DGAFFB', 'DGAFFC', 'DGAIFF', 'DGATIC'],
            'AEGF' => ['DGAFGF', 'DGAGFA', 'DGAGFB', 'DGAGFC', 'DGAGFD', 'DGEGF']
        ];

        // Obtener datos agrupados por UAA
        $baseQuery = DB::table('aditorias as a')
            ->join('cat_uaa as cu', 'a.uaa', '=', 'cu.id')
            ->where('a.entrega', $entregaId)
            ->where('a.cuenta_publica', $cuentaPublicaId);

        // Aplicar exclusiones RIASF
        $baseQuery = $this->aplicarExclusionesRIASF($baseQuery, $entregaId, $cuentaPublicaId);

        $rawData = $baseQuery
            ->select(
                'cu.valor as uaa_codigo',
                'cu.nombre as uaa_nombre',
                DB::raw('IFNULL(a.estatus_checklist, "Sin Revisar") as estatus_original'),
                DB::raw('COUNT(a.id) as total')
            )
            ->groupBy('cu.id', 'cu.valor', 'cu.nombre', 'a.estatus_checklist')
            ->orderBy('cu.valor')
            ->get();

        // Normalizar estatus
        $normalizedData = collect($rawData)->map(function($item) {
            return [
                'uaa_codigo' => $item->uaa_codigo,
                'uaa_nombre' => $item->uaa_nombre,
                'estatus_checklist' => $this->normalizeChecklistStatus($item->estatus_original),
                'total' => $item->total
            ];
        });

        // Agrupar datos por UAA
        $uaaGrouped = $normalizedData->groupBy('uaa_codigo');
        
        $resultado = collect();

        // Procesar cada grupo (AECF, AEGF)
        foreach ($ordenUAA as $grupo => $uaasDelGrupo) {
            // Datos para el total del grupo
            $totalGrupo = [
                'aceptado' => 0,
                'devuelto' => 0, 
                'en_revision' => 0,
                'sin_revisar' => 0,
                'total_general' => 0
            ];

            // Recopilar datos de UAA individuales del grupo
            $uaasData = [];
            
            foreach ($uaasDelGrupo as $uaaCodigo) {
                if (isset($uaaGrouped[$uaaCodigo])) {
                    $uaaExpedientes = $uaaGrouped[$uaaCodigo];
                    $estatusAgrupados = $this->agruparPorEstatus($uaaExpedientes);
                    $total = $uaaExpedientes->sum('total');

                    $datosUAA = [
                        'aceptado' => $estatusAgrupados['Aceptado'] ?? 0,
                        'devuelto' => $estatusAgrupados['Devuelto'] ?? 0,
                        'en_revision' => $estatusAgrupados['En proceso de revisión (lista de verificación)'] ?? 0,
                        'sin_revisar' => $estatusAgrupados['Pendientes de Revisión'] ?? 0,
                        'total_general' => $total
                    ];

                    // Sumar al total del grupo
                    $totalGrupo['aceptado'] += $datosUAA['aceptado'];
                    $totalGrupo['devuelto'] += $datosUAA['devuelto'];
                    $totalGrupo['en_revision'] += $datosUAA['en_revision'];
                    $totalGrupo['sin_revisar'] += $datosUAA['sin_revisar'];
                    $totalGrupo['total_general'] += $datosUAA['total_general'];

                    // Guardar datos de la UAA individual
                    $uaasData[$uaaCodigo] = $datosUAA;
                }
            }

            // Solo agregar el grupo si tiene datos
            if ($totalGrupo['total_general'] > 0) {
                // Agregar fila del grupo total
                $resultado->push((object)[
                    'responsable' => $grupo,
                    'aceptado' => $totalGrupo['aceptado'],
                    'devuelto' => $totalGrupo['devuelto'],
                    'en_revision' => $totalGrupo['en_revision'],
                    'sin_revisar' => $totalGrupo['sin_revisar'],
                    'total_general' => $totalGrupo['total_general'],
                    'porcentaje_avance' => $totalGrupo['total_general'] > 0 ? 
                        round(($totalGrupo['aceptado'] / $totalGrupo['total_general']) * 100, 1) : 0,
                    'es_grupo_principal' => true,
                    'es_uaa_especial' => false
                ]);

                // Agregar filas de UAA individuales en el orden especificado
                foreach ($uaasDelGrupo as $uaaCodigo) {
                    if (isset($uaasData[$uaaCodigo]) && $uaasData[$uaaCodigo]['total_general'] > 0) {
                        $datos = $uaasData[$uaaCodigo];
                        $resultado->push((object)[
                            'responsable' => $uaaCodigo,
                            'aceptado' => $datos['aceptado'],
                            'devuelto' => $datos['devuelto'],
                            'en_revision' => $datos['en_revision'],
                            'sin_revisar' => $datos['sin_revisar'],
                            'total_general' => $datos['total_general'],
                            'porcentaje_avance' => $datos['total_general'] > 0 ? 
                                round(($datos['aceptado'] / $datos['total_general']) * 100, 1) : 0,
                            'es_grupo_principal' => false,
                            'es_uaa_especial' => true
                        ]);
                    }
                }
            }
        }

        // Agregar otras UAA que no pertenecen a AECF ni AEGF
        foreach ($uaaGrouped as $uaaCodigo => $uaaExpedientes) {
            if (!isset($uaaToGroup[$uaaCodigo])) {
                $estatusAgrupados = $this->agruparPorEstatus($uaaExpedientes);
                $total = $uaaExpedientes->sum('total');

                if ($total > 0) {
                    $resultado->push((object)[
                        'responsable' => $uaaCodigo,
                        'aceptado' => $estatusAgrupados['Aceptado'] ?? 0,
                        'devuelto' => $estatusAgrupados['Devuelto'] ?? 0,
                        'en_revision' => $estatusAgrupados['En proceso de revisión (lista de verificación)'] ?? 0,
                        'sin_revisar' => $estatusAgrupados['Pendientes de Revisión'] ?? 0,
                        'total_general' => $total,
                        'porcentaje_avance' => $total > 0 ? 
                            round((($estatusAgrupados['Aceptado'] ?? 0) / $total) * 100, 1) : 0,
                        'es_grupo_principal' => false,
                        'es_uaa_especial' => false
                    ]);
                }
            }
        }

        return $resultado;
    }

    /**
     * Normaliza el estatus del checklist a las 4 categorías principales
     */
    private function normalizeChecklistStatus($estatus)
    {
        if (empty($estatus) || $estatus === 'Sin Revisar') {
            return "Pendientes de Revisión";
        } elseif ($estatus === "Aceptado") {
            return "Aceptado";
        } elseif ($estatus === "Devuelto") {
            return "Devuelto";
        } else {
            return "En proceso de revisión (lista de verificación)";
        }
    }

    /**
     * Determina si un responsable es AECF o AEGF
     */
    private function esAECFoAEGF($responsable)
    {
        // AECF y AEGF son direcciones especiales que tienen múltiples UAA
        return in_array($responsable, ['AECF', 'AEGF']) || 
               str_contains($responsable, 'Auditoría Especial de') ||
               str_contains($responsable, 'AE');
    }

    /**
     * Obtiene el formato de presentación para UAA de AECF/AEGF
     */
    private function formatUAALabel($uaaValor, $uaaNombre, $responsable)
    {
        // Para AECF y AEGF, mostrar solo el código de la UAA ya que son más específicas
        if ($this->esAECFoAEGF($responsable)) {
            return !empty($uaaNombre) ? $uaaNombre : $uaaValor;
        }
        
        // Para otros, mantener formato completo
        return $uaaValor . (!empty($uaaNombre) ? ' - ' . $uaaNombre : '');
    }

    /**
     * Agrupa expedientes por estatus
     */
    private function agruparPorEstatus($expedientes)
    {
        return $expedientes->groupBy('estatus_checklist')->map(function($group) {
            return $group->sum('total');
        })->toArray();
    }

    private function aplicarExclusionesRIASF($query, $entregaId, $cuentaPublicaId)
    {
        // Solo aplicar el filtro especial cuando entrega=18 Y cuenta_publica=1
        if ($entregaId == 18 && $cuentaPublicaId == 1) {
            $query->where(function($q) {
                $q->where(function($subQ) {
                    // Excluir todos los registros donde siglas_auditoria_especial = 38
                    $subQ->where('a.siglas_auditoria_especial', '!=', 38);
                })->where(function($subQ) {
                    // Para siglas_auditoria_especial = 39, solo incluir siglas_tipo_accion = 35
                    $subQ->where('a.siglas_auditoria_especial', '!=', 39)
                         ->orWhere(function($innerQ) {
                             $innerQ->where('a.siglas_auditoria_especial', 39)
                                    ->where('a.siglas_tipo_accion', 35);
                         });
                });
            });
        }
        
        return $query;
    }
}
