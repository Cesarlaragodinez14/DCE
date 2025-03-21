<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RecepcionHistoryController extends Controller
{
    public function index(Request $request)
    {
        // 1) Leer parámetros de filtro
        $claveAccion    = $request->input('clave_accion');
        $estado         = $request->input('estado');
        $fechaRecepcion = $request->input('fecha_recepcion');
        $generadoPor    = $request->input('generado_por'); // <-- Nuevo filtro

        // 2) Obtener listas "distinct" para poblar los selects:
        //    a) Estados => "eh.estado"
        $estadosArr = DB::table('entregas_historial')
            ->select('estado')
            ->whereNotNull('estado')
            ->distinct()
            ->pluck('estado');

        //    b) Fechas de Recepción => "fecha_real_entrega" (solo la parte DATE)
        $fechasRecepcionArr = DB::table('entregas')
            ->selectRaw('DISTINCT DATE(fecha_real_entrega) as fecha')
            ->whereNotNull('fecha_real_entrega')
            ->orderBy('fecha', 'desc')
            ->pluck('fecha');

        //    c) Generado por => usuarios que aparecen en 'entregas_historial.usuario_recibe_id'
        //       unimos con 'users' para obtener su nombre (u.name)
        $generadosArr = DB::table('entregas_historial as eh')
                ->join('users as us', 'us.id', '=', 'eh.usuario_recibe_id')
                ->select('us.name', DB::raw('COUNT(eh.usuario_recibe_id) as total'))
                ->groupBy('us.name')
                ->orderBy('us.name', 'asc') // Ordenar de A a Z
            ->get();

        // 3) Construir la consulta principal al historial
        $query = DB::table('entregas_historial as eh')
            ->join('entregas as e', 'eh.entrega_id', '=', 'e.id')
            ->join('aditorias as a', 'a.id', '=', 'e.auditoria_id')
            ->join('users as u', 'u.id', '=', 'eh.usuario_recibe_id')
            ->select(
                'eh.id',                // ID del historial
                'eh.entrega_id',
                'eh.estado as hist_estado',
                'eh.fecha_estado',
                'eh.pdf_path as hist_pdf',
                'eh.created_at as hist_created_at',
                'eh.updated_at as hist_updated_at',
                
                'a.clave_de_accion as clave_accion',
                'u.name as responsable', // "Generado por"

                'e.id as entrega_id',
                'e.tipo_accion',
                'e.fecha_entrega',
                'e.numero_legajos',
                'e.estado as estado_actual',
                'e.fecha_real_entrega', // "fecha de recepción"
                'e.created_at as entrega_created_at',
                'e.updated_at as entrega_updated_at'
            )
            ->orderBy('eh.fecha_estado', 'desc'); // <-- Si realmente quieres solo 1 registro, de lo contrario quita el limit.

        // 4) Aplicar filtros
        // a) Clave de Acción

        if (Auth::user()->hasRole('Director General')) {
            $query->where('a.uaa', Auth::user()->uaa_id);
        }

        if (!empty($claveAccion)) {
            $query->where('a.clave_de_accion', 'like', '%'.$claveAccion.'%');
        }

        // b) Estado
        if (!empty($estado)) {
            $query->where('eh.estado', $estado);
        }

        // c) Fecha de Recepción
        if (!empty($fechaRecepcion)) {
            $query->whereDate('e.fecha_real_entrega', '=', $fechaRecepcion);
        }
        
        // d) Generado por (usuario_recibe_id => users.name)
        if (!empty($generadoPor)) {
            // Buscamos coincidencia exacta y ordenamos de A a Z
            $query->where('u.name', $generadoPor);
        }

        // 5) Ejecutar la consulta
        $movimientos = $query->get();

        // 6) Retornar la vista
        return view('dashboard.historial-expedientes.index', [
            'movimientos'       => $movimientos,
            'estados'           => $estadosArr,
            'fechasRecepcion'   => $fechasRecepcionArr,
            'generados'         => $generadosArr, // <-- nuevo array para el select de "Generado por"

            // Filtros
            'claveAccion'       => $claveAccion,
            'estado'            => $estado,
            'fechaRecepcion'    => $fechaRecepcion,
            'generadoPor'       => $generadoPor,
        ]);
    }
}
