<?php

namespace App\Http\Controllers;

use App\Models\Apartado;
use App\Models\ChecklistApartado;
use App\Models\Auditorias;
use App\Models\ApartadoPlantilla; // Asegúrate de importar ApartadoPlantilla
use Illuminate\Http\Request;
use App\Models\Archivo; // Suponiendo que tienes un modelo Archivo

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
            Auditorias::where('id', $auditoria_id)->update([
                'estatus_checklist' => $request->estatus_checklist,
                'auditor_nombre' => $request->auditor_nombre,
                'auditor_puesto' => $request->auditor_puesto,
                'seguimiento_nombre' => $request->seguimiento_nombre,
                'seguimiento_puesto' => $request->seguimiento_puesto,
                'comentarios' => $request->comentarios,
                'estatus_firmas' => $request->estatus_firmas,
            ]);
            ChecklistApartado::upsert($data, ['apartado_id', 'auditoria_id'], ['se_aplica', 'es_obligatorio', 'se_integra', 'observaciones', 'comentarios_uaa']);
        }

        return redirect()->back()->with('success', 'Checklist guardado exitosamente.');
    }

    /**
     * Almacena el archivo de Seguimiento con Firma.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeSeguimiento(Request $request)
    {
        // Validación del servidor
        $request->validate([
            'auditoria_id' => 'required|exists:auditorias,id',
            'seguimiento_archivo' => 'required|file|mimes:pdf,doc,docx,png,jpg,jpeg|max:2048',
        ]);

        $auditoria = Auditorias::findOrFail($request->auditoria_id);

        // Almacenar el archivo
        $path = $request->file('seguimiento_archivo')->store('auditorias_seguimiento', 'public');

        // Guardar la información en la base de datos
        $auditoria->archivo_seguimiento = $path;
        $auditoria->estatus_firmas = 'Paso 1 Completado';
        $auditoria->save();

        return response()->json([
            'success' => true,
            'message' => 'Seguimiento cargado exitosamente.'
        ]);
    }

    /**
     * Almacena el archivo de Firma de la UAA.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeUua(Request $request)
    {
        // Validación del servidor
        $request->validate([
            'auditoria_id' => 'required|exists:auditorias,id',
            'uua_archivo' => 'required|file|mimes:pdf,doc,docx,png,jpg,jpeg|max:2048',
        ]);

        $auditoria = Auditorias::findOrFail($request->auditoria_id);

        // Verificar que el Paso 1 ya esté completado
        if (is_null($auditoria->archivo_seguimiento)) {
            return response()->json([
                'success' => false,
                'message' => 'Debe completar el Paso 1 antes de subir la Firma de la UAA.'
            ]);
        }

        // Almacenar el archivo
        $path = $request->file('uua_archivo')->store('auditorias_uua', 'public');

        // Guardar la información en la base de datos
        $auditoria->archivo_uua = $path;
        $auditoria->estatus_firmas = 'En espera de revisión';
        $auditoria->save();

        return response()->json([
            'success' => true,
            'message' => 'Firma de la UAA cargada exitosamente.'
        ]);
    }
}
