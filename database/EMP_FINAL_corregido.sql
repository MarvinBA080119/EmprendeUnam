/*==============================================================*/
/* DBMS name:      PostgreSQL 8                                 */
/* Created on:     13/10/2026 06:00:53 p. m.                    */
/*==============================================================*/


drop index if exists RELATIONSHIP_23_FK;

drop index if exists ALUMNO_PK;

drop table if exists ALUMNO cascade;

drop index if exists REFERENCE_55_FK;

drop index if exists ALUMNO_PROGRAMA_PK;

drop index if exists RELATIONSHIP_52_FK;

drop index if exists RELATIONSHIP_51_FK;

drop table if exists ALUMNO_PROGRAMA cascade;

drop index if exists ARTICULOTEMA_FK;

drop index if exists ARTICULO_PK;

drop table if exists ARTICULO_CURSO cascade;

drop table if exists ARTICULO_REVISTA cascade;

drop function if exists FN_ARRE_ARTICULO_APROBADO() cascade;

drop table if exists ARTICULO cascade;

drop index if exists COAUTOR_FK;

drop index if exists PERSONAAUTOR2_FK;

drop index if exists AUTOR_PK;

drop table if exists AUTOR cascade;

drop index if exists AUTORARTICULO2_FK;

drop index if exists AUTORARTICULO_FK;

drop index if exists AUTORARTICULO_PK;

drop table if exists AUTOR_ARTICULO cascade;

drop index if exists BANCO_PK;

drop table if exists BANCO cascade;

drop index if exists CODIGO_POSTAL_PK;

drop table if exists CODIGO_POSTAL cascade;

drop index if exists ARTICULOCONSTANCIA_FK;

drop index if exists CONSTANCIA_PK;

drop table if exists CONSTANCIA cascade;

drop index if exists RELATIONSHIP_42_FK;

drop index if exists RELATIONSHIP_41_FK;

drop index if exists COMUNICACION_PK;

drop table if exists COMUNICACION cascade;

drop index if exists COSTO_PK;

drop table if exists COSTO cascade;

drop index if exists CREDENCIALUSUARIO2_FK;

drop index if exists CREDENCIAL_UNAM_PK;

drop table if exists CREDENCIAL_UNAM cascade;

drop index if exists CURSO_PK;

drop table if exists CURSO cascade;

drop index if exists CODIGODATO_FK;

drop index if exists REGIMENDATO_FK;

drop index if exists PERFILFISCALUSUARIO_FK;

drop index if exists PERFIL_FISCAL_PK;

drop table if exists DATO_FISCAL cascade;

drop index if exists DESCUENTO_PK;

drop table if exists DESCUENTO cascade;

drop index if exists ESTADOTIPOESTADOPAGO_FK;

drop index if exists PAGOESTADOPAGO_FK;

drop index if exists ESTADO_PAGO_PK;

drop table if exists ESTADO_PAGO cascade;

drop index if exists ARTICULOFAVORITOARTICULO_FK;

drop index if exists HISTORIALDARTICULOU_FK;

drop index if exists FAVORITO_ARTICULO_PK;

drop table if exists FAVORITO_ARTICULO cascade;

drop index if exists PAGINAFAVORITOREVISTA_FK;

drop index if exists FAVORITOPAGINAUSUARIO_FK;

drop index if exists FAVORITO_PAGINA_PK;

drop table if exists FAVORITO_PAGINA cascade;

drop index if exists FAVORITOREVISTAUSUARIO_FK;

drop index if exists REVISTAFAVORITOREVISTA_FK;

drop index if exists FAVORITO_REVISTA_PK;

drop table if exists FAVORITO_REVISTA cascade;

drop index if exists RELATIONSHIP_13_FK;

drop index if exists FAVORITOARTICULOUSUARIO_FK;

drop index if exists HISTORIAL_DESCARGA_ARTICULO_PK;

drop table if exists HISTORIAL_DESCARGA_ARTICULO cascade;

drop index if exists HISTORIALDREVISTAU_FK;

drop index if exists REVISTAHISDESCREV_FK;

drop index if exists HISTORIAL_DESCARGA_REVISTA_PK;

drop table if exists HISTORIAL_DESCARGA_REVISTA cascade;

drop index if exists ARTICULOHISTLECTART_FK;

drop index if exists RELATIONSHIP_9_FK;

drop index if exists HISTORIAL_LECTURA_ARTICULO_PK;

drop table if exists HISTORIAL_LECTURA_ARTICULO cascade;

drop index if exists HISTORIALLECTURAREVISTA_FK;

drop index if exists REVISTAHISTLECTREV_FK;

drop index if exists HISTORIAL_LECTURA_REVISTA_PK;

drop table if exists HISTORIAL_LECTURA_REVISTA cascade;

drop index if exists REFERENCE_59_FK;

drop index if exists MENSAJEUSUARIO_FK;

drop index if exists MENSAJE_PK;

drop table if exists MENSAJE cascade;

drop index if exists METODO_PAGO_PK;

drop table if exists METODO_PAGO cascade;

drop index if exists C_MODALIDAD_PK;

drop table if exists MODALIDAD cascade;

drop index if exists C_NIVEL_PK;

drop table if exists NIVEL cascade;

drop index if exists NOTIFICACIONUSUARIO_FK;

drop index if exists NOTIFICACION_PK;

drop table if exists NOTIFICACION cascade;

drop index if exists PAGINAREVISTA_FK;

drop index if exists PAGINA_PK;

drop table if exists PAGINA cascade;

drop index if exists PAGOMETODOPAGO_FK;

drop index if exists BANCOPAGO_FK;

drop index if exists RESPONSABLEPAGO_FK;

drop index if exists COMPROBANTEPOSITO2_FK;

drop index if exists COMPROBANTE_PAGO_PK;

drop table if exists PAGO cascade;

drop index if exists PALABRA_PK;

drop table if exists PALABRA cascade;

drop index if exists REFERENCE_58_FK;

drop index if exists REFERENCE_57_FK;

drop index if exists PALABRA_ARTICULO_PK;

drop table if exists PALABRA_ARTICULO cascade;

drop index if exists REFERENCE_61_FK;

drop index if exists REFERENCE_60_FK;

drop index if exists PALABRA_CURSO_PK;

drop table if exists PALABRA_CURSO cascade;

drop index if exists PERSONA_PK;

drop table if exists PERSONA cascade;

drop index if exists PRIVILEGIO_PK;

drop table if exists PRIVILEGIO cascade;

drop index if exists RELATIONSHIP_24_FK;

drop index if exists PROFESOR_PK;

drop table if exists PROFESOR cascade;

drop index if exists REFERENCE_56_FK;

drop index if exists C_PROGRAMA_ACADEMICO_PK;

drop table if exists PROGRAMA_ACADEMICO cascade;

drop index if exists FICHA_DEPOSITO_PK;

drop table if exists REFERENCIA_BANCARIA cascade;

drop index if exists REGIMEN_FISCAL_PK;

drop table if exists REGIMEN_FISCAL cascade;

drop index if exists RESPONSABLE_PK;

drop table if exists RESPONSABLE cascade;

drop index if exists REVISTAUSUARIO_FK;

drop index if exists REVISTA_PK;

drop table if exists REVISTA cascade;

drop index if exists ROL_PK;

drop table if exists ROL cascade;

drop index if exists ROLPRIVILEGIO2_FK;

drop index if exists ROLPRIVILEGIO_FK;

drop index if exists ROLPRIVILEGIO_PK;

drop table if exists ROL_PRIVILEGIO cascade;

drop index if exists ROLUSUARIO2_FK;

drop index if exists ROLUSUARIO_FK;

drop index if exists ROLUSUARIO_PK;

drop table if exists ROL_USUARIO cascade;

drop index if exists SOLICITUDFACTURAUSUARIO_FK;

drop index if exists SOLICITUD_FACTURA_PK;

drop table if exists SOLICITUD_FACTURA cascade;

drop index if exists DESCUENTOSOLICITUD_FK;

drop index if exists COSTOSOLICITUDSUSCRIPCION_FK;

drop index if exists FICHADEPOSITOSOLICITUD_FK;

drop index if exists SOLICITUDSUSCRIBCIONUSUARIO_FK;

drop index if exists SOLICITUD_SUSCRIPCION_PK;

drop table if exists SOLICITUD_SUSCRIPCION cascade;

drop index if exists TEMA_PK;

drop table if exists TEMA cascade;

drop index if exists RELATIONSHIP_43_FK;

drop index if exists TEMA_NEWS_PK;

drop table if exists TEMA_NEWS cascade;

drop index if exists TIPO_COMUNICACION_PK;

drop table if exists TIPO_COMUNICACION cascade;

drop index if exists TIPO_ESTADO_PAGO_PK;

drop table if exists TIPO_ESTADO_PAGO cascade;

drop index if exists PERSONAUSUARIO_FK;

drop index if exists USUARIO_PK;

drop table if exists USUARIO cascade;

/*==============================================================*/
/* Table: ALUMNO                                            */
/*==============================================================*/
create table ALUMNO (
   ALUM_ID_ALUMNO           SERIAL               not null,
   PERS_ID_PERSONA          INT4                 not null,
   ALUM_NUM_CUENTA          VARCHAR(20)          not null,
   constraint PK_ALUMNO primary key (ALUM_ID_ALUMNO)
);

/*==============================================================*/
/* Index: ALUMNO_PK                                         */
/*==============================================================*/
create unique index ALUMNO_PK on ALUMNO (
ALUM_ID_ALUMNO
);

/*==============================================================*/
/* Index: RELATIONSHIP_23_FK                                */
/*==============================================================*/
create  index RELATIONSHIP_23_FK on ALUMNO (
PERS_ID_PERSONA
);

/*==============================================================*/
/* Table: ALUMNO_PROGRAMA                                   */
/*==============================================================*/
create table ALUMNO_PROGRAMA (
   ALPR_ID_ALUMNO_PROGRAMA  SERIAL               not null,
   ALPR_ID_PROGRAMA_ACADEMICO INT4                 not null,
   ALUM_ID_ALUMNO           INT4                 not null,
   ALPR_ID_MODALIDAD        INT4                 null,
   ALPR_STATUS              BOOL                 not null,
   constraint PK_ALUMNO_PROGRAMA primary key (ALPR_ID_ALUMNO_PROGRAMA)
);

/*==============================================================*/
/* Index: RELATIONSHIP_51_FK                                */
/*==============================================================*/
create  index RELATIONSHIP_51_FK on ALUMNO_PROGRAMA (
ALPR_ID_PROGRAMA_ACADEMICO
);

/*==============================================================*/
/* Index: RELATIONSHIP_52_FK                                */
/*==============================================================*/
create  index RELATIONSHIP_52_FK on ALUMNO_PROGRAMA (
ALUM_ID_ALUMNO
);

/*==============================================================*/
/* Index: ALUMNO_PROGRAMA_PK                                */
/*==============================================================*/
create unique index ALUMNO_PROGRAMA_PK on ALUMNO_PROGRAMA (
ALPR_ID_ALUMNO_PROGRAMA
);

