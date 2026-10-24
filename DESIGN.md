---
name: Revista EmprendeUNAM — Sistema
description: Área de trabajo serena para autores y equipo editorial; el orden lo dan el aire, la tipografía y una sola hoja blanca, no las cajas.
colors:
  emp-cafe: "#664300"
  emp-cafe-hover: "#553800"
  emp-cafe-50: "#f7f3ec"
  emp-cafe-100: "#efe8dc"
  emp-cafe-200: "#e3d8c6"
  emp-azul: "#001328"
  emp-azul-velo: "rgba(0, 19, 40, 0.06)"
  emp-blanco: "#ffffff"
  emp-hueso: "#f5f2ec"
  emp-hueso-2: "#ebe6dd"
  emp-hueso-velo: "rgba(245, 242, 236, 0.92)"
  emp-n-25: "#fbfaf8"
  emp-n-50: "#f7f5f1"
  emp-n-100: "#f1eee8"
  emp-n-150: "#ebe6de"
  emp-n-200: "#e2dcd2"
  emp-n-300: "#d6cec1"
  emp-n-500: "#8c959e"
  emp-n-600: "#56626e"
  emp-n-700: "#46535f"
typography:
  display:
    fontFamily: "'Newsreader Variable', 'Iowan Old Style', Georgia, serif"
    fontSize: "clamp(2.375rem, 1.6rem + 2vw, 3.25rem)"
    fontWeight: 420
    lineHeight: 1.04
    letterSpacing: "-0.028em"
  display-acceso:
    fontFamily: "'Newsreader Variable', 'Iowan Old Style', Georgia, serif"
    fontSize: "clamp(2.375rem, 1.4rem + 2.4vw, 3.5rem)"
    fontWeight: 420
    lineHeight: 1.04
    letterSpacing: "-0.028em"
  headline:
    fontFamily: "'Newsreader Variable', 'Iowan Old Style', Georgia, serif"
    fontSize: "clamp(1.875rem, 1.5rem + 1vw, 2.375rem)"
    fontWeight: 420
    lineHeight: 1.08
    letterSpacing: "-0.022em"
  headline-acceso:
    fontFamily: "'Newsreader Variable', 'Iowan Old Style', Georgia, serif"
    fontSize: "clamp(2.125rem, 1.7rem + 1.2vw, 2.625rem)"
    fontWeight: 420
    lineHeight: 1.08
    letterSpacing: "-0.022em"
  headline-sm:
    fontFamily: "'Newsreader Variable', 'Iowan Old Style', Georgia, serif"
    fontSize: "1.625rem"
    fontWeight: 420
    lineHeight: 1.2
    letterSpacing: "-0.022em"
  title:
    fontFamily: "'Geist Variable', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif"
    fontSize: "0.9375rem"
    fontWeight: 600
    lineHeight: 1.5
    letterSpacing: "-0.01em"
  body-entrada:
    fontFamily: "'Geist Variable', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif"
    fontSize: "1rem"
    fontWeight: 400
    lineHeight: 1.6
    letterSpacing: "normal"
  body:
    fontFamily: "'Geist Variable', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif"
    fontSize: "0.9375rem"
    fontWeight: 400
    lineHeight: 1.5
    letterSpacing: "normal"
  body-control:
    fontFamily: "'Geist Variable', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif"
    fontSize: "0.875rem"
    fontWeight: 500
    lineHeight: 1.5
    letterSpacing: "-0.005em"
  body-meta:
    fontFamily: "'Geist Variable', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif"
    fontSize: "0.8125rem"
    fontWeight: 400
    lineHeight: 1.5
    letterSpacing: "normal"
  label:
    fontFamily: "'Geist Variable', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif"
    fontSize: "0.75rem"
    fontWeight: 500
    lineHeight: 1.25
    letterSpacing: "0.01em"
    fontFeature: "\"tnum\""
  cifra-paso:
    fontFamily: "'Newsreader Variable', 'Iowan Old Style', Georgia, serif"
    fontSize: "1.25rem"
    fontWeight: 420
    lineHeight: 1.2
    fontFeature: "\"lnum\""
rounded:
  xs: "0.25rem"
  sm: "0.375rem"
  md: "0.5rem"
  control: "0.625rem"
  menu: "0.75rem"
  contenedor: "0.875rem"
  hoja: "1rem"
  pill: "999px"
  full: "50%"
spacing:
  xs: "0.375rem"
  sm: "0.5rem"
  md: "0.75rem"
  lg: "1rem"
  xl: "1.5rem"
  encabezado: "2.75rem"
  seccion: "3.5rem"
  columna: "4.5rem"
