<?php

namespace App\Http\Requests\Publico;

use Illuminate\Foundation\Http\FormRequest;

class BuscarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:100'],
            'tipo' => ['nullable', 'in:articulo,curso'],
            'tema' => ['nullable', 'integer', 'exists:tema,tema_id_tema'],
            'anio' => ['nullable', 'integer', 'between:1990,2100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['q' => 'búsqueda', 'anio' => 'año'];
    }
}
