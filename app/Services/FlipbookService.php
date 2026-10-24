<?php

namespace App\Services;

use App\Jobs\GenerarFlipbook;
use App\Models\ArticuloRevista;
use App\Models\Revista;
use App\Models\Usuario;
use App\Support\Manuscrito;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Symfony\Component\Process\Process;
use Throwable;

/**
 * Generador de flipbooks (visor StPageFlip, MIT; plantilla en
 * resources/flipbook/plantilla). A partir del PDF completo de un número:
 *  1. convierte cada página en JPG numerado por su posición (img/1.jpg,
 *     img/2.jpg…) con su versión ampliada (img/hd/) y su miniatura
 *     (img/thumbs/), con pdftoppm (poppler-utils);
 *  2. arma el visor (index.html, css, js) y su índice con los artículos del
 *     número registrados en el gestor;
 *  3. lo guarda en storage/app/private/flipbooks/{numero}/ y lo publica en
 *     /flipbook/{numero}.
 *
 * La conversión corre en la cola, por lotes de unos segundos (cada lote
 * vuelve a encolar el siguiente): así ningún trabajo se acerca a los límites
 * de tiempo del worker. El avance se guarda en estado.json para la barra de
 * progreso del panel.
 */
class FlipbookService
{
    /** Segundos de conversión por trabajo de la cola antes de encolar el resto. */
    public const LOTE_SEGUNDOS = 25;

    /** Ancho en píxeles de la hoja normal, la ampliada y la miniatura (como la plantilla). */
    public const ANCHO = 1275;

    public const ANCHO_HD = 2040;

    public const ANCHO_MINIATURA = 240;

    /** Un trabajo sin avance en este tiempo se considera abandonado y se puede reiniciar. */
    private const MINUTOS_ABANDONO = 15;

    private const TIPOS = [
        'html' => 'text/html; charset=UTF-8',
        'css' => 'text/css; charset=UTF-8',
        'js' => 'text/javascript; charset=UTF-8',
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'txt' => 'text/plain; charset=UTF-8',
    ];

    /* ------------------------------------------------------------------ */
    /* Rutas */
    /* ------------------------------------------------------------------ */

    /** Ruta pública con la que se enlaza (y se guarda en revi_url_flipbook). */
    public static function ruta(string $numero): string
    {
        return '/flipbook/'.$numero;
    }

    /** Número del flipbook al que apunta un enlace propio, o null si es externo. */
    public static function numeroDeRuta(?string $url): ?string
    {
        return $url !== null && preg_match('#^/flipbook/([0-9]{1,4})/?$#', $url, $m) ? $m[1] : null;
    }

    private function carpeta(string $numero): string
    {
        return 'flipbooks/'.$numero;
    }

    private function trabajo(string $numero): string
    {
        return 'flipbooks/.trabajo/'.$numero;
    }

    private function absoluta(string $relativa): string
    {
        return Storage::disk('local')->path($relativa);
    }

    /* ------------------------------------------------------------------ */
    /* Inicio */
    /* ------------------------------------------------------------------ */

