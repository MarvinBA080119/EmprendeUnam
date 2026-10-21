<?php

namespace Tests\Unit;

use App\Support\Texto;
use PHPUnit\Framework\TestCase;

class TextoTest extends TestCase
{
    public function test_cantidad_en_singular_y_plural(): void
    {
        $this->assertSame('1 artículo', Texto::cantidad(1, 'artículo', 'artículos'));
        $this->assertSame('0 artículos', Texto::cantidad(0, 'artículo', 'artículos'));
        $this->assertSame('4 artículos', Texto::cantidad(4, 'artículo', 'artículos'));
    }

    public function test_lista_con_comas_y_y(): void
    {
        $this->assertSame('a', Texto::lista(['a']));
        $this->assertSame('a y b', Texto::lista(['a', null, 'b']));
        $this->assertSame('a, b y c', Texto::lista(['a', 'b', 'c']));
    }
}
