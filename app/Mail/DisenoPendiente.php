<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

/** A la cuenta de diseño: se aprobó un artículo y está listo para diseñar. */
class DisenoPendiente extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public string $titulo, public ?string $seccion, public string $enlace) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Nuevo artículo por diseñar: «'.Str::limit($this->titulo, 60).'»');
    }

    public function content(): Content
    {
        return new Content(view: 'correos.diseno_pendiente');
    }
}
