<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AvisarEdicionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:150'],
            'url' => ['required', 'string', 'max:256', 'url:https'],
            'confirmo' => ['accepted'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'url.url' => 'La liga debe ser una dirección válida que empiece con https://.',
            'confirmo.accepted' => 'Confirma que la edición ya está publicada.',
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['titulo' => 'título de la edición', 'url' => 'liga al newsletter'];
    }
}
