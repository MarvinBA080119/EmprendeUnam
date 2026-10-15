<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class RespuestaConsultorio extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $nombre,
        public string $asunto,
        public string $pregunta,
        public string $respuesta,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: Str::limit('Re: '.$this->asunto, 78));
    }

    public function content(): Content
    {
        return new Content(view: 'correos.respuesta_consultorio');
    }
}
