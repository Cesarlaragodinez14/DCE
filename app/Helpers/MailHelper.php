<?php

namespace App\Helpers;

use App\Mail\DynamicMail;
use Illuminate\Support\Facades\Mail;

class MailHelper
{
    /**
     * Envía un correo electrónico dinámico.
     *
     * @param array $recipients      Lista de correos electrónicos.
     * @param string $subject        Asunto del correo.
     * @param string $content        Contenido del correo (HTML).
     * @param array $data            Datos adicionales para la vista (opcional).
     * @return void
     */
    public static function sendDynamicMail(array $recipients, string $subject, string $content, array $data = [])
    {
        // Crea una instancia del Mailable
        $mail = new DynamicMail($subject, $content, $data);

        // Envía el correo a cada destinatario
        Mail::to($recipients)->send($mail);
    }
}