    /**
     * Recibe el PDF, cuenta sus páginas y encola la conversión.
     *
     * @return array<string, mixed> estado inicial
     */
    public function iniciar(string $numero, UploadedFile $pdf, Usuario $administrador): array
    {
        if (! preg_match('/^[0-9]{1,4}$/', $numero)) {
            throw ValidationException::withMessages(['numero' => 'El número de edición solo lleva dígitos (por ejemplo 220).']);
        }
        if (Manuscrito::detectar((string) $pdf->getRealPath()) !== 'pdf') {
            throw ValidationException::withMessages(['pdf' => 'El archivo no es un PDF válido.']);
        }

        $actual = $this->estado($numero);
        if ($actual && in_array($actual['estado'], ['en_cola', 'convirtiendo', 'armando'], true) && ! $this->abandonado($actual)) {
            throw ValidationException::withMessages(['numero' => "Ya se está generando el flipbook del número {$numero}. Espera a que termine."]);
        }

        $disco = Storage::disk('local');
        $disco->deleteDirectory($this->trabajo($numero));
        $disco->putFileAs($this->trabajo($numero), $pdf, 'fuente.pdf');

        try {
            $total = $this->contarPaginas($this->absoluta($this->trabajo($numero).'/fuente.pdf'));
        } catch (Throwable $e) {
            $disco->deleteDirectory($this->trabajo($numero));

            throw ValidationException::withMessages(['pdf' => $e->getMessage()]);
        }

        $maximo = (int) config('emprende.flipbook.max_paginas');
        if ($total < 1 || $total > $maximo) {
            $disco->deleteDirectory($this->trabajo($numero));

            throw ValidationException::withMessages(['pdf' => "El PDF debe tener entre 1 y {$maximo} páginas (tiene {$total})."]);
        }

        $estado = $this->escribirEstado($numero, [
            'estado' => 'en_cola',
            'actual' => 0,
            'total' => $total,
            'archivo' => $pdf->getClientOriginalName(),
            'iniciado' => now()->toIso8601String(),
        ]);

        Log::channel('admin')->info('Flipbook en preparación', [
            'administrador' => $administrador->usua_nombre_usuario, 'numero' => $numero, 'paginas' => $total,
        ]);

        try {
            GenerarFlipbook::dispatch($numero, 1);
        } catch (Throwable $e) {
            // Con la cola «sync» (desarrollo) el error llega aquí; el trabajo ya
            // dejó el estado en «error» con su mensaje.
            report($e);

            return $this->estado($numero) ?? $estado;
        }

        return $this->estado($numero) ?? $estado;
    }

    /* ------------------------------------------------------------------ */
    /* Conversión por lotes */
    /* ------------------------------------------------------------------ */

    /**
     * Convierte páginas desde $desde hasta agotar el tiempo del lote.
     *
     * @return int|null siguiente página por convertir, o null si ya se armó el flipbook
     */
    public function procesarLote(string $numero, int $desde): ?int
    {
        $estado = $this->estado($numero);
        if (! $estado || $estado['estado'] === 'error') {
            return null;
        }

        $total = (int) $estado['total'];
        $inicio = microtime(true);

        for ($pagina = $desde; $pagina <= $total; $pagina++) {
            $this->convertirPagina($numero, $pagina);
            $this->escribirEstado($numero, ['estado' => 'convirtiendo', 'actual' => $pagina] + $estado);

            if ($pagina < $total && microtime(true) - $inicio > self::LOTE_SEGUNDOS) {
                return $pagina + 1;
            }
        }

        $this->armar($numero);

        return null;
    }

    private function convertirPagina(string $numero, int $pagina): void
    {
        $base = $this->absoluta($this->trabajo($numero));
        File::ensureDirectoryExists("{$base}/img/hd");
        File::ensureDirectoryExists("{$base}/img/thumbs");

        // La versión ampliada sale del PDF; la normal y la miniatura se
        // reducen de ella (más rápido que rasterizar tres veces).
        $this->rasterizar("{$base}/fuente.pdf", $pagina, self::ANCHO_HD, "{$base}/img/hd/{$pagina}");

        if (extension_loaded('gd')) {
            $this->reducir("{$base}/img/hd/{$pagina}.jpg", self::ANCHO, "{$base}/img/{$pagina}.jpg", 82);
            $this->reducir("{$base}/img/hd/{$pagina}.jpg", self::ANCHO_MINIATURA, "{$base}/img/thumbs/{$pagina}.jpg", 72);
        } else {
            $this->rasterizar("{$base}/fuente.pdf", $pagina, self::ANCHO, "{$base}/img/{$pagina}");
            $this->rasterizar("{$base}/fuente.pdf", $pagina, self::ANCHO_MINIATURA, "{$base}/img/thumbs/{$pagina}");
        }
    }

