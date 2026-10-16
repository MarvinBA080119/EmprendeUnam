<?php

use Illuminate\Support\Facades\Schedule;

// Tareas programadas: requieren `php artisan schedule:run` cada minuto (ver README).

// Borra los enlaces de recuperación de contraseña ya vencidos.
Schedule::command('auth:clear-resets')->daily();
