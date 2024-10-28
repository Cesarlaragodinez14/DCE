<?php

namespace App\Http\Controllers;

use App\Models\Apartado;
use App\Models\ChecklistApartado;
use App\Models\Auditorias;
use App\Models\ApartadoPlantilla;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Archivo;
use App\Helpers\MailHelper;
use Illuminate\Support\Facades\Auth;

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

    public function storeChecklist(Request $request)
    {
        // Validación de los datos de entrada
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
        $apartadosData = collect($request->input('apartados'))->filter(function($apartado) {
            return isset($apartado['id']);
        })->map(function($apartado) use ($auditoria_id) {
            return [
                'apartado_id' => $apartado['id'],
                'auditoria_id' => $auditoria_id,
                'se_aplica' => $apartado['es_aplicable'] ?? false,
                'es_obligatorio' => $apartado['es_obligatorio'] ?? false,
                'se_integra' => $apartado['se_integra'] ?? false,
                'observaciones' => $apartado['observaciones'] ?? null,
                'comentarios_uaa' => $apartado['comentarios_uaa'] ?? null,
                'updated_at' => now(),
                'created_at' => now(),
            ];
        })->toArray();

        $auditoria = Auditorias::findOrFail($auditoria_id);

        if (!empty($apartadosData)) {
            // Actualización de la auditoría
            $auditoria->update([
                'estatus_checklist' => $request->estatus_checklist,
                'auditor_nombre' => $request->auditor_nombre,
                'auditor_puesto' => $request->auditor_puesto,
                'seguimiento_nombre' => $request->seguimiento_nombre,
                'seguimiento_puesto' => $request->seguimiento_puesto,
                'comentarios' => $request->comentarios,
                'estatus_firmas' => $request->estatus_firmas,
            ]);

            // Actualización o inserción de los apartados del checklist
            ChecklistApartado::upsert(
                $apartadosData, 
                ['apartado_id', 'auditoria_id'], 
                ['se_aplica', 'es_obligatorio', 'se_integra', 'observaciones', 'comentarios_uaa']
            );

            // Obtener el usuario actual y su rol
            $currentUser = Auth::user();
            $currentUserName = $currentUser->name;
            $currentUserRole = $currentUser->getRoleNames()->first(); // Asumiendo un solo rol

            // Buscar el usuario auditor
            $auditorUser = User::where('name', $auditoria->auditor_nombre)->first();
            if (!$auditorUser) {
                return redirect()->back()->with('error', 'El usuario auditor "' . $auditoria->auditor_nombre . '" no se ha encontrado. Verifica que el nombre esté escrito correctamente o que el usuario exista en el sistema.');
            }

            // Buscar el usuario de seguimiento
            $seguimientoUser = User::where('name', $auditoria->seguimiento_nombre)->first();
            if (!$seguimientoUser) {
                return redirect()->back()->with('error', 'El usuario de seguimiento "' . $auditoria->seguimiento_nombre . '" no se ha encontrado. Verifica que el nombre esté escrito correctamente o que el usuario exista en el sistema.');
            }

            // Definir los destinatarios del correo
            $recipients = collect([
                $currentUser->email,      // Correo del usuario actual
                $auditorUser->email,      // Correo del auditor
                $seguimientoUser->email,  // Correo de seguimiento
            ])->unique()->values()->toArray();

            // Verificar el estatus del checklist para determinar el contenido del correo
            if (in_array($request->estatus_checklist, ["Devuelto", "Aceptado"])) {
                if ($request->estatus_checklist == "Aceptado") {
                    $subject = 'Clave de acción aprobada por Seguimiento';
                    $content = "<p>El usuario {$currentUserName} ({$currentUserRole}), ha aprobado la revisión de expediente para auditoría con clave de acción: <strong>{$auditoria->clave_de_accion}</strong>.</p>
                                <p>El expediente está ahora en espera de firma de la UAA, sube el archivo firmado lo antes posible, tienes un máximo de 7 días hábiles.</p>
                                <p>Gracias.</p>";
                } else { // Devuelto
                    $subject = 'Clave de acción actualizada - ' . $auditoria->clave_de_accion . ' | Estatus: ' . $auditoria->estatus_checklist;
                    $content = "<p>El usuario: {$currentUserName} ({$currentUserRole}), Ha realizado una actualización en la clave de acción: <strong>{$auditoria->clave_de_accion}</strong>.</p>
                                <p>El expediente está en estatus <strong>{$auditoria->estatus_checklist}</strong>.</p>
                                <p>Gracias.</p>";
                }

                $mailData = [
                    'footer' => 'Este es un correo automático, por favor no respondas.',
                    'action' => [
                        'text' => 'Ver Auditoría',
                        'url' => route('auditorias.apartados', $auditoria->id)
                    ]
                ];

                // Enviar el correo
                MailHelper::sendDynamicMail($recipients, $subject, $content, $mailData);

                return redirect()->back()->with('success', 'Revisión de expediente guardada exitosamente, se ha notificado a la UAA.');
            }

            return redirect()->back()->with('success', 'Revisión de expediente guardada exitosamente.');
        }

        return redirect()->back()->with('error', 'No se encontraron datos para actualizar.');
    }
 
    /**
     * Almacena la Firma de la UAA.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeUua(Request $request)
    {
        // Validación de los datos de entrada
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

            // Obtener el usuario actual y su rol
            $currentUser = Auth::user();
            $currentUserName = $currentUser->name;
            $currentUserRole = $currentUser->getRoleNames()->first(); // Asumiendo un solo rol

            // Buscar el usuario auditor
            $auditorUser = User::where('name', $auditoria->auditor_nombre)->first();
            if (!$auditorUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario auditor "' . $auditoria->auditor_nombre . '" no se ha encontrado. Verifica que el nombre esté escrito correctamente o que el usuario exista en el sistema.'
                ], 404);
            }

            // Buscar el usuario de seguimiento
            $seguimientoUser = User::where('name', $auditoria->seguimiento_nombre)->first();
            if (!$seguimientoUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario de seguimiento "' . $auditoria->seguimiento_nombre . '" no se ha encontrado. Verifica que el nombre esté escrito correctamente o que el usuario exista en el sistema.'
                ], 404);
            }

            // Definir los destinatarios del correo sin duplicados
            $recipients = collect([
                $currentUser->email,      // Correo del usuario actual
                $auditorUser->email,      // Correo del auditor
                $seguimientoUser->email,  // Correo de seguimiento
                // Agrega más correos si es necesario
            ])->unique()->values()->toArray();

            // Contenido del correo
            $subject = 'Firma de la UAA Subida con Éxito';
            $content = "
                <p>El usuario {$currentUserName} ({$currentUserRole}),</p>
                <p>ha subido su firma para la auditoría con clave de acción: <strong>{$auditoria->clave_de_accion}</strong>.</p>
                <p>El expediente está ahora en espera de revisión.</p>
                <p>Gracias.</p>
            ";

            $mailData = [
                'footer' => 'Este es un correo automático, por favor no respondas.',
                'action' => [
                    'text' => 'Ver Auditoría',
                    'url' => route('auditorias.apartados', $auditoria->id)
                ]
            ];

            // Enviar el correo
            MailHelper::sendDynamicMail($recipients, $subject, $content, $mailData);

            return response()->json([
                'success' => true,
                'message' => 'Firma de la UAA cargada exitosamente y notificaciones enviadas.'
            ]);

        } catch (\Exception $e) {
            // Obtener la lista de destinatarios para el correo de error
            $supportEmail = env("ASF_SUPPORT_EMAIL");
            $errorRecipients = [
                $supportEmail,
                // Puedes agregar más correos de soporte si es necesario
            ];

            // Construir el contenido del correo de error
            $errorSubject = "Se ha detectado un error en el SAES";
            $errorContent = "
                <p>Hola Administradores,</p>
                <p>La UAA ha intentado subir su firma para la auditoría con clave de acción: <strong>" . ($auditoria->clave_de_accion ?? 'Desconocida') . "</strong>.</p>
                <p>Pero ha ocurrido un error en el sistema.</p>
                <p><strong>Error:</strong> {$e->getMessage()}</p>
                <p>Recuerda que este correo es detonado en 'AuditoriasController.php' para su pronta solución.</p>
                <p>Gracias.</p>
            ";

            $errorMailData = [
                'footer' => 'Este es un correo automático, por favor no respondas.',
                // No se incluye 'action' ya que es un correo de error
            ];

            // Enviar el correo de error
            MailHelper::sendDynamicMail($errorRecipients, $errorSubject, $errorContent, $errorMailData);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al procesar la solicitud. Se ha notificado a los administradores.'
            ], 500);
        }
    }
}
