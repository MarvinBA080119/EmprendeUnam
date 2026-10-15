<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FiltroAutoresRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'estatus' => ['nullable', 'in:pendiente,activa'],
            'texto' => ['nullable', 'string', 'max:100'],
        ];
    }
}
