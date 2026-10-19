<?php

namespace App\Services;

use App\Mail\CuentaActivada;
use App\Mail\CuentaRecibida;
use App\Models\Autor;
use App\Models\Comunicacion;
use App\Models\Persona;
use App\Models\Rol;
use App\Models\RolUsuario;
use App\Models\TipoComunicacion;
use App\Models\Usuario;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Registro de autores (sección 6.1) y activación de sus cuentas (6.2).
 */
class RegistroAutorService
{
    /**
     * Crea persona, correo, usuario (inactivo), autor y rol en UNA transacción:
     * el trigger diferido tr_usuario_con_correo se revisa al COMMIT.
     *
     * Si el correo ya pertenece a una persona SIN cuenta (suscriptora del
     * newsletter o coautora de un artículo), se reutiliza esa persona: se
     * actualizan sus datos con los del registro y se le crea la cuenta. Así
     * nadie «aparta» un correo ajeno suscribiéndolo, y el coautor que se
     * registra ve sus artículos. La activación del administrador sigue siendo
     * el filtro antes de que la cuenta pueda entrar.
     *
     * @param  array{nombre: string, primer_apellido: string, segundo_apellido?: ?string,
     *               correo: string, clave: string, institucion: string,
     *               grado_academico?: ?string, nombre_usuario?: ?string}  $datos
     *
     * @throws ValidationException si el correo o el nombre de usuario ya existen.
     */
    public function registrar(array $datos): Usuario
    {
        $correo = Str::lower(trim($datos['correo']));

        try {
            $usuario = DB::transaction(function () use ($datos, $correo) {
                $nombre = [
                    'pers_nombre' => $datos['nombre'],
                    'pers_primer_apellido' => $datos['primer_apellido'],
                    'pers_segundo_apellido' => $datos['segundo_apellido'] ?? null,
                ];

                $existente = Comunicacion::correos()->conValor($correo)->lockForUpdate()->first();

                if ($existente?->pers_id_persona) {
                    if (Usuario::where('pers_id_persona', $existente->pers_id_persona)->exists()) {
                        throw ValidationException::withMessages(['correo' => 'Este correo ya está registrado.']);
                    }

                    $persona = Persona::lockForUpdate()->findOrFail($existente->pers_id_persona);
                    $persona->forceFill($nombre)->save();

                    // El trigger exige que el usuario tenga un correo activo.
                    if (! $existente->comu_activo) {
                        $existente->forceFill(['comu_activo' => true])->save();
                    }
                } else {
                    $persona = Persona::create($nombre);

                    Comunicacion::create([
                        'pers_id_persona' => $persona->pers_id_persona,
                        'comu_id_tipo_comunicacion' => $this->idTipoCorreo(),
                        'comu_valor_comunicacion' => $correo,
                        'comu_etiqueta' => 'Principal',
                        'comu_activo' => true,
                    ]);
                }

                $usuario = Usuario::create([
                    'pers_id_persona' => $persona->pers_id_persona,
                    'usua_nombre_usuario' => filled($datos['nombre_usuario'] ?? null)
                        ? trim($datos['nombre_usuario'])
                        : $this->generarNombreUsuario($correo),
                    'usua_clave_acceso' => Hash::make($datos['clave']),
                    'usua_fecha_registro' => today(),
                    'usua_activo' => false,
                ]);

                // Un coautor ya tiene registro de autor: se conserva y se actualiza.
                Autor::updateOrCreate(
                    ['pers_id_persona' => $persona->pers_id_persona],
                    [
                        'auto_institucion' => $datos['institucion'],
                        'auto_grado_academico' => $datos['grado_academico'] ?? null,
                    ],
                );

                RolUsuario::create([
                    'usua_id_usuario' => $usuario->usua_id_usuario,
                    'rol_id_rol' => Rol::where('rol_nombre', Rol::AUTOR)->value('rol_id_rol'),
                ]);

                return $usuario;
            });
        } catch (UniqueConstraintViolationException $e) {
            // Otro registro ganó la carrera entre la validación y el INSERT.
            throw ValidationException::withMessages(
                str_contains($e->getMessage(), 'uq_comunicacion_valor')
                    ? ['correo' => 'Este correo ya está registrado.']
                    : ['nombre_usuario' => 'Este nombre de usuario ya está en uso.']
            );
        }

        Mail::to($correo)->queue(new CuentaRecibida($datos['nombre']));

        return $usuario;
    }

