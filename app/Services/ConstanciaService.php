<?php

namespace App\Services;

use App\Enums\EstatusArticulo;
use App\Enums\TipoDocumento;
use App\Mail\ArticuloAprobado;
use App\Models\Articulo;
use App\Models\ArticuloRevista;
use App\Models\Constancia;
use App\Models\Usuario;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * Aprobación de artículos y documentos con folio (6.4): la constancia de
 * aprobación ({prefijo}-{AAAA}-{NNNN}) y la carta de agradecimiento
 * (AGR-{AAAA}-{NNNN}), que se emite cuando el artículo forma parte de un
 * número visible. Ninguna se regenera.
 */
class ConstanciaService
{
    public function __construct(private ArticuloService $articulos) {}

    /**
     * Aprueba un artículo en revisión, genera su constancia (PDF con QR) y
     * avisa al autor principal.
     *
     * @throws ValidationException si ya fue dictaminado.
     */
    public function aprobar(Articulo $articulo, Usuario $administrador): Constancia
    {
        $disco = Storage::disk('local');
        $ruta = null;

        try {
            [$articulo, $constancia] = DB::transaction(function () use ($articulo, $disco, &$ruta) {
                $articulo = Articulo::whereKey($articulo->getKey())->lockForUpdate()->firstOrFail();
                $this->articulos->exigirEnRevision($articulo);

                $hoy = today();
                $articulo->forceFill([
                    'arti_status' => EstatusArticulo::APROBADO,
                    'arti_fecha_modificacion' => $hoy,
                ])->save();

                $folio = $this->siguienteFolio((int) $hoy->year);
                $ruta = config('emprende.carpeta_constancias')."/{$folio}.pdf";

                $disco->put($ruta, $this->generarPdf($articulo, $folio, $hoy));

                $constancia = Constancia::create([
                    'arti_id_autor' => $articulo->arti_id_autor,
                    'cons_fecha_emision' => $hoy,
                    'cons_url_archivo' => $ruta,
                    'cons_folio' => $folio,
                    'cons_tipo' => TipoDocumento::CONSTANCIA,
                ]);

                return [$articulo, $constancia];
            });
        } catch (Throwable $e) {
            if ($ruta !== null) {
                $disco->delete($ruta);
            }

            throw $e;
        }

        Log::channel('admin')->info('Artículo aprobado', [
            'administrador' => $administrador->usua_nombre_usuario,
            'arti_id_autor' => $articulo->arti_id_autor,
            'folio' => $constancia->cons_folio,
        ]);

        $principal = $this->articulos->principalDe($articulo);
        $correo = $principal?->persona?->correo?->comu_valor_comunicacion;
        if ($correo) {
            Mail::to($correo)->queue(new ArticuloAprobado(
                $principal->persona->pers_nombre,
                $articulo->arti_titulo,
                $constancia->cons_folio,
                route('autor.articulos.aprobado', $articulo),
            ));
        }

        return $constancia;
    }

    /**
     * Siguiente folio del año. Debe llamarse dentro de la transacción: el
     * bloqueo asesor (pg_advisory_xact_lock) serializa las aprobaciones del
     * mismo año hasta el COMMIT, así dos aprobaciones simultáneas no calculan
     * el mismo consecutivo.
     */
    public function siguienteFolio(int $anio, ?string $prefijo = null): string
    {
        $prefijo = ($prefijo ?? config('emprende.folio_prefijo'))."-{$anio}-";

        DB::select('select pg_advisory_xact_lock(hashtext(?))', ['constancia-folio-'.$prefijo]);

        $ultimo = (int) DB::scalar(
            'select coalesce(max(cast(substr(cons_folio, ?) as integer)), 0)
               from constancia
              where starts_with(cons_folio, ?) and substr(cons_folio, ?) ~ \'^[0-9]+$\'',
            [strlen($prefijo) + 1, $prefijo, strlen($prefijo) + 1],
        );

        return $prefijo.str_pad((string) ($ultimo + 1), (int) config('emprende.folio_digitos'), '0', STR_PAD_LEFT);
    }

    /** Número visible de la revista en el que salió el artículo (el primero publicado). */
    public function numeroDe(Articulo $articulo): ?ArticuloRevista
    {
        return ArticuloRevista::query()
            ->join('revista', 'revista.revi_id_revista', '=', 'articulo_revista.revi_id_revista')
            ->where('articulo_revista.arti_id_autor', $articulo->arti_id_autor)
            ->where('revista.revi_visible', true)
            ->orderBy('revista.revi_fecha_publicacion')
            ->select('articulo_revista.*')
            ->with('revista')
            ->first();
    }

