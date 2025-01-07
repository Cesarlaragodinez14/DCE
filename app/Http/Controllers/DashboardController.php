<?php

namespace App\Http\Controllers;

use App\Models\Apartado; // Asegúrate de que este modelo esté disponible
use App\Models\Auditorias;
use App\Models\AuditoriasHistory;
use App\Models\ChecklistApartadoHistory;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function dashboardIndex()
    {
        // 1. Expedientes por estatus
        $countsByStatus = Auditorias::select('estatus_checklist', DB::raw('count(*) as total'))
            ->groupBy('estatus_checklist')
            ->orderByDesc('total')
            ->get();

        // 2. Expedientes agrupados por UAA y Estatus
        $countsByUaaAndStatus = Auditorias::with('catUaa')
            ->select('uaa', 'estatus_checklist', DB::raw('count(*) as total'))
            ->groupBy('uaa', 'estatus_checklist')
            ->orderBy('total', 'DESC')
            ->get();

        // 3. Expedientes con comentarios antes de ser aceptadas
        $withCommentsBeforeAccepted = Auditorias::whereNotNull('comentarios')
            ->where('estatus_checklist', '!=', 'Aceptado')
            ->count();

        // 4. Expedientes por Ente Fiscalizado
        $countsByEnteFiscalizado = Auditorias::with('catEnteFiscalizado')
            ->select('ente_fiscalizado', DB::raw('count(*) as total'))
            ->groupBy('ente_fiscalizado')
            ->get();

        // 5. Expedientes por Auditoría Especial
        $countsByAuditoriaEspecial = Auditorias::with('catAuditoriaEspecial')
            ->select('auditoria_especial', DB::raw('count(*) as total'))
            ->groupBy('auditoria_especial')
            ->get();

        // 6. Expedientes por Siglas de Auditoría Especial
        $countsBySiglasAuditoriaEspecial = Auditorias::with('catSiglasAuditoriaEspecial')
            ->select('siglas_auditoria_especial', DB::raw('count(*) as total'))
            ->groupBy('siglas_auditoria_especial')
            ->get();

        // 7. Expedientes por Siglas Tipo Acción
        $countsBySiglasTipoAccion = Auditorias::with('catSiglasTipoAccion')
            ->select('siglas_tipo_accion', DB::raw('count(*) as total'))
            ->groupBy('siglas_tipo_accion')
            ->get();

        // 8. Expedientes por DGSEG EF
        $countsByDgsegEf = Auditorias::with('catDgsegEf')
            ->select('dgseg_ef', DB::raw('count(*) as total'))
            ->groupBy('dgseg_ef')
            ->get();

        // 9. Cambios en Expedientes (Últimos 30 días)
        $last30Days = Carbon::now()->subDays(30);
        $auditoriasChangesByDay = AuditoriasHistory::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as total_changes')
            )
            ->where('created_at', '>=', $last30Days)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy(DB::raw('DATE(created_at)'))
            ->get();

        // 10. Cambios en Checklist Apartados (Por semana)
        $checklistChangesByWeek = ChecklistApartadoHistory::select(
                DB::raw('YEARWEEK(created_at, 1) as week'),
                DB::raw('count(*) as total_changes')
            )
            ->groupBy(DB::raw('YEARWEEK(created_at, 1)'))
            ->orderBy(DB::raw('YEARWEEK(created_at, 1)'))
            ->get();

        // 11. Top 5 usuarios con más cambios en Expedientes
        $topUsersChanges = AuditoriasHistory::with('user')
            ->select('changed_by', DB::raw('count(*) as total_changes'))
            ->groupBy('changed_by')
            ->orderBy('total_changes', 'desc')
            ->get();

        // 12. Campos más modificados en Checklist Apartados
        // Obtenemos los datos de los cambios de la base de datos directamente
        $apartadosData = DB::table('checklist_apartado_histories as cah')
        ->join('checklist_apartados as ca', 'ca.id', '=', 'cah.checklist_apartado_id')
        ->join('apartados as a', 'a.id', '=', 'ca.apartado_id')
        ->select(
            'a.id as apartado_id',
            'a.nombre as apartado_nombre',
            DB::raw('SUM(CASE WHEN JSON_UNQUOTE(JSON_EXTRACT(cah.changes, "$.after.se_integran")) IS NOT NULL THEN 1 ELSE 0 END) as se_integran_changes'),
            DB::raw('SUM(CASE WHEN JSON_UNQUOTE(JSON_EXTRACT(cah.changes, "$.after.observaciones")) IS NOT NULL THEN 1 ELSE 0 END) as observaciones_changes'),
            DB::raw('SUM(CASE WHEN JSON_UNQUOTE(JSON_EXTRACT(cah.changes, "$.after.se_integran")) IS NOT NULL OR JSON_UNQUOTE(JSON_EXTRACT(cah.changes, "$.after.observaciones")) IS NOT NULL THEN 1 ELSE 0 END) as total_changes')
        )
        ->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(cah.changes, "$.after.se_integran")) IS NOT NULL OR JSON_UNQUOTE(JSON_EXTRACT(cah.changes, "$.after.observaciones")) IS NOT NULL')
        ->groupBy('a.id', 'a.nombre')
        ->orderByDesc('total_changes')
        ->get();

        // Convertimos la colección en un array para pasar a la vista
        $apartadosData = $apartadosData->mapWithKeys(function ($item) {
            return [
                $item->apartado_id => [
                    'nombre' => $item->apartado_nombre,
                    'observaciones' => $item->observaciones_changes,
                    'total' => $item->total_changes
                ]
            ];
        });

        // Agrupar los datos en una única variable
        $dashboardData = [
            'countsByStatus' => $countsByStatus,
            'countsByUaaAndStatus' => $countsByUaaAndStatus,
            'withCommentsBeforeAccepted' => $withCommentsBeforeAccepted,
            'countsByEnteFiscalizado' => $countsByEnteFiscalizado,
            'countsByAuditoriaEspecial' => $countsByAuditoriaEspecial,
            'countsBySiglasAuditoriaEspecial' => $countsBySiglasAuditoriaEspecial,
            'countsBySiglasTipoAccion' => $countsBySiglasTipoAccion,
            'countsByDgsegEf' => $countsByDgsegEf,
            'auditoriasChangesByDay' => $auditoriasChangesByDay,
            'checklistChangesByWeek' => $checklistChangesByWeek,
            'topUsersChanges' => $topUsersChanges,
            'apartadosData' => $apartadosData // Datos del reporte
        ];

        return view('admin.dashboard', [
            'dashboardData' => $dashboardData
        ]);
    }
}