components:
  pagina:
    backgroundColor: "{colors.emp-hueso}"
    textColor: "{colors.emp-azul}"
  cabecera:
    backgroundColor: "{colors.emp-azul}"
    height: "4rem"
    padding: "0 clamp(1rem, 2.5vw, 1.75rem)"
  lateral:
    backgroundColor: "{colors.emp-hueso}"
    textColor: "{colors.emp-n-700}"
    width: "15.5rem"
    padding: "1.75rem 0.875rem 0.875rem"
  lateral-franja-movil:
    backgroundColor: "{colors.emp-hueso-velo}"
    padding: "0.5rem clamp(1rem, 2.5vw, 1.75rem)"
  nav-grupo:
    textColor: "{colors.emp-n-600}"
    typography: "{typography.label}"
    padding: "0 0.75rem"
  nav-enlace:
    backgroundColor: "transparent"
    textColor: "{colors.emp-n-700}"
    rounded: "{rounded.md}"
    padding: "0 0.75rem"
    height: "2.25rem"
  nav-enlace-hover:
    backgroundColor: "{colors.emp-hueso-2}"
    textColor: "{colors.emp-azul}"
  nav-enlace-activo:
    backgroundColor: "{colors.emp-blanco}"
    textColor: "{colors.emp-azul}"
  cuenta:
    backgroundColor: "transparent"
    textColor: "{colors.emp-n-600}"
    typography: "{typography.label}"
  cuenta-activa:
    textColor: "{colors.emp-azul}"
  cuenta-menu-boton:
    backgroundColor: "transparent"
    textColor: "{colors.emp-azul}"
    rounded: "{rounded.control}"
    padding: "0.5rem"
  cuenta-menu-boton-hover:
    backgroundColor: "{colors.emp-hueso-2}"
    textColor: "{colors.emp-azul}"
  avatar:
    backgroundColor: "{colors.emp-azul}"
    textColor: "{colors.emp-blanco}"
    rounded: "{rounded.full}"
    size: "2rem"
  menu-cuenta:
    backgroundColor: "{colors.emp-blanco}"
    textColor: "{colors.emp-azul}"
    rounded: "{rounded.menu}"
    padding: "0.375rem"
    width: "14.5rem"
  hoja:
    backgroundColor: "{colors.emp-blanco}"
    textColor: "{colors.emp-azul}"
    rounded: "{rounded.hoja}"
    padding: "clamp(1.75rem, 4vw, 3.5rem) clamp(1.25rem, 4vw, 3.5rem) 5rem"
    width: "72rem"
  saludo-titulo:
    textColor: "{colors.emp-azul}"
    typography: "{typography.display}"
  saludo-resumen:
    textColor: "{colors.emp-n-700}"
    typography: "{typography.body-entrada}"
    width: "54ch"
  titulo-pagina:
    textColor: "{colors.emp-azul}"
    typography: "{typography.headline}"
  button-primary:
    backgroundColor: "{colors.emp-cafe}"
    textColor: "{colors.emp-blanco}"
    typography: "{typography.body-control}"
    rounded: "{rounded.control}"
    padding: "0.5rem 0.875rem"
  button-primary-hover:
    backgroundColor: "{colors.emp-cafe-hover}"
    textColor: "{colors.emp-blanco}"
  button-primary-lg:
    backgroundColor: "{colors.emp-cafe}"
    textColor: "{colors.emp-blanco}"
    rounded: "{rounded.control}"
    padding: "0.6875rem 0.875rem"
    height: "2.875rem"
  button-secondary:
    backgroundColor: "{colors.emp-blanco}"
    textColor: "{colors.emp-azul}"
    typography: "{typography.body-control}"
    rounded: "{rounded.control}"
    padding: "0.5rem 0.875rem"
  button-secondary-hover:
    backgroundColor: "{colors.emp-n-50}"
    textColor: "{colors.emp-azul}"
  button-secondary-active:
    backgroundColor: "{colors.emp-n-100}"
    textColor: "{colors.emp-azul}"
  button-quieto:
    backgroundColor: "transparent"
    textColor: "{colors.emp-n-700}"
    rounded: "{rounded.control}"
    padding: "0.3125rem 0.625rem"
  button-quieto-hover:
    backgroundColor: "{colors.emp-cafe-50}"
    textColor: "{colors.emp-cafe}"
  input:
    backgroundColor: "{colors.emp-blanco}"
    textColor: "{colors.emp-azul}"
    typography: "{typography.body-control}"
    rounded: "{rounded.control}"
    padding: "0.5rem 0.75rem"
    height: "2.625rem"
  input-acceso:
    backgroundColor: "{colors.emp-blanco}"
    textColor: "{colors.emp-azul}"
    rounded: "{rounded.control}"
    height: "2.875rem"
  input-invalid:
    backgroundColor: "{colors.emp-cafe-50}"
    textColor: "{colors.emp-azul}"
  insignia-revision:
    backgroundColor: "{colors.emp-cafe-50}"
    textColor: "{colors.emp-cafe}"
    typography: "{typography.label}"
    rounded: "{rounded.pill}"
    padding: "0.1875rem 0.5rem 0.1875rem 0.4375rem"
  insignia-aprobado:
    backgroundColor: "{colors.emp-azul-velo}"
    textColor: "{colors.emp-azul}"
    typography: "{typography.label}"
    rounded: "{rounded.pill}"
    padding: "0.1875rem 0.5rem 0.1875rem 0.4375rem"
  insignia-rechazado:
    backgroundColor: "{colors.emp-blanco}"
    textColor: "{colors.emp-n-700}"
    typography: "{typography.label}"
    rounded: "{rounded.pill}"
    padding: "0.1875rem 0.5rem 0.1875rem 0.4375rem"
  fila:
    backgroundColor: "transparent"
    textColor: "{colors.emp-azul}"
    rounded: "{rounded.control}"
    padding: "0.8125rem 0.75rem"
  fila-hover:
    backgroundColor: "{colors.emp-n-50}"
    textColor: "{colors.emp-azul}"
  panel-accion:
    backgroundColor: "{colors.emp-n-50}"
    textColor: "{colors.emp-azul}"
    rounded: "{rounded.contenedor}"
    padding: "1.5rem"
  apartado:
    backgroundColor: "{colors.emp-blanco}"
    textColor: "{colors.emp-azul}"
    rounded: "{rounded.contenedor}"
    padding: "1.25rem"
  cita:
    backgroundColor: "{colors.emp-n-50}"
    textColor: "{colors.emp-azul}"
    rounded: "{rounded.menu}"
    padding: "1rem 1.125rem"
  vacio-grande:
    backgroundColor: "{colors.emp-n-50}"
    textColor: "{colors.emp-azul}"
    rounded: "{rounded.hoja}"
    padding: "4.5rem 2rem"
  aviso:
    backgroundColor: "{colors.emp-n-25}"
    textColor: "{colors.emp-azul}"
    rounded: "{rounded.md}"
    padding: "0.75rem 0.875rem"
  aviso-atencion:
    backgroundColor: "{colors.emp-cafe-50}"
    textColor: "{colors.emp-azul}"
  etiqueta:
    backgroundColor: "{colors.emp-n-50}"
    textColor: "{colors.emp-azul}"
    typography: "{typography.body-meta}"
    rounded: "{rounded.sm}"
    padding: "0.125rem 0.5rem"
  paginacion-activa:
    backgroundColor: "{colors.emp-azul}"
    textColor: "{colors.emp-blanco}"
    rounded: "{rounded.sm}"
    width: "2rem"
  acceso-nota:
    backgroundColor: "{colors.emp-hueso}"
    textColor: "{colors.emp-azul}"
    padding: "4rem clamp(2.5rem, 5vw, 5.5rem) 6rem"
  acceso-formulario:
    backgroundColor: "{colors.emp-blanco}"
    textColor: "{colors.emp-azul}"
    width: "24rem"
  acceso-formulario-ancho:
    backgroundColor: "{colors.emp-blanco}"
    width: "34rem"
---

# Design System: Revista EmprendeUNAM — Sistema

Este documento cubre el **sistema** (iniciar sesión, registro y contraseña, área del autor, área del administrador y páginas de error). El sitio público (`layouts/sitio.blade.php`, `resources/css/sitio/*`, `resources/js/sitio*`) conserva su diseño original y no forma parte de este sistema: no se rediseña.

