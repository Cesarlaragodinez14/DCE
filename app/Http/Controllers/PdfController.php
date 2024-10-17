<?php

namespace App\Http\Controllers;

use App\Models\Apartado;
use App\Models\ChecklistApartado;
use App\Models\Auditorias;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
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
        $estatus_checklist = $auditoria->estatus_checklist;
        // Generate the PDF in landscape mode
        $pdf = PDF::loadView('pdf.checklist', compact('auditoria', 'apartados', 'checklist', 'estatus_checklist'))
            ->setPaper('a4', 'landscape');  // Set paper size to A4 and orientation to landscape

        // Stream or download the generated PDF
        return $pdf->download('checklist_auditoria_' . $auditoria->id . '.pdf');
    }
    /**
     * Descargar el archivo firmado por la UAA.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function downloadUua($id)
    {
        // Obtener la auditoría
        $auditoria = Auditorias::findOrFail($id);

        // Verificar si el usuario está autorizado a descargar el archivo
        // Puedes personalizar esta lógica según tus necesidades
        if (Auth::user()->id !== $auditoria->user_id && !Auth::user()->hasRole('admin')) {
            abort(403, 'No tienes permiso para acceder a este archivo.');
        }

        // Verificar si el archivo existe
        if (!$auditoria->archivo_uua || !Storage::disk('public')->exists($auditoria->archivo_uua)) {
            abort(404, 'Archivo no encontrado.');
        }

        // Descargar el archivo
        return Storage::disk('public')->download($auditoria->archivo_uua, 'Firma_UAA_' . $auditoria->id . '.pdf');
    }
}