/*==============================================================*/
/* Index: REFERENCE_55_FK                                   */
/*==============================================================*/
create  index REFERENCE_55_FK on ALUMNO_PROGRAMA (
ALPR_ID_MODALIDAD
);

/*==============================================================*/
/* Table: ARTICULO                                          */
/*==============================================================*/
create table ARTICULO (
   ARTI_ID_AUTOR            SERIAL               not null,
   TEMA_ID_TEMA             INT4                 not null,
   ARTI_TITULO              VARCHAR(200)         not null,
   ARTI_RESUMEN             VARCHAR(500)         not null,
   ARTI_URL_PDF             VARCHAR(256)         not null,
   ARTI_STATUS              VARCHAR(50)          not null,
   ARTI_FECHA_REGISTRO      DATE                 not null,
   ARTI_FECHA_MODIFICACION  DATE                 not null,
   ARTI_COMENTARIO_RECHAZO  VARCHAR(350)         null,
   constraint PK_ARTICULO primary key (ARTI_ID_AUTOR)
);

/*==============================================================*/
/* Index: ARTICULO_PK                                       */
/*==============================================================*/
create unique index ARTICULO_PK on ARTICULO (
ARTI_ID_AUTOR
);

/*==============================================================*/
/* Index: ARTICULOTEMA_FK                                   */
/*==============================================================*/
create  index ARTICULOTEMA_FK on ARTICULO (
TEMA_ID_TEMA
);

/*==============================================================*/
/* Table: AUTOR                                             */
/*==============================================================*/
create table AUTOR (
   AUTO_ID_AUTOR            SERIAL               not null,
   AUTO_ID_COAUTOR          INT4                 null,
   PERS_ID_PERSONA          INT4                 null,
   AUTO_INSTITUCION         VARCHAR(100)         null,
   AUTO_GRADO_ACADEMICO     VARCHAR(100)         null,
   constraint PK_AUTOR primary key (AUTO_ID_AUTOR)
);

/*==============================================================*/
/* Index: AUTOR_PK                                          */
/*==============================================================*/
create unique index AUTOR_PK on AUTOR (
AUTO_ID_AUTOR
);

/*==============================================================*/
/* Index: PERSONAAUTOR2_FK                                  */
/*==============================================================*/
create  index PERSONAAUTOR2_FK on AUTOR (
PERS_ID_PERSONA
);

/*==============================================================*/
/* Index: COAUTOR_FK                                        */
/*==============================================================*/
create  index COAUTOR_FK on AUTOR (
AUTO_ID_COAUTOR
);

/*==============================================================*/
/* Table: AUTOR_ARTICULO                                    */
/*==============================================================*/
create table AUTOR_ARTICULO (
   AUAR_ID_AUTOR_ARTICULO   SERIAL               not null,
   AUAR_ID_AUTOR            INT4                 not null,
   ARTI_ID_AUTOR            INT4                 not null,
   AUAR_PRINCIPAL           BOOL default false   not null,
   constraint PK_AUTOR_ARTICULO primary key (AUAR_ID_AUTOR_ARTICULO)
);

/*==============================================================*/
/* Index: AUTORARTICULO_PK                                  */
/*==============================================================*/
create unique index AUTORARTICULO_PK on AUTOR_ARTICULO (
AUAR_ID_AUTOR_ARTICULO
);

/*==============================================================*/
/* Index: AUTORARTICULO_FK                                  */
/*==============================================================*/
create  index AUTORARTICULO_FK on AUTOR_ARTICULO (
AUAR_ID_AUTOR
);

/*==============================================================*/
/* Index: AUTORARTICULO2_FK                                 */
/*==============================================================*/
create  index AUTORARTICULO2_FK on AUTOR_ARTICULO (
ARTI_ID_AUTOR
);

/*==============================================================*/
/* Table: BANCO                                             */
/*==============================================================*/
create table BANCO (
   BANC_ID_BANCO            SERIAL               not null,
   BANC_BANCO               VARCHAR(75)          not null,
   BANC_CONVENIO            VARCHAR(100)         null,
   constraint PK_BANCO primary key (BANC_ID_BANCO)
);

/*==============================================================*/
/* Index: BANCO_PK                                          */
/*==============================================================*/
create unique index BANCO_PK on BANCO (
BANC_ID_BANCO
);

/*==============================================================*/
/* Table: CODIGO_POSTAL                                     */
/*==============================================================*/
create table CODIGO_POSTAL (
   COPO_ID_CODIGO_POSTAL    VARCHAR(5)           not null,
   constraint PK_CODIGO_POSTAL primary key (COPO_ID_CODIGO_POSTAL)
);

/*==============================================================*/
/* Index: CODIGO_POSTAL_PK                                  */
/*==============================================================*/
create unique index CODIGO_POSTAL_PK on CODIGO_POSTAL (
COPO_ID_CODIGO_POSTAL
);

/*==============================================================*/
/* Table: CONSTANCIA                                        */
/*==============================================================*/
create table CONSTANCIA (
   CONS_ID_CONSTANCIA       SERIAL               not null,
   ARTI_ID_AUTOR            INT4                 not null,
   CONS_FECHA_EMISION       DATE                 not null,
   CONS_URL_ARCHIVO         VARCHAR(256)         not null,
   CONS_FOLIO               VARCHAR(30)          null,
   CONS_TIPO                VARCHAR(15)          not null,
   constraint PK_CONSTANCIA primary key (CONS_ID_CONSTANCIA)
);

/*==============================================================*/
/* Index: CONSTANCIA_PK                                     */
/*==============================================================*/
create unique index CONSTANCIA_PK on CONSTANCIA (
CONS_ID_CONSTANCIA
);

/*==============================================================*/
/* Index: ARTICULOCONSTANCIA_FK                             */
/*==============================================================*/
create  index ARTICULOCONSTANCIA_FK on CONSTANCIA (
ARTI_ID_AUTOR
);

/*==============================================================*/
/* Table: COMUNICACION                                      */
/*==============================================================*/
create table COMUNICACION (
   COMU_ID_COMUNICACION         SERIAL               not null,
   PERS_ID_PERSONA          INT4                 null,
   COMU_ID_TIPO_COMUNICACION    INT4                 not null,
   COMU_VALOR_COMUNICACION      VARCHAR(254)         not null,
   COMU_EXTENSION           VARCHAR(5)           null,
   COMU_ETIQUETA            VARCHAR(50)          not null,
   COMU_ACTIVO              BOOL                 not null,
   constraint PK_COMUNICACION primary key (COMU_ID_COMUNICACION)
);

/*==============================================================*/
/* Index: COMUNICACION_PK                                   */
/*==============================================================*/
create unique index COMUNICACION_PK on COMUNICACION (
COMU_ID_COMUNICACION
);

/*==============================================================*/
/* Index: RELATIONSHIP_41_FK                                */
/*==============================================================*/
create  index RELATIONSHIP_41_FK on COMUNICACION (
COMU_ID_TIPO_COMUNICACION
);

/*==============================================================*/
/* Index: RELATIONSHIP_42_FK                                */
/*==============================================================*/
create  index RELATIONSHIP_42_FK on COMUNICACION (
PERS_ID_PERSONA
);

/*==============================================================*/
/* Table: COSTO                                             */
/*==============================================================*/
create table COSTO (
   COST_ID_COSTO            SERIAL               not null,
   COST_NOMBRE              CHAR(30)             not null,
   COST_PRECIO              MONEY                not null,
   COST_FECHA_INICIO        DATE                 not null,
   COST_FECHA_FIN           DATE                 not null,
   constraint PK_COSTO primary key (COST_ID_COSTO)
);

/*==============================================================*/
/* Index: COSTO_PK                                          */
/*==============================================================*/
create unique index COSTO_PK on COSTO (
COST_ID_COSTO
);

/*==============================================================*/
/* Table: CREDENCIAL_UNAM                                   */
/*==============================================================*/
create table CREDENCIAL_UNAM (
   CRED_ID_CREDENCIAL       SERIAL               not null,
   USUA_ID_USUARIO          INT4                 not null,
   CRED_NUMERO              VARCHAR(12)          not null,
   CRED_URL_ARCHIVO         VARCHAR(256)         not null,
   CRED_ESTADO              VARCHAR(20)          not null,
   CRED_COMENTARIO          VARCHAR(256)         not null,
   CRED_FECHA_REGISTRO      DATE                 not null,
   CRED_FECHA_REVISION      DATE                 not null,
   constraint PK_CREDENCIAL_UNAM primary key (CRED_ID_CREDENCIAL)
);

/*==============================================================*/
/* Index: CREDENCIAL_UNAM_PK                                */
/*==============================================================*/
create unique index CREDENCIAL_UNAM_PK on CREDENCIAL_UNAM (
CRED_ID_CREDENCIAL
);

/*==============================================================*/
/* Index: CREDENCIALUSUARIO2_FK                             */
/*==============================================================*/
create  index CREDENCIALUSUARIO2_FK on CREDENCIAL_UNAM (
USUA_ID_USUARIO
);

/*==============================================================*/
/* Table: CURSO                                             */
/*==============================================================*/
create table CURSO (
   CURS_ID_CURSO            SERIAL               not null,
   CURS_NOMBRE_CURSO        VARCHAR(100)         not null,
   CURS_URL                 VARCHAR(256)         not null,
   CURS_INSTITUCION         VARCHAR(150)         not null,
   CURS_FECHA               DATE                 null,
   constraint PK_CURSO primary key (CURS_ID_CURSO)
);

/*==============================================================*/
/* Index: CURSO_PK                                          */
/*==============================================================*/
create unique index CURSO_PK on CURSO (
CURS_ID_CURSO
);

/*==============================================================*/
/* Table: DATO_FISCAL                                       */
/*==============================================================*/
create table DATO_FISCAL (
   PEFI_ID_PERFIL_FISCAL    SERIAL               not null,
   DAFI_ID_REGIMEN_FISCAL   INT4                 not null,
   DAFI_ID_CODIGO_POSTAL    VARCHAR(5)           not null,
   USUA_ID_USUARIO          INT4                 not null,
   PEFI_TIPO_PERSONA        BOOL                 not null,
   PEFI_RFC                 VARCHAR(13)          not null,
   PEFI_RAZON_SOCIAL        VARCHAR(100)         not null,
   PEFI_USO_CFDI            BOOL                 not null,
   constraint PK_DATO_FISCAL primary key (PEFI_ID_PERFIL_FISCAL)
);

/*==============================================================*/
/* Index: PERFIL_FISCAL_PK                                  */
/*==============================================================*/
create unique index PERFIL_FISCAL_PK on DATO_FISCAL (
PEFI_ID_PERFIL_FISCAL
);

/*==============================================================*/
/* Index: PERFILFISCALUSUARIO_FK                            */
/*==============================================================*/
create  index PERFILFISCALUSUARIO_FK on DATO_FISCAL (
USUA_ID_USUARIO
);

