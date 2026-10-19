<?php

namespace App\Http\Requests\Publico;

use Illuminate\Foundation\Http\FormRequest;

class EnviarMensajeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:75'],
            'correo' => ['required', 'string', 'email:rfc', 'max:254'],
            'lugar' => ['nullable', 'string', 'max:100'],
            'asunto' => ['required', 'string', 'max:50'],
            'mensaje' => ['required', 'string', 'max:700'],
            // Campo trampa (honeypot): las personas no lo ven; los robots lo llenan.
            'sitio_web' => ['nullable', 'string', 'max:200'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'nombre' => 'nombre completo',
            'correo' => 'correo electrónico',
            'lugar' => 'ciudad o sector',
            'asunto' => 'asunto de la consulta',
            'mensaje' => 'descripción del caso',
        ];
    }

    public function esRobot(): bool
    {
        return $this->filled('sitio_web');
    }
}
