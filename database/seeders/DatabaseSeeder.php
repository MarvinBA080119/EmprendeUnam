<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Los catálogos y el administrador se cargan con database/datos_iniciales.sql,
     * no con seeders. Aquí solo se agregarán datos de demostración para
     * desarrollo local (DatosDemoSeeder), nunca en otros entornos.
     */
    public function run(): void
    {
        //
    }
}
