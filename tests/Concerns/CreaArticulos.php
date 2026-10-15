<?php

namespace Tests\Concerns;

use App\Models\Articulo;
use App\Models\Tema;
use App\Models\Usuario;
use App\Services\ArticuloService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;

trait CreaArticulos
{
    protected function pdfDePrueba(string $nombre = 'articulo.pdf', int $kb = 0): UploadedFile
    {
        $contenido = "%PDF-1.4\n1 0 obj << /Type /Catalog >> endobj\n".str_repeat('0', $kb * 1024)."\n%%EOF\n";

        return UploadedFile::fake()->createWithContent($nombre, $contenido);
    }

    /** @return array<string, mixed> */
    protected function datosArticulo(array $cambios = []): array
    {
        return array_merge([
            'titulo' => 'Innovación social en las pymes mexicanas',
            'resumen' => 'Cómo las pequeñas empresas mexicanas generan impacto social con modelos de negocio sostenibles.',
            'tema_id_tema' => Tema::where('tema_nombre', 'Emprendimiento social')->value('tema_id_tema'),
            'palabras' => ['Innovación', 'pymes', 'emprendimiento social', 'México', 'redes'],
            'tiene_coautores' => false,
        ], $cambios);
    }

    /** Envía un artículo directamente con el servicio (Storage debe estar en fake). */
    protected function enviarArticulo(Usuario $autor, array $cambios = []): Articulo
    {
        Mail::fake();

        return app(ArticuloService::class)->enviar($autor, $this->datosArticulo($cambios), $this->pdfDePrueba());
    }
}
