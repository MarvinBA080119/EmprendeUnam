<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Correo del comando emprende:probar-correo. No va en cola salvo con --cola. */
class CorreoPrueba extends Mailable
{
    public function __construct(public string $enviadoDesde) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Prueba de correo de la Revista EmprendeUNAM');
    }

    public function content(): Content
    {
        return new Content(view: 'correos.prueba');
    }
}