    /**
     * Carta de agradecimiento del artículo: la devuelve si ya existe o la
     * genera (folio AGR-AAAA-NNNN y PDF) si el artículo aprobado forma parte
     * de un número visible.
     *
     * @throws ValidationException si el artículo aún no sale en un número.
     */
    public function agradecimiento(Articulo $articulo): Constancia
    {
        if ($existente = $articulo->cartaAgradecimiento()->first()) {
            return $existente;
        }

        $disco = Storage::disk('local');
        $ruta = null;

        try {
            return DB::transaction(function () use ($articulo, $disco, &$ruta) {
                $articulo = Articulo::whereKey($articulo->getKey())->lockForUpdate()->firstOrFail();

                if ($existente = $articulo->cartaAgradecimiento()->first()) {
                    return $existente;
                }

                $entrada = $articulo->arti_status === EstatusArticulo::APROBADO ? $this->numeroDe($articulo) : null;
                if ($entrada === null) {
                    throw ValidationException::withMessages([
                        'articulo' => 'La carta de agradecimiento estará disponible cuando el artículo salga en un número de la revista.',
                    ]);
                }

                $hoy = today();
                $folio = $this->siguienteFolio((int) $hoy->year, config('emprende.folio_prefijo_agradecimiento'));
                $ruta = config('emprende.carpeta_agradecimientos')."/{$folio}.pdf";

                $disco->put($ruta, $this->generarAgradecimiento($articulo, $entrada, $folio, $hoy));

                return Constancia::create([
                    'arti_id_autor' => $articulo->arti_id_autor,
                    'cons_fecha_emision' => $hoy,
                    'cons_url_archivo' => $ruta,
                    'cons_folio' => $folio,
                    'cons_tipo' => TipoDocumento::AGRADECIMIENTO,
                ]);
            });
        } catch (Throwable $e) {
            if ($ruta !== null) {
                $disco->delete($ruta);
            }

            throw $e;
        }
    }

    public function buscarPorFolio(string $folio): ?Constancia
    {
        // La carta de cesión no tiene folio: solo se verifican constancias y cartas de agradecimiento.
        return Constancia::with('articulo')->where('cons_folio', $folio)->first();
    }

    public function urlVerificacion(string $folio): string
    {
        return route('constancias.verificar', $folio);
    }

    /** PDF de la carta de agradecimiento (resources/views/pdf/agradecimiento.blade.php). */
    private function generarAgradecimiento(Articulo $articulo, ArticuloRevista $entrada, string $folio, Carbon $fecha): string
    {
        $articulo->load(['autores' => fn ($q) => $q->orderByDesc('autor_articulo.auar_principal'), 'autores.persona']);
        $imagen = fn (string $archivo) => 'data:image/png;base64,'.base64_encode(
            (string) file_get_contents(resource_path("plantillas/agradecimiento/{$archivo}"))
        );

        return Pdf::loadView('pdf.agradecimiento', [
            'titulo' => $articulo->arti_titulo,
            'autores' => $articulo->autores->map(fn ($a) => $a->persona?->nombre_completo)->filter()->values()->all(),
            'numero' => $entrada->revista->revi_num_edicion,
            'periodo' => trim("{$entrada->revista->revi_mes} de {$entrada->revista->revi_anio}"),
            'fecha' => $fecha->locale('es')->translatedFormat('j \d\e F \d\e Y'),
            'folio' => $folio,
            'urlVerificacion' => $this->urlVerificacion($folio),
            'firma' => config('emprende.agradecimiento_firma'),
            'logoFca' => $imagen('logo_fca.png'),
            'escudoUnam' => $imagen('escudo_unam.png'),
            'fca2026' => $imagen('fca_2026.png'),
        ])->setPaper('letter')->output();
    }

    /** PDF provisional de la constancia (resources/views/pdf/constancia.blade.php). */
    private function generarPdf(Articulo $articulo, string $folio, Carbon $fecha): string
    {
        $articulo->load(['autores.persona']);

        $principal = $articulo->autores->firstWhere('pivot.auar_principal', true);
        $coautores = $articulo->autores->where('pivot.auar_principal', false)->values();

        $qr = (new QRCode(new QROptions([
            'outputInterface' => QRGdImagePNG::class,
            'outputBase64' => true,
            'scale' => 6,
            'addQuietzone' => true,
        ])))->render($this->urlVerificacion($folio));

        return Pdf::loadView('pdf.constancia', [
            'titulo' => $articulo->arti_titulo,
            'autor' => $principal?->persona?->nombre_completo,
            'coautores' => $coautores->map(fn ($a) => $a->persona?->nombre_completo)->filter()->all(),
            'fecha' => $fecha->locale('es')->translatedFormat('j \d\e F \d\e Y'),
            'folio' => $folio,
            'qr' => $qr,
            'urlVerificacion' => $this->urlVerificacion($folio),
        ])->setPaper('letter', 'landscape')->output();
    }
}
