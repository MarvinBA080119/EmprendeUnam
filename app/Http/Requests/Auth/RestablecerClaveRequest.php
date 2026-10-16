<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RestablecerClaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'correo' => ['required', 'string', 'email', 'max:254'],
            'clave' => ['required', 'string', 'min:8', 'max:72', 'confirmed'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'correo.required' => 'Escribe tu correo electrónico.',
            'correo.email' => 'Escribe un correo válido, por ejemplo nombre@unam.mx.',
            'clave.required' => 'Escribe la nueva contraseña.',
            'clave.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'clave.confirmed' => 'Las contraseñas no coinciden.',
        ];
    }
}
