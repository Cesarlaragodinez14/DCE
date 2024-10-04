<?php

namespace App\Http\Controllers;

use App\Models\Apartado;
use App\Models\ChecklistApartado;
use App\Models\Auditorias;
use Illuminate\Http\Request;

class ApartadosController extends Controller
{
    // Mostrar la vista principal con los apartados y subapartados
    public function index($auditoria_id)
    {
        // Obtener la auditoría
        $auditoria = Auditorias::findOrFail($auditoria_id);

        // Obtener los apartados principales y subapartados en una sola consulta con carga ansiosa
        $apartados = Apartado::whereNull('parent_id')->with('subapartados')->get();

        // Obtener el checklist de apartados para esta auditoría y usar `keyBy` para acceso más rápido por ID
        $checklist = ChecklistApartado::where('auditoria_id', $auditoria_id)->get()->keyBy('apartado_id');

        return view('apartados.index', compact('auditoria', 'apartados', 'checklist'));
    }

    // Guardar el estado del checklist de apartados para una auditoría específica
    public function storeChecklist(Request $request)
    {
        $request->validate([
            'auditoria_id' => 'required|exists:aditorias,id',
            'apartados' => 'array',
            'apartados.*.id' => 'exists:apartados,id',
            'apartados.*.se_aplica' => 'nullable|boolean',
            'apartados.*.es_obligatorio' => 'nullable|boolean',
            'apartados.*.se_integra' => 'nullable|boolean',
            'apartados.*.observaciones' => 'nullable|string',
            'apartados.*.comentarios_uaa' => 'nullable|string',
        ]);

        $auditoria_id = $request->input('auditoria_id');
        $data = [];

        // Preparar los datos para una inserción/actualización masiva
        foreach ($request->input('apartados') as $apartadoData) {
            if (isset($apartadoData['id'])) {
                $data[] = [
                    'apartado_id' => $apartadoData['id'],
                    'auditoria_id' => $auditoria_id,
                    'se_aplica' => $apartadoData['se_aplica'] ?? false,
                    'es_obligatorio' => $apartadoData['es_obligatorio'] ?? false,
                    'se_integra' => $apartadoData['se_integra'] ?? false,
                    'observaciones' => $apartadoData['observaciones'] ?? null,
                    'comentarios_uaa' => $apartadoData['comentarios_uaa'] ?? null,
                    'updated_at' => now(), // Para evitar conflicto con upsert
                    'created_at' => now(), // También necesario en caso de insert
                ];
            }
        }

        // Realizar la actualización/inserción en bloque
        if (!empty($data)) {
            ChecklistApartado::upsert($data, ['apartado_id', 'auditoria_id'], ['se_aplica', 'es_obligatorio', 'se_integra', 'observaciones', 'comentarios_uaa']);
        }

        return redirect()->back()->with('success', 'Checklist guardado exitosamente.');
    }
}
