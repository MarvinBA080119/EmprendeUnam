<?php

namespace App\Services;

use App\Mail\AvisoNuevaEdicion;
use App\Mail\ConfirmarSuscripcion;
use App\Models\Comunicacion;
use App\Models\Persona;
use App\Models\TemaNews;
use App\Models\TipoComunicacion;
use App\Models\Usuario;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Newsletter (6.8): suscripción con doble confirmación, baja, lista y
 * exportación de suscriptores, y aviso de nueva edición.
 *
 * Suscriptor activo = pers_subs_newsletter y confirmado y sin baja.
 */
class NewsletterService
{
    public const NUEVO = 'nuevo';

    public const REACTIVADO = 'reactivado';

    public const SIN_CONFIRMAR = 'sin_confirmar';

    public const YA_SUSCRITO = 'ya_suscrito';

    /**
     * Suscribe (o reactiva) a una persona en UNA transacción: persona + correo +
     * tema_news, como exigen ck_pers_newsletter y el trigger diferido
     * tr_newsletter_completa. Envía el correo de confirmación.
     *
     * @param  array{nombre: string, apellido: string, correo: string, tema: string}  $datos
     * @return array{estado: string, persona: Persona}
     */
    public function suscribir(array $datos): array
    {
        $correo = Str::lower(trim($datos['correo']));

        [$estado, $persona] = DB::transaction(function () use ($datos, $correo) {
            $existente = Comunicacion::correos()->conValor($correo)->lockForUpdate()->first();

            if ($existente?->pers_id_persona) {
                $persona = Persona::lockForUpdate()->findOrFail($existente->pers_id_persona);

                if ($persona->pers_subs_newsletter && $persona->pers_fecha_baja === null
                    && $persona->pers_fecha_confirmacion !== null) {
                    return [self::YA_SUSCRITO, $persona];
                }

                $estado = $persona->pers_fecha_baja !== null ? self::REACTIVADO
                    : ($persona->pers_subs_newsletter ? self::SIN_CONFIRMAR : self::NUEVO);

                // El trigger exige un correo activo para la suscripción.
                if (! $existente->comu_activo) {
                    $existente->forceFill(['comu_activo' => true])->save();
                }
            } else {
                $estado = self::NUEVO;
                $persona = Persona::create([
                    'pers_nombre' => $datos['nombre'],
                    'pers_primer_apellido' => $datos['apellido'],
                ]);
                Comunicacion::create([
                    'pers_id_persona' => $persona->pers_id_persona,
                    'comu_id_tipo_comunicacion' => (int) TipoComunicacion::correo()->value('comu_id_tipo_comunicacion'),
                    'comu_valor_comunicacion' => $correo,
                    'comu_etiqueta' => 'Newsletter',
                    'comu_activo' => true,
                ]);
            }

            // Doble confirmación: toda (re)suscripción se confirma de nuevo.
            $persona->forceFill([
                'pers_subs_newsletter' => true,
                'pers_acepta_aviso' => true,
                'pers_fecha_suscripcion' => now(),
                'pers_token_confirmacion' => bin2hex(random_bytes(32)),
                'pers_fecha_confirmacion' => null,
                'pers_fecha_baja' => null,
            ])->save();

            // El tema se agrega solo si no estaba registrado para la persona.
            TemaNews::firstOrCreate([
                'pers_id_persona' => $persona->pers_id_persona,
                'tene_tema_interes' => $datos['tema'],
            ]);

            return [$estado, $persona];
        });

        if ($estado !== self::YA_SUSCRITO) {
            Mail::to($correo)->queue(new ConfirmarSuscripcion(
                $persona->pers_nombre,
                route('newsletter.confirmar', $persona->pers_token_confirmacion),
                route('newsletter.baja', $persona->pers_token_confirmacion),
            ));
        }

        return ['estado' => $estado, 'persona' => $persona];
    }

    public function porToken(string $token): ?Persona
    {
        return Persona::where('pers_token_confirmacion', $token)->with('correo')->first();
    }

    /**
     * Confirma la suscripción (enlace del correo).
     *
     * @return bool false si ya estaba confirmada.
     */
    public function confirmar(Persona $persona): bool
    {
        if ($persona->pers_fecha_confirmacion !== null) {
            return false;
        }

        $persona->forceFill(['pers_fecha_confirmacion' => now()])->save();

        return true;
    }

    /**
     * Da de baja (enlace de cada correo, sin sesión).
     *
     * @return bool false si ya estaba dada de baja.
     */
    public function darDeBaja(Persona $persona): bool
    {
        if (! $persona->pers_subs_newsletter || $persona->pers_fecha_baja !== null) {
            return false;
        }

        $persona->forceFill(['pers_subs_newsletter' => false, 'pers_fecha_baja' => now()])->save();

        return true;
    }

