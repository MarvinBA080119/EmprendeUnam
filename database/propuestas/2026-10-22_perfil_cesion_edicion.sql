/*======================================================================
  PROPUESTA (NO APLICADA) · 2026-10-22
  Perfil del autor, carta de cesión y gestor de la edición en portada.

  Este archivo NO se aplica todavía: espera la aprobación del responsable
  de la base. Al aprobarse se copia a database/cambios/ con la fecha de
  aplicación y se incorpora a EMP_FINAL_corregido.sql para las bases nuevas.

  Se probó contra una copia de la base (dentro de una transacción revertida).
======================================================================*/
BEGIN;

/*----------------------------------------------------------------------
  1. Tipo de perfil del autor (registro)
     Alumno, Académico, Emprendedor u Otro; con «Otro» se especifica.
     Nulo para autores ya existentes y coautores sin cuenta; la aplicación
     lo exige en el registro. Se descartaron ALUMNO y PROFESOR: piden número
     de cuenta, RFC y número de trabajador de la UNAM.
----------------------------------------------------------------------*/
ALTER TABLE AUTOR ADD COLUMN AUTO_TIPO_PERFIL      VARCHAR(12)  NULL;
ALTER TABLE AUTOR ADD COLUMN AUTO_TIPO_PERFIL_OTRO VARCHAR(100) NULL;

ALTER TABLE AUTOR ADD CONSTRAINT CK_AUTO_TIPO_PERFIL
   CHECK (AUTO_TIPO_PERFIL IS NULL OR AUTO_TIPO_PERFIL IN ('ALUMNO', 'ACADEMICO', 'EMPRENDEDOR', 'OTRO'));

-- «Otro» lleva su descripción; los demás tipos, no.
ALTER TABLE AUTOR ADD CONSTRAINT CK_AUTO_TIPO_PERFIL_OTRO
   CHECK ((AUTO_TIPO_PERFIL = 'OTRO' AND AUTO_TIPO_PERFIL_OTRO IS NOT NULL AND btrim(AUTO_TIPO_PERFIL_OTRO) <> '')
       OR (AUTO_TIPO_PERFIL IS DISTINCT FROM 'OTRO' AND AUTO_TIPO_PERFIL_OTRO IS NULL));

/*----------------------------------------------------------------------
  2. Carta de cesión de derechos firmada (PDF), requisito para enviar.
     Nula en los artículos enviados antes del cambio.
----------------------------------------------------------------------*/
ALTER TABLE ARTICULO ADD COLUMN ARTI_URL_CARTA_CESION VARCHAR(256) NULL;

/*----------------------------------------------------------------------
  3. REVISTA: la edición que aparece en el inicio del sitio
     - rutas de 256 caracteres (hoy 100) y enlace del Flipbook;
     - periodo legible para las cartas («julio-agosto 2026»): REVI_MES
       admite 10 caracteres;
     - una sola edición en portada, y debe ser visible;
     - una edición en preparación puede no tener aún portada ni PDF, pero
       una visible sí debe tenerlos.
----------------------------------------------------------------------*/
ALTER TABLE REVISTA ALTER COLUMN REVI_URL_PORTADA     TYPE VARCHAR(256);
ALTER TABLE REVISTA ALTER COLUMN REVI_URL_REVISTA_PDF TYPE VARCHAR(256);
ALTER TABLE REVISTA ALTER COLUMN REVI_URL_PORTADA     DROP NOT NULL;
ALTER TABLE REVISTA ALTER COLUMN REVI_URL_REVISTA_PDF DROP NOT NULL;

ALTER TABLE REVISTA ADD COLUMN REVI_URL_FLIPBOOK VARCHAR(256) NULL;
ALTER TABLE REVISTA ADD COLUMN REVI_PERIODO      VARCHAR(40)  NULL;
ALTER TABLE REVISTA ADD COLUMN REVI_EN_PORTADA   BOOL         NOT NULL DEFAULT FALSE;

ALTER TABLE REVISTA ADD CONSTRAINT CK_REVI_VISIBLE_COMPLETA
   CHECK (NOT REVI_VISIBLE OR (REVI_URL_PORTADA IS NOT NULL AND REVI_URL_REVISTA_PDF IS NOT NULL));
ALTER TABLE REVISTA ADD CONSTRAINT CK_REVI_PORTADA_VISIBLE
   CHECK (NOT REVI_EN_PORTADA OR REVI_VISIBLE);

CREATE UNIQUE INDEX UQ_REVISTA_EN_PORTADA ON REVISTA (REVI_EN_PORTADA) WHERE REVI_EN_PORTADA;

