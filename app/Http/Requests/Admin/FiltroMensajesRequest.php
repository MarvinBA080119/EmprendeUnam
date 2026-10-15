<?php

namespace App\Http\Requests\Admin;

use App\Enums\EstatusMensaje;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FiltroMensajesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'estatus' => ['nullable', Rule::enum(EstatusMensaje::class)],
            'q' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['q' => 'búsqueda'];
    }
}
