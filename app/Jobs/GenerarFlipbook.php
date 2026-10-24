<?php

namespace App\Jobs;

use App\Services\FlipbookService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

/**
 * Un lote de la conversión de un flipbook: convierte páginas unos segundos y,
 * si faltan, encola el siguiente lote. Así cada trabajo dura poco y la cola
 * de correos no se queda esperando detrás de una revista de 80 páginas.
 */
class GenerarFlipbook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public int $timeout = 300;

    public function __construct(public string $numero, public int $desde) {}

    public function handle(FlipbookService $flipbooks): void
    {
        $siguiente = $flipbooks->procesarLote($this->numero, $this->desde);

        if ($siguiente !== null) {
            self::dispatch($this->numero, $siguiente);
        }
    }

    public function failed(Throwable $error): void
    {
        app(FlipbookService::class)->falla($this->numero, $error->getMessage());
    }
}
