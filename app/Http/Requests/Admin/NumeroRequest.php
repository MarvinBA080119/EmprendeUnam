<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/** Alta o cambio de un número de la revista (gestor de portada). */
class NumeroRequest extends FormRequest
{
    /** Tamaño máximo de la portada, en KB. */
    public const IMAGEN_MAX_KB = 5120;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['visible' => $this->boolean('visible')]);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $creando = $this->route('revista') === null;

        return [
            'titulo' => ['required', 'string', 'max:60'],
            'numero' => ['required', 'string', 'max:3', 'regex:/^[0-9]+$/'],
            'anio' => ['required', 'digits:4'],
            'periodo' => ['required', 'string', 'max:20'],
            'fecha_publicacion' => ['required', 'date'],
            'flipbook' => ['nullable', 'url:https', 'max:256'],
            'visible' => ['boolean'],
            'portada' => [$creando ? 'required' : 'nullable', 'file', 'mimetypes:image/jpeg,image/png,image/webp', 'max:'.self::IMAGEN_MAX_KB],
            'pdf' => [$creando ? 'required' : 'nullable', 'file', 'mimetypes:application/pdf', 'max:'.config('emprende.pdf_max_revista_kb')],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'numero.regex' => 'El número solo lleva dígitos (por ejemplo 220).',
            'periodo.max' => 'El periodo admite hasta 20 caracteres (por ejemplo «septiembre-octubre»).',
            'flipbook.url' => 'El enlace del Flipbook debe empezar con https://',
            'portada.required' => 'Sube la portada del número.',
            'portada.mimetypes' => 'La portada debe ser una imagen JPG, PNG o WebP.',
            'pdf.required' => 'Sube el PDF completo del número.',
            'pdf.mimetypes' => 'El número completo debe ser un PDF.',
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'titulo' => 'título',
            'numero' => 'número',
            'anio' => 'año',
            'fecha_publicacion' => 'fecha de publicación',
            'pdf' => 'PDF completo',
        ];
    }
}
