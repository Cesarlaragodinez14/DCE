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
use Illuminate\Support\Facades\Storage;


class ExcelUploadController extends Controller
{
    public function showUploadForm()
    {
        return view('dashboard.upload-excel');
    }

    public function uploadExcel(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);
    
        $filePath = $request->file('archivo')->store('temp');
    
        ProcessExcelImport::dispatch($filePath);
    
        return redirect()->back()->with('success', 'El archivo Excel se está procesando, recibirás una notificación con el cambio de estado.');
    }
}
