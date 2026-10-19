<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class AvisoNuevaEdicion extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $nombre,
        public string $titulo,
        public string $enlace,
        public string $enlaceBaja,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Ya está disponible: '.$this->titulo);
    }

    public function headers(): Headers
    {
        return new Headers(text: ['List-Unsubscribe' => '<'.$this->enlaceBaja.'>']);
    }

    public function content(): Content
    {
        return new Content(view: 'correos.aviso_nueva_edicion');
    }
}