/*----------------------------------------------------------------------
  4. ARTICULO_REVISTA: contenido de cada edición (tabla de contenidos del
     inicio). Cada entrada puede ligarse a un artículo aprobado del sistema
     o ser una entrada editorial (artículos que no pasaron por el sistema).
     El orden único es diferible para reordenar (arrastrar y soltar) en una
     sola transacción.
----------------------------------------------------------------------*/
CREATE TABLE ARTICULO_REVISTA (
   ARRE_ID_ARTICULO_REVISTA SERIAL        NOT NULL,
   REVI_ID_REVISTA          INT4          NOT NULL,
   ARTI_ID_AUTOR            INT4          NULL,
   ARRE_SECCION             VARCHAR(60)   NULL,
   ARRE_TITULO              VARCHAR(200)  NOT NULL,
   ARRE_DESCRIPCION         VARCHAR(500)  NULL,
   ARRE_AUTORES             VARCHAR(300)  NOT NULL,
   ARRE_PAGINAS             VARCHAR(15)   NULL,
   ARRE_URL_PDF             VARCHAR(256)  NULL,
   ARRE_URL_IMAGEN          VARCHAR(256)  NULL,
   ARRE_ORDEN               INT2          NOT NULL,
   ARRE_PRINCIPAL           BOOL          NOT NULL DEFAULT FALSE,
   ARRE_VISIBLE             BOOL          NOT NULL DEFAULT TRUE,
   CONSTRAINT PK_ARTICULO_REVISTA PRIMARY KEY (ARRE_ID_ARTICULO_REVISTA),
   CONSTRAINT FK_ARRE_REVISTA FOREIGN KEY (REVI_ID_REVISTA)
      REFERENCES REVISTA (REVI_ID_REVISTA) ON DELETE CASCADE,
   CONSTRAINT FK_ARRE_ARTICULO FOREIGN KEY (ARTI_ID_AUTOR)
      REFERENCES ARTICULO (ARTI_ID_AUTOR) ON DELETE RESTRICT,
   CONSTRAINT UQ_ARRE_ORDEN UNIQUE (REVI_ID_REVISTA, ARRE_ORDEN) DEFERRABLE INITIALLY DEFERRED,
   CONSTRAINT UQ_ARRE_ARTICULO UNIQUE (REVI_ID_REVISTA, ARTI_ID_AUTOR),
   CONSTRAINT CK_ARRE_ORDEN CHECK (ARRE_ORDEN > 0)
);

CREATE INDEX ARRE_REVISTA_FK  ON ARTICULO_REVISTA (REVI_ID_REVISTA);
CREATE INDEX ARRE_ARTICULO_FK ON ARTICULO_REVISTA (ARTI_ID_AUTOR);

-- Un solo artículo principal por edición.
CREATE UNIQUE INDEX UQ_ARRE_PRINCIPAL ON ARTICULO_REVISTA (REVI_ID_REVISTA) WHERE ARRE_PRINCIPAL;

-- Solo un artículo APROBADO del sistema puede formar parte de una edición.
CREATE OR REPLACE FUNCTION FN_ARRE_ARTICULO_APROBADO() RETURNS TRIGGER AS $$
BEGIN
   IF NEW.ARTI_ID_AUTOR IS NOT NULL AND NOT EXISTS (
         SELECT 1 FROM ARTICULO
          WHERE ARTI_ID_AUTOR = NEW.ARTI_ID_AUTOR AND ARTI_STATUS = 'APROBADO') THEN
      RAISE EXCEPTION 'Solo un artículo aprobado puede formar parte de una edición'
         USING ERRCODE = '23514';
   END IF;
   RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER TR_ARRE_ARTICULO_APROBADO
   BEFORE INSERT OR UPDATE OF ARTI_ID_AUTOR ON ARTICULO_REVISTA
   FOR EACH ROW EXECUTE FUNCTION FN_ARRE_ARTICULO_APROBADO();

/*----------------------------------------------------------------------
  5. ARTICULO_CURSO: ajustes manuales del administrador a la relación
     artículo ↔ curso. Las sugerencias automáticas se calculan (palabras
     clave compartidas y palabras del título, la sección y la descripción);
     aquí solo se guarda lo que el administrador agrega (INCLUIDO = true) o
     quita (INCLUIDO = false), que tiene prioridad sobre lo automático.
----------------------------------------------------------------------*/
CREATE TABLE ARTICULO_CURSO (
   ARCU_ID_ARTICULO_CURSO   SERIAL  NOT NULL,
   ARRE_ID_ARTICULO_REVISTA INT4    NOT NULL,
   CURS_ID_CURSO            INT4    NOT NULL,
   ARCU_INCLUIDO            BOOL    NOT NULL,
   CONSTRAINT PK_ARTICULO_CURSO PRIMARY KEY (ARCU_ID_ARTICULO_CURSO),
   CONSTRAINT FK_ARCU_ARTICULO_REVISTA FOREIGN KEY (ARRE_ID_ARTICULO_REVISTA)
      REFERENCES ARTICULO_REVISTA (ARRE_ID_ARTICULO_REVISTA) ON DELETE CASCADE,
   CONSTRAINT FK_ARCU_CURSO FOREIGN KEY (CURS_ID_CURSO)
      REFERENCES CURSO (CURS_ID_CURSO) ON DELETE CASCADE,
   CONSTRAINT UQ_ARCU UNIQUE (ARRE_ID_ARTICULO_REVISTA, CURS_ID_CURSO)
);

CREATE INDEX ARCU_CURSO_FK ON ARTICULO_CURSO (CURS_ID_CURSO);

COMMIT;