    /** pdftoppm de una sola página al ancho dado: escribe {$salida}.jpg. */
    private function rasterizar(string $pdf, int $pagina, int $ancho, string $salida): void
    {
        $proceso = new Process([
            config('emprende.flipbook.pdftoppm'), '-jpeg', '-jpegopt', 'quality=85,progressive=y',
            '-f', (string) $pagina, '-l', (string) $pagina, '-singlefile',
            '-scale-to-x', (string) $ancho, '-scale-to-y', '-1',
            $pdf, $salida,
        ]);
        $proceso->setTimeout(180);

        try {
            $proceso->run();
        } catch (Throwable $e) {
            throw new RuntimeException('No se pudo ejecutar pdftoppm. Instala poppler-utils en el servidor.', 0, $e);
        }

        if (! $proceso->isSuccessful() || ! is_file("{$salida}.jpg")) {
            throw new RuntimeException("No se pudo convertir la página {$pagina} del PDF: ".trim($proceso->getErrorOutput() ?: 'pdftoppm no está instalado (poppler-utils).'));
        }
    }

    private function reducir(string $origen, int $ancho, string $destino, int $calidad): void
    {
        $imagen = imagecreatefromjpeg($origen);
        $reducida = imagescale($imagen, $ancho, -1, IMG_BICUBIC);
        imageinterlace($reducida, true);
        imagejpeg($reducida, $destino, $calidad);
        imagedestroy($imagen);
        imagedestroy($reducida);
    }

    /* ------------------------------------------------------------------ */
    /* Armado */
    /* ------------------------------------------------------------------ */

