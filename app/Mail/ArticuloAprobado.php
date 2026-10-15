<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ArticuloAprobado extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public string $nombre, public string $titulo, public string $folio, public string $enlace) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Tu artículo fue aprobado');
    }

    public function content(): Content
    {
        return new Content(view: 'correos.articulo_aprobado');
    }
}
