<?php

namespace App\Services;

use App\Models\Curso;
use App\Models\PalabraCurso;
use App\Models\Usuario;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Cursos (6.7): alta, edición y baja con sus palabras clave (palabra_curso).
 */
class CursoService
{
    public function __construct(private PalabraClaveService $palabras) {}

    /** @param  'vigentes'|'pasados'|null  $vigencia */
    public function listar(?string $vigencia, int $porPagina): LengthAwarePaginator
    {
        return Curso::query()
            ->when($vigencia === 'vigentes', fn ($q) => $q->vigentes())
            ->when($vigencia === 'pasados', fn ($q) => $q->whereDate('curs_fecha', '<', today()))
            ->with('palabras')
            ->orderByDesc('curs_fecha')
            ->orderByDesc('curs_id_curso')
            ->paginate($porPagina)
            ->withQueryString();
    }

    /** @param  array{nombre: string, institucion: string, fecha: string, url: string, palabras: list<?string>}  $datos */
    public function crear(array $datos, ?Usuario $administrador = null): Curso
    {
        $curso = DB::transaction(function () use ($datos) {
            $curso = Curso::create($this->columnas($datos));
            $this->palabras->asignarACurso($curso, $datos['palabras']);

            return $curso;
        });

        $this->registrar('Curso publicado', $curso, $administrador);

        return $curso;
    }

    /** @param  array{nombre: string, institucion: string, fecha: string, url: string, palabras: list<?string>}  $datos */
    public function actualizar(Curso $curso, array $datos, ?Usuario $administrador = null): Curso
    {
        DB::transaction(function () use ($curso, $datos) {
            $curso->update($this->columnas($datos));
            $this->palabras->asignarACurso($curso, $datos['palabras']);
        });

        $this->registrar('Curso modificado', $curso, $administrador);

        return $curso;
    }

    /** Borra primero sus palabra_curso (las FK son ON DELETE RESTRICT). */
    public function eliminar(Curso $curso, ?Usuario $administrador = null): void
    {
        DB::transaction(function () use ($curso) {
            PalabraCurso::where('curs_id_curso', $curso->curs_id_curso)->delete();
            $curso->delete();
        });

        $this->registrar('Curso eliminado', $curso, $administrador);
    }

    private function registrar(string $accion, Curso $curso, ?Usuario $administrador): void
    {
        Log::channel('admin')->info($accion, [
            'administrador' => $administrador?->usua_nombre_usuario,
            'curs_id_curso' => $curso->curs_id_curso,
            'curso' => $curso->curs_nombre_curso,
        ]);
    }

    /** @return array<string, mixed> */
    private function columnas(array $datos): array
    {
        return [
            'curs_nombre_curso' => $datos['nombre'],
            'curs_institucion' => $datos['institucion'],
            'curs_fecha' => $datos['fecha'],
            'curs_url' => $datos['url'],
        ];
    }
}
