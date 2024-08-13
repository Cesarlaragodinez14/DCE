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


}
