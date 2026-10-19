<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

/**
 * Las pruebas corren contra emprendeunam_test (cargada con
 * scripts/preparar_bd_pruebas.sh). Cada prueba se ejecuta dentro de una
 * transacción que se revierte al terminar: nunca se usa RefreshDatabase.
 *
 * Los triggers diferidos (tr_usuario_con_correo, tr_newsletter_completa) solo
 * se revisan al COMMIT, que aquí nunca ocurre: las pruebas que los validan
 * ejecutan DB::statement('SET CONSTRAINTS ALL IMMEDIATE').
 */
abstract class TestCase extends BaseTestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $base = config('database.connections.'.config('database.default').'.database');
        if (! str_ends_with((string) $base, '_test')) {
            throw new RuntimeException("Las pruebas solo pueden correr en una base *_test (actual: {$base}).");
        }

        $this->withoutVite();
    }
}