/*==============================================================*/
/* Index: REGIMENDATO_FK                                    */
/*==============================================================*/
create  index REGIMENDATO_FK on DATO_FISCAL (
DAFI_ID_REGIMEN_FISCAL
);

/*==============================================================*/
/* Index: CODIGODATO_FK                                     */
/*==============================================================*/
create  index CODIGODATO_FK on DATO_FISCAL (
DAFI_ID_CODIGO_POSTAL
);

/*==============================================================*/
/* Table: DESCUENTO                                         */
/*==============================================================*/
create table DESCUENTO (
   DESC_ID_DESCUENTO        SERIAL               not null,
   DESC_NOMBRE              VARCHAR(256)         not null,
   DESC_DESCUENTO           VARCHAR(256)         null,
   DESC_PORCENTAJE          DECIMAL              not null,
   DESC_FECHA_INICIO        DATE                 not null,
   DESC_FECHA_FIN           DATE                 not null,
   constraint PK_DESCUENTO primary key (DESC_ID_DESCUENTO)
);

/*==============================================================*/
/* Index: DESCUENTO_PK                                      */
/*==============================================================*/
create unique index DESCUENTO_PK on DESCUENTO (
DESC_ID_DESCUENTO
);

/*==============================================================*/
/* Table: ESTADO_PAGO                                       */
/*==============================================================*/
create table ESTADO_PAGO (
   ESPA_ID_ESTADO_PAGO      SERIAL               not null,
   ESPA_ID_TIPO_ESTADO_PAGO INT4                 not null,
   COPA_ID_COMPROBANTE_PAGO INT4                 not null,
   ESPA_FECHA_ESTADO_PAGO   DATE                 not null,
   ESPA_COMENTARIO          TEXT                 null,
   constraint PK_ESTADO_PAGO primary key (ESPA_ID_ESTADO_PAGO)
);

/*==============================================================*/
/* Index: ESTADO_PAGO_PK                                    */
/*==============================================================*/
create unique index ESTADO_PAGO_PK on ESTADO_PAGO (
ESPA_ID_ESTADO_PAGO
);

/*==============================================================*/
/* Index: PAGOESTADOPAGO_FK                                 */
/*==============================================================*/
create  index PAGOESTADOPAGO_FK on ESTADO_PAGO (
COPA_ID_COMPROBANTE_PAGO
);

/*==============================================================*/
/* Index: ESTADOTIPOESTADOPAGO_FK                           */
/*==============================================================*/
create  index ESTADOTIPOESTADOPAGO_FK on ESTADO_PAGO (
ESPA_ID_TIPO_ESTADO_PAGO
);

/*==============================================================*/
/* Table: FAVORITO_ARTICULO                                 */
/*==============================================================*/
create table FAVORITO_ARTICULO (
   FAAR_ID_FAVORITOS_ARTICULO SERIAL               not null,
   USUA_ID_USUARIO          INT4                 null,
   ARTI_ID_AUTOR            INT4                 not null,
   FAAR_FECHA_AGREGADO      DATE                 null,
   constraint PK_FAVORITO_ARTICULO primary key (FAAR_ID_FAVORITOS_ARTICULO)
);

/*==============================================================*/
/* Index: FAVORITO_ARTICULO_PK                              */
/*==============================================================*/
create unique index FAVORITO_ARTICULO_PK on FAVORITO_ARTICULO (
FAAR_ID_FAVORITOS_ARTICULO
);

/*==============================================================*/
/* Index: HISTORIALDARTICULOU_FK                            */
/*==============================================================*/
create  index HISTORIALDARTICULOU_FK on FAVORITO_ARTICULO (
USUA_ID_USUARIO
);

/*==============================================================*/
/* Index: ARTICULOFAVORITOARTICULO_FK                       */
/*==============================================================*/
create  index ARTICULOFAVORITOARTICULO_FK on FAVORITO_ARTICULO (
ARTI_ID_AUTOR
);

/*==============================================================*/
/* Table: FAVORITO_PAGINA                                   */
/*==============================================================*/
create table FAVORITO_PAGINA (
   FAPA_ID_FAVORITOS_PAGINA SERIAL               not null,
   PAGI_ID_PAGINA           INT4                 not null,
   USUA_ID_USUARIO          INT4                 null,
   FAPA_FECHA_AGREGADO      DATE                 not null,
   constraint PK_FAVORITO_PAGINA primary key (FAPA_ID_FAVORITOS_PAGINA)
);

/*==============================================================*/
/* Index: FAVORITO_PAGINA_PK                                */
/*==============================================================*/
create unique index FAVORITO_PAGINA_PK on FAVORITO_PAGINA (
FAPA_ID_FAVORITOS_PAGINA
);

/*==============================================================*/
/* Index: FAVORITOPAGINAUSUARIO_FK                          */
/*==============================================================*/
create  index FAVORITOPAGINAUSUARIO_FK on FAVORITO_PAGINA (
USUA_ID_USUARIO
);

/*==============================================================*/
/* Index: PAGINAFAVORITOREVISTA_FK                          */
/*==============================================================*/
create  index PAGINAFAVORITOREVISTA_FK on FAVORITO_PAGINA (
PAGI_ID_PAGINA
);

/*==============================================================*/
/* Table: FAVORITO_REVISTA                                  */
/*==============================================================*/
create table FAVORITO_REVISTA (
   FARE_ID_FAVORITOS_REVISTA SERIAL               not null,
   USUA_ID_USUARIO          INT4                 not null,
   REVI_ID_REVISTA          INT4                 not null,
   FARE_FECHA_AGREGADO      DATE                 not null,
   constraint PK_FAVORITO_REVISTA primary key (FARE_ID_FAVORITOS_REVISTA)
);

/*==============================================================*/
/* Index: FAVORITO_REVISTA_PK                               */
/*==============================================================*/
create unique index FAVORITO_REVISTA_PK on FAVORITO_REVISTA (
FARE_ID_FAVORITOS_REVISTA
);

/*==============================================================*/
/* Index: REVISTAFAVORITOREVISTA_FK                         */
/*==============================================================*/
create  index REVISTAFAVORITOREVISTA_FK on FAVORITO_REVISTA (
REVI_ID_REVISTA
);

/*==============================================================*/
/* Index: FAVORITOREVISTAUSUARIO_FK                         */
/*==============================================================*/
create  index FAVORITOREVISTAUSUARIO_FK on FAVORITO_REVISTA (
USUA_ID_USUARIO
);

/*==============================================================*/
/* Table: HISTORIAL_DESCARGA_ARTICULO                       */
/*==============================================================*/
create table HISTORIAL_DESCARGA_ARTICULO (
   HIDA_ID_HIS_DES_ART      SERIAL               not null,
   USUA_ID_USUARIO          INT4                 null,
   ARTI_ID_AUTOR            INT4                 not null,
   HIDA_FECHA_DESCARGA      DATE                 not null,
   constraint PK_HISTORIAL_DESCARGA_ARTICULO primary key (HIDA_ID_HIS_DES_ART)
);

/*==============================================================*/
/* Index: HISTORIAL_DESCARGA_ARTICULO_PK                    */
/*==============================================================*/
create unique index HISTORIAL_DESCARGA_ARTICULO_PK on HISTORIAL_DESCARGA_ARTICULO (
HIDA_ID_HIS_DES_ART
);

/*==============================================================*/
/* Index: FAVORITOARTICULOUSUARIO_FK                        */
/*==============================================================*/
create  index FAVORITOARTICULOUSUARIO_FK on HISTORIAL_DESCARGA_ARTICULO (
USUA_ID_USUARIO
);

/*==============================================================*/
/* Index: RELATIONSHIP_13_FK                                */
/*==============================================================*/
create  index RELATIONSHIP_13_FK on HISTORIAL_DESCARGA_ARTICULO (
ARTI_ID_AUTOR
);

/*==============================================================*/
/* Table: HISTORIAL_DESCARGA_REVISTA                        */
/*==============================================================*/
create table HISTORIAL_DESCARGA_REVISTA (
   HIDR_ID_HIS_DES_REV      SERIAL               not null,
   USUA_ID_USUARIO          INT4                 not null,
   REVI_ID_REVISTA          INT4                 null,
   HIDR_FECHA_DESCARGA      DATE                 not null,
   HIDR_LUGAR_DESCARGA      VARCHAR(100)         not null,
   constraint PK_HISTORIAL_DESCARGA_REVISTA primary key (HIDR_ID_HIS_DES_REV)
);

/*==============================================================*/
/* Index: HISTORIAL_DESCARGA_REVISTA_PK                     */
/*==============================================================*/
create unique index HISTORIAL_DESCARGA_REVISTA_PK on HISTORIAL_DESCARGA_REVISTA (
HIDR_ID_HIS_DES_REV
);

/*==============================================================*/
/* Index: REVISTAHISDESCREV_FK                              */
/*==============================================================*/
create  index REVISTAHISDESCREV_FK on HISTORIAL_DESCARGA_REVISTA (
REVI_ID_REVISTA
);

/*==============================================================*/
/* Index: HISTORIALDREVISTAU_FK                             */
/*==============================================================*/
create  index HISTORIALDREVISTAU_FK on HISTORIAL_DESCARGA_REVISTA (
USUA_ID_USUARIO
);

/*==============================================================*/
/* Table: HISTORIAL_LECTURA_ARTICULO                        */
/*==============================================================*/
create table HISTORIAL_LECTURA_ARTICULO (
   HILA_ID_HIS_LEC_ART      SERIAL               not null,
   ARTI_ID_AUTOR            INT4                 not null,
   USUA_ID_USUARIO          INT4                 null,
   HILA_FECHA_LECTURA       DATE                 not null,
   constraint PK_HISTORIAL_LECTURA_ARTICULO primary key (HILA_ID_HIS_LEC_ART)
);

/*==============================================================*/
/* Index: HISTORIAL_LECTURA_ARTICULO_PK                     */
/*==============================================================*/
create unique index HISTORIAL_LECTURA_ARTICULO_PK on HISTORIAL_LECTURA_ARTICULO (
HILA_ID_HIS_LEC_ART
);

/*==============================================================*/
/* Index: RELATIONSHIP_9_FK                                 */
/*==============================================================*/
create  index RELATIONSHIP_9_FK on HISTORIAL_LECTURA_ARTICULO (
USUA_ID_USUARIO
);

/*==============================================================*/
/* Index: ARTICULOHISTLECTART_FK                            */
/*==============================================================*/
create  index ARTICULOHISTLECTART_FK on HISTORIAL_LECTURA_ARTICULO (
ARTI_ID_AUTOR
);

/*==============================================================*/
/* Table: HISTORIAL_LECTURA_REVISTA                         */
/*==============================================================*/
create table HISTORIAL_LECTURA_REVISTA (
   HILR_ID_HIS_LEC_REV      SERIAL               not null,
   REVI_ID_REVISTA          INT4                 null,
   USUA_ID_USUARIO          INT4                 not null,
   HILR_FECHA_LECTURA       DATE                 not null,
   constraint PK_HISTORIAL_LECTURA_REVISTA primary key (HILR_ID_HIS_LEC_REV)
);