## Overview

**Creative North Star: "La hoja sobre el escritorio"**

El sistema es un escritorio en calma: una superficie hueso cálida sobre la que descansa una sola hoja blanca de trabajo, con la quietud y la precisión de iCloud y macOS, la calidez material de Nespresso y la limpieza de Linear o Stripe. La interfaz busca ser invisible: nada de marcos encadenados ni tarjetas sobre tarjetas; el orden lo dan el aire entre secciones, las proporciones, una serif editorial en los títulos grandes y hilos de 1px donde hace falta separar.

Arriba, una franja azul marino que solo lleva los logotipos de la UNAM y de la FCA, juntos y pegados a la izquierda: es la firma institucional, no una barra de herramientas. La navegación vive en una barra lateral sobre hueso, discreta, con grupos y contadores como cifras sueltas. Los paneles no abren con métricas, sino con un saludo según la hora en serif grande, la fecha del día y una frase en lenguaje natural que resume lo pendiente («3 artículos esperan dictamen y 1 cuenta de autor por activar»). Debajo, listas abiertas sin cajas cuyas filas se iluminan con un hover redondeado.

El color trabaja por exclusión y por temperatura: superficies e hilos son blanco mezclado con café, los textos son azul mezclado con blanco, el azul sólido es la cabecera y la tinta, y el café aparece solo donde algo pide acción o atención. El movimiento es una sola entrada suave y escalonada del contenido al cargar, y transiciones de estado de 160ms; todo se apaga con `prefers-reduced-motion`.

**Key Characteristics:**
- Encabezado azul `#001328` de 4rem con únicamente los logotipos UNAM y FCA a la izquierda; sin texto, sin navegación, sin translucidez.
- Fondo hueso cálido; una hoja blanca de 16px de radio con sombra casi imperceptible es la única superficie elevada.
- Contenido sin cajas: secciones separadas por aire (2.75 a 4.5rem) y por hilos `emp-n-150`; filas con hover redondeado.
- Geist para toda la interfaz; Newsreader con tamaño óptico, peso 420, solo en títulos grandes.
- Café `#664300` para la acción; botones sólidos sin degradado.
- Estados siempre con texto (insignia con punto); sin verdes ni rojos.
- Acceso en dos mitades: nota editorial sobre hueso y formulario sobre blanco, sin tarjeta.

## Colors

Paleta cerrada de cuatro colores (café, azul, blanco y negro) más sus mezclas y transparencias, repartida por temperatura: lo que es superficie o hilo se calienta con café, lo que es texto se tiñe de azul.

### Primary
- **Café Imprenta** (`emp-cafe`): el color de la acción. Botón primario, enlaces, borde y anillo de foco, cursor de texto, ícono del enlace activo de la barra lateral, números del proceso de registro, estado «En revisión / Pendiente», campos con error y avisos de atención. ≈ 8.9:1 sobre blanco y 7.9:1 sobre hueso.
- **Café Tostado** (`emp-cafe-hover`): hover y presionado del botón primario y de los enlaces.
- **Café Papel** (`emp-cafe-50`): fondo de la insignia «En revisión», de campos inválidos, de avisos de atención, del hover del botón quieto y de la zona de archivo al pasar o arrastrar.
- **Café Velado** (`emp-cafe-100`): presionado de los botones con tinte café.
- **Café Bruma** (`emp-cafe-200`): borde de la insignia «En revisión» y de avisos de atención; fondo de la selección de texto.

### Secondary
- **Azul Marino** (`emp-azul`): la tinta de todo texto principal y título, y la única superficie de color sólido: el encabezado, el avatar de la cuenta, el sello de artículo aprobado y la página activa de la paginación. También el estado «Aprobado / Contestado».
- **Velo Azul** (`emp-azul-velo`): fondo de la insignia «Aprobado» (borde azul al 14%).

### Neutral
- **Blanco** (`emp-blanco`): la hoja de trabajo, el lado del formulario en acceso, campos, botón secundario, menú de la cuenta, enlace activo de la barra lateral y la página de error.
- **Hueso** (`emp-hueso`): fondo de las áreas de trabajo y de la barra lateral, y mitad editorial del acceso (con un halo radial café al 7% desde la esquina superior izquierda).
- **Hueso Hondo** (`emp-hueso-2`): hover de los enlaces de la barra lateral y del botón de la cuenta.
- **Hueso Velado** (`emp-hueso-velo`): la franja de secciones en pantallas angostas, translúcida con desenfoque; vuelve a hueso sólido con `prefers-reduced-transparency`.
- **Niebla** (`emp-n-25`): hover de filas de tabla, avisos neutros.
- **Nube** (`emp-n-50`): el tono sin sombra de paneles de acción, citas (respuesta, motivo del dictamen) y estado vacío grande; hover de filas de bandeja, del botón secundario y de opciones de menú; etiquetas de palabras clave; campos deshabilitados.
- **Vapor** (`emp-n-100`): presionado del botón secundario; divisores entre celdas de tabla y renglones de registro.
- **Hilo** (`emp-n-150`): el hilo estructural: bajo la cabecera de cada bandeja, encima del resumen, entre envíos, bloques, ajustes y grupos de campos, borde inferior de la franja móvil, borde de apartados.
- **Trazo** (`emp-n-200`): borde del botón secundario, hilos del proceso de registro, borde de la zona de archivo ya llena.
- **Marco** (`emp-n-300`): borde de campos y casillas, de la insignia «Rechazado», de la zona de archivo punteada y barra de desplazamiento.
- **Azul Ceniza** (`emp-n-500`): solo texto deshabilitado (3.0:1, no apto para texto legible).
- **Azul Pizarra** (`emp-n-600`): texto secundario: fecha del saludo, rótulos de grupo de la barra lateral, contadores, metadatos, ayudas, encabezados de tabla, placeholders (6.2:1 sobre blanco, 5.6:1 sobre hueso, 5.0:1 sobre el hover hueso-2).
- **Azul Grafito** (`emp-n-700`): enlaces de la barra lateral en reposo, frase del saludo, texto de la nota editorial, notas e insignia «Rechazado» (7.9:1 sobre blanco, 7.1:1 sobre hueso).

El negro forma parte de la paleta permitida, pero el sistema no lo usa como color visible: la tinta es siempre el azul.

