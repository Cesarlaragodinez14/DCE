<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\DashboardEntregasController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DebugController extends Controller
{
    public function debugEntregas(Request $request)
    {
        try {
            // Instanciar el controlador original
            $controller = new DashboardEntregasController();
            
            // Obtener catálogos
            $catalogos = $controller->getCatalogos();
            
            // Intentar obtener los datos para los gráficos
            try {
                $deliveryStatus = $controller->getDeliveryStatusSummary($request);
            } catch (\Exception $e) {
                $deliveryStatus = ['error' => $e->getMessage()];
            }
            
            try {
                $deliveryStatusBySigla = $controller->getDeliveryStatusBySigla($request);
            } catch (\Exception $e) {
                $deliveryStatusBySigla = ['error' => $e->getMessage()];
            }
            
            try {
                $deliveryStatusByAeUaa = $controller->getDeliveryStatusByAeUaa($request);
            } catch (\Exception $e) {
                $deliveryStatusByAeUaa = ['error' => $e->getMessage()];
            }
            
            // Reunir toda la información de depuración
            $debug = [
                'catalogos' => $catalogos,
                'deliveryStatus' => $deliveryStatus,
                'deliveryStatusBySigla' => $deliveryStatusBySigla,
                'deliveryStatusByAeUaa' => $deliveryStatusByAeUaa,
                'request' => [
                    'entrega' => $request->input('entrega'),
                    'cuenta_publica' => $request->input('cuenta_publica'),
                    'uaa_id' => $request->input('uaa_id'),
                    'dg_id' => $request->input('dg_id'),
                ],
                'database_info' => [
                    'aditorias_count' => DB::table('aditorias')->count(),
                    'entregas_count' => DB::table('entregas')->count(),
                    'cat_entrega_count' => DB::table('cat_entrega')->count(),
                    'cat_cuenta_publica_count' => DB::table('cat_cuenta_publica')->count(),
                    'cat_uaa_count' => DB::table('cat_uaa')->count(),
                    'cat_dgseg_ef_count' => DB::table('cat_dgseg_ef')->count(),
                ]
            ];
            
            // Registrar en el log para referencia
            Log::info('Debug entregas', $debug);
            
            return response()->json($debug);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}