/*==============================================================*/
/* Index: HISTORIAL_LECTURA_REVISTA_PK                      */
/*==============================================================*/
create unique index HISTORIAL_LECTURA_REVISTA_PK on HISTORIAL_LECTURA_REVISTA (
HILR_ID_HIS_LEC_REV
);

/*==============================================================*/
/* Index: REVISTAHISTLECTREV_FK                             */
/*==============================================================*/
create  index REVISTAHISTLECTREV_FK on HISTORIAL_LECTURA_REVISTA (
REVI_ID_REVISTA
);

/*==============================================================*/
/* Index: HISTORIALLECTURAREVISTA_FK                        */
/*==============================================================*/
create  index HISTORIALLECTURAREVISTA_FK on HISTORIAL_LECTURA_REVISTA (
USUA_ID_USUARIO
);

/*==============================================================*/
/* Table: MENSAJE                                           */
/*==============================================================*/
create table MENSAJE (
   MENS_ID_MENSAJE          SERIAL               not null,
   USUA_ID_USUARIO          INT4                 null,
   MEN_MENS_ID_MENSAJE      INT4                 null,
   MENS_REMITENTE           VARCHAR(75)          not null,
   MENS_LUGAR               VARCHAR(100)         null,
   MENS_CORREO              VARCHAR(254)         not null,
   MENS_ASUNTO              VARCHAR(50)          not null,
   MENS_DESCRIPCION         VARCHAR(700)         not null,
   MENS_STATUS              VARCHAR(75)          not null,
   MENS_FECHA_ENVIO         TIMESTAMP default now() not null,
   constraint PK_MENSAJE primary key (MENS_ID_MENSAJE)
);

/*==============================================================*/
/* Index: MENSAJE_PK                                        */
/*==============================================================*/
create unique index MENSAJE_PK on MENSAJE (
MENS_ID_MENSAJE
);

/*==============================================================*/
/* Index: MENSAJEUSUARIO_FK                                 */
/*==============================================================*/
create  index MENSAJEUSUARIO_FK on MENSAJE (
USUA_ID_USUARIO
);

/*==============================================================*/
/* Index: REFERENCE_59_FK                                   */
/*==============================================================*/
create  index REFERENCE_59_FK on MENSAJE (
MEN_MENS_ID_MENSAJE
);

/*==============================================================*/
/* Table: METODO_PAGO                                       */
/*==============================================================*/
create table METODO_PAGO (
   MEPA_ID_METODO_PAGO      SERIAL               not null,
   MEPA_METODO_PAGO         VARCHAR(55)          not null,
   constraint PK_METODO_PAGO primary key (MEPA_ID_METODO_PAGO)
);

/*==============================================================*/
/* Index: METODO_PAGO_PK                                    */
/*==============================================================*/
create unique index METODO_PAGO_PK on METODO_PAGO (
MEPA_ID_METODO_PAGO
);

/*==============================================================*/
/* Table: MODALIDAD                                         */
/*==============================================================*/
create table MODALIDAD (
   MODA_ID_MODALIDAD        SERIAL               not null,
   MODA_NOMBRE              VARCHAR(100)         not null,
   constraint PK_MODALIDAD primary key (MODA_ID_MODALIDAD)
);

/*==============================================================*/
/* Index: C_MODALIDAD_PK                                    */
/*==============================================================*/
create unique index C_MODALIDAD_PK on MODALIDAD (
MODA_ID_MODALIDAD
);

/*==============================================================*/
/* Table: NIVEL                                             */
/*==============================================================*/
create table NIVEL (
   NIVE_ID_NIVEL            SERIAL               not null,
   NIVE_NOMBRE              VARCHAR(100)         not null,
   constraint PK_NIVEL primary key (NIVE_ID_NIVEL)
);

/*==============================================================*/
/* Index: C_NIVEL_PK                                        */
/*==============================================================*/
create unique index C_NIVEL_PK on NIVEL (
NIVE_ID_NIVEL
);

/*==============================================================*/
/* Table: NOTIFICACION                                      */
/*==============================================================*/
create table NOTIFICACION (
   NOTI_ID_NOTIFICACION     SERIAL               not null,
   USUA_ID_USUARIO          INT4                 not null,
   NOTI_TITULO              VARCHAR(75)          not null,
   NOTI_MENSAJE             VARCHAR(500)         not null,
   NOTI_LEIDA               BOOL                 not null,
   NOTI_FECHA_ENVIO         DATE                 not null,
   ARTI_ID_AUTOR            INT4                 null,
   constraint PK_NOTIFICACION primary key (NOTI_ID_NOTIFICACION)
);

/*==============================================================*/
/* Index: NOTIFICACION_PK                                   */
/*==============================================================*/
create unique index NOTIFICACION_PK on NOTIFICACION (
NOTI_ID_NOTIFICACION
);

/*==============================================================*/
/* Index: NOTIFICACIONUSUARIO_FK                            */
/*==============================================================*/
create  index NOTIFICACIONARTICULO_FK on NOTIFICACION (
ARTI_ID_AUTOR
);

/*==============================================================*/
/* Index: NOTIFICACIONUSUARIO_FK                            */
/*==============================================================*/
create  index NOTIFICACIONUSUARIO_FK on NOTIFICACION (
USUA_ID_USUARIO
);

/*==============================================================*/
/* Table: PAGINA                                            */
/*==============================================================*/
create table PAGINA (
   PAGI_ID_PAGINA           SERIAL               not null,
   REVI_ID_REVISTA          INT4                 not null,
   PAGI_NUM_PAGINA          VARCHAR(4)           not null,
   PAGI_URL_IMAGEN          VARCHAR(256)         not null,
   constraint PK_PAGINA primary key (PAGI_ID_PAGINA)
);

/*==============================================================*/
/* Index: PAGINA_PK                                         */
/*==============================================================*/
create unique index PAGINA_PK on PAGINA (
PAGI_ID_PAGINA
);

/*==============================================================*/
/* Index: PAGINAREVISTA_FK                                  */
/*==============================================================*/
create  index PAGINAREVISTA_FK on PAGINA (
REVI_ID_REVISTA
);

/*==============================================================*/
/* Table: PAGO                                              */
/*==============================================================*/
create table PAGO (
   COPA_ID_COMPROBANTE_PAGO SERIAL               not null,
   PAGO_ID_BANCO            INT4                 null,
   FIDE_ID_FICHA_DEPOSITO   INT4                 not null,
   PAGO_ID_RESPONSABLE      INT4                 null,
   PAGO_ID_METODO_PAGO      INT4                 null,
   COPA_URL_ARCHIVO         VARCHAR(100)         not null,
   COPA_FECHA_CARGA         DATE                 not null,
   COPA_ESTADO              DECIMAL(10,4)        not null,
   COPA_COMENTARIO          VARCHAR(25)          not null,
   constraint PK_PAGO primary key (COPA_ID_COMPROBANTE_PAGO)
);

/*==============================================================*/
/* Index: COMPROBANTE_PAGO_PK                               */
/*==============================================================*/
create unique index COMPROBANTE_PAGO_PK on PAGO (
COPA_ID_COMPROBANTE_PAGO
);

/*==============================================================*/
/* Index: COMPROBANTEPOSITO2_FK                             */
/*==============================================================*/
create  index COMPROBANTEPOSITO2_FK on PAGO (
FIDE_ID_FICHA_DEPOSITO
);

/*==============================================================*/
/* Index: RESPONSABLEPAGO_FK                                */
/*==============================================================*/
create  index RESPONSABLEPAGO_FK on PAGO (
PAGO_ID_RESPONSABLE
);

/*==============================================================*/
/* Index: BANCOPAGO_FK                                      */
/*==============================================================*/
create  index BANCOPAGO_FK on PAGO (
PAGO_ID_BANCO
);

/*==============================================================*/
/* Index: PAGOMETODOPAGO_FK                                 */
/*==============================================================*/
create  index PAGOMETODOPAGO_FK on PAGO (
PAGO_ID_METODO_PAGO
);

/*==============================================================*/
/* Table: PALABRA                                           */
/*==============================================================*/
create table PALABRA (
   PALABRA_ID_PALABRA       SERIAL               not null,
   PALA_DESCRIPCION         VARCHAR(100)         not null,
   constraint PK_PALABRA primary key (PALABRA_ID_PALABRA)
);

/*==============================================================*/
/* Index: PALABRA_PK                                        */
/*==============================================================*/
create unique index PALABRA_PK on PALABRA (
PALABRA_ID_PALABRA
);

/*==============================================================*/
/* Table: PALABRA_ARTICULO                                  */
/*==============================================================*/
create table PALABRA_ARTICULO (
   PAAR_ID_PALABRA_ARTICULO SERIAL               not null,
   PALABRA_ID_PALABRA       INT4                 not null,
   ARTI_ID_AUTOR            INT4                 not null,
   constraint PK_PALABRA_ARTICULO primary key (PAAR_ID_PALABRA_ARTICULO)
);

/*==============================================================*/
/* Index: PALABRA_ARTICULO_PK                               */
/*==============================================================*/
create unique index PALABRA_ARTICULO_PK on PALABRA_ARTICULO (
PAAR_ID_PALABRA_ARTICULO
);

/*==============================================================*/
/* Index: REFERENCE_57_FK                                   */
/*==============================================================*/
create  index REFERENCE_57_FK on PALABRA_ARTICULO (
ARTI_ID_AUTOR
);

/*==============================================================*/
/* Index: REFERENCE_58_FK                                   */
/*==============================================================*/
create  index REFERENCE_58_FK on PALABRA_ARTICULO (
PALABRA_ID_PALABRA
);

/*==============================================================*/
/* Table: PALABRA_CURSO                                     */
/*==============================================================*/
create table PALABRA_CURSO (
   PACU_ID_PALABRA_CURSO    SERIAL               not null,
   CURS_ID_CURSO            INT4                 not null,
   PALABRA_ID_PALABRA       INT4                 not null,
   constraint PK_PALABRA_CURSO primary key (PACU_ID_PALABRA_CURSO)
);

/*==============================================================*/
/* Index: PALABRA_CURSO_PK                                  */
/*==============================================================*/
create unique index PALABRA_CURSO_PK on PALABRA_CURSO (
PACU_ID_PALABRA_CURSO
);

/*==============================================================*/
/* Index: REFERENCE_60_FK                                   */
/*==============================================================*/
create  index REFERENCE_60_FK on PALABRA_CURSO (
CURS_ID_CURSO
);

/*==============================================================*/
/* Index: REFERENCE_61_FK                                   */
/*==============================================================*/
create  index REFERENCE_61_FK on PALABRA_CURSO (
PALABRA_ID_PALABRA
);

