<?php

namespace App\Http\Requests\Publico;

use App\Models\Tema;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SuscribirNewsletterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],
            'apellido' => ['required', 'string', 'max:100'],
            'correo' => ['required', 'string', 'email:rfc', 'max:254'],
            // Uno de los 13 temas; se guarda el nombre en tene_tema_interes.
            'tema' => ['required', 'string', Rule::in(Tema::pluck('tema_nombre')->all())],
            'acepta' => ['accepted'],
            'sitio_web' => ['nullable', 'string', 'max:200'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'acepta.accepted' => 'Para suscribirte es necesario aceptar el aviso de privacidad.',
            'tema.in' => 'Elige uno de los temas de la lista.',
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['tema' => 'tema de interés', 'correo' => 'correo electrónico'];
    }

    public function esRobot(): bool
    {
        return $this->filled('sitio_web');
    }
}
