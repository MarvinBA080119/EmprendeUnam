<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'acceso' => ['required', 'string', 'max:254'],
            'clave' => ['required', 'string', 'max:255'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'acceso.required' => 'Escribe tu correo o tu nombre de usuario.',
            'clave.required' => 'Escribe tu contraseña.',
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'acceso' => 'correo o nombre de usuario',
            'clave' => 'contraseña',
        ];
    }
}
