<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SolicitarClaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'correo' => ['required', 'string', 'email', 'max:254'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'correo.required' => 'Escribe el correo con el que te registraste.',
            'correo.email' => 'Escribe un correo válido, por ejemplo nombre@unam.mx.',
        ];
    }
}