/*==============================================================*/
/* Table: PERSONA                                           */
/*==============================================================*/
create table PERSONA (
   PERS_ID_PERSONA          SERIAL               not null,
   PERS_NOMBRE              VARCHAR(100)         not null,
   PERS_PRIMER_APELLIDO     VARCHAR(100)         not null,
   PERS_SEGUNDO_APELLIDO    VARCHAR(100)         null,
   PERS_SUBS_NEWSLETTER     BOOL default false   not null,
   PERS_ACEPTA_AVISO        BOOL default false   not null,
   PERS_FECHA_SUSCRIPCION   TIMESTAMP            null,
   PERS_TOKEN_CONFIRMACION  VARCHAR(64)          null,
   PERS_FECHA_CONFIRMACION  TIMESTAMP            null,
   PERS_FECHA_BAJA          TIMESTAMP            null,
   constraint PK_PERSONA primary key (PERS_ID_PERSONA)
);

/*==============================================================*/
/* Index: PERSONA_PK                                        */
/*==============================================================*/
create unique index PERSONA_PK on PERSONA (
PERS_ID_PERSONA
);

/*==============================================================*/
/* Table: PRIVILEGIO                                        */
/*==============================================================*/
create table PRIVILEGIO (
   PRIV_ID_PRIVILEGIO       SERIAL               not null,
   PRIV_NOMBRE              VARCHAR(50)          not null,
   PRIV_DESCRIPCION         VARCHAR(100)         not null,
   constraint PK_PRIVILEGIO primary key (PRIV_ID_PRIVILEGIO)
);

/*==============================================================*/
/* Index: PRIVILEGIO_PK                                     */
/*==============================================================*/
create unique index PRIVILEGIO_PK on PRIVILEGIO (
PRIV_ID_PRIVILEGIO
);

/*==============================================================*/
/* Table: PROFESOR                                          */
/*==============================================================*/
create table PROFESOR (
   PROF_ID_PROFESOR         SERIAL               not null,
   PERS_ID_PERSONA          INT4                 not null,
   PROF_RFC                 VARCHAR(13)          not null,
   PROF_NUMERO_TRABAJADOR   VARCHAR(10)          not null,
   constraint PK_PROFESOR primary key (PROF_ID_PROFESOR)
);

/*==============================================================*/
/* Index: PROFESOR_PK                                       */
/*==============================================================*/
create unique index PROFESOR_PK on PROFESOR (
PROF_ID_PROFESOR
);

/*==============================================================*/
/* Index: RELATIONSHIP_24_FK                                */
/*==============================================================*/
create  index RELATIONSHIP_24_FK on PROFESOR (
PERS_ID_PERSONA
);

/*==============================================================*/
/* Table: PROGRAMA_ACADEMICO                                */
/*==============================================================*/
create table PROGRAMA_ACADEMICO (
   PRAC_ID_PROGRAMA_ACADEMICO SERIAL               not null,
   PRAC_ID_NIVEL            INT4                 null,
   PRAC_NOMBRE              VARCHAR(256)         not null,
   constraint PK_PROGRAMA_ACADEMICO primary key (PRAC_ID_PROGRAMA_ACADEMICO)
);

/*==============================================================*/
/* Index: C_PROGRAMA_ACADEMICO_PK                           */
/*==============================================================*/
create unique index C_PROGRAMA_ACADEMICO_PK on PROGRAMA_ACADEMICO (
PRAC_ID_PROGRAMA_ACADEMICO
);

/*==============================================================*/
/* Index: REFERENCE_56_FK                                   */
/*==============================================================*/
create  index REFERENCE_56_FK on PROGRAMA_ACADEMICO (
PRAC_ID_NIVEL
);

/*==============================================================*/
/* Table: REFERENCIA_BANCARIA                               */
/*==============================================================*/
create table REFERENCIA_BANCARIA (
   FIDE_ID_FICHA_DEPOSITO   SERIAL               not null,
   FIDE_REFERENCIA          VARCHAR(20)          not null,
   FIDE_URL_ARCHIVO         VARCHAR(100)         not null,
   FIDE_NUMERO_CONVENIO     VARCHAR(8)           not null,
   FIDE_FECHA_CREACION      DATE                 not null,
   FIDE_FECHA_VENCIMIENTO   DATE                 not null,
   FIDE_CONCEPTO            VARCHAR(50)          not null,
   REBA_FECHA_CARGA         DATE                 not null,
   REBA_VIGENCIA            DATE                 not null,
   REBA_MONTO               DECIMAL(10,4)        not null,
   REBA_ESPECIAL            BOOL                 not null,
   constraint PK_REFERENCIA_BANCARIA primary key (FIDE_ID_FICHA_DEPOSITO)
);

/*==============================================================*/
/* Index: FICHA_DEPOSITO_PK                                 */
/*==============================================================*/
create unique index FICHA_DEPOSITO_PK on REFERENCIA_BANCARIA (
FIDE_ID_FICHA_DEPOSITO
);

/*==============================================================*/
/* Table: REGIMEN_FISCAL                                    */
/*==============================================================*/
create table REGIMEN_FISCAL (
   REFI_ID_REGIMEN_FISCAL   SERIAL               not null,
   REFI_REGIMEN_FISCAL      VARCHAR(35)          not null,
   constraint PK_REGIMEN_FISCAL primary key (REFI_ID_REGIMEN_FISCAL)
);

/*==============================================================*/
/* Index: REGIMEN_FISCAL_PK                                 */
/*==============================================================*/
create unique index REGIMEN_FISCAL_PK on REGIMEN_FISCAL (
REFI_ID_REGIMEN_FISCAL
);

/*==============================================================*/
/* Table: RESPONSABLE                                       */
/*==============================================================*/
create table RESPONSABLE (
   RESP_ID_RESPONSABLE      SERIAL               not null,
   RESP_NOMBRE              VARCHAR(55)          not null,
   RESP_PRIMER_APELLIDO     VARCHAR(55)          not null,
   RESP_SEGUNDO_APELLIDO    VARCHAR(55)          not null,
   constraint PK_RESPONSABLE primary key (RESP_ID_RESPONSABLE)
);

/*==============================================================*/
/* Index: RESPONSABLE_PK                                    */
/*==============================================================*/
create unique index RESPONSABLE_PK on RESPONSABLE (
RESP_ID_RESPONSABLE
);

/*==============================================================*/
/* Table: REVISTA                                           */
/*==============================================================*/
create table REVISTA (
   REVI_ID_REVISTA          SERIAL               not null,
   USUA_ID_USUARIO          INT4                 not null,
   REVI_TITULO              VARCHAR(60)          not null,
   REVI_ANIO                VARCHAR(4)           not null,
   REVI_MES                 VARCHAR(20)          not null,
   REVI_NUM_EDICION         VARCHAR(3)           not null,
   REVI_URL_PORTADA         VARCHAR(256)         not null,
   REVI_URL_REVISTA_PDF     VARCHAR(256)         not null,
   REVI_FECHA_PUBLICACION   DATE                 not null,
   REVI_VISIBLE             BOOL                 not null,
   REVI_URL_FLIPBOOK        VARCHAR(256)         null,
   REVI_EN_PORTADA          BOOL                 not null default false,
   constraint PK_REVISTA primary key (REVI_ID_REVISTA)
);

/*==============================================================*/
/* Index: REVISTA_PK                                        */
/*==============================================================*/
create unique index REVISTA_PK on REVISTA (
REVI_ID_REVISTA
);

/*==============================================================*/
/* Index: REVISTAUSUARIO_FK                                 */
/*==============================================================*/
create  index REVISTAUSUARIO_FK on REVISTA (
USUA_ID_USUARIO
);

/*==============================================================*/
/* Table: ROL                                               */
/*==============================================================*/
create table ROL (
   ROL_ID_ROL               SERIAL               not null,
   ROL_NOMBRE               VARCHAR(50)          not null,
   ROL_DESCRIPCION          VARCHAR(100)         not null,
   constraint PK_ROL primary key (ROL_ID_ROL)
);

/*==============================================================*/
/* Index: ROL_PK                                            */
/*==============================================================*/
create unique index ROL_PK on ROL (
ROL_ID_ROL
);

/*==============================================================*/
/* Table: ROL_PRIVILEGIO                                    */
/*==============================================================*/
create table ROL_PRIVILEGIO (
   ROPR_ID_ROL_PRIVILEGIO   SERIAL               not null,
   ROL_ID_ROL               INT4                 not null,
   ROPR_ID_PRIVILEGIO       INT4                 not null,
   constraint PK_ROL_PRIVILEGIO primary key (ROPR_ID_ROL_PRIVILEGIO)
);

/*==============================================================*/
/* Index: ROLPRIVILEGIO_PK                                  */
/*==============================================================*/
create unique index ROLPRIVILEGIO_PK on ROL_PRIVILEGIO (
ROPR_ID_ROL_PRIVILEGIO
);

/*==============================================================*/
/* Index: ROLPRIVILEGIO_FK                                  */
/*==============================================================*/
create  index ROLPRIVILEGIO_FK on ROL_PRIVILEGIO (
ROL_ID_ROL
);

/*==============================================================*/
/* Index: ROLPRIVILEGIO2_FK                                 */
/*==============================================================*/
create  index ROLPRIVILEGIO2_FK on ROL_PRIVILEGIO (
ROPR_ID_PRIVILEGIO
);

/*==============================================================*/
/* Table: ROL_USUARIO                                       */
/*==============================================================*/
create table ROL_USUARIO (
   ROUS_ID_ROL_USUARIO      SERIAL               not null,
   USUA_ID_USUARIO          INT4                 not null,
   ROL_ID_ROL               INT4                 not null,
   constraint PK_ROL_USUARIO primary key (ROUS_ID_ROL_USUARIO)
);

/*==============================================================*/
/* Index: ROLUSUARIO_PK                                     */
/*==============================================================*/
create unique index ROLUSUARIO_PK on ROL_USUARIO (
ROUS_ID_ROL_USUARIO
);

/*==============================================================*/
/* Index: ROLUSUARIO_FK                                     */
/*==============================================================*/
create  index ROLUSUARIO_FK on ROL_USUARIO (
USUA_ID_USUARIO
);

/*==============================================================*/
/* Index: ROLUSUARIO2_FK                                    */
/*==============================================================*/
create  index ROLUSUARIO2_FK on ROL_USUARIO (
ROL_ID_ROL
);

/*==============================================================*/
/* Table: SOLICITUD_FACTURA                                 */
/*==============================================================*/
create table SOLICITUD_FACTURA (
   SOFA_ID_NOTIFICACION     SERIAL               not null,
   USUA_ID_USUARIO          INT4                 not null,
   SOFA_FECHA_SOLICITUD     DATE                 not null,
   SOFA_ESTADO              VARCHAR(20)          not null,
   SOFA_COMENTARIO          VARCHAR(256)         not null,
   SOFA_FECHA_REVISION      DATE                 not null,
   constraint PK_SOLICITUD_FACTURA primary key (SOFA_ID_NOTIFICACION)
);

