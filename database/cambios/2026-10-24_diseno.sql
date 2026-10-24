/*======================================================================
  2026-10-24 · Rol Diseño y PDF diseñado de cada artículo (aprobado el
  2026-10-24). Para bases creadas ANTES de este cambio. Las bases nuevas
  ya lo traen en EMP_FINAL_corregido.sql y datos_iniciales.sql.

  - ARTICULO: PDF diseñado (maquetado) del artículo, cuándo se subió y
    quién lo subió. Pertenece al artículo, no al número: los autores
    envían sin saber en qué número saldrán. Reemplazarlo sobrescribe el
    anterior (sin versiones).
  - ROL «Diseño» con dos privilegios nuevos; el Administrador también
    los recibe (tiene todos).

  La cuenta de diseño NO se crea aquí: se crea en el servidor con
      php artisan emprende:crear-diseno
  que pide el correo y la contraseña en la terminal (nunca se escriben
  en un archivo).

  IMPORTANTE: aplícalo con el USUARIO DE LA APLICACIÓN (el de DB_USERNAME),
  no con postgres:
     psql -h 127.0.0.1 -U <DB_USERNAME> -d <DB_DATABASE> -f database/cambios/2026-10-24_diseno.sql
======================================================================*/
BEGIN;

/*----------------------------------------------------------------------
  1. ARTICULO: PDF diseñado.
     Las tres columnas van juntas (todas con valor o todas nulas) y solo
     un artículo APROBADO puede tener PDF diseñado.
----------------------------------------------------------------------*/
ALTER TABLE ARTICULO ADD COLUMN ARTI_URL_PDF_DISENO VARCHAR(256) NULL;
ALTER TABLE ARTICULO ADD COLUMN ARTI_FECHA_DISENO   TIMESTAMP    NULL;
ALTER TABLE ARTICULO ADD COLUMN ARTI_ID_DISENADOR   INT4         NULL;

ALTER TABLE ARTICULO
   ADD CONSTRAINT FK_ARTICULO_DISENADOR_USUARIO FOREIGN KEY (ARTI_ID_DISENADOR)
      REFERENCES USUARIO (USUA_ID_USUARIO)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

CREATE INDEX ARTICULODISENADOR_FK ON ARTICULO (ARTI_ID_DISENADOR);

ALTER TABLE ARTICULO ADD CONSTRAINT CK_ARTI_DISENO_COMPLETO
   CHECK ((ARTI_URL_PDF_DISENO IS NULL AND ARTI_FECHA_DISENO IS NULL AND ARTI_ID_DISENADOR IS NULL)
       OR (ARTI_URL_PDF_DISENO IS NOT NULL AND ARTI_FECHA_DISENO IS NOT NULL AND ARTI_ID_DISENADOR IS NOT NULL));

ALTER TABLE ARTICULO ADD CONSTRAINT CK_ARTI_DISENO_APROBADO
   CHECK (ARTI_URL_PDF_DISENO IS NULL OR ARTI_STATUS = 'APROBADO');

/*----------------------------------------------------------------------
  2. Rol Diseño y sus privilegios.
----------------------------------------------------------------------*/
INSERT INTO ROL (ROL_NOMBRE, ROL_DESCRIPCION)
SELECT 'Diseño', 'Descarga los textos aprobados y sube el PDF diseñado de cada artículo'
 WHERE NOT EXISTS (SELECT 1 FROM ROL WHERE ROL_NOMBRE = 'Diseño');

INSERT INTO PRIVILEGIO (PRIV_NOMBRE, PRIV_DESCRIPCION)
SELECT n, d FROM (VALUES
  ('DESCARGAR_ORIGINALES', 'Descargar el texto original de los artículos aprobados'),
  ('SUBIR_DISENO',         'Subir el PDF diseñado de un artículo aprobado')
) v(n, d)
WHERE NOT EXISTS (SELECT 1 FROM PRIVILEGIO p WHERE p.PRIV_NOMBRE = v.n);

INSERT INTO ROL_PRIVILEGIO (ROL_ID_ROL, ROPR_ID_PRIVILEGIO)
SELECT r.ROL_ID_ROL, p.PRIV_ID_PRIVILEGIO
  FROM ROL r
  JOIN PRIVILEGIO p ON p.PRIV_NOMBRE IN ('DESCARGAR_ORIGINALES', 'SUBIR_DISENO')
 WHERE r.ROL_NOMBRE IN ('Administrador', 'Diseño')
ON CONFLICT (ROL_ID_ROL, ROPR_ID_PRIVILEGIO) DO NOTHING;

COMMIT;
