<?php

namespace App\Http\Requests\Publico;

use Illuminate\Foundation\Http\FormRequest;

class SuscribirNewsletterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * El tema es texto libre: se quitan etiquetas y caracteres de control y se
     * compactan los espacios (como hacía el newsletter del sitio original).
     */
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('tema'))) {
            $tema = preg_replace('/\s+/u', ' ', strip_tags($this->input('tema')));
            $this->merge(['tema' => trim((string) preg_replace('/[\x00-\x1F\x7F]/u', '', (string) $tema))]);
        }
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],
            'apellido' => ['required', 'string', 'max:100'],
            'correo' => ['required', 'string', 'email:rfc', 'max:254'],
            // Texto libre de la persona lectora (tene_tema_interes, varchar 256).
            'tema' => ['required', 'string', 'min:2', 'max:120'],
            'acepta' => ['accepted'],
            'sitio_web' => ['nullable', 'string', 'max:200'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'acepta.accepted' => 'Para suscribirte es necesario aceptar el aviso de privacidad.',
            'tema.required' => 'Cuéntanos qué tema te interesa.',
            'tema.min' => 'Escribe un poco más sobre el tema que te interesa.',
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
