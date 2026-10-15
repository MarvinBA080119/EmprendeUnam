<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RechazarArticuloRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'comentario' => ['required', 'string', 'min:20', 'max:350'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['comentario' => 'motivo del rechazo'];
    }
}
