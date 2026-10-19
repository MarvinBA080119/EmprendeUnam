<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class ConfirmarSuscripcion extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $nombre,
        public string $enlaceConfirmar,
        public string $enlaceBaja,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Confirma tu suscripción al newsletter EmprendeUNAM');
    }

    public function headers(): Headers
    {
        return new Headers(text: ['List-Unsubscribe' => '<'.$this->enlaceBaja.'>']);
    }

    public function content(): Content
    {
        return new Content(view: 'correos.confirmar_suscripcion');
    }
}
