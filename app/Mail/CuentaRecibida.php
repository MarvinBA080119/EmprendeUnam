<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CuentaRecibida extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public string $nombre) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Recibimos tu registro como autor');
    }

    public function content(): Content
    {
        return new Content(view: 'correos.cuenta_recibida');
    }
}
