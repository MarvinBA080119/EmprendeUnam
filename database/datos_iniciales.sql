/*======================================================================
  datos_iniciales.sql — Alta de datos iniciales de Revista EmprendeUNAM
  Se ejecuta DESPUÉS de EMP_FINAL_corregido.sql. Se puede correr más de
  una vez: no duplica nada.

  Contenido:
    1. Temáticas (13)
    2. Roles, privilegios y su relación (según los requerimientos)
    3. Tipos de comunicación "Correo" y "Teléfono" (el correo lo exigen el newsletter
       y el inicio de sesión)
    4. Usuario administrador de Emprendedores (contraseña con hash bcrypt;
       la contraseña NO aparece en este script)

  Los demás datos los capturan los usuarios desde el sistema.
======================================================================*/
BEGIN;

/*---------------- 1. Temáticas ---------------------------------------*/
-- TEMA_DESCRIPCION es obligatoria en el modelo: se usa el mismo nombre
INSERT INTO TEMA (TEMA_NOMBRE, TEMA_DESCRIPCION)
SELECT t, t FROM (VALUES
  ('Habilidades directivas'),
  ('Procesos creativos'),
  ('Diagnóstico de problemas'),
  ('Dirección de talento'),
  ('Escenarios y estrategia competitiva'),
  ('Cultura organizacional'),
  ('Análisis de mercados'),
  ('Comunicación'),
  ('Economía y sociedad'),
  ('Relación con el consumidor'),
  ('Negocios internacionales'),
  ('Toma de decisiones y resolución de problemas'),
  ('Emprendimiento social')
) v(t)
ON CONFLICT (TEMA_NOMBRE) DO NOTHING;

/*---------------- 2. Roles y privilegios -----------------------------*/
INSERT INTO ROL (ROL_NOMBRE, ROL_DESCRIPCION)
SELECT n, d FROM (VALUES
  ('Administrador',            'Todos los privilegios del sistema'),
  ('Autor',                    'Envía y consulta sus artículos y descarga su constancia'),
  ('Lector',                   'Permisos mínimos: consulta contenido público y escribe al consultorio'),
  ('Suscriptor_newsletter',    'Permisos mínimos: se suscribe al newsletter'),
  ('Diseño',                   'Descarga los textos aprobados y sube el PDF diseñado de cada artículo')
) v(n, d)
WHERE NOT EXISTS (SELECT 1 FROM ROL r WHERE r.ROL_NOMBRE = v.n);

INSERT INTO PRIVILEGIO (PRIV_NOMBRE, PRIV_DESCRIPCION)
SELECT n, d FROM (VALUES
  ('ENVIAR_ARTICULO',               'Enviar un artículo nuevo'),
  ('CONSULTAR_ARTICULOS_PROPIOS',   'Ver el historial y estatus de sus propios artículos'),
  ('DESCARGAR_CONSTANCIA',          'Descargar la constancia de sus artículos aprobados'),
  ('CONSULTAR_CONTENIDO_PUBLICO',   'Buscar y consultar artículos, cursos y consultorio públicos'),
  ('ENVIAR_MENSAJE_CONSULTORIO',    'Enviar un mensaje al consultorio'),
  ('SUSCRIBIRSE_NEWSLETTER',        'Suscribirse al newsletter'),
  ('ACTIVAR_AUTOR',                 'Activar las cuentas de los autores'),
  ('DICTAMINAR_ARTICULO',           'Aprobar o rechazar artículos'),
  ('CONSULTAR_TODOS_ARTICULOS',     'Ver los artículos de todos los autores'),
  ('GESTIONAR_CONSULTORIO',         'Responder o rechazar mensajes del consultorio'),
  ('GESTIONAR_CURSOS',              'Crear, editar y eliminar cursos'),
  ('CONSULTAR_SUSCRIPTORES',        'Consultar y exportar los suscriptores del newsletter'),
  ('DESCARGAR_ORIGINALES',          'Descargar el texto original de los artículos aprobados'),
  ('SUBIR_DISENO',                  'Subir el PDF diseñado de un artículo aprobado')
) v(n, d)
WHERE NOT EXISTS (SELECT 1 FROM PRIVILEGIO p WHERE p.PRIV_NOMBRE = v.n);

