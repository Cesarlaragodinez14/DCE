<?php

namespace App\Http\Controllers;

use App\Models\Apartado;
use App\Models\ChecklistApartado;
use App\Models\Auditorias;
use App\Models\ApartadoPlantilla; // Asegúrate de importar ApartadoPlantilla
use Illuminate\Http\Request;
use App\Models\Archivo; // Suponiendo que tienes un modelo Archivo
use App\Helpers\MailHelper;

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
     * Almacena la Firma de la UAA.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeUua(Request $request)
    {
        // Validación del servidor
        $validated = $request->validate([
            'auditoria_id' => 'required|exists:aditorias,id', // Corregido
            'uua_archivo' => 'required|file|mimes:pdf|max:2048',
        ]);

        try {
            $auditoria = Auditorias::findOrFail($validated['auditoria_id']);

            // Almacenar el archivo
            $path = $request->file('uua_archivo')->store('auditorias_uua', 'public');

            // Guardar la información en la base de datos
            $auditoria->archivo_uua = $path;
            $auditoria->estatus_firmas = 'En espera de revisión';
            $auditoria->save();

            // Enviar correo al jefe de departamento y al equipo de revisión
            $subject = 'Firma de la UAA Subida con Éxito';
            $content = "<p>Hola {$auditoria->jefe_de_departamento},</p>
                        <p>La UAA ha subido su firma para la auditoría con clave de acción: <strong>{$auditoria->clave_de_accion}</strong>.</p>
                        <p>El expediente está ahora en espera de revisión.</p>
                        <p>Gracias.</p>";
            
            $recipients = [
                'janarvaez@asf.gob.mx', // 
                'clara@asf.gob.mx', // 
                'ablozano@asf.gob.mx', // 
                // Agrega más correos si es necesario
            ];

            $data = [
                'footer' => 'Este es un correo automático, por favor no respondas.',
                'action' => [
                    'text' => 'Ver Auditoría',
                    'url' => route('auditorias.apartados', $auditoria->id)
                ]
            ];

            MailHelper::sendDynamicMail($recipients, $subject, $content, $data);

            return response()->json([
                'success' => true,
                'message' => 'Firma de la UAA cargada exitosamente y notificaciones enviadas.'
            ]);
        } catch (\Exception $e) {
            $recipient = env("ASF_SUPPORT_EMAIL");
            $subject = "Se ha detectado un error en el SAES";
            $content = "<p>Hola Administradores,</p>
                        <p>La UAA ha intentado subir su firma para la auditoría con clave de acción: <strong>{$auditoria->clave_de_accion}</strong>.</p>
                        <p>Pero ha ocurrido un error en el sistema.</p>
                        <p>{$e->getMessage()}</p>
                        <p>Recuerda que este correo es detonado en 'ApartadosControlle.php' para su pronta solució .</p>
                        <p>Gracias.</p>";
                        
            MailHelper::sendDynamicMail($recipients, $subject, $content, $data);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