### Named Rules
**La Regla del No Cruce.** Nunca texto café sobre azul ni azul sobre café. Sobre el azul solo van los logotipos blancos; el contorno de foco dentro del encabezado cambia de café a blanco.

**La Regla de la Temperatura Dividida.** Superficies e hilos se mezclan con café (hueso, `emp-n-25` a `emp-n-300`); textos se mezclan con azul (`emp-n-500` a `emp-n-700`). No se usan grises neutros.

**La Regla del Café Escaso.** El café marca lo que pide acción o atención: la acción principal, el foco, lo pendiente y el error. Una zona de acción tiene un solo botón primario; lo demás es secundario o quieto.

**La Regla de la Paleta Cerrada.** Ningún color fuera de café, azul, blanco, negro y sus mezclas o transparencias. Sin verdes ni rojos: los colores de estado de Bootstrap están remapeados (`success`/`info` → azul, `warning`/`danger` → café).

## Typography

**Display Font:** Newsreader Variable, con tamaño óptico (con Iowan Old Style, Georgia, serif)
**Body Font:** Geist Variable (con system-ui, -apple-system, Segoe UI, Roboto, Helvetica Neue, Arial, sans-serif)

**Character:** Geist, neutra y exacta, hace todo el trabajo de interfaz; Newsreader, con su eje óptico y un peso ligero de 420, pone la voz editorial de una revista solo donde el título es grande. Ambas se instalan localmente con `@fontsource-variable` (Geist y Newsreader `opsz`), sin CDN; la página de error las carga desde `public/fonts`.

### Hierarchy
- **Display** (Newsreader 420, clamp 2.375–3.25rem, 1.04, -0.028em): el saludo de los paneles («Buenas tardes», «Hola, Ana»).
- **Display acceso** (Newsreader 420, clamp 2.375–3.5rem, 1.04, 13ch): el lema de la nota editorial; en registro baja a clamp 2.125–2.875rem y 16ch para que quepa el proceso.
- **Headline** (Newsreader 420, clamp 1.875–2.375rem, 1.08, -0.022em): título de página (`h1`) en las áreas de trabajo. En acceso sube a clamp 2.125–2.625rem.
- **Headline pequeño** (Newsreader 420, 1.625rem): título del estado vacío grande y de la pantalla de artículo aprobado.
- **Title** (Geist 600, 0.9375rem, -0.01em): títulos de bandeja, sección, bloque, ajuste, panel de acción, envío y leyendas de grupo.
- **Body entrada** (Geist 400, 1rem, 1.6–1.65, `emp-n-700`): la frase del saludo (54ch) y el texto de la nota editorial (36ch).
- **Body** (Geist 400, 0.9375rem, 1.5): base del documento (15px). El texto largo (`emp-lectura`) usa 1.65 y 68ch; los subtítulos de página, 1.6.
- **Body control** (Geist 500, 0.875rem, -0.005em): botones, campos y enlaces de la barra lateral. En acceso los campos suben a 0.9375rem.
- **Body meta** (Geist 400/500, 0.8125rem): fecha del saludo, etiquetas de campo (500), metadatos de fila, ayudas, errores (500), nombre en la cuenta.
- **Label** (Geist 500, 0.75rem, 0.01em, cifras tabulares): rótulos de grupo de la barra lateral, contadores, insignias, encabezados de tabla, rol de la cuenta.
- **Cifra de paso** (Newsreader, 1.25rem, café, cifras de línea): los números 1–4 del proceso de registro; única serif por debajo del tamaño de título.

Los títulos llevan `text-wrap: balance`; los párrafos, `text-wrap: pretty`. Nada va en mayúsculas espaciadas; la fecha del saludo solo capitaliza su primera letra.

### Named Rules
**La Regla de las Dos Voces.** Geist para todo lo que se opera; Newsreader solo para títulos grandes (saludo, título de página, lema, vacío y logro) y los números del proceso. Nunca serif en botones, campos, tablas, navegación ni cuerpo de texto.

**La Regla de los Pesos Contados.** Geist en 400, 500 y 600; Newsreader en 420. El 600 es para títulos pequeños y cifras de resumen; el 500 para controles y etiquetas; el 400 para leer.

**La Regla de la Cifra Tabular.** Toda tabla, fecha, folio, contador y conteo usa `font-variant-numeric: tabular-nums` (clase `emp-num`), para que las columnas no bailen.

## Layout

Un encabezado global y dos plantillas que comparten tokens:

- **Encabezado** (acceso, autor, administrador y error): pegado arriba (`position: sticky`), 4rem de alto, relleno lateral `clamp(1rem, 2.5vw, 1.75rem)`. Solo los logotipos de la UNAM (2.5rem) y la FCA (2.375rem), separados 0.875rem, a la izquierda y enlazados al inicio del área.
- **Escritorio** (autor y administrador): desde 992px, dos columnas: barra lateral de 15.5rem sobre hueso, fija bajo el encabezado con su propio desplazamiento, y la hoja blanca con 0.75rem de margen arriba, abajo y a la derecha. Dentro de la hoja, el contenido se centra a 72rem (62rem en el autor) con relleno `clamp(1.75rem, 4vw, 3.5rem)` arriba y a los lados y 5rem abajo. Debajo de 992px la hoja ocupa todo el ancho sin radio ni sombra y la barra lateral se vuelve una franja horizontal pegada bajo el encabezado: enlaces en renglón desplazable (sin rótulos de grupo, con desvanecido al borde derecho) y la cuenta reducida a su avatar.
- **Acceso** (iniciar sesión, registro, contraseña): debajo del encabezado, dos mitades a toda la altura, `5fr` / `7fr` desde 992px. A la izquierda la nota editorial sobre hueso, fija al desplazar, centrada en vertical con relleno `4rem clamp(2.5rem, 5vw, 5.5rem) 6rem` y un pie institucional de 12px; a la derecha, sobre blanco, el enlace «Volver al sitio» arriba y la columna del formulario de 24rem centrada (34rem en registro). En pantallas angostas solo se muestra el formulario.

