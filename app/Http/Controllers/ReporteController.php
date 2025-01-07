<?php

namespace App\Http\Controllers;

use App\Exports\ReporteAuditoriasExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    public function exportarReporte()
    {
        return Excel::download(new ReporteAuditoriasExport, 'reporte_auditorias.xlsx');
    }
}