    /**
     * Activa la cuenta de un autor y le avisa por correo.
     *
     * @return bool false si la cuenta ya estaba activa.
     */
    public function activar(Usuario $usuario, Usuario $administrador): bool
    {
        if ($usuario->usua_activo) {
            return false;
        }

        $usuario->forceFill(['usua_activo' => true])->save();

        Log::channel('admin')->info('Cuenta de autor activada', [
            'administrador' => $administrador->usua_nombre_usuario,
            'usuario' => $usuario->usua_nombre_usuario,
            'usua_id_usuario' => $usuario->usua_id_usuario,
        ]);

        $usuario->loadMissing('persona.correo');
        if ($correo = $usuario->correo()) {
            Mail::to($correo)->queue(new CuentaActivada($usuario->persona->pers_nombre, $correo));
        }

        return true;
    }

    /**
     * Cuentas con rol Autor para la lista del administrador.
     *
     * @param  'pendiente'|'activa'|null  $estatus
     */
    public function listar(?string $estatus, ?string $texto, int $porPagina): LengthAwarePaginator
    {
        return Usuario::query()
            ->whereHas('roles', fn (Builder $q) => $q->where('rol_nombre', Rol::AUTOR))
            ->when($estatus === 'pendiente', fn (Builder $q) => $q->where('usua_activo', false))
            ->when($estatus === 'activa', fn (Builder $q) => $q->where('usua_activo', true))
            ->when(filled($texto), function (Builder $q) use ($texto) {
                $patron = BuscadorService::patronLike($texto);
                $q->where(fn (Builder $w) => $w
                    ->whereRaw(BuscadorService::sinAcentos('usua_nombre_usuario').' like ?', [$patron])
                    ->orWhereHas('persona', fn (Builder $p) => $p->whereRaw(
                        BuscadorService::sinAcentos("concat_ws(' ', pers_nombre, pers_primer_apellido, pers_segundo_apellido)").' like ?',
                        [$patron],
                    ))
                    ->orWhereHas('persona.comunicaciones', fn (Builder $c) => $c
                        ->correos()
                        ->whereRaw('lower(comu_valor_comunicacion) like ?', [$patron])));
            })
            ->with(['persona.correo', 'persona.autor'])
            ->orderBy('usua_activo')
            ->orderByDesc('usua_fecha_registro')
            ->orderByDesc('usua_id_usuario')
            ->paginate($porPagina)
            ->withQueryString();
    }

    public function esAutor(Usuario $usuario): bool
    {
        return $usuario->roles()->where('rol_nombre', Rol::AUTOR)->exists();
    }

    /**
     * ¿El correo ya pertenece a una cuenta de usuario? Un correo de alguien
     * sin cuenta (suscriptor o coautor) sí puede registrarse.
     */
    public function correoRegistrado(string $correo): bool
    {
        return Comunicacion::correos()
            ->conValor(trim($correo))
            ->whereExists(fn ($q) => $q->selectRaw('1')->from('usuario')
                ->whereColumn('usuario.pers_id_persona', 'comunicacion.pers_id_persona'))
            ->exists();
    }

    public function nombreUsuarioEnUso(string $nombre): bool
    {
        return Usuario::whereRaw('lower(usua_nombre_usuario) = lower(?)', [trim($nombre)])->exists();
    }

    /**
     * Nombre de usuario a partir de la parte local del correo
     * (letras, números, punto, guion y guion bajo), único sin importar mayúsculas.
     */
    public function generarNombreUsuario(string $correo): string
    {
        $base = Str::of(Str::before($correo, '@'))
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9._-]/', '')
            ->substr(0, 45)
            ->toString();

        $base = $base !== '' ? $base : 'autor';
        $candidato = $base;

        for ($i = 2; $this->nombreUsuarioEnUso($candidato); $i++) {
            $candidato = $base.$i;
        }

        return $candidato;
    }

    private function idTipoCorreo(): int
    {
        return (int) TipoComunicacion::correo()->value('comu_id_tipo_comunicacion');
    }
}
