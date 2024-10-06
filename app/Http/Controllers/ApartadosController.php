<?php

namespace App\Http\Controllers;

use App\Models\Apartado;
use App\Models\ChecklistApartado;
use App\Models\Auditorias;
use App\Models\ApartadoPlantilla; // Asegúrate de importar ApartadoPlantilla
use Illuminate\Http\Request;

class ApartadosController extends Controller
{
    public function index($auditoria_id)
    {
        // Obtener la auditoría
        $auditoria = Auditorias::findOrFail($auditoria_id);

        // Obtener los apartados principales y subapartados
        $apartados = Apartado::whereNull('parent_id')->with('subapartados')->get();

        // Obtener el checklist de apartados para esta auditoría
        $checklist = ChecklistApartado::where('auditoria_id', $auditoria_id)->get()->keyBy('apartado_id');

        // Obtener el formato de la auditoría
        $formato = explode('-', $auditoria->catClaveAccion->valor)[5];

        // Obtener los valores predefinidos de la plantilla para el formato específico
        $plantillaDatos = ApartadoPlantilla::where('plantilla', $formato)->get()->keyBy('apartado_id');

        // Pasar los datos a la vista
        return view('apartados.index', compact('auditoria', 'apartados', 'checklist', 'plantillaDatos'));
    }

    // Guardar el checklist actualizado
    public function storeChecklist(Request $request)
    {
        $request->validate([
            'auditoria_id' => 'required|exists:aditorias,id',
            'apartados' => 'array',
            'apartados.*.id' => 'exists:apartados,id',
            'apartados.*.es_aplicable' => 'nullable|boolean',
            'apartados.*.es_obligatorio' => 'nullable|boolean',
            'apartados.*.se_integra' => 'nullable|boolean',
            'apartados.*.observaciones' => 'nullable|string',
            'apartados.*.comentarios_uaa' => 'nullable|string',
        ]);

        $auditoria_id = $request->input('auditoria_id');
        $data = [];

        foreach ($request->input('apartados') as $apartadoData) {
            if (isset($apartadoData['id'])) {
                $data[] = [
                    'apartado_id' => $apartadoData['id'],
                    'auditoria_id' => $auditoria_id,
                    'se_aplica' => $apartadoData['es_aplicable'] ?? false,
                    'es_obligatorio' => $apartadoData['es_obligatorio'] ?? false,
                    'se_integra' => $apartadoData['se_integra'] ?? false,
                    'observaciones' => $apartadoData['observaciones'] ?? null,
                    'comentarios_uaa' => $apartadoData['comentarios_uaa'] ?? null,
                    'updated_at' => now(),
                    'created_at' => now(),
                ];
            }
        }

        if (!empty($data)) {
            ChecklistApartado::upsert($data, ['apartado_id', 'auditoria_id'], ['se_aplica', 'es_obligatorio', 'se_integra', 'observaciones', 'comentarios_uaa']);
        }

        return redirect()->back()->with('success', 'Checklist guardado exitosamente.');
    }
}
