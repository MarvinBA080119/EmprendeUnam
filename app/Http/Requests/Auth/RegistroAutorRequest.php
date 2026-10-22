<?php

namespace App\Http\Requests\Auth;

use App\Models\Autor;
use App\Services\RegistroAutorService;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegistroAutorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $servicio = app(RegistroAutorService::class);

        return [
            'nombre' => ['required', 'string', 'max:100'],
            'primer_apellido' => ['required', 'string', 'max:100'],
            'segundo_apellido' => ['nullable', 'string', 'max:100'],
            'correo' => [
                'required', 'string', 'email:rfc', 'max:254',
                function (string $atributo, mixed $valor, Closure $falla) use ($servicio) {
                    if (is_string($valor) && $servicio->correoRegistrado($valor)) {
                        $falla('Este correo ya está registrado.');
                    }
                },
            ],
            // bcrypt solo toma en cuenta los primeros 72 bytes.
            'clave' => ['required', 'string', 'min:8', 'max:72', 'confirmed'],
            'institucion' => ['required', 'string', 'max:100'],
            'grado_academico' => ['nullable', 'string', Rule::in(Autor::GRADOS)],
            'nombre_usuario' => [
                'nullable', 'string', 'min:3', 'max:50', 'regex:/^[A-Za-z0-9._-]+$/',
                function (string $atributo, mixed $valor, Closure $falla) use ($servicio) {
                    if (is_string($valor) && $servicio->nombreUsuarioEnUso($valor)) {
                        $falla('Este nombre de usuario ya está en uso.');
                    }
                },
            ],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'nombre.required' => 'Escribe tu nombre.',
            'primer_apellido.required' => 'Escribe tu primer apellido.',
            'institucion.required' => 'Escribe la institución donde trabajas o estudias.',
            'grado_academico.in' => 'Elige un grado académico de la lista.',
            'correo.required' => 'Escribe tu correo electrónico.',
            'correo.email' => 'Escribe un correo válido, por ejemplo nombre@unam.mx.',
            'clave.required' => 'Escribe una contraseña.',
            'clave.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'clave.confirmed' => 'Las contraseñas no coinciden.',
            'nombre_usuario.regex' => 'El nombre de usuario solo puede tener letras sin acento, números, punto, guion y guion bajo.',
        ];
    }
}