-- Qué privilegios tiene cada rol (el Administrador tiene todos)
INSERT INTO ROL_PRIVILEGIO (ROL_ID_ROL, ROPR_ID_PRIVILEGIO)
SELECT r.ROL_ID_ROL, p.PRIV_ID_PRIVILEGIO
  FROM ROL r
  JOIN PRIVILEGIO p
    ON r.ROL_NOMBRE = 'Administrador'
    OR (r.ROL_NOMBRE = 'Autor' AND p.PRIV_NOMBRE IN
          ('ENVIAR_ARTICULO','CONSULTAR_ARTICULOS_PROPIOS','DESCARGAR_CONSTANCIA'))
    OR (r.ROL_NOMBRE = 'Lector' AND p.PRIV_NOMBRE IN
          ('CONSULTAR_CONTENIDO_PUBLICO','ENVIAR_MENSAJE_CONSULTORIO'))
    OR (r.ROL_NOMBRE = 'Suscriptor_newsletter' AND p.PRIV_NOMBRE IN
          ('CONSULTAR_CONTENIDO_PUBLICO','SUSCRIBIRSE_NEWSLETTER'))
    OR (r.ROL_NOMBRE = 'Diseño' AND p.PRIV_NOMBRE IN
          ('DESCARGAR_ORIGINALES','SUBIR_DISENO'))
ON CONFLICT (ROL_ID_ROL, ROPR_ID_PRIVILEGIO) DO NOTHING;

/*---------------- 3. Tipo de comunicación ----------------------------*/
-- El trigger del newsletter busca un tipo cuyo nombre empiece con "Correo"
INSERT INTO TIPO_COMUNICACION (COMU_NOMBRE)
SELECT n FROM (VALUES ('Correo'), ('Teléfono')) v(n)
 WHERE NOT EXISTS (SELECT 1 FROM TIPO_COMUNICACION t WHERE t.COMU_NOMBRE = v.n);

/*---------------- 4. Usuario administrador ---------------------------*/
-- Usuario: EmprendeUnam. Contraseña: hash bcrypt ($2y$, compatible con Laravel).
-- El correo del administrador vive en COMUNICACION (abajo).
INSERT INTO PERSONA (PERS_NOMBRE, PERS_PRIMER_APELLIDO)
SELECT 'Administrador', 'EmprendeUNAM'
 WHERE NOT EXISTS (SELECT 1 FROM USUARIO WHERE lower(USUA_NOMBRE_USUARIO) = 'emprendeunam');

INSERT INTO USUARIO (PERS_ID_PERSONA, USUA_NOMBRE_USUARIO, USUA_CLAVE_ACCESO, USUA_FECHA_REGISTRO,
                     USUA_ACTIVO, USUA_CORREO_VERIFICADO, USUA_FECHA_VERIFICACION)
SELECT (SELECT max(PERS_ID_PERSONA) FROM PERSONA WHERE PERS_NOMBRE = 'Administrador' AND PERS_PRIMER_APELLIDO = 'EmprendeUNAM'),
       'EmprendeUnam',
       '$2y$12$O4fub1Kb25.mRMV577HsnucvWapFacLkgqFbOxlIvmAfbQZBu9jUe',
       current_date,
       true, true, now()
 WHERE NOT EXISTS (SELECT 1 FROM USUARIO WHERE lower(USUA_NOMBRE_USUARIO) = 'emprendeunam');

-- IMPORTANTE: cambia el correo por el real del administrador.
INSERT INTO COMUNICACION (PERS_ID_PERSONA, COMU_ID_TIPO_COMUNICACION, COMU_VALOR_COMUNICACION, COMU_ETIQUETA, COMU_ACTIVO)
SELECT u.PERS_ID_PERSONA, t.COMU_ID_TIPO_COMUNICACION,
       'admin@emprendeunam.invalid',   -- CAMBIAR por el correo real
       'Principal', true
  FROM USUARIO u, TIPO_COMUNICACION t
 WHERE lower(u.USUA_NOMBRE_USUARIO) = 'emprendeunam' AND t.COMU_NOMBRE = 'Correo'
   AND NOT EXISTS (SELECT 1 FROM COMUNICACION c WHERE c.PERS_ID_PERSONA = u.PERS_ID_PERSONA);

INSERT INTO ROL_USUARIO (USUA_ID_USUARIO, ROL_ID_ROL)
SELECT u.USUA_ID_USUARIO, r.ROL_ID_ROL
  FROM USUARIO u, ROL r
 WHERE lower(u.USUA_NOMBRE_USUARIO) = 'emprendeunam' AND r.ROL_NOMBRE = 'Administrador'
ON CONFLICT (USUA_ID_USUARIO, ROL_ID_ROL) DO NOTHING;

COMMIT;