Patrones de página recurrentes:
- **Saludo de los paneles**: fecha, saludo y frase a la izquierda; acciones a la derecha, alineadas al pie; 3.5rem de aire antes del contenido.
- **Encabezado de página**: enlace de regreso opcional, título serif y subtítulo (máx. 48rem) a la izquierda, acciones a la derecha; 2.75rem antes del contenido.
- **Panel de pendientes**: la bandeja de artículos a la izquierda y las de autores y mensajes apiladas a la derecha (`1.35fr` / `1fr` desde 1200px), separadas por 3.5rem en vertical y 4.5rem entre columnas. Al pie, un resumen en línea (término y cifra) tras un hilo, a 4rem.
- **Detalle**: columna de lectura de hasta 52rem; con acción lateral, `1fr` + 21rem fijo desde 1200px, separación 2.5rem.
- **Formulario por bloques y ajustes**: cada bloque es título y explicación (16rem; 18rem en ajustes) a la izquierda y campos (máx. 40rem) a la derecha desde 992px, separados por un hilo `emp-n-150` y 2rem de aire; los ajustes, 3rem.
- **Lista de datos**: término de 11rem y valor a la derecha desde 576px, entre hilos; en teléfono se apilan.

Ritmo: grupos apretados (0.375rem ícono-texto, 0.5rem entre botones) y separación amplia entre secciones (2.75rem, 3.5rem, 4.5rem). Las filas de bandeja sobresalen 0.75rem a cada lado para que su hover respire sin desalinear el texto. Cortes de Bootstrap: 576, 768, 992 y 1200px; 992px es el cambio estructural.

### Named Rules
**La Regla del Encabezado Mudo.** El encabezado azul lleva únicamente los dos logotipos, juntos y pegados a la izquierda. Ni nombre de la revista, ni navegación, ni cuenta, ni botones: todo eso vive en la barra lateral o en la página.

**La Regla del Aire antes que la Caja.** Para separar contenido se usa primero el espacio, después un hilo de 1px; un contenedor con fondo es el último recurso y nunca lleva sombra.

## Elevation & Depth

El sistema es plano salvo por una sola superficie: la hoja. Se separa del hueso con un anillo de 1px casi invisible, una sombra de contacto y una sombra amplia de desplazamiento negativo, todas teñidas del azul de la tinta. Lo demás se distingue por tono (hueso, blanco, nube) o por hilo. Solo flotan, además, lo que de verdad está encima: el menú de la cuenta y el enlace «Saltar al contenido».

### Shadow Vocabulary
- **Hoja** (`--emp-sombra-hoja`: `0 0 0 1px rgba(0,19,40,.045), 0 1px 2px rgba(0,19,40,.03), 0 16px 48px -32px rgba(0,19,40,.14)`): la hoja de trabajo, desde 992px.
- **Menú** (`--emp-sombra-menu`: `0 0 0 1px rgba(0,19,40,.06), 0 2px 6px rgba(0,19,40,.04), 0 12px 32px -12px rgba(0,19,40,.18)`): menú de la cuenta y enlace de salto.
- **Enlace activo** (`0 0 0 1px rgba(0,19,40,.06), 0 1px 2px rgba(0,19,40,.06)`): el hilo que recorta el enlace activo blanco sobre la barra hueso.
- **Controles** (`0 1px 1px rgba(0,19,40,.08)` con filo interior blanco al 8% en el primario; `0 1px 1px rgba(0,19,40,.04)` en el secundario; `0 1px 1px rgba(0,19,40,.03)` en campos): un píxel de apoyo, no elevación.
- **Anillo de foco** (`--emp-anillo`: `0 0 0 3px rgba(102,67,0,.14)`): campos, casillas, paginación, botones y zona de archivo al enfocarse. Lo demás usa contorno café de 2px con 2px de separación (blanco dentro del encabezado).

### Named Rules
**La Regla de la Hoja Única.** Solo la hoja y lo que flota (menú) llevan sombra amplia. Paneles de acción, citas y estados vacíos se resuelven con un tono `emp-n-50` sin sombra; bandejas y tablas no tienen contenedor.

**La Regla del Encabezado Sólido.** El encabezado es azul sólido, sin translucidez ni `backdrop-filter`. La única superficie translúcida es la franja de secciones en pantallas angostas.

## Shapes

Esquinas suaves escalonadas por tamaño: 10px (`rounded.control`) en todo control (botones, campos, selectores), en el hover de las filas y en el botón de la cuenta; 8px (`rounded.md`) en los enlaces de la barra lateral, avisos, opciones del menú y el cuadro del ícono de archivo; 6px (`rounded.sm`) en etiquetas de palabras clave, paginación y el enlace de salto; 12px (`rounded.menu`) en el menú de la cuenta, citas y la zona de archivo; 14px (`rounded.contenedor`) en paneles de acción y apartados (formulario de aviso, constancia); 16px (`rounded.hoja`) en la hoja y el estado vacío grande. La píldora (`rounded.pill`) se reserva a las insignias de estado; el círculo, al avatar, al sello de aprobado, al ícono del estado vacío y a los puntos de estado.

Los hilos son de 1px y separan contenido dentro de la hoja; casi nunca dibujan un contorno cerrado.

**La Regla del Borde Punteado.** El borde punteado significa «espacio por llenar u opcional»: la zona para soltar el PDF y el bloque de coautor. Al llenarse, la zona de archivo pasa a borde sólido.

## Components

### Buttons
Sólidos, callados y precisos.
- **Shape:** 10px; relleno 0.5rem × 0.875rem, Geist 500 a 14px. El grande (`btn-lg`) sube a 0.9375rem y 2.875rem de alto en acceso.
- **Primary:** café sólido, sin degradado, texto blanco, un píxel de sombra y filo interior blanco al 8%.
- **Hover / Focus:** transición de 160ms; el fondo pasa a café tostado. Foco con el anillo café de 3px. Al presionar, `scale(0.97)` con salida exponencial.
- **Secondary:** blanco, borde `emp-n-200`, tinta azul; hover `emp-n-50` con borde `emp-n-300`, presionado `emp-n-100`. Es el botón de toda acción que no es la principal.
- **Quieto:** texto `emp-n-700` sin fondo ni borde para acciones discretas en filas (PDF, quitar); en hover, café sobre café papel.
- **Link:** texto café sin subrayado que se subraya en hover.
- **Deshabilitado:** opacidad 0.5; el secundario usa texto `emp-n-500`.

### Chips (insignias de estado)
- **Style:** píldora con punto de 7px a la izquierda, texto Label, borde de 1px.
- **State:** «En revisión / Pendiente» café sobre café papel con borde café bruma; «Aprobado / Contestado» azul sobre velo azul con borde azul al 14%; «Rechazado» grafito sobre blanco con borde `emp-n-300` y punto hueco. El tipo de punto distingue el estado aun sin color, y el texto siempre lo nombra.
- **Etiquetas de palabras clave:** rectángulo de 6px, fondo `emp-n-50`, borde `emp-n-150`, 13px; no son estados y no llevan punto.