/*==============================================================*/
/* Index: SOLICITUD_FACTURA_PK                              */
/*==============================================================*/
create unique index SOLICITUD_FACTURA_PK on SOLICITUD_FACTURA (
SOFA_ID_NOTIFICACION
);

/*==============================================================*/
/* Index: SOLICITUDFACTURAUSUARIO_FK                        */
/*==============================================================*/
create  index SOLICITUDFACTURAUSUARIO_FK on SOLICITUD_FACTURA (
USUA_ID_USUARIO
);

/*==============================================================*/
/* Table: SOLICITUD_SUSCRIPCION                             */
/*==============================================================*/
create table SOLICITUD_SUSCRIPCION (
   SOIN_ID_SOLICITUD_SUSCRIPCION SERIAL               not null,
   COST_ID_COSTO            INT4                 not null,
   USUA_ID_USUARIO          INT4                 not null,
   DESC_ID_DESCUENTO        INT4                 null,
   FIDE_ID_FICHA_DEPOSITO   INT4                 not null,
   SOIN_FECHA_SOLICITUD     DATE                 not null,
   SOIN_ESTADO              VARCHAR(30)          not null,
   SOIN_FECHA_REVISION      DATE                 null,
   SOIN_COMENTARIO          VARCHAR(256)         null,
   SOIN_FECHA_INICIO_VIGENCIA DATE                 null,
   SOIN_FECHA_FIN_VIGENCIA  DATE                 null,
   SOIN_IMPORTE_PAGADO      MONEY                null,
   constraint PK_SOLICITUD_SUSCRIPCION primary key (SOIN_ID_SOLICITUD_SUSCRIPCION)
);

/*==============================================================*/
/* Index: SOLICITUD_SUSCRIPCION_PK                          */
/*==============================================================*/
create unique index SOLICITUD_SUSCRIPCION_PK on SOLICITUD_SUSCRIPCION (
SOIN_ID_SOLICITUD_SUSCRIPCION
);

/*==============================================================*/
/* Index: SOLICITUDSUSCRIBCIONUSUARIO_FK                    */
/*==============================================================*/
create  index SOLICITUDSUSCRIBCIONUSUARIO_FK on SOLICITUD_SUSCRIPCION (
USUA_ID_USUARIO
);

/*==============================================================*/
/* Index: FICHADEPOSITOSOLICITUD_FK                         */
/*==============================================================*/
create  index FICHADEPOSITOSOLICITUD_FK on SOLICITUD_SUSCRIPCION (
FIDE_ID_FICHA_DEPOSITO
);

/*==============================================================*/
/* Index: COSTOSOLICITUDSUSCRIPCION_FK                      */
/*==============================================================*/
create  index COSTOSOLICITUDSUSCRIPCION_FK on SOLICITUD_SUSCRIPCION (
COST_ID_COSTO
);

/*==============================================================*/
/* Index: DESCUENTOSOLICITUD_FK                             */
/*==============================================================*/
create  index DESCUENTOSOLICITUD_FK on SOLICITUD_SUSCRIPCION (
DESC_ID_DESCUENTO
);

/*==============================================================*/
/* Table: TEMA                                              */
/*==============================================================*/
create table TEMA (
   TEMA_ID_TEMA             SERIAL               not null,
   TEMA_NOMBRE              VARCHAR(50)          not null,
   TEMA_DESCRIPCION         VARCHAR(100)         not null,
   constraint PK_TEMA primary key (TEMA_ID_TEMA)
);

/*==============================================================*/
/* Index: TEMA_PK                                           */
/*==============================================================*/
create unique index TEMA_PK on TEMA (
TEMA_ID_TEMA
);

/*==============================================================*/
/* Table: TEMA_NEWS                                         */
/*==============================================================*/
create table TEMA_NEWS (
   TENE_ID_TEMA_NEWS        SERIAL               not null,
   PERS_ID_PERSONA          INT4                 not null,
   TENE_TEMA_INTERES        VARCHAR(256)         not null,
   constraint PK_TEMA_NEWS primary key (TENE_ID_TEMA_NEWS)
);

/*==============================================================*/
/* Index: TEMA_NEWS_PK                                      */
/*==============================================================*/
create unique index TEMA_NEWS_PK on TEMA_NEWS (
TENE_ID_TEMA_NEWS
);

/*==============================================================*/
/* Index: RELATIONSHIP_43_FK                                */
/*==============================================================*/
create  index RELATIONSHIP_43_FK on TEMA_NEWS (
PERS_ID_PERSONA
);

/*==============================================================*/
/* Table: TIPO_COMUNICACION                                 */
/*==============================================================*/
create table TIPO_COMUNICACION (
   COMU_ID_TIPO_COMUNICACION    SERIAL               not null,
   COMU_NOMBRE              VARCHAR(25)          not null,
   constraint PK_TIPO_COMUNICACION primary key (COMU_ID_TIPO_COMUNICACION)
);

/*==============================================================*/
/* Index: TIPO_COMUNICACION_PK                              */
/*==============================================================*/
create unique index TIPO_COMUNICACION_PK on TIPO_COMUNICACION (
COMU_ID_TIPO_COMUNICACION
);

/*==============================================================*/
/* Table: TIPO_ESTADO_PAGO                                  */
/*==============================================================*/
create table TIPO_ESTADO_PAGO (
   TIEP_ID_TIPO_ESTADO_PAGO SERIAL               not null,
   TIEP_ESTADO_PAGO         VARCHAR(15)          not null,
   constraint PK_TIPO_ESTADO_PAGO primary key (TIEP_ID_TIPO_ESTADO_PAGO)
);

/*==============================================================*/
/* Index: TIPO_ESTADO_PAGO_PK                               */
/*==============================================================*/
create unique index TIPO_ESTADO_PAGO_PK on TIPO_ESTADO_PAGO (
TIEP_ID_TIPO_ESTADO_PAGO
);

/*==============================================================*/
/* Table: USUARIO                                           */
/*==============================================================*/
create table USUARIO (
   USUA_ID_USUARIO          SERIAL               not null,
   PERS_ID_PERSONA          INT4                 not null,
   USUA_NOMBRE_USUARIO      VARCHAR(50)          not null,
   USUA_CLAVE_ACCESO        VARCHAR(255)         not null,
   USUA_FECHA_REGISTRO      DATE                 not null,
   USUA_ULTIMO_LOGIN        DATE                 null,
   USUA_ACTIVO              BOOL                 not null,
   USUA_CORREO_VERIFICADO   BOOL default false   not null,
   USUA_FECHA_VERIFICACION  TIMESTAMP            null,
   constraint PK_USUARIO primary key (USUA_ID_USUARIO)
);

/*==============================================================*/
/* Index: USUARIO_PK                                        */
/*==============================================================*/
create unique index USUARIO_PK on USUARIO (
USUA_ID_USUARIO
);

/*==============================================================*/
/* Index: PERSONAUSUARIO_FK                                 */
/*==============================================================*/
create  index PERSONAUSUARIO_FK on USUARIO (
PERS_ID_PERSONA
);

alter table ALUMNO
   add constraint FK_ALUMNO_RELATIONS_PERSONA foreign key (PERS_ID_PERSONA)
      references PERSONA (PERS_ID_PERSONA)
      on delete restrict on update restrict;

alter table ALUMNO_PROGRAMA
   add constraint FK_ALUMNO_P_REFERENCE_MODALIDA foreign key (ALPR_ID_MODALIDAD)
      references MODALIDAD (MODA_ID_MODALIDAD)
      on delete restrict on update restrict;

alter table ALUMNO_PROGRAMA
   add constraint FK_ALUMNO_P_RELATIONS_PROGRAMA foreign key (ALPR_ID_PROGRAMA_ACADEMICO)
      references PROGRAMA_ACADEMICO (PRAC_ID_PROGRAMA_ACADEMICO)
      on delete restrict on update restrict;

alter table ALUMNO_PROGRAMA
   add constraint FK_ALUMNO_P_RELATIONS_ALUMNO foreign key (ALUM_ID_ALUMNO)
      references ALUMNO (ALUM_ID_ALUMNO)
      on delete restrict on update restrict;

alter table ARTICULO
   add constraint FK_ARTICULO_ARTICULOT_TEMA foreign key (TEMA_ID_TEMA)
      references TEMA (TEMA_ID_TEMA)
      on delete restrict on update restrict;

alter table AUTOR
   add constraint FK_AUTOR_COAUTOR_AUTOR foreign key (AUTO_ID_COAUTOR)
      references AUTOR (AUTO_ID_AUTOR)
      on delete restrict on update restrict;

alter table AUTOR
   add constraint FK_AUTOR_PERSONAAU_PERSONA foreign key (PERS_ID_PERSONA)
      references PERSONA (PERS_ID_PERSONA)
      on delete restrict on update restrict;

alter table AUTOR_ARTICULO
   add constraint FK_AUTOR_AR_AUTORARTI_AUTOR foreign key (AUAR_ID_AUTOR)
      references AUTOR (AUTO_ID_AUTOR)
      on delete restrict on update restrict;

alter table AUTOR_ARTICULO
   add constraint FK_AUTOR_AR_AUTORARTI_ARTICULO foreign key (ARTI_ID_AUTOR)
      references ARTICULO (ARTI_ID_AUTOR)
      on delete restrict on update restrict;

alter table CONSTANCIA
   add constraint FK_CONSTANC_ARTICULOC_ARTICULO foreign key (ARTI_ID_AUTOR)
      references ARTICULO (ARTI_ID_AUTOR)
      on delete restrict on update restrict;

alter table COMUNICACION
   add constraint FK_COMUNICACION_RELATIONS_TIPO_CON foreign key (COMU_ID_TIPO_COMUNICACION)
      references TIPO_COMUNICACION (COMU_ID_TIPO_COMUNICACION)
      on delete restrict on update restrict;

alter table COMUNICACION
   add constraint FK_COMUNICACION_RELATIONS_PERSONA foreign key (PERS_ID_PERSONA)
      references PERSONA (PERS_ID_PERSONA)
      on delete restrict on update restrict;

alter table CREDENCIAL_UNAM
   add constraint FK_CREDENCI_CREDENCIA_USUARIO foreign key (USUA_ID_USUARIO)
      references USUARIO (USUA_ID_USUARIO)
      on delete restrict on update restrict;

alter table DATO_FISCAL
   add constraint FK_DATO_FIS_CODIGODAT_CODIGO_P foreign key (DAFI_ID_CODIGO_POSTAL)
      references CODIGO_POSTAL (COPO_ID_CODIGO_POSTAL)
      on delete restrict on update restrict;

alter table DATO_FISCAL
   add constraint FK_DATO_FIS_PF__USUAR_USUARIO foreign key (USUA_ID_USUARIO)
      references USUARIO (USUA_ID_USUARIO)
      on delete restrict on update restrict;

alter table DATO_FISCAL
   add constraint FK_DATO_FIS_REGIMENDA_REGIMEN foreign key (DAFI_ID_REGIMEN_FISCAL)
      references REGIMEN_FISCAL (REFI_ID_REGIMEN_FISCAL)
      on delete restrict on update restrict;

