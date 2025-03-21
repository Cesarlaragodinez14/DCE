<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Auditorias;
use App\Models\CatCuentaPublica;
use App\Models\CatEntrega;
use App\Models\CatDgsegEf;
use App\Models\CatClaveAccion;
use App\Models\CatEnteDeLaAccion;
use App\Models\CatTipoDeAuditoria;
use App\Models\CatEnteFiscalizado;
use App\Models\CatSiglasTipoAccion;
use App\Models\CatAuditoriaEspecial;
use App\Models\CatSiglasAuditoriaEspecial;
use App\Models\CatUaa;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AuditoriasImport;

use App\Jobs\ProcessExcelImport;
use App\Models\Import;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;


class ExcelUploadController extends Controller
{
    public function showUploadForm()
    {
        return view('dashboard.upload-excel');
    }

    public function uploadExcel(Request $request)
    {
        ini_set('memory_limit', '1512M');

        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);
    
        $filePath = $request->file('archivo')->store('temp');
    
        $import = Import::create([
            'file_path' => $filePath,
            'status' => 'pending',
        ]);
    
        ProcessExcelImport::dispatch($import->id);
    
        return redirect()->route('dashboard.progress')->with('success', 'El archivo Excel se está procesando, recibirás una notificación con el cambio de estado.');
    } 
    public function showProgress()
    {
        $imports = Import::all();
        return view('dashboard.progress', compact('imports'));
    }
    public function showImportedData($id)
    {
        // Suponiendo que tienes un modelo Import y una relación con los datos importados
        $import = Import::findOrFail($id);
        $importedData = $import->importedData; // Asumiendo una relación llamada importedData

        return view('imports.show', compact('import', 'importedData'));
    }

    public function generarReporte($entregaSeleccionada = null, $cuentaPublicaSeleccionada = null) {
        // Verificar si se proporcionó una entrega, si no, usar "Entrega 3"
        $entrega = $entregaSeleccionada ?? DB::table('cat_entrega')->where('valor', 'Entrega 3')->value('id');
        
        // Verificar si se proporcionó una cuenta pública, si no, usar la de menor valor
        $cuentaPublica = $cuentaPublicaSeleccionada ?? DB::table('cat_cuenta_publica')->orderBy('valor', 'asc')->value('id');
        
        // Construir la parte dinámica de la consulta
        $dynamicColumns = DB::table('cat_siglas_tipo_accion')
            ->selectRaw("GROUP_CONCAT(DISTINCT CONCAT(
                'SUM(CASE WHEN cat_siglas_tipo_accion.valor = \"', valor, '\" THEN 1 ELSE 0 END) AS `', valor, '`'
            )) as columns")
            ->value('columns');
        
        // Verifica que $dynamicColumns no sea nulo
        if (!$dynamicColumns) {
            $dynamicColumns = ''; // Manejar un caso sin columnas dinámicas
        }
    
        // Construir la consulta completa
        $sql = "SELECT 
                    cat_uaa.descripcion AS 'Auditoria Especial', 
                    cat_uaa.valor AS UAA, 
                    COUNT(aditorias.uaa) AS 'TOTAL AUDITORÍAS', 
                    SUM(CASE WHEN aditorias.clave_de_accion = 'Sin clave de acción' THEN 1 ELSE 0 END) AS 'AUDITORÍAS SIN ACCIONES',
                    SUM(CASE WHEN aditorias.clave_de_accion != 'Sin clave de acción' THEN 1 ELSE 0 END) AS 'AUDITORÍAS CON ACCIONES', 
                    $dynamicColumns 
                FROM 
                    aditorias 
                JOIN 
                    cat_uaa ON aditorias.uaa = cat_uaa.id 
                JOIN 
                    cat_siglas_tipo_accion ON aditorias.siglas_tipo_accion = cat_siglas_tipo_accion.id 
                WHERE 
                    aditorias.entrega = ? 
                    AND aditorias.cuenta_publica = ?
                GROUP BY 
                    cat_uaa.descripcion, cat_uaa.valor 
                ORDER BY 
                    cat_uaa.descripcion ASC";
    
        // Ejecutar la consulta con los parámetros de entrega y cuenta pública
        $result = DB::select($sql, [$entrega, $cuentaPublica]);
    
        return $result;
    }
    

    public function mostrarReporte(Request $request) {
        // Obtener el filtro de entrega y cuenta pública desde el request
        $entregaSeleccionada = $request->input('entrega');
        $cuentaPublicaSeleccionada = $request->input('cuenta_publica');
    
        // Obtener todos los valores de catálogos
        $entregas = DB::table('cat_entrega')->get();
        $cuentasPublicas = DB::table('cat_cuenta_publica')->get();
    
        // Obtener las siglas de acciones distintas
        $acciones = DB::table('cat_siglas_tipo_accion')->pluck('valor')->toArray();
    
        // Generar el reporte con los filtros de entrega y cuenta pública
        $reporte = collect($this->generarReporte($entregaSeleccionada, $cuentaPublicaSeleccionada));
    
        // Construir dinámicamente las columnas para la segunda tabla
        $dynamicColumns = collect($acciones)->map(function ($accion) {
            return "SUM(CASE WHEN cat_siglas_tipo_accion.valor = '$accion' THEN 1 ELSE 0 END) AS `$accion`";
        })->implode(', ');
    
        // Ejecutar la consulta para la segunda tabla
        $sql = "SELECT 
                    COALESCE(cat_dgseg_ef.valor, 'Total de Acciones') AS `Direccion General de Seguimiento`,
                    $dynamicColumns,
                    COUNT(*) AS `Total general`
                FROM 
                    aditorias
                LEFT JOIN 
                    cat_dgseg_ef ON aditorias.dgseg_ef = cat_dgseg_ef.id
                LEFT JOIN 
                    cat_siglas_tipo_accion ON aditorias.siglas_tipo_accion = cat_siglas_tipo_accion.id
                WHERE 
                    aditorias.entrega = ? 
                    AND aditorias.cuenta_publica = ?
                GROUP BY 
                    cat_dgseg_ef.valor
                WITH ROLLUP";
    
        $reporteSegundaTabla = DB::select($sql, [
            $entregaSeleccionada ?? DB::table('cat_entrega')->where('valor', 'Entrega 1')->value('id'), 
            $cuentaPublicaSeleccionada ?? DB::table('cat_cuenta_publica')->orderBy('valor', 'asc')->value('valor')
        ]);
    
        // Pasar los datos a la vista
        return view('dashboard.distribucion', [
            'reporte' => $reporte,
            'acciones' => $acciones,
            'numAcciones' => count($acciones),
            'reporteSegundaTabla' => $reporteSegundaTabla,
            'entregas' => $entregas,
            'cuentasPublicas' => $cuentasPublicas,
        ]);
    }
    
      
    
    public function mostrarReporteOficio(Request $request) {

        // Obtener el filtro de entrega y cuenta pública desde el request
        $entregaSeleccionada = $request->input('entrega');
        $cuentaPublicaSeleccionada = $request->input('cuenta_publica');
    
        $acciones = DB::table('cat_siglas_tipo_accion')->pluck('valor')->toArray();
        // Generar el reporte para la primera tabla
        $reporte = collect($this->generarReporte($entregaSeleccionada, $cuentaPublicaSeleccionada));
    
        // Obtener todos los valores de catálogos
        $entregas = DB::table('cat_entrega')->get();
        $cuentasPublicas = DB::table('cat_cuenta_publica')->get();
    
        // Pasar los datos de ambas tablas a la vista
        return view('dashboard.oficio-uaa', [
            'reporte' => $reporte,
            'acciones' => $acciones,
            'numAcciones' => count($acciones),
            'entregas' => $entregas,
            'cuentasPublicas' => $cuentasPublicas,
        ]);
    }

}
