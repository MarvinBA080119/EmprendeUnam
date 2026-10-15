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
        ];
    }
}
