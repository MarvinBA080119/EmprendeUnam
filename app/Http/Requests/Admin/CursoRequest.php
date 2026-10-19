<?php

namespace App\Http\Requests\Admin;

use App\Services\PalabraClaveService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class CursoRequest extends FormRequest
{
    public const MAX_PALABRAS = 5;

    public function authorize(): bool
    {
        return true;
    }

    /** Descarta los campos de palabra clave que se dejaron vacíos. */
    protected function prepareForValidation(): void
    {
        $palabras = array_values(array_filter(
            (array) $this->input('palabras', []),
            fn ($p) => is_string($p) && trim($p) !== '',
        ));

        $this->merge(['palabras' => $palabras]);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],
            'institucion' => ['required', 'string', 'max:150'],
            'fecha' => ['required', 'date_format:Y-m-d'],
            'url' => ['required', 'string', 'max:256', 'url:https'],
            'palabras' => ['required', 'array', 'min:1', 'max:'.self::MAX_PALABRAS],
            'palabras.*' => ['string', 'max:100'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $palabras = (array) $this->input('palabras', []);
                if ($palabras !== [] && ! $validator->errors()->has('palabras.*')) {
                    $normalizadas = app(PalabraClaveService::class)->normalizarLista($palabras);
                    if (count($normalizadas) !== count($palabras)) {
                        $validator->errors()->add('palabras', 'Las palabras clave no deben repetirse.');
                    }
                }
            },
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'url.url' => 'El enlace debe ser una dirección válida que empiece con https://.',
            'palabras.required' => 'Escribe al menos una palabra clave.',
            'palabras.min' => 'Escribe al menos una palabra clave.',
            'palabras.max' => 'Escribe como máximo 5 palabras clave.',
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'nombre' => 'nombre del curso',
            'institucion' => 'institución',
            'fecha' => 'fecha',
            'url' => 'enlace',
            'palabras.*' => 'palabra clave',
        ];
    }
}
