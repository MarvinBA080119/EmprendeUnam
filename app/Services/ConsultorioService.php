<?php

namespace App\Services;

use App\Enums\EstatusMensaje;
use App\Mail\RespuestaConsultorio;
use App\Models\Mensaje;
use App\Models\Usuario;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Consultorio (6.6). Bandeja del administrador: responder y rechazar.
 * El formulario público y la sección pública se agregan en la fase E.
 */
class ConsultorioService
{
    /** Bandeja: solo mensajes de lectores (men_mens_id_mensaje IS NULL). */
    public function bandeja(?string $estatus, ?string $texto, int $porPagina): LengthAwarePaginator
    {
        $texto = trim((string) $texto);

        return Mensaje::originales()
            ->when($estatus, fn ($q) => $q->where('mens_status', $estatus))
            ->when($texto !== '', function ($q) use ($texto) {
                $patron = BuscadorService::patronLike($texto);
                $q->where(fn ($w) => $w
                    ->whereRaw(BuscadorService::sinAcentos('mens_remitente').' like ?', [$patron])
                    ->orWhereRaw(BuscadorService::sinAcentos('mens_asunto').' like ?', [$patron])
                    ->orWhereRaw('lower(mens_correo) like ?', [$patron]));
            })
            ->orderByRaw("case when mens_status = 'PENDIENTE' then 0 else 1 end")
            ->orderByDesc('mens_fecha_envio')
            ->orderByDesc('mens_id_mensaje')
            ->paginate($porPagina)
            ->withQueryString();
    }

    /**
     * Guarda la respuesta ligada al original, marca ambos como CONTESTADO y
     * envía la respuesta al correo del lector.
     *
     * @throws ValidationException si el mensaje ya no está pendiente.
     */
    public function responder(Mensaje $original, string $texto, Usuario $administrador): Mensaje
    {
        $correoAdmin = (string) $administrador->correo();

        [$original, $respuesta] = DB::transaction(function () use ($original, $texto, $administrador, $correoAdmin) {
            $original = $this->bloquearPendiente($original);

            $respuesta = Mensaje::create([
                'usua_id_usuario' => $administrador->usua_id_usuario,
                'men_mens_id_mensaje' => $original->mens_id_mensaje,
                'mens_remitente' => config('emprende.remitente_consultorio'),
                'mens_correo' => $correoAdmin,
                'mens_asunto' => Str::limit('Re: '.$original->mens_asunto, 50, ''),
                'mens_descripcion' => $texto,
                'mens_status' => EstatusMensaje::CONTESTADO,
                'mens_fecha_envio' => now(),
            ]);

            $original->forceFill(['mens_status' => EstatusMensaje::CONTESTADO])->save();

            return [$original, $respuesta];
        });

        Log::channel('admin')->info('Mensaje del consultorio respondido', [
            'administrador' => $administrador->usua_nombre_usuario,
            'mens_id_mensaje' => $original->mens_id_mensaje,
        ]);

        Mail::to($original->mens_correo)->queue(new RespuestaConsultorio(
            $original->mens_remitente,
            $original->mens_asunto,
            $original->mens_descripcion,
            $texto,
        ));

        return $respuesta;
    }

    /** @throws ValidationException si el mensaje ya no está pendiente. */
    public function rechazar(Mensaje $original, Usuario $administrador): void
    {
        DB::transaction(function () use ($original) {
            $this->bloquearPendiente($original)
                ->forceFill(['mens_status' => EstatusMensaje::RECHAZADO])
                ->save();
        });

        Log::channel('admin')->info('Mensaje del consultorio rechazado', [
            'administrador' => $administrador->usua_nombre_usuario,
            'mens_id_mensaje' => $original->mens_id_mensaje,
        ]);
    }

    private function bloquearPendiente(Mensaje $mensaje): Mensaje
    {
        $mensaje = Mensaje::originales()->whereKey($mensaje->getKey())->lockForUpdate()->firstOrFail();

        if ($mensaje->mens_status !== EstatusMensaje::PENDIENTE) {
            throw ValidationException::withMessages([
                'mensaje' => 'Este mensaje ya fue atendido; solo se pueden responder o rechazar mensajes pendientes.',
            ]);
        }

        return $mensaje;
    }
}
