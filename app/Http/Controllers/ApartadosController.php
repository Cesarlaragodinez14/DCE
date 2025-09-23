<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Archivo;
use App\Models\Apartado;
use App\Models\Auditorias;
use App\Models\ChecklistApartado;
use App\Models\ApartadoPlantilla;
use App\Models\AuditoriasHistory;
use App\Models\ChecklistApartadoHistory;
use App\Http\Controllers\PdfController;
use App\Helpers\MailHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Color\Color;

use App\Models\PdfHistory;
use App\Models\PdfHash;
use setasign\Fpdi\Fpdi;

class ApartadosController extends Controller
{
    public function index($auditoria_id)
    {
        // Obtener la auditoría
        $auditoria = Auditorias::with('catSiglasTipoAccion')->findOrFail($auditoria_id);

        // Obtener el formato de la auditoría
        $formato = explode('-', $auditoria->catClaveAccion->valor)[5];

        // NUEVA LÓGICA: Detectar si es superveniente
        $esSuperveniente = $auditoria->es_superveniente == 1;
        
        // Usar plantilla superveniente si aplica
        $plantillaFormato = ($formato === '06' && $esSuperveniente) ? '06-superveniente' : $formato;

        // Obtener los valores predefinidos de la plantilla para el formato específico
        $plantillaDatos = ApartadoPlantilla::where('plantilla', $plantillaFormato)->get()->keyBy('apartado_id');

        // NUEVA LÓGICA: Ordenar apartados correctamente para supervenientes
        if ($formato === '06' && $esSuperveniente) {
            // Para supervenientes, obtener apartados en orden específico
            $apartados = Apartado::whereNull('parent_id')
                ->whereIn('id', $plantillaDatos->keys())
                ->with('subapartados')
                ->get()
                ->sortBy(function($apartado) {
                    // Apartados supervenientes (67-73) van después de los normales pero antes de 57 y 60
                    if ($apartado->id >= 67 && $apartado->id <= 73) {
                        return $apartado->id + 100; // Después de normales pero antes de 57,60
                    } elseif ($apartado->id == 57 || $apartado->id == 60) {
                        return $apartado->id + 200; // Al final
                    }
                    return $apartado->id;
                });
        } else {
            // Para formatos normales, usar orden estándar
            $apartados = Apartado::whereNull('parent_id')->with('subapartados')->get();
        }

        // Obtener el checklist de apartados para esta auditoría
        $checklist = ChecklistApartado::where('auditoria_id', $auditoria_id)->get()->keyBy('apartado_id');

        // Pasar los datos a la vista
        return view('apartados.index', compact('auditoria', 'apartados', 'checklist', 'plantillaDatos', 'formato', 'esSuperveniente'));
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
            'seguimiento_nombre' => 'required|string|max:255', // Agregado
            'seguimiento_puesto' => 'required|string|max:255', // Agregado si es necesario
        ], [
            'seguimiento_nombre.required' => 'Para guardar toda la información, debe ingresar el nombre del personal de la UAA que entrega el expediente.',
            'seguimiento_puesto.required' => 'Debe ingresar el puesto del personal de la UAA que entrega el expediente.',
            // Puedes agregar más mensajes personalizados si lo deseas
        ]);


        // Iniciar una transacción para asegurar la integridad de los datos
        DB::beginTransaction();

        try {
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

            // Registrar cambios en AuditoriasHistory antes de actualizar
            $originalAuditoria = $auditoria->replicate(); // Clonar el objeto original

            if(empty($request->auditor_puesto)){
                return redirect()->back()->with('error', 'El puesto del auditor esta vacio.');
            }
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

            // Registrar cambios en AuditoriasHistory
            $changesAuditoria = $auditoria->getChanges(); // Obtener solo los campos que cambiaron
            if (!empty($changesAuditoria)) {
                AuditoriasHistory::create([
                    'auditoria_id' => $auditoria->id,
                    'changed_by' => Auth::id(),
                    'changes' => json_encode([
                        'before' => $originalAuditoria->only(array_keys($changesAuditoria)),
                        'after' => $auditoria->only(array_keys($changesAuditoria)),
                    ]),
                ]);
            }

            if (!empty($apartadosData)) {
                // Actualización o inserción de los apartados del checklist
                ChecklistApartado::upsert(
                    $apartadosData, 
                    ['apartado_id', 'auditoria_id'], 
                    ['se_aplica', 'es_obligatorio', 'se_integra', 'observaciones', 'comentarios_uaa']
                );

                // Registrar cambios en ChecklistApartadoHistory
                foreach ($apartadosData as $apartadoData) {
                    // Buscar el registro existente o crear uno nuevo para obtener el objeto
                    $checklistApartado = ChecklistApartado::where('apartado_id', $apartadoData['apartado_id'])
                                                        ->where('auditoria_id', $auditoria_id)
                                                        ->first();

                    if ($checklistApartado) {
                        $originalChecklist = $checklistApartado->replicate(); // Clonar el objeto original

                        // Actualizar los campos (esto ya se hace con upsert)
                        $checklistApartado->update([
                            'se_aplica' => $apartadoData['se_aplica'],
                            'es_obligatorio' => $apartadoData['es_obligatorio'],
                            'se_integra' => $apartadoData['se_integra'],
                            'observaciones' => $apartadoData['observaciones'],
                            'comentarios_uaa' => $apartadoData['comentarios_uaa'],
                        ]);

                        // Registrar cambios en ChecklistApartadoHistory
                        $changesChecklist = $checklistApartado->getChanges();
                        if (!empty($changesChecklist)) {
                            ChecklistApartadoHistory::create([
                                'checklist_apartado_id' => $checklistApartado->id,
                                'changed_by' => Auth::id(),
                                'changes' => json_encode([
                                    'before' => $originalChecklist->only(array_keys($changesChecklist)),
                                    'after' => $checklistApartado->only(array_keys($changesChecklist)),
                                ]),
                            ]);
                        }
                    }
                }
            }


            // Obtener el usuario actual y su rol
            $currentUser = Auth::user();
            $currentUserName = $currentUser->name;
            $currentUserRole = $currentUser->puesto; // Asumiendo que 'puesto' es el rol

            // Buscar el usuario auditor
            $auditorUser = User::where('name', $auditoria->auditor_nombre)->first();
            if (!$auditorUser) {
                DB::rollBack();
                return redirect()->back()->with('error', 'El usuario auditor "' . $auditoria->auditor_nombre . '" no se ha encontrado. Verifica que el nombre esté escrito correctamente o que el usuario exista en el sistema.');
            }

            // Buscar el usuario de seguimiento
            $seguimientoUser = User::where('name', $auditoria->seguimiento_nombre)->first();
            if (!$seguimientoUser) {
                DB::rollBack();
                return redirect()->back()->with('error', 'El usuario de seguimiento "' . $auditoria->seguimiento_nombre . '" no se ha encontrado. Verifica que el nombre esté escrito correctamente o que el usuario exista en el sistema.');
            }

            // Definir los destinatarios del correo
            $recipients = collect([
                $currentUser->email,      // Correo del usuario actual
                $auditorUser->email,      // Correo del auditor
                $seguimientoUser->email,  // Correo de seguimiento
                "archivoseg@asf.gob.mx" // Correo para archivo de seguimiento 
            ])->unique()->values()->toArray();

            // Verificar el estatus del checklist para determinar el contenido del correo
            if (in_array($request->estatus_checklist, ["Devuelto", "Aceptado"])) {
                PdfController::generateChecklistPdf($auditoria_id, 1);
                if ($request->estatus_checklist == "Aceptado") {
                    $subject = 'La clave de acción ' . $auditoria->clave_de_accion . ' fue aprobada por Seguimiento';
                    $content = "<p>El usuario {$currentUserName} ({$currentUserRole}), ha aprobado la revisión de expediente para auditoría con clave de acción: <strong>{$auditoria->clave_de_accion}</strong>.</p>
                                <p>El expediente está ahora en espera de firma de la UAA, ingresa a la plataforma y firma digitalmente lo antes posible.</p>
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

                DB::commit();
                return redirect()->back()->with('success', 'Revisión de expediente guardada exitosamente, se ha notificado a la UAA.');
            }

            DB::commit();
            return redirect()->back()->with('success', 'Revisión de expediente guardada exitosamente.');

        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            DB::rollBack();

            // Registrar el error en los logs para su posterior revisión
            \Log::error('Error al guardar el checklist: ' . $e->getMessage());

            // Retornar una respuesta de error al usuario
            return redirect()->back()->with('error', 'Ocurrió un error al guardar la revisión del expediente: ' . $e->getMessage());
        }
    }
 
    /**
     * Confirma la Conformidad de la UAA y genera el PDF con el segundo hash.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeUua(Request $request)
    {
        // Obtener la auditoría y los datos relacionados
        $auditoria = Auditorias::findOrFail($request->auditoria_id);
        $apartados = Apartado::whereNull('parent_id')->with('subapartados')->get();
        $checklist = ChecklistApartado::where('auditoria_id', $request->auditoria_id)->get()->keyBy('apartado_id');
        $estatus_checklist = $auditoria->estatus_checklist;
        $formato = explode('-', $auditoria->catClaveAccion->valor)[5];

        // NUEVA LÓGICA: Detectar si es superveniente
        $esSuperveniente = $auditoria->es_superveniente == 1;
        
        // Usar plantilla superveniente si aplica
        $plantillaFormato = ($formato === '06' && $esSuperveniente) ? '06-superveniente' : $formato;

        // Verificar si ya existe un PDF generado en el historial
        $pdfHistory = PdfHistory::where('auditoria_id', $auditoria->id)->first();

        if ($pdfHistory) {
            // Si ya existe el PDF, buscar el hash correspondiente en PdfHash
            $pdfHash = PdfHash::where('auditoria_id', $auditoria->id)->first();
        }

        // Obtener el usuario basado en seguimiento_nombre
        $usuarioSeguimiento = User::where('name', $auditoria->seguimiento_nombre)->first();

        // Inicializar firmaPath como null
        $firmaPath = null;

        // Verificar si se encontró el usuario y si tiene una firma
        if ($usuarioSeguimiento && $usuarioSeguimiento->firma_autografa) {
            $firmaPath = storage_path('app/public/' . $usuarioSeguimiento->firma_autografa);
        }

        // Obtener el usuario actual
        $user = Auth::user();

        // Obtener la dirección IP actual
        $ipAddress = request()->ip();

        // Obtener la fecha y hora actual del servidor
        $generatedAt = now();

        // Generar un hash único (puedes utilizar un UUID o SHA256)
        $hashString = $user->email . '|' . $ipAddress . '|' . $generatedAt->toDateTimeString();
        $hash = hash('sha256', $hashString);

        // Almacenar el hash y la información en la base de datos (modelo PdfHash)
        PdfHash::create([
            'auditoria_id' => $auditoria->id,
            'hash' => $hash,
            'email' => $user->email,
            'ip_address' => $ipAddress,
            'generated_at' => $generatedAt,
        ]);

        // Generar la URL para el QR
        $url = route('validador', ['hash' => $hash]);

        // Generar el código QR usando endroid/qr-code
        $qrCode = new QrCode(
            data: $url,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 150,
            margin: 0,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255)
        );

        // Crear el escritor PNG
        $writer = new PngWriter();

        // Generar la imagen del código QR
        $qrCodeImage = $writer->write($qrCode);

        // Obtener la URI de datos para incrustar la imagen en el PDF
        $qrCodeDataUri = $qrCodeImage->getDataUri();

        // Pasar el código QR y la información del hash a la vista
        $pdf = PDF::loadView('pdf.checklist', compact(
                                'auditoria',
                                'apartados',
                                'checklist',
                                'estatus_checklist',
                                'firmaPath',
                                'formato',
                                'qrCodeDataUri',
                                'hash',
                                'ipAddress',
                                'generatedAt',
                                'user')) // Si necesitas el usuario
                            ->setPaper('a4', 'landscape');

        // Obtener el contenido del PDF generado
        $pdfContent = $pdf->output();

        // Definir el nombre del archivo con la clave de acción y fecha
        $fileName = $auditoria->clave_de_accion . '-' . $estatus_checklist . '-' . now()->format('YmdHis') . '.pdf';

        // Almacenar el PDF en el disco público (storage/app/public/pdfs)
        $filePath = 'pdfs/' . $fileName;
        Storage::disk('public')->put($filePath, $pdfContent);

        // Registrar el histórico en la tabla 'pdf_histories'
        PdfHistory::create([
            'auditoria_id'    => $auditoria->id,
            'clave_de_accion' => $auditoria->clave_de_accion,
            'pdf_path'        => $filePath,
            'generated_by'    => Auth::id(),
            'hash'            => $hash, // Guardamos el hash también en el historial
        ]);

        // Descargar el PDF generado con un nombre descriptivo
        return response()->streamDownload(function () use ($pdfContent) {
            echo $pdfContent;
        }, $fileName);
    }


    public function show()
    {
        return view('apartados.show');
    }
}
