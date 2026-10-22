<?php

namespace App\Support;

use ZipArchive;

/**
 * Manuscrito del autor: PDF o Word (.docx). El tipo se reconoce por el
 * contenido, no por la extensión: un PDF empieza con «%PDF»; un .docx es un
 * ZIP con word/document.xml y sin macros (vbaProject.bin). El .doc antiguo no
 * se acepta porque puede traer macros.
 */
class Manuscrito
{
    public const TIPOS = [
        'pdf' => 'application/pdf',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];

    /** Extensión real («pdf» o «docx») o null si el archivo no es un manuscrito válido. */
    public static function detectar(string $ruta): ?string
    {
        $manejador = @fopen($ruta, 'rb');
        if ($manejador === false) {
            return null;
        }
        $inicio = (string) fread($manejador, 4);
        fclose($manejador);

        if ($inicio === '%PDF') {
            return 'pdf';
        }

        if ($inicio === "PK\x03\x04" && class_exists(ZipArchive::class)) {
            $zip = new ZipArchive;
            if ($zip->open($ruta, ZipArchive::RDONLY) !== true) {
                return null;
            }
            $esWord = $zip->locateName('word/document.xml') !== false
                && $zip->locateName('word/vbaProject.bin') === false;
            $zip->close();

            return $esWord ? 'docx' : null;
        }

        return null;
    }

    public static function tipoContenido(string $ruta): string
    {
        return self::TIPOS[strtolower(pathinfo($ruta, PATHINFO_EXTENSION))] ?? 'application/octet-stream';
    }

    /** Nombre de descarga con la extensión del archivo guardado («articulo-12.docx»). */
    public static function nombreDescarga(string $ruta, int|string $id): string
    {
        return "articulo-{$id}.".(strtolower(pathinfo($ruta, PATHINFO_EXTENSION)) ?: 'pdf');
    }
}
