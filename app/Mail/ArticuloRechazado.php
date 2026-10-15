<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ArticuloRechazado extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public string $nombre, public string $titulo, public string $motivo) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Dictamen de tu artículo');
    }

    public function content(): Content
    {
        return new Content(view: 'correos.articulo_rechazado');
    }
}
