/*======================================================================
  PROPUESTA (NO APLICADA) · 2026-10-22 · versión 3
  Documentos del artículo en CONSTANCIA, edición en portada y los 5
  artículos del índice.

  Espera la aprobación del responsable de la base. Cambios respecto a la
  versión 2: CONSTANCIA usa CONS_TIPO (no un booleano) y ARTICULO_REVISTA
  guarda la posición en el índice (1 a 5) y cuándo se mostró cada artículo,
  para rotar los artículos del número hasta mostrarlos todos.
======================================================================*/
BEGIN;

/*----------------------------------------------------------------------
  1. CONSTANCIA: los tres documentos de un artículo.
     CONS_TIPO = 'CONSTANCIA'      constancia de aprobación   -> con folio
                 'AGRADECIMIENTO'  carta de agradecimiento    -> con folio
                 'CESION'          carta de cesión firmada    -> sin folio
     Las filas existentes son constancias. Un documento de cada tipo por
     artículo (sustituye a UQ_CONSTANCIA_ARTICULO, una fila por artículo).
----------------------------------------------------------------------*/
ALTER TABLE CONSTANCIA ADD COLUMN CONS_TIPO VARCHAR(15) NOT NULL DEFAULT 'CONSTANCIA';
ALTER TABLE CONSTANCIA ALTER COLUMN CONS_TIPO DROP DEFAULT;
ALTER TABLE CONSTANCIA ALTER COLUMN CONS_FOLIO DROP NOT NULL;

ALTER TABLE CONSTANCIA ADD CONSTRAINT CK_CONS_TIPO
   CHECK (CONS_TIPO IN ('CONSTANCIA', 'AGRADECIMIENTO', 'CESION'));

ALTER TABLE CONSTANCIA ADD CONSTRAINT CK_CONS_FOLIO_SEGUN_TIPO
   CHECK ((CONS_TIPO = 'CESION' AND CONS_FOLIO IS NULL)
       OR (CONS_TIPO <> 'CESION' AND CONS_FOLIO IS NOT NULL));

ALTER TABLE CONSTANCIA DROP CONSTRAINT UQ_CONSTANCIA_ARTICULO;
ALTER TABLE CONSTANCIA ADD CONSTRAINT UQ_CONSTANCIA_ARTICULO_TIPO UNIQUE (ARTI_ID_AUTOR, CONS_TIPO);

/*----------------------------------------------------------------------
  2. REVISTA: Flipbook y edición en portada.
     Se usan las columnas que ya existen: REVI_URL_PORTADA (portada) y
     REVI_MES, que pasa a guardar el periodo («julio-agosto»; se amplía a 20
     porque «septiembre-octubre» mide 18).
----------------------------------------------------------------------*/
ALTER TABLE REVISTA ADD COLUMN REVI_URL_FLIPBOOK VARCHAR(256) NULL;
ALTER TABLE REVISTA ADD COLUMN REVI_EN_PORTADA   BOOL NOT NULL DEFAULT FALSE;

ALTER TABLE REVISTA ALTER COLUMN REVI_MES             TYPE VARCHAR(20);
ALTER TABLE REVISTA ALTER COLUMN REVI_URL_PORTADA     TYPE VARCHAR(256);
ALTER TABLE REVISTA ALTER COLUMN REVI_URL_REVISTA_PDF TYPE VARCHAR(256);

ALTER TABLE REVISTA ADD CONSTRAINT CK_REVI_PORTADA_VISIBLE CHECK (NOT REVI_EN_PORTADA OR REVI_VISIBLE);
CREATE UNIQUE INDEX UQ_REVISTA_EN_PORTADA ON REVISTA (REVI_EN_PORTADA) WHERE REVI_EN_PORTADA;

/*----------------------------------------------------------------------
  3. ARTICULO_REVISTA (nueva): todos los artículos de un número.
     El índice del sitio muestra 5 artículos del número en portada, elegidos
     por el administrador; después los cambia por otros 5 del mismo número,
     hasta mostrarlos todos.
     - ARRE_POSICION_INDEX: 1 a 5 si se ve ahora en el índice, nulo si no.
       Única por número: como mucho 5 a la vez y sin posiciones repetidas.
       Es diferible para reacomodar posiciones en una sola transacción.
     - ARRE_FECHA_INDEX: cuándo entró al índice por última vez; nulo = aún no
       se ha mostrado (el panel lista primero los pendientes).
     Cada fila puede ligarse a un artículo APROBADO del sistema o ser una
     entrada editorial (ARTI_ID_AUTOR nulo).
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
   ARRE_POSICION_INDEX      INT2          NULL,
   ARRE_FECHA_INDEX         DATE          NULL,
   CONSTRAINT PK_ARTICULO_REVISTA PRIMARY KEY (ARRE_ID_ARTICULO_REVISTA),
   CONSTRAINT FK_ARRE_REVISTA  FOREIGN KEY (REVI_ID_REVISTA) REFERENCES REVISTA (REVI_ID_REVISTA) ON DELETE CASCADE,
   CONSTRAINT FK_ARRE_ARTICULO FOREIGN KEY (ARTI_ID_AUTOR)   REFERENCES ARTICULO (ARTI_ID_AUTOR) ON DELETE RESTRICT,
   CONSTRAINT UQ_ARRE_POSICION UNIQUE (REVI_ID_REVISTA, ARRE_POSICION_INDEX) DEFERRABLE INITIALLY DEFERRED,
   CONSTRAINT UQ_ARRE_ARTICULO UNIQUE (REVI_ID_REVISTA, ARTI_ID_AUTOR),
   CONSTRAINT CK_ARRE_POSICION CHECK (ARRE_POSICION_INDEX BETWEEN 1 AND 5),
   CONSTRAINT CK_ARRE_EN_INDEX_CON_FECHA CHECK (ARRE_POSICION_INDEX IS NULL OR ARRE_FECHA_INDEX IS NOT NULL)
);
CREATE INDEX ARRE_REVISTA_FK  ON ARTICULO_REVISTA (REVI_ID_REVISTA);
CREATE INDEX ARRE_ARTICULO_FK ON ARTICULO_REVISTA (ARTI_ID_AUTOR);

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
  4. ARTICULO_CURSO (nueva): cursos que se muestran con cada artículo del
     índice. El panel sugiere cursos automáticamente (palabras clave y
     título); lo que el administrador decide se guarda aquí y manda:
     INCLUIDO = true lo muestra, false lo descarta aunque se sugiera.
     Un artículo puede tener varios cursos: por eso es tabla y no columna.
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