alter table ESTADO_PAGO
   add constraint FK_ESTADO_P_ESTADOTIP_TIPO_EST foreign key (ESPA_ID_TIPO_ESTADO_PAGO)
      references TIPO_ESTADO_PAGO (TIEP_ID_TIPO_ESTADO_PAGO)
      on delete restrict on update restrict;

alter table ESTADO_PAGO
   add constraint FK_ESTADO_P_PAGOESTAD_PAGO foreign key (COPA_ID_COMPROBANTE_PAGO)
      references PAGO (COPA_ID_COMPROBANTE_PAGO)
      on delete restrict on update restrict;

alter table FAVORITO_ARTICULO
   add constraint FK_FAVORITO_AFARTICUL_ARTICULO foreign key (ARTI_ID_AUTOR)
      references ARTICULO (ARTI_ID_AUTOR)
      on delete restrict on update restrict;

alter table FAVORITO_ARTICULO
   add constraint FK_FAVORITO_HDA_USUAR_USUARIO foreign key (USUA_ID_USUARIO)
      references USUARIO (USUA_ID_USUARIO)
      on delete restrict on update restrict;

alter table FAVORITO_PAGINA
   add constraint FK_FAVORITO_FP_USUARI_USUARIO foreign key (USUA_ID_USUARIO)
      references USUARIO (USUA_ID_USUARIO)
      on delete restrict on update restrict;

alter table FAVORITO_PAGINA
   add constraint FK_FAVORITO_PF_REVIST_PAGINA foreign key (PAGI_ID_PAGINA)
      references PAGINA (PAGI_ID_PAGINA)
      on delete restrict on update restrict;

alter table FAVORITO_REVISTA
   add constraint FK_FAVORITO_FR_USUARI_USUARIO foreign key (USUA_ID_USUARIO)
      references USUARIO (USUA_ID_USUARIO)
      on delete restrict on update restrict;

alter table FAVORITO_REVISTA
   add constraint FK_FAVORITO_RF_REVIST_REVISTA foreign key (REVI_ID_REVISTA)
      references REVISTA (REVI_ID_REVISTA)
      on delete restrict on update restrict;

alter table HISTORIAL_DESCARGA_ARTICULO
   add constraint FK_HISTORIA_FA_USUARI_USUARIO foreign key (USUA_ID_USUARIO)
      references USUARIO (USUA_ID_USUARIO)
      on delete restrict on update restrict;

alter table HISTORIAL_DESCARGA_ARTICULO
   add constraint FK_HISTORIA_RELATIONS_ARTICULO foreign key (ARTI_ID_AUTOR)
      references ARTICULO (ARTI_ID_AUTOR)
      on delete restrict on update restrict;

alter table HISTORIAL_DESCARGA_REVISTA
   add constraint FK_HISTORIA_HDR_USUAR_USUARIO foreign key (USUA_ID_USUARIO)
      references USUARIO (USUA_ID_USUARIO)
      on delete restrict on update restrict;

alter table HISTORIAL_DESCARGA_REVISTA
   add constraint FK_HISTORIA_RHD_REVIS_REVISTA foreign key (REVI_ID_REVISTA)
      references REVISTA (REVI_ID_REVISTA)
      on delete restrict on update restrict;

alter table HISTORIAL_LECTURA_ARTICULO
   add constraint FK_HISTORIA_AHL_ARTIC_ARTICULO foreign key (ARTI_ID_AUTOR)
      references ARTICULO (ARTI_ID_AUTOR)
      on delete restrict on update restrict;

alter table HISTORIAL_LECTURA_ARTICULO
   add constraint FK_HISTORIA_RELATIONS_USUARIO foreign key (USUA_ID_USUARIO)
      references USUARIO (USUA_ID_USUARIO)
      on delete restrict on update restrict;

alter table HISTORIAL_LECTURA_REVISTA
   add constraint FK_HISTORIA_HLR_USUAR_USUARIO foreign key (USUA_ID_USUARIO)
      references USUARIO (USUA_ID_USUARIO)
      on delete restrict on update restrict;

alter table HISTORIAL_LECTURA_REVISTA
   add constraint FK_HISTORIA_RHL_REVIS_REVISTA foreign key (REVI_ID_REVISTA)
      references REVISTA (REVI_ID_REVISTA)
      on delete restrict on update restrict;

alter table MENSAJE
   add constraint FK_MENSAJE_MENSAJEUS_USUARIO foreign key (USUA_ID_USUARIO)
      references USUARIO (USUA_ID_USUARIO)
      on delete restrict on update restrict;

alter table MENSAJE
   add constraint FK_MENSAJE_REFERENCE_MENSAJE foreign key (MEN_MENS_ID_MENSAJE)
      references MENSAJE (MENS_ID_MENSAJE)
      on delete restrict on update restrict;

alter table NOTIFICACION
   add constraint FK_NOTIFICA_NOTIFICAC_ARTICULO foreign key (ARTI_ID_AUTOR)
      references ARTICULO (ARTI_ID_AUTOR)
      on delete restrict on update restrict;

alter table NOTIFICACION
   add constraint FK_NOTIFICA_NOTIFICAC_USUARIO foreign key (USUA_ID_USUARIO)
      references USUARIO (USUA_ID_USUARIO)
      on delete restrict on update restrict;

alter table PAGINA
   add constraint FK_PAGINA_PAGINAREV_REVISTA foreign key (REVI_ID_REVISTA)
      references REVISTA (REVI_ID_REVISTA)
      on delete restrict on update restrict;

alter table PAGO
   add constraint FK_PAGO_BANCOPAGO_BANCO foreign key (PAGO_ID_BANCO)
      references BANCO (BANC_ID_BANCO)
      on delete restrict on update restrict;

alter table PAGO
   add constraint FK_PAGO_CPF_DEPOS_REFERENC foreign key (FIDE_ID_FICHA_DEPOSITO)
      references REFERENCIA_BANCARIA (FIDE_ID_FICHA_DEPOSITO)
      on delete restrict on update restrict;

alter table PAGO
   add constraint FK_PAGO_PAGOMETOD_METODO_P foreign key (PAGO_ID_METODO_PAGO)
      references METODO_PAGO (MEPA_ID_METODO_PAGO)
      on delete restrict on update restrict;

alter table PAGO
   add constraint FK_PAGO_RESPONSAB_RESPONSA foreign key (PAGO_ID_RESPONSABLE)
      references RESPONSABLE (RESP_ID_RESPONSABLE)
      on delete restrict on update restrict;

alter table PALABRA_ARTICULO
   add constraint FK_PALABRA__REFERENCE_ARTICULO foreign key (ARTI_ID_AUTOR)
      references ARTICULO (ARTI_ID_AUTOR)
      on delete restrict on update restrict;

alter table PALABRA_ARTICULO
   add constraint FK_PALABRA__REFERENCE_PALABRA foreign key (PALABRA_ID_PALABRA)
      references PALABRA (PALABRA_ID_PALABRA)
      on delete restrict on update restrict;

alter table PALABRA_CURSO
   add constraint FK_PALABRA__REFERENCE_CURSO foreign key (CURS_ID_CURSO)
      references CURSO (CURS_ID_CURSO)
      on delete restrict on update restrict;

alter table PALABRA_CURSO
   add constraint FK_PALABRA__REFERENCE_PALABRA foreign key (PALABRA_ID_PALABRA)
      references PALABRA (PALABRA_ID_PALABRA)
      on delete restrict on update restrict;

alter table PROFESOR
   add constraint FK_PROFESOR_RELATIONS_PERSONA foreign key (PERS_ID_PERSONA)
      references PERSONA (PERS_ID_PERSONA)
      on delete restrict on update restrict;

alter table PROGRAMA_ACADEMICO
   add constraint FK_PROGRAMA_REFERENCE_NIVEL foreign key (PRAC_ID_NIVEL)
      references NIVEL (NIVE_ID_NIVEL)
      on delete restrict on update restrict;

alter table REVISTA
   add constraint FK_REVISTA_REVISTAUS_USUARIO foreign key (USUA_ID_USUARIO)
      references USUARIO (USUA_ID_USUARIO)
      on delete restrict on update restrict;

alter table ROL_PRIVILEGIO
   add constraint FK_ROL_PRIV_ROLPRIVIL_ROL foreign key (ROL_ID_ROL)
      references ROL (ROL_ID_ROL)
      on delete restrict on update restrict;

alter table ROL_PRIVILEGIO
   add constraint FK_ROL_PRIV_ROLPRIVIL_PRIVILEG foreign key (ROPR_ID_PRIVILEGIO)
      references PRIVILEGIO (PRIV_ID_PRIVILEGIO)
      on delete restrict on update restrict;

alter table ROL_USUARIO
   add constraint FK_ROL_USUA_ROLUSUARI_USUARIO foreign key (USUA_ID_USUARIO)
      references USUARIO (USUA_ID_USUARIO)
      on delete restrict on update restrict;

alter table ROL_USUARIO
   add constraint FK_ROL_USUA_ROLUSUARI_ROL foreign key (ROL_ID_ROL)
      references ROL (ROL_ID_ROL)
      on delete restrict on update restrict;

alter table SOLICITUD_FACTURA
   add constraint FK_SOLICITU_SF_USUARI_USUARIO foreign key (USUA_ID_USUARIO)
      references USUARIO (USUA_ID_USUARIO)
      on delete restrict on update restrict;

alter table SOLICITUD_SUSCRIPCION
   add constraint FK_SOLICITU_CSSUSCRIP_COSTO foreign key (COST_ID_COSTO)
      references COSTO (COST_ID_COSTO)
      on delete restrict on update restrict;

alter table SOLICITUD_SUSCRIPCION
   add constraint FK_SOLICITU_DS_SUSCRI_DESCUENT foreign key (DESC_ID_DESCUENTO)
      references DESCUENTO (DESC_ID_DESCUENTO)
      on delete restrict on update restrict;

alter table SOLICITUD_SUSCRIPCION
   add constraint FK_SOLICITU_FDS_SUSCR_REFERENC foreign key (FIDE_ID_FICHA_DEPOSITO)
      references REFERENCIA_BANCARIA (FIDE_ID_FICHA_DEPOSITO)
      on delete restrict on update restrict;

alter table SOLICITUD_SUSCRIPCION
   add constraint FK_SOLICITU_SS_USUARI_USUARIO foreign key (USUA_ID_USUARIO)
      references USUARIO (USUA_ID_USUARIO)
      on delete restrict on update restrict;

alter table TEMA_NEWS
   add constraint FK_TEMA_NEW_RELATIONS_PERSONA foreign key (PERS_ID_PERSONA)
      references PERSONA (PERS_ID_PERSONA)
      on delete restrict on update restrict;

alter table USUARIO
   add constraint FK_USUARIO_PERSONAUS_PERSONA foreign key (PERS_ID_PERSONA)
      references PERSONA (PERS_ID_PERSONA)
      on delete restrict on update restrict;

