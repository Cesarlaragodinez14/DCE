<?php

namespace App\Http\Controllers;

use App\Models\Apartado;
use App\Models\ChecklistApartado;
use App\Models\Auditorias;
use Barryvdh\DomPDF\Facade\Pdf; // Use this for Laravel 11
use Illuminate\Http\Request;

class PdfController extends Controller
{
    /**
     * Generate a PDF for the checklist of a given Auditoria.
     *
     * @param int $auditoria_id
     * @return \Illuminate\Http\Response
     */
    public function generateChecklistPdf($auditoria_id)
    {
        // Get the auditoria and related data
        $auditoria = Auditorias::findOrFail($auditoria_id);
        $apartados = Apartado::whereNull('parent_id')->with('subapartados')->get();
        $checklist = ChecklistApartado::where('auditoria_id', $auditoria_id)->get()->keyBy('apartado_id');

        // Generate the PDF in landscape mode
        $pdf = PDF::loadView('pdf.checklist', compact('auditoria', 'apartados', 'checklist'))
            ->setPaper('a4', 'landscape');  // Set paper size to A4 and orientation to landscape

        // Stream or download the generated PDF
        return $pdf->download('checklist_auditoria_' . $auditoria->id . '.pdf');
    }
}
