/*======================================================================
  PROPUESTA (NO APLICADA) · 2026-10-22 · versión 2
  Carta de cesión en CONSTANCIA, edición en portada y contenido del índice.

  Espera la aprobación del responsable de la base. Cambios respecto a la
  versión 1: se quita todo lo de AUTOR y ARTICULO; la carta de cesión va en
  CONSTANCIA; REVISTA no agrega portada ni periodo (se usan REVI_URL_PORTADA y
  REVI_MES).
======================================================================*/
BEGIN;

/*----------------------------------------------------------------------
  1. CONSTANCIA guarda también la carta de cesión.
     CONS_CARTA_CESION = true  -> carta de cesión firmada: sin folio.
     CONS_CARTA_CESION = false o nulo -> constancia (o carta de agradecimiento): con folio.
     Un artículo puede tener entonces varios documentos, así que se cambia
     UQ_CONSTANCIA_ARTICULO (una sola fila por artículo) por una fila por
     tipo de documento.
----------------------------------------------------------------------*/
ALTER TABLE CONSTANCIA ADD COLUMN CONS_CARTA_CESION BOOL NULL;
ALTER TABLE CONSTANCIA ALTER COLUMN CONS_FOLIO DROP NOT NULL;

ALTER TABLE CONSTANCIA ADD CONSTRAINT CK_CONS_FOLIO_SEGUN_TIPO
   CHECK ((CONS_CARTA_CESION IS TRUE  AND CONS_FOLIO IS NULL)
       OR (CONS_CARTA_CESION IS NOT TRUE AND CONS_FOLIO IS NOT NULL));

ALTER TABLE CONSTANCIA DROP CONSTRAINT UQ_CONSTANCIA_ARTICULO;
CREATE UNIQUE INDEX UQ_CONSTANCIA_ARTICULO_TIPO
   ON CONSTANCIA (ARTI_ID_AUTOR, (COALESCE(CONS_CARTA_CESION, FALSE)));

/*----------------------------------------------------------------------
  2. REVISTA: Flipbook y edición en portada.
     No se agrega portada ni periodo: se usan REVI_URL_PORTADA y REVI_MES
     (que pasa a guardar el periodo, p. ej. «julio-agosto»; hoy admite 10
     caracteres y «septiembre-octubre» tiene 18).
----------------------------------------------------------------------*/
ALTER TABLE REVISTA ADD COLUMN REVI_URL_FLIPBOOK VARCHAR(256) NULL;
ALTER TABLE REVISTA ADD COLUMN REVI_EN_PORTADA   BOOL NOT NULL DEFAULT FALSE;

ALTER TABLE REVISTA ALTER COLUMN REVI_MES            TYPE VARCHAR(20);
ALTER TABLE REVISTA ALTER COLUMN REVI_URL_PORTADA     TYPE VARCHAR(256);
ALTER TABLE REVISTA ALTER COLUMN REVI_URL_REVISTA_PDF TYPE VARCHAR(256);

ALTER TABLE REVISTA ADD CONSTRAINT CK_REVI_PORTADA_VISIBLE CHECK (NOT REVI_EN_PORTADA OR REVI_VISIBLE);
CREATE UNIQUE INDEX UQ_REVISTA_EN_PORTADA ON REVISTA (REVI_EN_PORTADA) WHERE REVI_EN_PORTADA;

/*----------------------------------------------------------------------
  3. ARTICULO_REVISTA (nueva): artículos que se muestran en el índice junto
     a la edición. Cada fila puede ligarse a un artículo APROBADO del sistema
     o ser una entrada editorial (ARTI_ID_AUTOR nulo).
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
   CONSTRAINT FK_ARRE_REVISTA  FOREIGN KEY (REVI_ID_REVISTA) REFERENCES REVISTA (REVI_ID_REVISTA) ON DELETE CASCADE,
   CONSTRAINT FK_ARRE_ARTICULO FOREIGN KEY (ARTI_ID_AUTOR)   REFERENCES ARTICULO (ARTI_ID_AUTOR) ON DELETE RESTRICT,
   CONSTRAINT UQ_ARRE_ORDEN UNIQUE (REVI_ID_REVISTA, ARRE_ORDEN) DEFERRABLE INITIALLY DEFERRED,
   CONSTRAINT UQ_ARRE_ARTICULO UNIQUE (REVI_ID_REVISTA, ARTI_ID_AUTOR),
   CONSTRAINT CK_ARRE_ORDEN CHECK (ARRE_ORDEN > 0)
);
CREATE INDEX ARRE_REVISTA_FK  ON ARTICULO_REVISTA (REVI_ID_REVISTA);
CREATE INDEX ARRE_ARTICULO_FK ON ARTICULO_REVISTA (ARTI_ID_AUTOR);
CREATE UNIQUE INDEX UQ_ARRE_PRINCIPAL ON ARTICULO_REVISTA (REVI_ID_REVISTA) WHERE ARRE_PRINCIPAL;

CREATE OR REPLACE FUNCTION FN_ARRE_ARTICULO_APROBADO() RETURNS TRIGGER AS $$
BEGIN
   IF NEW.ARTI_ID_AUTOR IS NOT NULL AND NOT EXISTS (
         SELECT 1 FROM ARTICULO WHERE ARTI_ID_AUTOR = NEW.ARTI_ID_AUTOR AND ARTI_STATUS = 'APROBADO') THEN
      RAISE EXCEPTION 'Solo un artículo aprobado puede formar parte de una edición' USING ERRCODE = '23514';
   END IF;
   RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER TR_ARRE_ARTICULO_APROBADO
   BEFORE INSERT OR UPDATE OF ARTI_ID_AUTOR ON ARTICULO_REVISTA
   FOR EACH ROW EXECUTE FUNCTION FN_ARRE_ARTICULO_APROBADO();

/*----------------------------------------------------------------------
  4. ARTICULO_CURSO (nueva): cursos de cada artículo del índice que el
     administrador agrega (INCLUIDO = true) o quita (false) a mano; prevalece
     sobre la relación automática por palabras clave. Un artículo puede tener
     varios cursos, por eso no cabe como columna de ARTICULO_REVISTA.
----------------------------------------------------------------------*/
CREATE TABLE ARTICULO_CURSO (
   ARCU_ID_ARTICULO_CURSO   SERIAL NOT NULL,
   ARRE_ID_ARTICULO_REVISTA INT4   NOT NULL,
   CURS_ID_CURSO            INT4   NOT NULL,
   ARCU_INCLUIDO            BOOL   NOT NULL,
   CONSTRAINT PK_ARTICULO_CURSO PRIMARY KEY (ARCU_ID_ARTICULO_CURSO),
   CONSTRAINT FK_ARCU_ARTICULO_REVISTA FOREIGN KEY (ARRE_ID_ARTICULO_REVISTA)
      REFERENCES ARTICULO_REVISTA (ARRE_ID_ARTICULO_REVISTA) ON DELETE CASCADE,
   CONSTRAINT FK_ARCU_CURSO FOREIGN KEY (CURS_ID_CURSO) REFERENCES CURSO (CURS_ID_CURSO) ON DELETE CASCADE,
   CONSTRAINT UQ_ARCU UNIQUE (ARRE_ID_ARTICULO_REVISTA, CURS_ID_CURSO)
);
CREATE INDEX ARCU_CURSO_FK ON ARTICULO_CURSO (CURS_ID_CURSO);

COMMIT;
