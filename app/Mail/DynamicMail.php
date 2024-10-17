<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DynamicMail extends Mailable
{
    use Queueable, SerializesModels;

    public $content; // Contenido del correo (puede ser HTML o texto plano)
    public $data;    // Datos adicionales para la vista (opcional)

    /**
     * Create a new message instance.
     *
     * @param string $subject
     * @param string $content
     * @param array $data
     * @return void
     */
    public function __construct($subject, $content, $data = [])
    {
        $this->subject($subject);
        $this->content = $content;
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Utiliza una única plantilla de correo y pasa el contenido dinámico
        return $this->view('emails.dynamic')
                    ->with('content', $this->content)
                    ->with('data', $this->data);
    }
}
