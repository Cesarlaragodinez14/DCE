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

    public function generarReporte() {
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
                GROUP BY 
                    cat_uaa.descripcion, cat_uaa.valor 
                ORDER BY 
                    cat_uaa.descripcion ASC";
        
        // Ejecutar la consulta
        $result = DB::select($sql);
        
        // Retornar los resultados o hacer algo con ellos
        return $result;
    }
    
    public function mostrarReporte() {
        // Obtener todas las siglas de acciones distintas
        $acciones = DB::table('cat_siglas_tipo_accion')->pluck('valor')->toArray();
    
        // Generar el reporte para la primera tabla
        $reporte = collect($this->generarReporte()); // Convertir a colección
    
        // Construir dinámicamente las columnas para la segunda tabla
        $dynamicColumns = collect($acciones)->map(function ($accion) {
            return "SUM(CASE WHEN cat_siglas_tipo_accion.valor = '$accion' THEN 1 ELSE 0 END) AS `$accion`";
        })->implode(', ');
    
        // Construir la consulta completa para la segunda tabla
        $sql = "SELECT 
                    COALESCE(cat_dgseg_ef.valor, 'Sin asignar') AS `Direccion General de Seguimiento`,
                    $dynamicColumns,
                    COUNT(*) AS `Total general`
                FROM 
                    aditorias
                LEFT JOIN 
                    cat_dgseg_ef ON aditorias.dgseg_ef = cat_dgseg_ef.id
                LEFT JOIN 
                    cat_siglas_tipo_accion ON aditorias.siglas_tipo_accion = cat_siglas_tipo_accion.id
                GROUP BY 
                    cat_dgseg_ef.valor
                WITH ROLLUP";
    
        // Ejecutar la consulta para la segunda tabla
        $reporteSegundaTabla = DB::select($sql); // Eliminar DB::raw()
    
        // Pasar los datos de ambas tablas a la vista
        return view('dashboard.distribucion', [
            'reporte' => $reporte,
            'acciones' => $acciones,
            'numAcciones' => count($acciones),
            'reporteSegundaTabla' => $reporteSegundaTabla,
        ]);
    }
    
    public function mostrarReporteOficio() {
    
        $acciones = DB::table('cat_siglas_tipo_accion')->pluck('valor')->toArray();
        // Generar el reporte para la primera tabla
        $reporte = collect($this->generarReporte()); // Convertir a colección
    
        // Pasar los datos de ambas tablas a la vista
        return view('dashboard.oficio-uaa', [
            'reporte' => $reporte,
            'acciones' => $acciones,
            'numAcciones' => count($acciones),
        ]);
    }

}