### Cards / Containers
No hay tarjetas: hay una hoja y tonos.
- **Hoja:** blanca, 16px, sombra de hoja; contiene toda pantalla de trabajo.
- **Panel de acción** (dictamen, respuesta): tono `emp-n-50`, 14px, relleno 1.5rem, sin borde ni sombra; título Title, explicación en 13px pizarra, botón primario a todo lo ancho, separador «o» con hilos y la alternativa como secundaria.
- **Citas** (respuesta del consultorio, motivo del dictamen): tono `emp-n-50`, 12px, sin borde.
- **Apartado** (formulario de aviso de edición, constancia): blanco con borde `emp-n-150`, 14px, relleno 1.25rem; la excepción enmarcada para un objeto que debe leerse como documento.
- **Estado vacío grande:** tono `emp-n-50`, 16px, relleno 4.5 × 2rem, centrado: ícono café en círculo blanco de 3rem, título en Headline pequeño, explicación de hasta 46ch y el botón primario que resuelve el vacío.

### Inputs / Fields
- **Style:** fondo blanco, borde `emp-n-300` de 1px, un píxel de sombra, radio 10px, alto mínimo 2.625rem (2.875rem y 15px en acceso). Etiqueta arriba en 13px 500 azul; los opcionales llevan «(opcional)» en pizarra. Ayuda en 13px pizarra.
- **Hover:** el borde se oscurece un paso.
- **Focus:** borde café y anillo café de 3px al 14%; cursor de texto café.
- **Error:** borde café con refuerzo interior de 1px, fondo café papel y mensaje en 13px 500 precedido de un ícono de alerta SVG. Si hay más de un error, un aviso arriba dice cuántos campos revisar; el foco va al primer campo marcado.
- **Contraseña:** botón de texto «Mostrar / Ocultar» dentro del campo, a la derecha.
- **Disabled:** fondo `emp-n-50`.
- **Grupos:** en formularios largos (registro), leyenda en Title con hilo inferior `emp-n-150`.
- **Zona de archivo:** punteada `emp-n-300`, 12px, sobre `emp-n-25`, con ícono café en cuadro blanco de 8px; hover o arrastre la tiñe de café papel con borde café; con archivo pasa a borde sólido `emp-n-200` sobre blanco.

### Navigation
- **Barra lateral:** sobre hueso, rótulos de grupo en Label pizarra en tipo oración («Trabajo editorial», «Difusión»), separados 1.75rem. Enlaces de 2.25rem con ícono y texto 14px 500 grafito; hover en hueso hondo con texto azul; activo en blanco con el hilo de enlace activo, texto azul, ícono café y `aria-current="page"`.
- **Contadores:** cifra suelta a la derecha del enlace, Label pizarra (azul en el activo), sin pastilla, con texto oculto para lectores de pantalla («3 en revisión»). Solo aparecen si hay pendientes. La misma cifra neutra acompaña los títulos de bandeja.
- **Cuenta:** al pie de la barra lateral, botón de 10px con avatar azul circular de 2rem (inicial blanca), nombre en 13px 500, rol en Label pizarra y flecha doble. El menú abre hacia arriba: blanco, 14.5rem, 12px, sombra de menú, opciones de 8px con ícono pizarra («Ver el sitio de la revista», «Cerrar sesión»); entra con opacidad y escala desde 0.97 en 180ms.
- **Móvil (debajo de 992px):** la barra lateral se vuelve una franja pegada bajo el encabezado, hueso velado al 92% con `backdrop-filter: saturate(1.4) blur(12px)` e hilo inferior; enlaces en renglón desplazable sin barra visible y desvanecido a la derecha; la cuenta queda solo como avatar circular y su menú se ancla arriba a la derecha.

### Saludo de los paneles (firma)
Ambos paneles abren igual: la fecha larga del día en 13px pizarra («Martes 20 de octubre»), el saludo en Display (según la hora en el administrador: «Buenos días» hasta las 11:59, «Buenas tardes» hasta las 18:59, «Buenas noches»; «Hola, {nombre}» en el autor) y una frase en Body entrada que dice lo pendiente con cantidades y concordancia («3 artículos esperan dictamen, 1 cuenta de autor por activar y 2 mensajes sin responder. Lo más antiguo aparece primero.») o, si no hay nada, que todo está al día. A la derecha, las acciones del panel con un solo primario.

### Listas abiertas (bandejas e historial)
- **Bandeja:** sin contenedor; título Title con su cifra neutra y «Ver todos» a la derecha, sobre un hilo `emp-n-150`. Filas sin divisores: título 500 y metadato en 13px pizarra, a la derecha fecha relativa tabular («hace 4 días», «ayer») y chevron. En hover la fila toma un fondo `emp-n-50` de 10px y el chevron avanza 2px. Vacío: una línea pizarra con palomita.
- **Historial del autor:** envíos separados por hilos, cada uno con título Title, metadatos pizarra, nota o motivo citado, y a la derecha la insignia y las acciones (PDF quieto, constancia secundaria).

### Tablas
Sin marco, alineadas con el texto de la página: encabezados en Label pizarra con hilo inferior `emp-n-150`, celdas con 0.9375rem de relleno vertical y divisores `emp-n-100`, primera y última columna sin relleno lateral, hover `emp-n-25`. El primer enlace de cada fila es azul 500 con subrayado `emp-n-300` que se vuelve café en hover. Debajo de 768px la tabla desplaza en horizontal (ancho mínimo 44rem). Paginación con números de 2rem, radio 6px y página activa en azul.

### Avisos
Caja de 8px con borde de 1px, ícono SVG de 16px pintado con `currentColor` y texto azul. Neutro: `emp-n-25` con borde `emp-n-150`; éxito e información: `emp-n-50` con borde `emp-n-200`; atención y error: café papel con borde café bruma e ícono café. Entran con 240ms desde 4px arriba y se pueden cerrar.
Se usan dentro de la página para lo que acompaña a un formulario (errores de validación, la sesión abierta en el login).

