<?php

// app/Http/Controllers/AuditoriaController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Auditorias;
use App\Models\PdfHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuditoriaController extends Controller
{

    /**
     * Resetea la clave de acción de una auditoría.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $auditoriaId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request, $auditoriaId)
    {
        // Verificar si el usuario tiene permiso para resetear
        $auditoria = Auditorias::findOrFail($auditoriaId);

        // Validar el formulario
        $validated = $request->validate([
            'confirmation_text' => ['required', 'string', 'in:Deseo reiniciar esta clave de acción'],
        ]);

        try {
            // Iniciar una transacción
            \DB::beginTransaction();

            // Resetear los campos en Auditoria
            $auditoria->archivo_uua = null;
            $auditoria->estatus_firmas = null;
            $auditoria->estatus_checklist = "En Proceso";
            $auditoria->save();

            // Borrar registros relacionados en PdfHistory
            PdfHistory::where('auditoria_id', $auditoriaId)->delete();

            // Confirmar la transacción
            \DB::commit();

            // Obtener información del usuario y la solicitud
            $user = Auth::user();
            $ipAddress = $request->ip();
            $fechaHora = now()->toDateTimeString();
            $claveAccion = $auditoria->clave_de_accion;

            // Escribir en el log personalizado
            Log::channel('reset_auditoria')->info('Reseteo de clave de acción', [
                'usuario_id' => $user->id,
                'correo_electronico' => $user->email,
                'ip' => $ipAddress,
                'fecha_hora' => $fechaHora,
                'clave_de_accion' => $claveAccion,
            ]);

            return redirect()->back()->with('success', 'La clave de acción ha sido reseteada exitosamente.');
        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            \DB::rollBack();

            // Log del error para depuración
            \Log::error('Error al resetear la clave de acción: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Ocurrió un error al resetejar la clave de acción: '.$e->getMessage());
        }
    }
}