/*==============================================================*/
/* Restricciones de integridad: UNIQUE                          */
/*==============================================================*/

alter table USUARIO
   add constraint UQ_USUARIO_NOMBRE unique (USUA_NOMBRE_USUARIO);

/* El usuario no se repite cambiando mayúsculas. El correo vive en COMUNICACION */
create unique index UQ_USUARIO_NOMBRE_CI on USUARIO (lower(USUA_NOMBRE_USUARIO));

alter table AUTOR_ARTICULO
   add constraint UQ_AUTOR_ARTICULO unique (AUAR_ID_AUTOR, ARTI_ID_AUTOR);

/* Un solo autor principal por artículo */
create unique index UQ_AUTOR_PRINCIPAL on AUTOR_ARTICULO (ARTI_ID_AUTOR) where AUAR_PRINCIPAL;

alter table PALABRA_ARTICULO
   add constraint UQ_PALABRA_ARTICULO unique (ARTI_ID_AUTOR, PALABRA_ID_PALABRA);

alter table PALABRA_CURSO
   add constraint UQ_PALABRA_CURSO unique (CURS_ID_CURSO, PALABRA_ID_PALABRA);

alter table ROL_USUARIO
   add constraint UQ_ROL_USUARIO unique (USUA_ID_USUARIO, ROL_ID_ROL);

alter table ROL_PRIVILEGIO
   add constraint UQ_ROL_PRIVILEGIO unique (ROL_ID_ROL, ROPR_ID_PRIVILEGIO);

alter table CONSTANCIA
   add constraint UQ_CONSTANCIA_ARTICULO_TIPO unique (ARTI_ID_AUTOR, CONS_TIPO);

alter table CONSTANCIA
   add constraint UQ_CONSTANCIA_FOLIO unique (CONS_FOLIO);

alter table PAGINA
   add constraint UQ_PAGINA_NUMERO unique (REVI_ID_REVISTA, PAGI_NUM_PAGINA);

alter table TEMA
   add constraint UQ_TEMA_NOMBRE unique (TEMA_NOMBRE);

alter table PERSONA
   add constraint UQ_PERSONA_TOKEN unique (PERS_TOKEN_CONFIRMACION);

create unique index UQ_PALABRA_DESCRIPCION on PALABRA (lower(PALA_DESCRIPCION));

create unique index UQ_COMUNICACION_VALOR on COMUNICACION (COMU_ID_TIPO_COMUNICACION, lower(COMU_VALOR_COMUNICACION));

/*==============================================================*/
/* Restricciones de integridad: CHECK                           */
/*==============================================================*/

/* Ajusta los valores de estatus a los que use la aplicación */
alter table ARTICULO
   add constraint CK_ARTI_STATUS check (ARTI_STATUS in ('EN_REVISION', 'APROBADO', 'RECHAZADO'));

alter table ARTICULO
   add constraint CK_ARTI_RECHAZO check (ARTI_STATUS <> 'RECHAZADO' or ARTI_COMENTARIO_RECHAZO is not null);

alter table MENSAJE
   add constraint CK_MENS_STATUS check (MENS_STATUS in ('PENDIENTE', 'CONTESTADO', 'RECHAZADO'));

alter table MENSAJE
   add constraint CK_MENS_CORREO check (MENS_CORREO ~* '^[^@[:space:]]+@[^@[:space:]]+\.[^@[:space:]]+$');

/* Newsletter: nombre y apellido no vacíos; suscribirse exige aceptar el aviso de privacidad */
alter table PERSONA
   add constraint CK_PERS_NOMBRE check (btrim(PERS_NOMBRE) <> '' and btrim(PERS_PRIMER_APELLIDO) <> '');

alter table PERSONA
   add constraint CK_PERS_NEWSLETTER check (not PERS_SUBS_NEWSLETTER
      or (PERS_ACEPTA_AVISO and PERS_FECHA_SUSCRIPCION is not null));

alter table PERSONA
   add constraint CK_PERS_BAJA check (PERS_FECHA_BAJA is null or PERS_FECHA_BAJA >= PERS_FECHA_SUSCRIPCION);

alter table TEMA_NEWS
   add constraint CK_TENE_TEMA check (btrim(TENE_TEMA_INTERES) <> '');

alter table COSTO
   add constraint CK_COST_FECHAS check (COST_FECHA_FIN >= COST_FECHA_INICIO);

alter table DESCUENTO
   add constraint CK_DESC_PORCENTAJE check (DESC_PORCENTAJE between 0 and 100);

alter table DESCUENTO
   add constraint CK_DESC_FECHAS check (DESC_FECHA_FIN >= DESC_FECHA_INICIO);

alter table SOLICITUD_SUSCRIPCION
   add constraint CK_SOIN_VIGENCIA check (SOIN_FECHA_FIN_VIGENCIA is null or SOIN_FECHA_FIN_VIGENCIA >= SOIN_FECHA_INICIO_VIGENCIA);

alter table REFERENCIA_BANCARIA
   add constraint CK_FIDE_FECHAS check (FIDE_FECHA_VENCIMIENTO >= FIDE_FECHA_CREACION);

alter table REFERENCIA_BANCARIA
   add constraint CK_REBA_MONTO check (REBA_MONTO >= 0);

/*==============================================================*/
/* Reglas entre tablas (triggers)                               */
/*==============================================================*/

/* Máximo 5 palabras clave por artículo */
create or replace function FN_MAX_PALABRAS_ARTICULO() returns trigger as $$
begin
   if (select count(*) from PALABRA_ARTICULO
        where ARTI_ID_AUTOR = new.ARTI_ID_AUTOR
          and PAAR_ID_PALABRA_ARTICULO <> new.PAAR_ID_PALABRA_ARTICULO) >= 5 then
      raise exception 'El artículo % ya tiene 5 palabras clave', new.ARTI_ID_AUTOR
         using errcode = 'check_violation';
   end if;
   return new;
end;
$$ language plpgsql;

create trigger TR_MAX_PALABRAS_ARTICULO
   before insert or update of ARTI_ID_AUTOR on PALABRA_ARTICULO
   for each row execute function FN_MAX_PALABRAS_ARTICULO();

/* Suscripción al newsletter completa: correo activo y tema de interés.
   Se revisa al confirmar la transacción (DEFERRABLE), para que la app
   inserte PERSONA, COMUNICACION y TEMA_NEWS en la misma transacción.
   El correo se identifica por el tipo de contacto llamado 'Correo...'. */
create or replace function FN_NEWSLETTER_COMPLETA() returns trigger as $$
begin
   if new.PERS_SUBS_NEWSLETTER and new.PERS_FECHA_BAJA is null then
      if not exists (select 1 from COMUNICACION c
                       join TIPO_COMUNICACION t on t.COMU_ID_TIPO_COMUNICACION = c.COMU_ID_TIPO_COMUNICACION
                      where c.PERS_ID_PERSONA = new.PERS_ID_PERSONA and c.COMU_ACTIVO
                        and t.COMU_NOMBRE ilike 'correo%') then
         raise exception 'La suscripción de la persona % no tiene correo electrónico', new.PERS_ID_PERSONA
            using errcode = 'check_violation';
      end if;
      if not exists (select 1 from TEMA_NEWS where PERS_ID_PERSONA = new.PERS_ID_PERSONA) then
         raise exception 'La suscripción de la persona % no tiene tema de interés', new.PERS_ID_PERSONA
            using errcode = 'check_violation';
      end if;
   end if;
   return null;
end;
$$ language plpgsql;

create constraint trigger TR_NEWSLETTER_COMPLETA
   after insert or update on PERSONA
   deferrable initially deferred
   for each row when (new.PERS_SUBS_NEWSLETTER)
   execute function FN_NEWSLETTER_COMPLETA();

/* Formato de correo: COMUNICACION guarda varios tipos (correo, teléfono...),
   por eso el formato se valida con trigger solo cuando el tipo es 'Correo...'. */
create or replace function FN_COMUNICACION_CORREO() returns trigger as $$
begin
   if exists (select 1 from TIPO_COMUNICACION t
               where t.COMU_ID_TIPO_COMUNICACION = new.COMU_ID_TIPO_COMUNICACION
                 and t.COMU_NOMBRE ilike 'correo%')
      and new.COMU_VALOR_COMUNICACION !~* '^[^@[:space:]]+@[^@[:space:]]+\.[^@[:space:]]+$' then
      raise exception 'El correo "%" no tiene un formato válido', new.COMU_VALOR_COMUNICACION
         using errcode = 'check_violation';
   end if;
   return new;
end;
$$ language plpgsql;

create trigger TR_COMUNICACION_CORREO
   before insert or update of COMU_ID_TIPO_COMUNICACION, COMU_VALOR_COMUNICACION on COMUNICACION
   for each row execute function FN_COMUNICACION_CORREO();

/* Todo usuario debe tener un correo activo (con él inicia sesión).
   Se revisa al confirmar la transacción: la app inserta PERSONA, USUARIO
   y COMUNICACION (tipo Correo) juntos. */
create or replace function FN_USUARIO_CON_CORREO() returns trigger as $$
begin
   if not exists (select 1 from COMUNICACION c
                    join TIPO_COMUNICACION t on t.COMU_ID_TIPO_COMUNICACION = c.COMU_ID_TIPO_COMUNICACION
                   where c.PERS_ID_PERSONA = new.PERS_ID_PERSONA and c.COMU_ACTIVO
                     and t.COMU_NOMBRE ilike 'correo%') then
      raise exception 'El usuario "%" no tiene un correo electrónico activo', new.USUA_NOMBRE_USUARIO
         using errcode = 'check_violation';
   end if;
   return null;
end;
$$ language plpgsql;

create constraint trigger TR_USUARIO_CON_CORREO
   after insert on USUARIO
   deferrable initially deferred
   for each row execute function FN_USUARIO_CON_CORREO();

/*==============================================================*/
/* 2026-10-22: documentos del artículo, edición en portada y     */
/* los 5 artículos del índice (database/cambios/2026-10-22_...)  */
/*==============================================================*/
alter table CONSTANCIA
   add constraint CK_CONS_TIPO check (CONS_TIPO in ('CONSTANCIA', 'AGRADECIMIENTO', 'CESION'));

/* La carta de cesión no lleva folio; la constancia y la de agradecimiento sí. */
alter table CONSTANCIA
   add constraint CK_CONS_FOLIO_SEGUN_TIPO check ((CONS_TIPO = 'CESION' and CONS_FOLIO is null)
       or (CONS_TIPO <> 'CESION' and CONS_FOLIO is not null));

/* Un solo número en portada, y debe ser visible. */
alter table REVISTA
   add constraint CK_REVI_PORTADA_VISIBLE check (not REVI_EN_PORTADA or REVI_VISIBLE);

create unique index UQ_REVISTA_EN_PORTADA on REVISTA (REVI_EN_PORTADA) where REVI_EN_PORTADA;

/* Artículos de cada número; los 5 del índice tienen posición 1 a 5. */
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
