#!/usr/bin/env bash
# ----------------------------------------------------------------------------
# Crea (o recrea) una base de la Revista EmprendeUNAM y carga, en este orden:
#   1. database/EMP_FINAL_corregido.sql  (esquema: 52 tablas, llaves, triggers)
#   2. database/datos_iniciales.sql      (catálogos y usuario administrador)
# Después verifica los conteos esperados.
#
# Uso:
#   DB_PASSWORD='...' scripts/crear_bd.sh [nombre_bd] [--forzar]
#
#   nombre_bd   por defecto: emprendeunam
#   --forzar    borra la base si ya existe (obligatorio para recrearla)
#
# Variables opcionales: DB_HOST (127.0.0.1), DB_PORT (5432), DB_USERNAME
# (emprende) y PSQL_SUPER (comando psql con superusuario; por defecto
# "sudo -u postgres psql", o "runuser -u postgres -- psql" si se corre como root).
# Si no se pasa DB_PASSWORD, se lee del archivo .env del proyecto.
# ----------------------------------------------------------------------------
set -euo pipefail

RAIZ="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

BD="emprendeunam"
FORZAR=0
for arg in "$@"; do
    case "$arg" in
        --forzar) FORZAR=1 ;;
        -*) echo "Opción desconocida: $arg" >&2; exit 1 ;;
        *) BD="$arg" ;;
    esac
done

leer_env() {
    [ -f "$RAIZ/.env" ] || return 0
    grep -E "^$1=" "$RAIZ/.env" | tail -n1 | cut -d= -f2- | sed -e 's/^"//' -e 's/"$//'
}

DB_HOST="${DB_HOST:-$(leer_env DB_HOST)}"; DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-$(leer_env DB_PORT)}"; DB_PORT="${DB_PORT:-5432}"
DB_USERNAME="${DB_USERNAME:-$(leer_env DB_USERNAME)}"; DB_USERNAME="${DB_USERNAME:-emprende}"
DB_PASSWORD="${DB_PASSWORD:-$(leer_env DB_PASSWORD)}"

if [ -z "$DB_PASSWORD" ]; then
    echo "Falta DB_PASSWORD (variable de entorno o archivo .env)." >&2
    exit 1
fi

if [ -z "${PSQL_SUPER:-}" ]; then
    if [ "$(id -u)" -eq 0 ]; then
        PSQL_SUPER="runuser -u postgres -- psql"
    else
        PSQL_SUPER="sudo -u postgres psql"
    fi
fi

super() { $PSQL_SUPER -p "$DB_PORT" -v ON_ERROR_STOP=1 -qtA "$@"; }

echo "==> Usuario de base de datos: $DB_USERNAME"
if [ "$(super -c "select 1 from pg_roles where rolname = '$DB_USERNAME'")" != "1" ]; then
    echo "create role :\"usuario\" login password :'clave';" |
        super -v usuario="$DB_USERNAME" -v clave="$DB_PASSWORD" >/dev/null
    echo "    creado"
else
    echo "alter role :\"usuario\" login password :'clave';" |
        super -v usuario="$DB_USERNAME" -v clave="$DB_PASSWORD" >/dev/null
    echo "    ya existía (contraseña actualizada)"
fi

echo "==> Base de datos: $BD"
if [ "$(super -c "select 1 from pg_database where datname = '$BD'")" = "1" ]; then
    if [ "$FORZAR" -ne 1 ]; then
        echo "La base '$BD' ya existe. Usa --forzar para borrarla y recrearla." >&2
        exit 1
    fi
    echo "drop database :\"bd\" with (force);" | super -v bd="$BD" >/dev/null
    echo "    borrada"
fi
echo "create database :\"bd\" owner :\"usuario\" encoding 'UTF8' template template0 locale_provider icu icu_locale 'es-MX' locale 'C.UTF-8';" |
    super -v bd="$BD" -v usuario="$DB_USERNAME" >/dev/null
echo "    creada (UTF-8, intercalación es-MX)"

# Los scripts se cargan con el usuario de la aplicación para que sea dueño de las tablas.
export PGPASSWORD="$DB_PASSWORD"
export PGOPTIONS="-c client_min_messages=warning"
app() { psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USERNAME" -d "$BD" -v ON_ERROR_STOP=1 -qtA "$@"; }

echo "==> Cargando database/EMP_FINAL_corregido.sql"
app -f "$RAIZ/database/EMP_FINAL_corregido.sql" >/dev/null
echo "==> Cargando database/datos_iniciales.sql"
app -f "$RAIZ/database/datos_iniciales.sql" >/dev/null

echo "==> Verificando"
fallo=0
verificar() {
    local etiqueta="$1" consulta="$2" esperado="$3" obtenido
    obtenido="$(app -c "$consulta")"
    if [ "$obtenido" = "$esperado" ]; then
        printf '    %-28s %s\n' "$etiqueta" "$obtenido"
    else
        printf '    %-28s %s (se esperaba %s)\n' "$etiqueta" "$obtenido" "$esperado"
        fallo=1
    fi
}
verificar "Tablas en public"   "select count(*) from information_schema.tables where table_schema = 'public' and table_type = 'BASE TABLE'" 52
verificar "Filas en tema"      "select count(*) from tema" 13
verificar "Filas en rol"       "select count(*) from rol" 5
verificar "Filas en privilegio" "select count(*) from privilegio" 14
verificar "Usuario EmprendeUnam" "select count(*) from usuario where usua_nombre_usuario = 'EmprendeUnam'" 1

if [ "$fallo" -ne 0 ]; then
    echo "La verificación falló." >&2
    exit 1
fi
echo "Listo: la base '$BD' quedó cargada."
