#!/usr/bin/env bash
# Recrea la base de pruebas (emprendeunam_test) con el esquema, los datos
# iniciales y las tablas técnicas de Laravel. Las pruebas PHPUnit trabajan
# dentro de transacciones que se revierten, así que basta con correr esto una
# vez o cuando cambien los scripts SQL.
set -euo pipefail
RAIZ="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

"$RAIZ/scripts/crear_bd.sh" emprendeunam_test --forzar

echo "==> Migraciones técnicas de Laravel en emprendeunam_test"
cd "$RAIZ"
DB_DATABASE=emprendeunam_test php artisan migrate --force --no-interaction
