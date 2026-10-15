<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ArticuloRecibido extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public string $nombre, public string $titulo) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Recibimos tu artículo');
    }

    public function content(): Content
    {
        return new Content(view: 'correos.articulo_recibido');
    }
}
