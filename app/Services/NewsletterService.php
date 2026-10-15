<?php

namespace App\Services;

use App\Models\Persona;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Newsletter (6.8). Lista y exportación de suscriptores para el administrador.
 * Suscripción, confirmación, baja y aviso de nueva edición: fase E.
 */
class NewsletterService
{
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
