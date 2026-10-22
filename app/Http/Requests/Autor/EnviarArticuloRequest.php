<?php

namespace App\Http\Requests\Autor;

use App\Services\PalabraClaveService;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

class EnviarArticuloRequest extends FormRequest
{
    public const PALABRAS = 5;

    /** Lineamientos: hasta dos personas autoras (el autor principal y 1 coautor). */
    public const MAX_COAUTORES = 1;

    /** Lineamientos: resumen de máximo 50 palabras (columna arti_resumen, varchar 500). */
    public const MAX_PALABRAS_RESUMEN = 50;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['tiene_coautores' => $this->boolean('tiene_coautores')]);

        if (! $this->boolean('tiene_coautores')) {
            $this->request->remove('coautores');
        }
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:200'],
            'resumen' => [
                'required', 'string', 'max:500',
                function (string $atributo, mixed $valor, Closure $falla) {
                    if (is_string($valor) && self::contarPalabras($valor) > self::MAX_PALABRAS_RESUMEN) {
                        $falla('El resumen no debe tener más de '.self::MAX_PALABRAS_RESUMEN.' palabras.');
                    }
                },
            ],
            'tema_id_tema' => ['required', 'integer', 'exists:tema,tema_id_tema'],
            'palabras' => ['required', 'array', 'size:'.self::PALABRAS],
            'palabras.*' => ['required', 'string', 'max:100'],
            'tiene_coautores' => ['boolean'],
            'coautores' => ['exclude_unless:tiene_coautores,true', 'required', 'array', 'min:1', 'max:'.self::MAX_COAUTORES],
            'coautores.*.nombre' => ['exclude_unless:tiene_coautores,true', 'required', 'string', 'max:100'],
            'coautores.*.primer_apellido' => ['exclude_unless:tiene_coautores,true', 'required', 'string', 'max:100'],
            'coautores.*.segundo_apellido' => ['exclude_unless:tiene_coautores,true', 'nullable', 'string', 'max:100'],
            'coautores.*.correo' => ['exclude_unless:tiene_coautores,true', 'required', 'string', 'email:rfc', 'max:254', 'distinct:ignore_case'],
            'coautores.*.institucion' => ['exclude_unless:tiene_coautores,true', 'required', 'string', 'max:100'],
            'pdf' => $this->reglasPdf(),
            'carta_cesion' => $this->reglasPdf(),
        ];
    }

    /** Reglas que dependen de varios campos. */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $palabras = $this->input('palabras');
                if (is_array($palabras) && count($palabras) === self::PALABRAS
                    && ! $validator->errors()->has('palabras.*')) {
                    $normalizadas = app(PalabraClaveService::class)->normalizarLista($palabras);
                    if (count($normalizadas) !== self::PALABRAS) {
                        $validator->errors()->add('palabras', 'Las 5 palabras clave deben ser distintas.');
                    }
                }

                $propio = Str::lower((string) $this->user()?->correo());
                foreach ((array) $this->input('coautores', []) as $i => $coautor) {
                    if ($propio !== '' && Str::lower(trim((string) ($coautor['correo'] ?? ''))) === $propio) {
                        $validator->errors()->add("coautores.$i.correo", 'No puedes registrarte como tu propio coautor.');
                    }
                }
            },
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'palabras.size' => 'Escribe exactamente 5 palabras clave.',
            'palabras.required' => 'Escribe exactamente 5 palabras clave.',
            'palabras.*.required' => 'Escribe las 5 palabras clave.',
            'coautores.required' => 'Agrega al menos un coautor o desmarca la casilla «¿Tiene coautores?».',
            'coautores.*.correo.distinct' => 'Este correo está repetido entre los coautores.',
            'coautores.max' => 'Solo se admite un coautor por artículo (hasta dos personas autoras).',
            'pdf.mimetypes' => 'El archivo debe ser un PDF.',
            'pdf.max' => 'El PDF no debe pesar más de '.round(config('emprende.pdf_max_kb') / 1024).' MB.',
            'carta_cesion.required' => 'Sube la carta de cesión de derechos firmada, en PDF.',
            'carta_cesion.mimetypes' => 'La carta de cesión debe ser un PDF.',
            'carta_cesion.max' => 'La carta de cesión no debe pesar más de '.round(config('emprende.pdf_max_kb') / 1024).' MB.',
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'titulo' => 'título',
            'resumen' => 'resumen',
            'tema_id_tema' => 'tema',
            'palabras.*' => 'palabra clave',
            'coautores.*.nombre' => 'nombre del coautor',
            'coautores.*.primer_apellido' => 'primer apellido del coautor',
            'coautores.*.segundo_apellido' => 'segundo apellido del coautor',
            'coautores.*.correo' => 'correo del coautor',
            'coautores.*.institucion' => 'institución del coautor',
            'pdf' => 'archivo PDF',
            'carta_cesion' => 'carta de cesión',
        ];
    }

    public static function contarPalabras(string $texto): int
    {
        $texto = trim($texto);

        return $texto === '' ? 0 : count(preg_split('/\s+/u', $texto));
    }

    /** PDF real: tipo por contenido (finfo), firma %PDF y tamaño máximo. */
    private function reglasPdf(): array
    {
        return [
            'required', 'file', 'mimetypes:application/pdf', 'max:'.config('emprende.pdf_max_kb'),
            function (string $atributo, mixed $archivo, Closure $falla) {
                if ($archivo instanceof UploadedFile && ! $this->empiezaConPdf($archivo)) {
                    $falla('El archivo no es un PDF válido.');
                }
            },
        ];
    }

    private function empiezaConPdf(UploadedFile $archivo): bool
    {
        $manejador = @fopen($archivo->getRealPath(), 'rb');
        if ($manejador === false) {
            return false;
        }
        $inicio = fread($manejador, 4);
        fclose($manejador);

        return $inicio === '%PDF';
    }
}