    /**
     * Encola un aviso por cada suscriptor activo, con su enlace de baja.
     * No se guarda historial: los envíos fallidos quedan en failed_jobs.
     *
     * @return int número de avisos encolados
     */
    public function avisarNuevaEdicion(string $titulo, string $enlace, Usuario $administrador): int
    {
        $enviados = 0;

        Persona::suscriptoresActivos()
            ->with('correo')
            ->lazyById(200, 'pers_id_persona')
            ->each(function (Persona $persona) use ($titulo, $enlace, &$enviados) {
                $correo = $persona->correo?->comu_valor_comunicacion;
                if (! $correo) {
                    return;
                }
                Mail::to($correo)->queue(new AvisoNuevaEdicion(
                    $persona->pers_nombre,
                    $titulo,
                    $enlace,
                    route('newsletter.baja', $persona->pers_token_confirmacion),
                ));
                $enviados++;
            });

        Log::channel('admin')->info('Aviso de nueva edición del newsletter', [
            'administrador' => $administrador->usua_nombre_usuario,
            'titulo' => $titulo,
            'enlace' => $enlace,
            'avisos' => $enviados,
        ]);

        return $enviados;
    }

    public const ESTADOS = [
        'activo' => 'Activo',
        'sin_confirmar' => 'Sin confirmar',
        'baja' => 'Dado de baja',
    ];

    /** @param  'activo'|'sin_confirmar'|'baja'|null  $estado */
    public function suscriptores(?string $estado, ?string $texto, int $porPagina): LengthAwarePaginator
    {
        return $this->consulta($estado, $texto)
            ->orderByDesc('pers_fecha_suscripcion')
            ->orderByDesc('pers_id_persona')
            ->paginate($porPagina)
            ->withQueryString();
    }

    /**
     * Datos de un suscriptor para la lista y el CSV.
     *
     * @return array{nombre: string, correo: ?string, temas: string, alta: ?string, confirmacion: ?string, baja: ?string, estado: string}
     */
    public function fila(Persona $persona): array
    {
        return [
            'nombre' => $persona->nombre_completo,
            'correo' => $persona->correo?->comu_valor_comunicacion,
            'temas' => $persona->temasNews->pluck('tene_tema_interes')->unique()->join(', '),
            'alta' => $persona->pers_fecha_suscripcion?->format('d/m/Y H:i'),
            'confirmacion' => $persona->pers_fecha_confirmacion?->format('d/m/Y H:i'),
            'baja' => $persona->pers_fecha_baja?->format('d/m/Y H:i'),
            'estado' => $this->estadoDe($persona),
        ];
    }

    public function estadoDe(Persona $persona): string
    {
        return match (true) {
            $persona->pers_fecha_baja !== null || ! $persona->pers_subs_newsletter => 'baja',
            $persona->pers_fecha_confirmacion === null => 'sin_confirmar',
            default => 'activo',
        };
    }

    /** CSV en UTF-8 con BOM (para que Excel respete los acentos). */
    public function exportarCsv(?string $estado, ?string $texto): StreamedResponse
    {
        $nombre = 'suscriptores-newsletter-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($estado, $texto) {
            $salida = fopen('php://output', 'w');
            fwrite($salida, "\xEF\xBB\xBF");
            fputcsv($salida, ['Nombre', 'Correo', 'Tema de interés', 'Fecha de alta', 'Fecha de confirmación', 'Fecha de baja', 'Estado'], escape: '');

            $this->consulta($estado, $texto)->lazyById(500, 'pers_id_persona')->each(function (Persona $persona) use ($salida) {
                $fila = $this->fila($persona);
                $fila['estado'] = self::ESTADOS[$fila['estado']];
                fputcsv($salida, array_map($this->celdaSegura(...), array_values($fila)), escape: '');
            });

            fclose($salida);
        }, $nombre, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function consulta(?string $estado, ?string $texto): Builder
    {
        $texto = trim((string) $texto);

        return Persona::query()
            ->whereNotNull('pers_fecha_suscripcion')
            ->when($estado === 'activo', fn (Builder $q) => $q->suscriptoresActivos())
            ->when($estado === 'sin_confirmar', fn (Builder $q) => $q
                ->where('pers_subs_newsletter', true)
                ->whereNull('pers_fecha_confirmacion')
                ->whereNull('pers_fecha_baja'))
            ->when($estado === 'baja', fn (Builder $q) => $q->where(fn (Builder $w) => $w
                ->whereNotNull('pers_fecha_baja')
                ->orWhere('pers_subs_newsletter', false)))
            ->when($texto !== '', function (Builder $q) use ($texto) {
                $patron = BuscadorService::patronLike($texto);
                $q->where(fn (Builder $w) => $w
                    ->whereRaw(BuscadorService::sinAcentos("concat_ws(' ', pers_nombre, pers_primer_apellido, pers_segundo_apellido)").' like ?', [$patron])
                    ->orWhereHas('comunicaciones', fn (Builder $c) => $c->correos()->whereRaw('lower(comu_valor_comunicacion) like ?', [$patron])));
            })
            ->with(['correo', 'temasNews']);
    }

    /** Evita que Excel interprete una celda como fórmula (inyección CSV). */
    private function celdaSegura(?string $valor): string
    {
        $valor = (string) $valor;

        return preg_match('/^[=+\-@\t\r]/', $valor) ? "'".$valor : $valor;
    }
}
