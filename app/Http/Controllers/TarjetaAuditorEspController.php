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
                'entregas' => $entregas,
                'cuentasPublicas' => $cuentasPublicas,
            ]);
        }

        // Generar reporte de responsables
        $reporte = $this->generarReporteResponsables($entregaId, $cuentaPublicaId);
        
        // Generar reporte de expedientes devueltos a la UAA
        $reporteDevueltos = $this->generarReporteExpedientesDevueltos($entregaId, $cuentaPublicaId);

        return view('dashboard.reporte-responsables', [
            'reporte' => $reporte,
            'reporteDevueltos' => $reporteDevueltos,
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