    private function armar(string $numero): void
    {
        $estado = $this->escribirEstado($numero, ['estado' => 'armando'] + ($this->estado($numero) ?? []));
        $trabajo = $this->absoluta($this->trabajo($numero));

        File::copyDirectory(resource_path('flipbook/plantilla'), $trabajo);
        $this->copiarOpcionales($trabajo);
        File::delete("{$trabajo}/fuente.pdf");

        [$ancho, $alto] = getimagesize("{$trabajo}/img/1.jpg") ?: [self::ANCHO, (int) round(self::ANCHO * 11 / 8.5)];
        $manifiesto = [
            'numero' => $numero,
            'paginas' => (int) $estado['total'],
            'ancho' => $ancho,
            'alto' => $alto,
            'archivo' => $estado['archivo'] ?? null,
            'generado' => now()->toIso8601String(),
        ];
        File::put("{$trabajo}/manifest.json", json_encode($manifiesto, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        File::put("{$trabajo}/index.html", $this->visor($manifiesto));
        File::delete("{$trabajo}/estado.json");

        // Reemplazo del flipbook anterior del mismo número, de una vez.
        $final = $this->absoluta($this->carpeta($numero));
        File::deleteDirectory($final);
        File::ensureDirectoryExists(dirname($final));
        if (! File::moveDirectory($trabajo, $final)) {
            throw new RuntimeException('No se pudo guardar el flipbook en el servidor.');
        }

        // Si el número ya existe en el gestor y no tiene eBook, queda como su eBook.
        Revista::where('revi_num_edicion', $numero)
            ->where(fn ($q) => $q->whereNull('revi_url_flipbook')->orWhere('revi_url_flipbook', ''))
            ->update(['revi_url_flipbook' => self::ruta($numero)]);

        Log::channel('admin')->info('Flipbook generado', ['numero' => $numero, 'paginas' => $manifiesto['paginas']]);
    }

    /**
     * Logotipo de la revista y fondo de lectura: no vienen con la plantilla.
     * Se toman de la plantilla si alguien los coloca ahí, o del sitio público.
     */
    private function copiarOpcionales(string $destino): void
    {
        $opcionales = [
            'emprendeunam.png' => [public_path('img/Emprendeunam_horizontal.png'), public_path('img/logo_nuevo_rojo.png')],
            'fondo_revista.jpg' => [public_path('img/fondo_revista.jpg')],
        ];
        foreach ($opcionales as $archivo => $fuentes) {
            if (is_file("{$destino}/img/diseno/{$archivo}")) {
                continue;
            }
            foreach ($fuentes as $fuente) {
                if (is_file($fuente)) {
                    File::copy($fuente, "{$destino}/img/diseno/{$archivo}");
                    break;
                }
            }
        }
    }

    /** index.html del visor (resources/views/flipbook/visor.blade.php). */
    private function visor(array $manifiesto): string
    {
        $numero = $manifiesto['numero'];
        $base = $this->absoluta($this->trabajo($numero));
        if (! is_dir($base)) {
            $base = $this->absoluta($this->carpeta($numero));
        }

        return view('flipbook.visor', [
            'numero' => $numero,
            'paginas' => $manifiesto['paginas'],
            'ancho' => $manifiesto['ancho'],
            'alto' => $manifiesto['alto'],
            'version' => substr(md5($manifiesto['generado']), 0, 8),
            'base' => self::ruta($numero).'/',
            'indice' => $this->indice($numero),
            'logo' => is_file("{$base}/img/diseno/emprendeunam.png"),
        ])->render();
    }

    /**
     * Índice del visor: los artículos del número en el gestor que tienen
     * páginas, ordenados por su primera página.
     *
     * @return list<array{titulo: string, seccion: ?string, autor: ?string, pagina: int, pdf: ?string}>
     */
    public function indice(string $numero): array
    {
        $revista = Revista::where('revi_num_edicion', $numero)->orderByDesc('revi_id_revista')->first();
        if (! $revista) {
            return [];
        }

        return $revista->articulos()->with('articulo')->get()
            ->filter(fn (ArticuloRevista $a) => preg_match('/^\s*([0-9]+)/', (string) $a->arre_paginas))
            ->map(fn (ArticuloRevista $a) => [
                'titulo' => $a->arre_titulo,
                'seccion' => $a->arre_seccion,
                'autor' => $a->arre_autores,
                'pagina' => (int) $a->arre_paginas,
                'pdf' => $a->rutaPdf() ? route('edicion.articulo.pdf', $a) : null,
            ])
            ->sortBy('pagina')
            ->values()
            ->all();
    }

    /** Vuelve a escribir el índice del visor con lo que hoy tiene el gestor (sin convertir de nuevo). */
    public function actualizarIndice(string $numero): void
    {
        $manifiesto = $this->manifiesto($numero);
        if (! $manifiesto) {
            throw ValidationException::withMessages(['numero' => "No hay flipbook del número {$numero}."]);
        }

        File::put($this->absoluta($this->carpeta($numero)).'/index.html', $this->visor($manifiesto));
    }

    /* ------------------------------------------------------------------ */
    /* Estado, lista y archivos */
    /* ------------------------------------------------------------------ */

    /** @return array<string, mixed>|null */
    public function estado(string $numero): ?array
    {
        $archivo = $this->absoluta($this->trabajo($numero)).'/estado.json';
        if (is_file($archivo)) {
            return json_decode((string) file_get_contents($archivo), true) ?: null;
        }

        $manifiesto = $this->manifiesto($numero);

        return $manifiesto ? ['estado' => 'listo', 'actual' => $manifiesto['paginas'], 'total' => $manifiesto['paginas']] + $manifiesto : null;
    }

    public function falla(string $numero, string $mensaje): void
    {
        $this->escribirEstado($numero, ['estado' => 'error', 'mensaje' => $mensaje] + ($this->estado($numero) ?? []));
        Storage::disk('local')->delete($this->trabajo($numero).'/fuente.pdf');
        Log::channel('admin')->warning('Flipbook con error', ['numero' => $numero, 'mensaje' => $mensaje]);
    }

    /** @return array<string, mixed>|null */
    public function manifiesto(string $numero): ?array
    {
        $archivo = $this->absoluta($this->carpeta($numero)).'/manifest.json';

        return is_file($archivo) ? (json_decode((string) file_get_contents($archivo), true) ?: null) : null;
    }

    /**
     * Flipbooks generados, del número más alto al más bajo, con los números
     * del gestor que los usan como eBook.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function listar(): Collection
    {
        $raiz = $this->absoluta('flipbooks');
        if (! is_dir($raiz)) {
            return collect();
        }

        $usos = Revista::whereNotNull('revi_url_flipbook')->get(['revi_id_revista', 'revi_num_edicion', 'revi_url_flipbook', 'revi_titulo'])
            ->groupBy(fn (Revista $r) => self::numeroDeRuta($r->revi_url_flipbook));

        return collect(File::directories($raiz))
            ->map(fn (string $dir) => basename($dir))
            ->filter(fn (string $n) => preg_match('/^[0-9]{1,4}$/', $n))
            ->map(fn (string $n) => $this->manifiesto($n))
            ->filter()
            ->map(fn (array $m) => $m + [
                'ruta' => self::ruta($m['numero']),
                'miniatura' => self::ruta($m['numero']).'/img/thumbs/1.jpg?v='.substr(md5($m['generado']), 0, 8),
                'usado_en' => $usos->get($m['numero'], collect())->values(),
            ])
            ->sortByDesc(fn (array $m) => (int) $m['numero'])
            ->values();
    }

    public function eliminar(string $numero): void
    {
        Storage::disk('local')->deleteDirectory($this->carpeta($numero));
        Storage::disk('local')->deleteDirectory($this->trabajo($numero));

        // Ningún número queda apuntando a un flipbook que ya no existe.
        Revista::where('revi_url_flipbook', self::ruta($numero))->update(['revi_url_flipbook' => null]);
    }

    /** Ruta absoluta y tipo de un archivo del flipbook publicado, o null si no existe. */
    public function archivo(string $numero, string $ruta, bool $enTrabajo = false): ?array
    {
        $ruta = $ruta === '' ? 'index.html' : $ruta;
        if (str_contains($ruta, '..') || str_starts_with($ruta, '/') || in_array(basename($ruta), ['estado.json', 'fuente.pdf'], true)) {
            return null;
        }

        $base = realpath($this->absoluta($enTrabajo ? $this->trabajo($numero) : $this->carpeta($numero)));
        $real = $base ? realpath($base.'/'.$ruta) : false;
        $tipo = self::TIPOS[strtolower(pathinfo((string) $real, PATHINFO_EXTENSION))] ?? null;

        if (! $real || ! str_starts_with($real, $base.DIRECTORY_SEPARATOR) || ! is_file($real) || ! $tipo) {
            return null;
        }

        return ['ruta' => $real, 'tipo' => $tipo];
    }

    /* ------------------------------------------------------------------ */

    private function contarPaginas(string $pdf): int
    {
        $proceso = new Process([config('emprende.flipbook.pdfinfo'), $pdf]);
        $proceso->setTimeout(60);

        try {
            $proceso->run();
        } catch (Throwable $e) {
            throw new RuntimeException('No se pudo leer el PDF: falta pdfinfo (poppler-utils) en el servidor.', 0, $e);
        }

        if (! $proceso->isSuccessful() || ! preg_match('/^Pages:\s+([0-9]+)/m', $proceso->getOutput(), $m)) {
            throw new RuntimeException('No se pudo leer el PDF. Revisa que no esté dañado ni protegido con contraseña.');
        }

        return (int) $m[1];
    }

    /** @param  array<string, mixed>  $estado */
    private function escribirEstado(string $numero, array $estado): array
    {
        $estado = ['numero' => $numero, 'actualizado' => now()->toIso8601String()] + $estado;
        $estado['actualizado'] = now()->toIso8601String();
        $carpeta = $this->absoluta($this->trabajo($numero));
        File::ensureDirectoryExists($carpeta);
        File::put("{$carpeta}/estado.json", json_encode($estado, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return $estado;
    }

    private function abandonado(array $estado): bool
    {
        return isset($estado['actualizado']) && now()->diffInMinutes($estado['actualizado'], true) > self::MINUTOS_ABANDONO;
    }
}