### Notificaciones
Los mensajes que llegan tras una acción (flash `exito`, `aviso`, `error` y los de las tablas Vue) son una hoja blanca flotante de 12px de radio con `--emp-sombra-menu`, 23rem de ancho, bajo el encabezado a la derecha (en celular, abajo, a todo lo ancho). Llevan ícono de 18px (azul en éxito, café en aviso y error), un título de 13px en 600 que nombra el estado («Listo», «Aviso», «No se pudo completar», café en aviso y error) y el mensaje en 14px grafito. Entran en 260ms desde 6px arriba con escala 0.98 y salen por el mismo camino en 160ms; éxito se cierra a los 6s, aviso a los 9s y error se queda hasta cerrarlo; el puntero o el foco pausan la cuenta (`resources/js/avisos.js`).

### Diálogos
Confirmaciones y el aviso de inactividad: `<dialog>` modal de 27rem y 16px de radio, fondo azul al 42%, título Newsreader 420 de 22px con `text-wrap: balance`, texto de 15px grafito y acciones a la derecha (secundario + primario; en celular, apiladas a todo lo ancho). Entran en 240ms desde 8px abajo con escala 0.98. Si la acción es destructiva, el foco inicial queda en «Cancelar». El título y el texto salen de `data-confirmar` (la pregunta hasta el «?» es el título) y el botón, de `data-confirmar-boton`.

### Envío en curso
Al enviar un formulario POST, el botón muestra un anillo de 13px que gira (700ms) y el texto «Enviando…» (o `data-texto-enviando`); un segundo clic no vuelve a enviar. Con movimiento reducido el anillo queda quieto.

### Acceso
- **Nota editorial:** sobre hueso con halo café, el lema en Display acceso y un párrafo en Body entrada grafito. En registro, el proceso de publicación en cuatro renglones separados por hilos `emp-n-200`: número en Cifra de paso café, paso en 600 y explicación en 14px pizarra.
- **Formulario:** sobre blanco, sin tarjeta: título serif en Headline acceso, subtítulo de 15px, campos de 2.875rem, botón primario grande a todo lo ancho y, al pie, el enlace a la otra pantalla en pizarra.

### Artículo aprobado
El único momento con movimiento propio: un sello circular azul con palomita blanca crece desde 0.6 en 500ms y, 80ms después, la constancia sube 10px y aparece (`@starting-style`, solo al llegar). Título en Headline pequeño; la constancia es un apartado con la insignia, el título del artículo en 600 y un registro de folio y fecha.

### Gestor de portada
Superficie de operación. El número principal se presenta con su portada en miniatura (proporción 7:9, radio 6px, hilo azul al 8% y una sombra baja) junto al título en Newsreader; debajo, el estado de cada archivo en una rejilla entre hilos: nombre en 13px/500 seguido de su insignia con texto (Listo azul, Falta café, Opcional neutra) y un detalle en 12px con números tabulares. Los demás números van en galería de portadas. En el formulario, la vista previa de la portada ocupa la columna derecha y cambia al elegir el archivo. En la pantalla del número, el esquema reproduce el acomodo del inicio (principal grande en hueso, cuatro secundarios en blanco con hilo) y se actualiza al mover las posiciones, que son radios segmentados («No se muestra · Principal · 1–4»); la casilla que cambia se tiñe de café claro 700ms. El pie con el conteo y «Guardar destacados» queda fijo al fondo. Los cursos son fichas marcables; las sugeridas llevan la insignia «Sugerido».

### Artículo del número (formulario)
- **Autollenado:** al elegir un artículo aprobado, los campos que se llenan destellan café papel 1.1s y un renglón con palomita café dice qué se llenó.
- **Origen del PDF:** dos renglones marcables (radio dentro de una caja de 10px con hilo; la elegida en café papel con borde café bruma): «Usar el PDF del sistema» con el estado del diseño y «Subir un PDF solo para este número». Con el primero, el campo de archivo queda deshabilitado al 55%.
- **Cursos:** fichas marcables (con «+» gris que pasa a palomita café al marcarla); las sugeridas llevan «Sugerido». Al final, «Agregar otro curso» como ficha punteada en café. El diálogo (32rem) busca entre los vigentes, lista nombre, institución y «Agregar / Agregado», y debajo de un separador «¿No está en la lista?» registra uno nuevo.

### Destacados: arrastrar y soltar
Toda la fila del artículo se arrastra (cursor de mano abierta); el asa de seis puntos a la izquierda solo lo indica (gris `emp-n-400`, más oscura al pasar sobre la fila, café sobre café papel al pasar sobre ella; cursor de mano). Al arrastrar, las casillas del esquema se vuelven punteadas y la que queda bajo el puntero toma borde café y fondo café papel; lo arrastrado baja a 45% de opacidad. La página se desplaza sola cerca de los bordes. En pantallas táctiles el asa no aparece.

### Indicadores de interacción
Lo que se pulsa se distingue del texto sin ruido: el botón quieto sin ícono lleva subrayado tenue `emp-n-300` que se vuelve café bruma; con ícono, el ícono sube de 70% a 100% de opacidad al pasar. Los enlaces dentro de párrafos y estados de archivo van en café con subrayado café bruma. Las opciones no elegidas de segmentos y posiciones toman un velo blanco al pasar. «Volver» adelanta su flecha 2px. Acciones discretas con ícono mínimo: «Quitar» (×), «Eliminar número» (bote).

### Textos largos
Títulos de página y párrafos se parten donde haga falta (`overflow-wrap: anywhere`) y crecen; en listas, títulos y metadatos se recortan a 2 renglones (3 en la mesa de diseño y el número principal) con puntos suspensivos y el texto completo en `title`.

### Generador de flipbooks
Superficie de operación en dos columnas sobre hueso (`emp-n-50`, radio 16px): a la izquierda los datos (número en Newsreader 26px con el prefijo «No.», zona punteada para el PDF que se vuelve sólida con el nombre y el peso al elegirlo, un solo primario «Generar flipbook»); a la derecha, en blanco, la escena: tres pasos (Subir · Convertir · Armar) con marca circular que pasa a café con palomita, una barra de 4px que crece con `scaleX` y su cifra tabular, y una tira de miniaturas que aparecen conforme se convierten (entrada 240ms, opacidad y 6px). Al terminar, la vista previa del visor en un marco 16:10 y «Abrir en otra pestaña». Debajo, «Generados»: portada 17:22 con «Ver» al pasar, número en Newsreader, páginas y fecha tabulares, insignia «eBook del No. X» o «Sin usar» y acciones quietas apiladas.

