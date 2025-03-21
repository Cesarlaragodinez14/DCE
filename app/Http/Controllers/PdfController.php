<?php

namespace App\Http\Controllers;

use App\Models\Apartado;
use App\Models\ChecklistApartado;
use App\Models\Auditorias;
use App\Models\User;
use App\Models\PdfHistory; // Importar el modelo PdfHistory
use App\Models\PdfHash; // Importar el modelo PdfHash
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Helpers\MailHelper;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Color\Color;

class PdfController extends Controller
{
   /**
      * Generate a PDF for the checklist of a given Auditoria.
     *
     * @param int $auditoria_id
     * @return \Illuminate\Http\Response
     */
    public static function generateChecklistPdf($auditoria_id, $regenerate = 0)
    {
        // Verificar si se solicita regeneración
        $regenerate_par = request()->query('regenerate') == 1;

        if($regenerate_par == 1){
            $regenerate = 1;
        }

        // Obtener la auditoría y los datos relacionados
        $auditoria = Auditorias::findOrFail($auditoria_id);
        $apartados = Apartado::whereNull('parent_id')->with('subapartados')->get();
        $checklist = ChecklistApartado::where('auditoria_id', $auditoria_id)->get()->keyBy('apartado_id');
        $estatus_checklist = $auditoria->estatus_checklist;
        $formato = explode('-', $auditoria->catClaveAccion->valor)[5];

        // Si no se solicita regeneración, verificar si ya existe un PDF generado en el historial
        if (!$regenerate) {
            $pdfHistory = PdfHistory::where('auditoria_id', $auditoria->id)
                                        ->orderBy('id', 'desc')
                                        ->first();
            if ($pdfHistory) {
                if (stripos($pdfHistory->pdf_path, 'Aceptado') !== false || stripos($pdfHistory->pdf_path, 'Devuelto') !== false) {
                    $pdfHash = PdfHash::where('auditoria_id', $auditoria->id)
                        ->orderBy('id', 'desc')
                        ->first();
                    if ($pdfHash) {
                        return redirect()->route('validador', ['hash' => $pdfHash->hash]);
                    }
                }
            }
        }

        // Si no se encontró un PDF con 'aceptado' en el 'pdf_path', continuar con la lógica existente

        if (!$regenerate) {
            $pdfHistory = PdfHistory::where('auditoria_id', $auditoria->id)
                ->where('generated_by', Auth::id())
                ->orderBy('id', 'desc')
                ->first();
            if ($pdfHistory) {
                $pdfHash = PdfHash::where('auditoria_id', $auditoria->id)
                    ->where('email', Auth::user()->email)
                    ->orderBy('id', 'desc')
                    ->first();
                if ($pdfHash) {
                    return redirect()->route('validador', ['hash' => $pdfHash->hash]);
                }
            }
        }

        // Obtener el usuario basado en seguimiento_nombre
        $usuarioSeguimiento = User::where('name', $auditoria->seguimiento_nombre)->first();
        $firmaPath = $usuarioSeguimiento && $usuarioSeguimiento->firma_autografa ? storage_path('app/public/' . $usuarioSeguimiento->firma_autografa) : null;

        // Obtener el usuario actual
        $user = Auth::user();
        $ipAddress = request()->ip();
        $generatedAt = now();
        $hashString = $user->email . '|' . $ipAddress . '|' . $generatedAt->toDateTimeString();
        $hash = hash('sha256', $hashString);

        PdfHash::create([
            'auditoria_id' => $auditoria->id,
            'hash' => $hash,
            'email' => $user->email,
            'ip_address' => $ipAddress,
            'generated_at' => $generatedAt,
        ]);

        $url = route('validador', ['hash' => $hash]);
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

        $writer = new PngWriter();
        $qrCodeImage = $writer->write($qrCode);
        $qrCodeDataUri = $qrCodeImage->getDataUri();

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
            'user'))
            ->setPaper('a4', 'landscape');

        $pdfContent = $pdf->output();
        $fileName = $auditoria->clave_de_accion . '-' . $estatus_checklist . '-' . now()->format('YmdHis') . '.pdf';
        $filePath = 'pdfs/' . $fileName;
        Storage::disk('public')->put($filePath, $pdfContent);

        PdfHistory::create([
            'auditoria_id'    => $auditoria->id,
            'clave_de_accion' => $auditoria->clave_de_accion,
            'pdf_path'        => $filePath,
            'generated_by'    => Auth::id(),
            'hash'            => $hash,
        ]);

        return response()->streamDownload(function () use ($pdfContent) {
            echo $pdfContent;
        }, $fileName);
    }

    public function validador($hash)
    {
        // Buscar el hash en la base de datos
        $hashRecord = PdfHash::where('hash', $hash)->first();
    
        if (!$hashRecord) {
            // Retornar una vista con mensaje de error
            return view('validador.hash_not_found');
        }
    
        // Obtener la información relacionada
        $auditoria = $hashRecord->auditoria;
        $userEmail = $hashRecord->email;
        $ipAddress = $hashRecord->ip_address;
        $generatedAt = $hashRecord->generated_at;
    
        // Retornar una vista con la información
        return view('validador.hash_info', compact('auditoria', 'userEmail', 'ipAddress', 'generatedAt', 'hash'));
    }
    
    public function downloadPdf($hash)
    {
        // Buscar el hash en la base de datos
        $hashRecord = PdfHash::where('hash', $hash)
                        ->orderBy('id', 'desc') // Asegurar que obtenga el último por ID
                        ->first();
    
        if (!$hashRecord) {
            // Redirigir con un mensaje de error si el hash no es válido
            return redirect()->back()->with('error', 'Hash no válido.');
        }
    
        // Obtener el último PDF generado asociado a la auditoría, ordenado por ID descendente
        $pdfHistory = PdfHistory::where('auditoria_id', $hashRecord->auditoria_id)
            ->orderBy('id', 'desc') // Asegurar que obtenga el último por ID
            ->first();
    
        if (!$pdfHistory) {
            // Redirigir con un mensaje de error si no se encuentra el PDF
            return redirect()->back()->with('error', 'PDF no encontrado.');
        }
    
        // Ruta completa al archivo PDF
        $pdfPath = storage_path('app/public/' . $pdfHistory->pdf_path);
    
        if (!file_exists($pdfPath)) {
            // Redirigir con un mensaje de error si el archivo no existe
            return redirect()->back()->with('error', 'El archivo PDF no existe.');
        }
    
        // Descargar el archivo PDF
        return response()->download($pdfPath, basename($pdfPath));
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
       /*if (Auth::user()->id !== $auditoria->user_id) {
            abort(403, 'No tienes permiso para acceder a este archivo.');
        }*/

        // Verificar si el archivo existe
        if (!$auditoria->archivo_uua || !Storage::disk('public')->exists($auditoria->archivo_uua)) {
            abort(404, 'Archivo no encontrado.');
        }

        // Descargar el archivo
        return Storage::disk('public')->download($auditoria->archivo_uua, 'Firma_UAA_' . $auditoria->clave_de_accion . '.pdf');
    }

    public function generateSignedChecklistPdf($auditoria_id)
    {
        // Obtener la auditoría y los datos relacionados
        $auditoria = Auditorias::findOrFail($auditoria_id);
        $apartados = Apartado::whereNull('parent_id')->with('subapartados')->get();
        $checklist = ChecklistApartado::where('auditoria_id', $auditoria_id)->get()->keyBy('apartado_id');
        $estatus_checklist = $auditoria->estatus_checklist;
        $formato = explode('-', $auditoria->catClaveAccion->valor)[5];

        $previousPdfHash = PdfHash::where('auditoria_id', $auditoria->id)
        ->orderBy('id', 'desc')
        ->first();

        if (!$previousPdfHash) {
            return redirect()->back()->with('error', 'No se encontró el hash de seguimiento previo.');
        }

        $trackingHash = $previousPdfHash->hash;
        $trackingUserEmail = $previousPdfHash->email;
        $trackingIpAddress = $previousPdfHash->ip_address;
        $trackingGeneratedAt = $previousPdfHash->generated_at;

        // Generar un nuevo hash para la conformidad de la UAA
        $user = Auth::user();
        $currentUserName = $user->name;
        $currentUserRole = $user->puesto; // Asumiendo que el usuario tiene un campo 'puesto'
        $ipAddress = request()->ip();
        $generatedAt = now();

        $hashString = $user->email . '|' . $ipAddress . '|' . $generatedAt->toDateTimeString();
        $uaaHash = hash('sha256', $hashString);

        // Almacenar el nuevo hash en la tabla PdfHash
        PdfHash::create([
            'auditoria_id' => $auditoria->id,
            'hash' => $uaaHash,
            'email' => $user->email,
            'ip_address' => $ipAddress,
            'generated_at' => $generatedAt,
        ]);

        // Generar los códigos QR para ambos hashes
        $urlTracking = route('validador', ['hash' => $trackingHash]);
        $urlUAA = route('validador', ['hash' => $uaaHash]);

        // Código QR para el hash de seguimiento
        $qrCodeTracking = new QrCode(
            data: $urlTracking,
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

        // Generar la imagen del código QR para el hash de seguimiento
        $qrCodeImageTracking = $writer->write($qrCodeTracking);

        // Obtener la URI de datos para incrustar la imagen en el PDF
        $qrCodeDataUriTracking = $qrCodeImageTracking->getDataUri();

        // Código QR para el hash de la UAA
        $qrCodeUAA = new QrCode(
            data: $urlUAA,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 150,
            margin: 0,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255)
        );

        // Generar la imagen del código QR para el hash de la UAA
        $qrCodeImageUAA = $writer->write($qrCodeUAA);

        // Obtener la URI de datos para incrustar la imagen en el PDF
        $qrCodeDataUriUAA = $qrCodeImageUAA->getDataUri();

        // Generar el PDF utilizando la vista 'pdf.checklist-signed'
        $pdf = PDF::loadView('pdf.checklist-signed', compact(
            'auditoria',
            'apartados',
            'checklist',
            'estatus_checklist',
            'formato',
            'trackingHash',
            'trackingUserEmail',
            'trackingIpAddress',
            'trackingGeneratedAt',
            'qrCodeDataUriTracking',
            'uaaHash',
            'qrCodeDataUriUAA',
            'user',
            'currentUserName',
            'currentUserRole',
            'ipAddress',
            'generatedAt'
        ))->setPaper('a4', 'landscape');

        // Obtener el contenido del PDF generado
        $pdfContent = $pdf->output();

        // Definir el nombre del archivo con la clave de acción y fecha
        $fileName = $auditoria->clave_de_accion . '-Conformidad-UAA-' . now()->format('YmdHis') . '.pdf';

        // Almacenar el PDF en el disco público (storage/app/public/pdfs-acciones-completadas)
        $filePath = 'pdfs-acciones-completadas/' . $fileName;
        Storage::disk('public')->put($filePath, $pdfContent);

        // Registrar el histórico en la tabla 'pdf_histories'
        PdfHistory::create([
            'auditoria_id'    => $auditoria->id,
            'clave_de_accion' => $auditoria->clave_de_accion,
            'pdf_path'        => $filePath,
            'generated_by'    => $user->id,
            'hash'            => $uaaHash,
        ]);

        // Actualizar el campo 'archivo_uua' en la auditoría
        $auditoria->archivo_uua = $filePath;
        $auditoria->estatus_firmas = 'En espera de revisión';
        $auditoria->save();

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

        $subject = 'Proceso de conformidad UAA finalizado - ' . $auditoria->clave_de_accion;
        $content = "<p>Se ha generado exitosamente el PDF de conformidad para la auditoría con clave de acción <strong>{$auditoria->clave_de_accion}</strong>.</p>
                    <p>El archivo se encuentra disponible para revisión en la plataforma.</p>
                    <p>Gracias.</p>";

        $mailData = [
            'footer' => 'Este es un correo automático, por favor no respondas.',
            'action' => [
                'text' => 'Ver Auditoría',
                'url' => route('auditorias.apartados', $auditoria->id)
            ]
        ];

        MailHelper::sendDynamicMail($recipients, $subject, $content, $mailData);

        // Devolver el PDF generado como una descarga
        return response()->streamDownload(function () use ($pdfContent) {
            echo $pdfContent;
        }, $fileName);
    }

}
