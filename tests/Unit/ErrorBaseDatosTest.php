<?php

namespace Tests\Unit;

use App\Support\ErrorBaseDatos;
use Illuminate\Database\QueryException;
use PDOException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ErrorBaseDatosTest extends TestCase
{
    private function excepcion(string $sqlstate, string $mensaje): QueryException
    {
        $pdo = new PDOException("SQLSTATE[{$sqlstate}]: {$mensaje}");
        $pdo->errorInfo = [$sqlstate, 7, $mensaje];

        return new QueryException('pgsql', 'insert into x values (?)', [1], $pdo);
    }

    /** @return array<string, array{string, string, ?string}> */
    public static function casos(): array
    {
        return [
            'correo repetido' => ['23505', 'duplicate key value violates unique constraint "uq_comunicacion_valor"', 'Este correo ya está registrado.'],
            'usuario repetido (índice sin mayúsculas)' => ['23505', 'duplicate key value violates unique constraint "uq_usuario_nombre_ci"', 'Ese nombre de usuario ya está en uso.'],
            'constancia repetida' => ['23505', 'duplicate key value violates unique constraint "uq_constancia_articulo"', 'La constancia de este artículo ya fue generada.'],
            'duplicado sin traducción propia' => ['23505', 'duplicate key value violates unique constraint "otra"', 'Ese registro ya existe.'],
            'sexta palabra clave (trigger)' => ['23514', 'ERROR:  El artículo 7 ya tiene 5 palabras clave', 'Un artículo admite como máximo 5 palabras clave.'],
            'rechazo sin motivo' => ['23514', 'new row for relation "articulo" violates check constraint "ck_arti_rechazo"', 'Para rechazar un artículo hay que escribir el motivo.'],
            'correo inválido (trigger)' => ['23514', 'ERROR:  El correo "x" no tiene un formato válido', 'El correo no tiene un formato válido.'],
            'usuario sin correo (trigger diferido)' => ['23514', 'ERROR:  El usuario "x" no tiene un correo electrónico activo', 'Toda cuenta necesita un correo electrónico activo.'],
            'suscripción sin correo (trigger diferido)' => ['23514', 'ERROR:  La suscripción de la persona 3 no tiene correo electrónico', 'La suscripción necesita un correo electrónico.'],
            'suscripción sin tema (trigger diferido)' => ['23514', 'ERROR:  La suscripción de la persona 3 no tiene tema de interés', 'Escribe el tema que te interesa.'],
            'borrado restringido (PG18)' => ['23001', 'update or delete on table "tema" violates RESTRICT setting of foreign key constraint', 'No se puede eliminar: el registro está en uso.'],
            'borrado con llave foránea' => ['23503', 'update or delete on table "tema" violates foreign key constraint "fk" on table "articulo"', 'No se puede eliminar: el registro está en uso.'],
            'llave foránea al insertar' => ['23503', 'insert or update on table "articulo" violates foreign key constraint "fk". DETAIL: Key (tema_id_tema)=(99) is not present in table "tema".', 'Uno de los datos relacionados ya no existe. Recarga la página e intenta de nuevo.'],
            'otro error' => ['42P01', 'relation "x" does not exist', null],
        ];
    }

    #[DataProvider('casos')]
    public function test_traduce_el_sqlstate_a_un_mensaje_claro(string $sqlstate, string $mensaje, ?string $esperado): void
    {
        $e = $this->excepcion($sqlstate, $mensaje);

        $this->assertSame($sqlstate, ErrorBaseDatos::codigo($e));
        $this->assertSame($esperado, ErrorBaseDatos::mensaje($e));
    }
}
