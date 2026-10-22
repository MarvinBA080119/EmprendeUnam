<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Artículo de un número: ligado a un artículo aprobado del sistema (título y
 * autores pueden quedar vacíos y se toman de él) o entrada editorial.
 */
class ArticuloNumeroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $delSistema = $this->filled('arti_id_autor');

        return [
            'arti_id_autor' => ['nullable', 'integer', Rule::exists('articulo', 'arti_id_autor')],
            'seccion' => ['nullable', 'string', 'max:60'],
            'titulo' => [$delSistema ? 'nullable' : 'required', 'string', 'max:200'],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'autores' => [$delSistema ? 'nullable' : 'required', 'string', 'max:300'],
            'paginas' => ['nullable', 'string', 'max:15', 'regex:/^[0-9]+(\s*-\s*[0-9]+)?$/'],
            'pdf' => ['nullable', 'file', 'mimetypes:application/pdf', 'max:'.config('emprende.pdf_max_kb')],
            'imagen' => ['nullable', 'file', 'mimetypes:image/jpeg,image/png,image/webp', 'max:'.NumeroRequest::IMAGEN_MAX_KB],
            'cursos' => ['array'],
            'cursos.*' => ['integer', Rule::exists('curso', 'curs_id_curso')],
            'sugeridos' => ['array'],
            'sugeridos.*' => ['integer'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'titulo.required' => 'Escribe el título o elige un artículo aprobado del sistema.',
            'autores.required' => 'Escribe las personas autoras o elige un artículo aprobado del sistema.',
            'paginas.regex' => 'Escribe las páginas como 12 o 12-19.',
            'imagen.mimetypes' => 'La imagen debe ser JPG, PNG o WebP.',
            'pdf.mimetypes' => 'El archivo del artículo debe ser un PDF.',
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'arti_id_autor' => 'artículo del sistema',
            'seccion' => 'sección',
            'titulo' => 'título',
            'descripcion' => 'descripción',
            'paginas' => 'páginas',
        ];
    }
}
