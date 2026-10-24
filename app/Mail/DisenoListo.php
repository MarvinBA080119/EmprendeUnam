<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

/** Al administrador: la cuenta de diseño subió (o reemplazó) el PDF de un artículo. */
class DisenoListo extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public string $titulo, public bool $reemplazo, public string $enlace) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: ($this->reemplazo ? 'PDF diseñado actualizado: «' : 'PDF diseñado listo: «').Str::limit($this->titulo, 60).'»');
    }

    public function content(): Content
    {
        return new Content(view: 'correos.diseno_listo');
    }
}
