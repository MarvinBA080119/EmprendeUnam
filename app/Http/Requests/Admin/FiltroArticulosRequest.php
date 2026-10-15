<?php

namespace App\Http\Requests\Admin;

use App\Enums\EstatusArticulo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FiltroArticulosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'estatus' => ['nullable', Rule::enum(EstatusArticulo::class)],
            'tema' => ['nullable', 'integer', 'exists:tema,tema_id_tema'],
            'desde' => ['nullable', 'date_format:Y-m-d'],
            'hasta' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:desde'],
            'q' => ['nullable', 'string', 'max:200'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['desde' => 'fecha inicial', 'hasta' => 'fecha final', 'q' => 'búsqueda'];
    }
}