### Tarjetas del sitio público
Cada artículo del inicio, del buscador y del consultorio se pulsa completo: el enlace del título se extiende sobre la tarjeta (`::after`), el título se vuelve rojo al pasar y el foco se dibuja alrededor de toda la tarjeta. En el consultorio, un clic en la tarjeta abre o cierra la respuesta. Títulos a 3 renglones (4 en el principal) y descripciones a 4, con puntos suspensivos, aunque el texto no tenga espacios.

### Mesa de diseño
Superficie de operación para la cuenta Diseño, con el mismo saludo de los paneles (fecha, «Mesa de diseño» en Display y una frase con lo pendiente) y un solo primario: descargar los textos pendientes en .zip.
- **Avance:** debajo del saludo, «4 de 9 artículos diseñados» y una tira con un pliego por artículo aprobado (10 × 24px, radio 3px): azul si ya tiene PDF diseñado, hueso con hilo café si falta. Con más de 40 artículos se vuelve una barra continua. Lo mismo encabeza «Manuscritos y diseños» del administrador.
- **Filtro segmentado:** pista `emp-n-100` de 10px; la opción activa en blanco con sombra mínima, texto azul y su cifra. «Por diseñar · Diseñados · Todos».
- **Renglones:** entre hilos, sin tarjetas. A la izquierda, la hoja del original (44 × 56px, esquina doblada con `clip-path`): blanca con hilo y la extensión (DOCX o PDF) en café 10px 600 mientras falta; azul con palomita blanca cuando ya está diseñado, para que el estado se lea sin color. Título en Newsreader 20px, tema en café 500 y autores en grafito, palabras clave como etiquetas y una línea de fechas tabulares. A la derecha, la insignia (Por diseñar café / Diseñado azul) y las acciones: «Original» quieto, «Ver PDF» quieto, y «Subir PDF diseñado» en café papel con borde café bruma (no primario, para no llenar la lista de café sólido); ya diseñado, «Reemplazar» secundario.
- **Subida:** al elegir o soltar el PDF sube en ese momento; bajo el renglón aparece una caja `emp-n-50` con el nombre, el porcentaje y una barra azul de 4px que avanza con `scaleX`. Errores en café papel con el motivo. Al arrastrar un archivo sobre la página, los renglones que lo aceptan muestran un contorno punteado y el que queda bajo el puntero se tiñe de café papel. Al volver, el renglón recién diseñado se resalta y su hoja pasa a azul con un solo gesto (520ms, escala desde 0.82).

### Sesión en el sitio público
Con una sesión abierta, el sitio público lleva arriba una franja negra de 2.25rem en el lenguaje del sitio (Barlow, 12px, mayúsculas espaciadas en las acciones): «Sesión iniciada como **cuenta** · panel» y, a la derecha, «Ir a mi panel» y «Cerrar sesión» subrayados. No cambia nada más del sitio.

### Páginas de error
Plantilla autónoma, sin Vite ni sesión, que repite en línea los valores del sistema: encabezado azul de 6.5rem (5rem en celular) con los dos logotipos, fondo blanco, código en 13px pizarra, título Newsreader 420 de 40px, mensaje grafito y el botón primario café de 10px.

### Movimiento
Entrada única al cargar: cada hijo de la hoja, la columna del formulario y los elementos de la nota editorial suben 6px y aparecen en 560ms con `cubic-bezier(0.23, 1, 0.32, 1)`, escalonados 0, 50, 100 y 150ms. Las transiciones de estado duran 160ms. Con `prefers-reduced-motion: reduce` todo se reduce a 0.01ms.

## Do's and Don'ts

### Do:
- **Do** poner el fondo de las áreas de trabajo en hueso `emp-hueso` y todo el contenido dentro de una sola hoja blanca de 16px con `--emp-sombra-hoja`.
- **Do** mantener el encabezado azul sólido de 4rem con únicamente los logotipos de la UNAM y la FCA, juntos y pegados a la izquierda.
- **Do** llevar la navegación en la barra lateral sobre hueso (grupos, enlace activo blanco con hilo, contadores como cifra suelta, cuenta al pie con menú hacia arriba) y convertirla en franja horizontal desplazable debajo de 992px.
- **Do** abrir cada panel con fecha, saludo en serif y una frase en lenguaje natural que resuma lo pendiente con cantidades.
- **Do** separar secciones con aire (2.75rem, 3.5rem, 4.5rem) y, si hace falta, un hilo `emp-n-150`; listas y tablas sin contenedor.
- **Do** resolver paneles de acción, citas y estados vacíos con el tono `emp-n-50`, sin borde ni sombra.
- **Do** usar Geist para toda la interfaz y Newsreader 420 con tamaño óptico solo en títulos grandes.
- **Do** escribir todo texto en azul `#001328` o en un neutro mezclado del azul (`emp-n-600` secundario, `emp-n-700` notas); `emp-n-500` solo para lo deshabilitado.
- **Do** mostrar cada estado con texto: insignia con punto (café «En revisión / Pendiente», azul «Aprobado / Contestado», grafito con punto hueco «Rechazado»).
- **Do** dejar un solo botón primario café, sólido y sin degradado, por zona de acción.
- **Do** poner cifras, fechas y folios en números tabulares.
- **Do** limitar el movimiento a la entrada única escalonada del contenido y a transiciones de 160ms, respetando `prefers-reduced-motion`.

### Don't:
- **Don't** poner texto café sobre azul ni azul sobre café.
- **Don't** usar verde, rojo ni ningún color fuera de café, azul, blanco, negro y sus mezclas, ni siquiera para éxito o error.
- **Don't** comunicar un estado solo con color o solo con un ícono.
- **Don't** agregar al encabezado texto, nombre de la revista, navegación, cuenta ni botones, ni darle translucidez o `backdrop-filter`.
- **Don't** meter contenido en tarjetas con sombra dentro de la hoja ni encadenar marcos; la hoja es la única superficie elevada.
- **Don't** abrir los paneles con franjas de tarjetas de métricas; lo pendiente se dice en la frase del saludo y en los contadores.
- **Don't** usar la serif en botones, campos, tablas, navegación o cuerpo de texto, ni pesos de Geist fuera de 400/500/600.
- **Don't** poner degradados en botones.
- **Don't** poner una etiqueta pequeña (kicker) encima de un título ni etiquetas en mayúsculas espaciadas.
- **Don't** usar la píldora para nada que no sea una insignia de estado.
