<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class SignatureController extends Controller
{
    public function show($filename)
    {
        $user = Auth::user();
        $signaturePath = 'firmas/' . $filename;

        // Verificar si el archivo existe
        if (!Storage::disk('public')->exists($signaturePath)) {
            abort(404);
        }

        // Verificar que el usuario tenga permiso para ver la firma
        if ($user->firma_autografa !== $signaturePath) {
            abort(403);
        }

        // Obtener el archivo y su tipo MIME
        $file = Storage::disk('public')->get($signaturePath);
        $type = Storage::disk('public')->mimeType($signaturePath);

        // Devolver la respuesta con el archivo
        return response($file, 200)->header('Content-Type', $type);
    }
}
