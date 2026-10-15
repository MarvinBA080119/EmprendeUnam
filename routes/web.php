<?php

/*
| Rutas web. Este archivo solo incluye los módulos:
|   publico.php  sitio público (sin sesión)
|   auth.php     inicio de sesión, registro y recuperación de contraseña
| Los módulos con sesión (autor.php y admin.php) se registran en
| bootstrap/app.php con su prefijo y middleware.
*/

require __DIR__.'/publico.php';
require __DIR__.'/auth.php';
