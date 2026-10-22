<?php

namespace App\Support;

use Illuminate\Database\QueryException;

/**
 * Traduce las violaciones de reglas de la base a mensajes claros (sección 8):
 * 23505 duplicado, 23514 regla de negocio, 23001/23503 registro en uso.
 *
 * Los formularios validan antes de escribir; esto cubre lo que solo la base
 * puede garantizar (p. ej. dos envíos simultáneos con el mismo correo).
 */
class ErrorBaseDatos
{
    public const DUPLICADO = '23505';

    public const REGLA = '23514';

    public const RESTRICT = '23001';

    public const LLAVE_FORANEA = '23503';

    /** Restricción (o texto del trigger) => mensaje. Se busca en el mensaje de PostgreSQL. */
    private const MENSAJES = [
        self::DUPLICADO => [
            'uq_comunicacion_valor' => 'Este correo ya está registrado.',
            'uq_usuario_nombre' => 'Ese nombre de usuario ya está en uso.',
            'uq_palabra_articulo' => 'Las palabras clave no deben repetirse.',
            'uq_palabra_curso' => 'Las palabras clave no deben repetirse.',
            'uq_palabra_descripcion' => 'Esa palabra clave ya existe.',
            'uq_autor_principal' => 'El artículo ya tiene un autor principal.',
            'uq_autor_articulo' => 'Esa persona ya figura como autora del artículo.',
            'uq_constancia_articulo_tipo' => 'Ese documento del artículo ya fue generado.',
            'uq_revista_en_portada' => 'Ya hay otro número en portada.',
            'uq_arre_posicion' => 'Dos artículos no pueden ocupar la misma posición del índice.',
            'uq_arre_articulo' => 'Ese artículo ya forma parte del número.',
            'uq_arcu' => 'Ese curso ya está ligado al artículo.',
            'uq_constancia_folio' => 'El folio de la constancia ya existe. Intenta de nuevo.',
            'uq_persona_token' => 'No pudimos generar el enlace de confirmación. Intenta de nuevo.',
            'uq_tema_nombre' => 'Ese tema ya existe.',
        ],
        self::REGLA => [
            'ya tiene 5 palabras clave' => 'Un artículo admite como máximo 5 palabras clave.',
            'ck_arti_rechazo' => 'Para rechazar un artículo hay que escribir el motivo.',
            'ck_arti_status' => 'El estado del artículo no es válido.',
            'ck_cons_folio_segun_tipo' => 'La carta de cesión no lleva folio; la constancia y la carta de agradecimiento sí.',
            'ck_cons_tipo' => 'El tipo de documento no es válido.',
            'ck_revi_portada_visible' => 'Solo un número visible puede estar en portada.',
            'ck_arre_posicion' => 'La posición en el índice va de 1 a 5.',
            'ck_arre_en_index_con_fecha' => 'Un artículo del índice necesita la fecha en que entró.',
            'solo un artículo aprobado puede formar parte de una edición' => 'Solo un artículo aprobado puede formar parte de un número.',
            'ck_mens_status' => 'El estado del mensaje no es válido.',
            'ck_mens_correo' => 'El correo no tiene un formato válido.',
            'no tiene un formato válido' => 'El correo no tiene un formato válido.',
            'ck_pers_nombre' => 'El nombre y el primer apellido son obligatorios.',
            'ck_pers_newsletter' => 'La suscripción al newsletter está incompleta.',
            'ck_pers_baja' => 'La fecha de baja no puede ser anterior a la de suscripción.',
            'ck_tene_tema' => 'Escribe el tema que te interesa.',
            'no tiene tema de interés' => 'Escribe el tema que te interesa.',
            'no tiene correo electrónico' => 'La suscripción necesita un correo electrónico.',
            'no tiene un correo electrónico activo' => 'Toda cuenta necesita un correo electrónico activo.',
        ],
    ];

    private const GENERICOS = [
        self::DUPLICADO => 'Ese registro ya existe.',
        self::REGLA => 'La operación no cumple una regla de la revista.',
        self::RESTRICT => 'No se puede eliminar: el registro está en uso.',
        self::LLAVE_FORANEA => 'No se puede eliminar: el registro está en uso.',
    ];

    /** Mensaje para el usuario, o null si el error no es una regla de la base que se traduzca. */
    public static function mensaje(QueryException $e): ?string
    {
        $codigo = self::codigo($e);

        if (! isset(self::GENERICOS[$codigo])) {
            return null;
        }

        $texto = mb_strtolower($e->getMessage());

        foreach (self::MENSAJES[$codigo] ?? [] as $clave => $mensaje) {
            if (str_contains($texto, $clave)) {
                return $mensaje;
            }
        }

        // 23503 al insertar o actualizar: el registro relacionado ya no existe.
        if ($codigo === self::LLAVE_FORANEA && str_contains($texto, 'is not present in table')) {
            return 'Uno de los datos relacionados ya no existe. Recarga la página e intenta de nuevo.';
        }

        return self::GENERICOS[$codigo];
    }

    /** SQLSTATE de la excepción (p. ej. '23505'). */
    public static function codigo(QueryException $e): string
    {
        $info = $e->errorInfo ?? null;

        return (string) (is_array($info) && isset($info[0]) ? $info[0] : $e->getCode());
    }
}
