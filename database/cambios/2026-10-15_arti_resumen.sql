/*======================================================================
  2026-10-15 · Resumen del artículo (lineamientos para autores)
  Para bases creadas ANTES de este cambio. Las bases nuevas ya lo traen
  en EMP_FINAL_corregido.sql.

  Cada artículo incluye un resumen de máximo 50 palabras; la aplicación
  valida las palabras y la columna admite hasta 500 caracteres.
  Los artículos existentes quedan con resumen vacío ('').
======================================================================*/
BEGIN;

ALTER TABLE ARTICULO ADD COLUMN ARTI_RESUMEN VARCHAR(500) NOT NULL DEFAULT '';
ALTER TABLE ARTICULO ALTER COLUMN ARTI_RESUMEN DROP DEFAULT;

COMMIT;
