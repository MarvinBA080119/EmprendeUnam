---
name: Revista EmprendeUNAM — Sistema
description: Área de trabajo cálida, sobria y con profundidad sutil para autores y equipo editorial, donde lo pendiente se ve primero.
colors:
  emp-cafe: "#664300"
  emp-cafe-hover: "#573900"
  emp-cafe-50: "#f6f4f0"
  emp-cafe-100: "#f0ece6"
  emp-cafe-200: "#e7e1d6"
  emp-azul: "#001328"
  emp-azul-velo: "rgba(0, 19, 40, 0.06)"
  emp-blanco: "#ffffff"
  emp-marfil: "#f1eee8"
  emp-n-25: "#faf8f6"
  emp-n-50: "#f7f6f2"
  emp-n-100: "#f3f0eb"
  emp-n-150: "#ede8e0"
  emp-n-200: "#e5dfd4"
  emp-n-300: "#dad2c2"
  emp-borde-campo: "#d6cdbd"
  emp-n-500: "#8c959e"
  emp-n-600: "#616d7a"
  emp-n-700: "#4c5a68"
  sobre-azul-texto: "rgba(255, 255, 255, 0.76)"
  sobre-azul-tenue: "rgba(255, 255, 255, 0.66)"
  sobre-azul-velo: "rgba(255, 255, 255, 0.08)"
  sobre-azul-activo: "rgba(255, 255, 255, 0.13)"
  sobre-azul-cuenta: "rgba(255, 255, 255, 0.16)"
typography:
  display:
    fontFamily: "Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif"
    fontSize: "clamp(1.75rem, 1.2rem + 1.6vw, 2.375rem)"
    fontWeight: 600
    lineHeight: 1.1
    letterSpacing: "-0.035em"
  cifra:
    fontFamily: "Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif"
    fontSize: "clamp(1.625rem, 1.2rem + 1.4vw, 2.125rem)"
    fontWeight: 600
    lineHeight: 1
    letterSpacing: "-0.04em"
    fontFeature: "\"tnum\""
  headline:
    fontFamily: "Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif"
    fontSize: "1.5rem"
    fontWeight: 600
    lineHeight: 1.25
    letterSpacing: "-0.025em"
  headline-acceso:
    fontFamily: "Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif"
    fontSize: "1.625rem"
    fontWeight: 600
    lineHeight: 1.25
    letterSpacing: "-0.03em"
  title:
    fontFamily: "Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif"
    fontSize: "0.9375rem"
    fontWeight: 600
    lineHeight: 1.5
    letterSpacing: "-0.01em"
  body:
    fontFamily: "Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif"
    fontSize: "0.875rem"
    fontWeight: 400
    lineHeight: 1.5
    letterSpacing: "normal"
  body-lectura:
    fontFamily: "Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif"
    fontSize: "0.9375rem"
    fontWeight: 400
    lineHeight: 1.65
    letterSpacing: "normal"
  body-meta:
    fontFamily: "Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif"
    fontSize: "0.8125rem"
    fontWeight: 400
    lineHeight: 1.5
    letterSpacing: "normal"
  label:
    fontFamily: "Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif"
    fontSize: "0.75rem"
    fontWeight: 500
    lineHeight: 1.25
    letterSpacing: "0.01em"
  label-cuenta:
    fontFamily: "Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif"
    fontSize: "0.6875rem"
    fontWeight: 600
    lineHeight: 1
    fontFeature: "\"tnum\""
rounded:
  sm: "0.375rem"
  aviso: "0.5rem"
  md: "0.625rem"
  control-lg: "0.75rem"
  menu: "0.875rem"
  lg: "1rem"
  xl: "1.25rem"
  pill: "999px"
spacing:
  xs: "0.375rem"
  sm: "0.5rem"
  md: "0.75rem"
  contenedor: "1.25rem"
  tarjeta: "1.5rem"
  xl: "1.75rem"
  2xl: "2.5rem"
components:
  pagina:
    backgroundColor: "{colors.emp-marfil}"
    textColor: "{colors.emp-azul}"
  button-primary:
    backgroundColor: "{colors.emp-cafe}"
    textColor: "{colors.emp-blanco}"
    rounded: "{rounded.md}"
    padding: "0.5rem 0.875rem"
  button-primary-hover:
    backgroundColor: "{colors.emp-cafe-hover}"
    textColor: "{colors.emp-blanco}"
  button-primary-lg:
    backgroundColor: "{colors.emp-cafe}"
    textColor: "{colors.emp-blanco}"
    rounded: "{rounded.control-lg}"
    padding: "0.6875rem 0.875rem"
    height: "3rem"
  button-secondary:
    backgroundColor: "{colors.emp-blanco}"
    textColor: "{colors.emp-azul}"
    rounded: "{rounded.md}"
    padding: "0.5rem 0.875rem"
  button-secondary-hover:
    backgroundColor: "{colors.emp-n-25}"
    textColor: "{colors.emp-azul}"
  button-secondary-active:
    backgroundColor: "{colors.emp-n-100}"
    textColor: "{colors.emp-azul}"
  button-quieto:
    backgroundColor: "transparent"
    textColor: "{colors.emp-n-700}"
    rounded: "{rounded.md}"
    padding: "0.3125rem 0.625rem"
  button-quieto-hover:
    backgroundColor: "{colors.emp-cafe-50}"
    textColor: "{colors.emp-cafe}"
  input:
    backgroundColor: "{colors.emp-n-25}"
    textColor: "{colors.emp-azul}"
    rounded: "{rounded.md}"
    padding: "0.5rem 0.75rem"
    height: "2.5rem"
  input-focus:
    backgroundColor: "{colors.emp-blanco}"
    textColor: "{colors.emp-azul}"
  input-acceso:
    backgroundColor: "{colors.emp-n-25}"
    textColor: "{colors.emp-azul}"
    rounded: "{rounded.md}"
    height: "2.75rem"
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
  cuenta:
    backgroundColor: "{colors.emp-n-150}"
    textColor: "{colors.emp-azul}"
    typography: "{typography.label-cuenta}"
    rounded: "{rounded.pill}"
    padding: "0 0.375rem"
    height: "1.25rem"
  cuenta-cabecera:
    backgroundColor: "{colors.sobre-azul-cuenta}"
    textColor: "{colors.emp-blanco}"
  cuenta-cabecera-activa:
    backgroundColor: "{colors.emp-blanco}"
    textColor: "{colors.emp-azul}"
  cuenta-neutra:
    backgroundColor: "{colors.emp-n-100}"
    textColor: "{colors.emp-n-700}"
  cabecera:
    backgroundColor: "{colors.emp-azul}"
    textColor: "{colors.emp-blanco}"
    height: "4.5rem"
    padding: "0.625rem clamp(1rem, 2.5vw, 2rem)"
  nav-enlace:
    backgroundColor: "transparent"
    textColor: "{colors.sobre-azul-texto}"
    rounded: "{rounded.pill}"
    padding: "0 0.75rem"
    height: "2.25rem"
  nav-enlace-hover:
    backgroundColor: "{colors.sobre-azul-velo}"
    textColor: "{colors.emp-blanco}"
  nav-enlace-activo:
    backgroundColor: "{colors.sobre-azul-activo}"
    textColor: "{colors.emp-blanco}"
  nav-panel:
    backgroundColor: "{colors.emp-azul}"
    textColor: "{colors.emp-blanco}"
    width: "19rem"
  nav-panel-enlace:
    backgroundColor: "transparent"
    textColor: "{colors.sobre-azul-texto}"
    rounded: "{rounded.control-lg}"
    padding: "0 0.875rem"
    height: "2.75rem"
  cabecera-boton:
    backgroundColor: "transparent"
    textColor: "{colors.emp-blanco}"
    rounded: "{rounded.pill}"
    padding: "0 0.875rem"
    height: "2.25rem"
  cabecera-boton-hover:
    backgroundColor: "{colors.emp-blanco}"
    textColor: "{colors.emp-azul}"
  menu-cuenta:
    backgroundColor: "{colors.emp-blanco}"
    textColor: "{colors.emp-azul}"
    rounded: "{rounded.menu}"
    padding: "0.375rem"
    width: "16rem"
  tarjeta:
    backgroundColor: "{colors.emp-blanco}"
    textColor: "{colors.emp-azul}"
    rounded: "{rounded.lg}"
    padding: "1rem 1.25rem"
  metrica:
    backgroundColor: "{colors.emp-blanco}"
    textColor: "{colors.emp-azul}"
    rounded: "{rounded.lg}"
    padding: "1.125rem 1.25rem"
  tarjeta-acceso:
    backgroundColor: "{colors.emp-blanco}"
    textColor: "{colors.emp-azul}"
    rounded: "{rounded.xl}"
    padding: "2.5rem 2.5rem 2.25rem"
    width: "27rem"
  aviso:
    backgroundColor: "{colors.emp-n-25}"
    textColor: "{colors.emp-azul}"
    rounded: "{rounded.aviso}"
    padding: "0.75rem 0.875rem"
  aviso-atencion:
    backgroundColor: "{colors.emp-cafe-50}"
    textColor: "{colors.emp-azul}"
---

# Design System: Revista EmprendeUNAM — Sistema

Este documento cubre el **sistema** (iniciar sesión, registro y contraseña, área del autor, área del administrador y páginas de error). El sitio público (`layouts/sitio.blade.php`, `resources/css/sitio/*`, `resources/js/sitio*`) conserva su diseño original y no forma parte de este sistema: no se rediseña.

## Overview

**Creative North Star: "La sala editorial cálida"**

El sistema es un área de trabajo moderna, cálida y estrictamente profesional, con el acabado sereno de Apple y la sobriedad material de Nespresso: un encabezado azul marino sólido que da el marco institucional, un fondo marfil que quita la frialdad del blanco puro, y tarjetas blancas que flotan apenas sobre él con sombras en capas. La profundidad es sutil y física, nunca decorativa: se nota que una tarjeta está encima del fondo, no se ve la sombra.

La densidad sigue siendo de herramienta de uso diario (cuerpo de 14px, filas compactas, cifras tabulares), pero con más aire que antes: tarjetas de 16px de radio, relleno de 1.25 a 1.5rem, controles de 10px. El color trabaja por exclusión y por temperatura: superficies y líneas son blanco mezclado con café (cálidas), los textos son azul mezclado con blanco (la tinta), el azul sólido es la superficie del encabezado y de su panel móvil, y el café aparece donde algo pide acción o atención.

La identidad institucional la llevan los logotipos reales de la UNAM y de la FCA, en blanco sobre el azul del encabezado, pegados a la izquierda en toda pantalla del sistema. Rechazos confirmados por la dirección: el sistema excesivamente blanco y plano de la etapa anterior, la plantilla de Bootstrap sin trabajar, los títulos serif decorativos y la ornamentación sin función.

El movimiento es breve y discreto: transiciones de 180ms con salida exponencial, botones que ceden a escala 0.98, métricas enlazadas que suben 2px en hover, la tarjeta de acceso y los avisos que entran una vez desde unos píxeles abajo o arriba, y el menú de la cuenta que crece desde 0.96. El único momento con movimiento propio es la pantalla de artículo aprobado. Todo se apaga con `prefers-reduced-motion`.

**Key Characteristics:**
- Encabezado global azul `#001328` sólido con logotipos UNAM y FCA a la izquierda y navegación en píldoras a la derecha.
- Fondo marfil cálido; tarjetas blancas sin borde con sombra en capas (anillo de 1px + sombras teñidas de azul).
- Superficies y líneas cálidas (blanco con café), textos azulados (azul con blanco); café `#664300` para la acción.
- Inter local en tres pesos (400/500/600), números tabulares en todo dato.
- Radios de 10px en controles, 16px en contenedores y 20px en la tarjeta de acceso; píldora para estados, contadores y navegación del encabezado.
- Estados siempre con texto (insignia con punto o punto junto a una etiqueta); sin verdes ni rojos.
- Métricas en tarjetas al inicio de ambos paneles y contadores de pendientes en la navegación.

## Colors

Paleta cerrada de cuatro colores (café, azul, blanco y negro) más sus mezclas y transparencias, repartida por temperatura: lo que es superficie o línea se calienta con café, lo que es texto se tiñe de azul.

### Primary
- **Café Imprenta** (`emp-cafe`): el color de la acción. Botón primario, enlaces, anillo y contorno de foco, cursor de texto, íconos de las etiquetas de métrica, número de los pasos del registro, estado «En revisión / Pendiente», campos con error y avisos de atención. Contraste ≈ 8.9:1 sobre blanco y 7.7:1 sobre marfil.
- **Café Tostado** (`emp-cafe-hover`): hover y presionado del botón primario y de los enlaces.
- **Café Papel** (`emp-cafe-50`): fondo de la insignia «En revisión», de campos inválidos, de avisos de atención y del hover de la zona de archivo y del botón quieto.
- **Café Velado** (`emp-cafe-100`): presionado de botones con tinte café.
- **Café Bruma** (`emp-cafe-200`): borde de la insignia «En revisión» y de avisos de atención; fondo de la selección de texto.

### Secondary
- **Azul Marino** (`emp-azul`): la tinta de todo texto principal y título, y la única superficie de color sólido: el encabezado global, su panel offcanvas, el avatar del menú abierto, el sello de artículo aprobado y la página activa de la paginación. También el estado «Aprobado / Contestado».
- **Velo Azul** (`emp-azul-velo`): fondo de la insignia «Aprobado» (el borde es el azul al 14%).

### Neutral
- **Blanco** (`emp-blanco`): toda tarjeta, campo enfocado, botón secundario y el menú de la cuenta.
- **Marfil** (`emp-marfil`): fondo de página de todo el sistema (blanco con 9% de café). En acceso lleva además dos halos radiales muy tenues: café al 9% arriba al centro y azul al 5% en la esquina inferior derecha.
- **Marfil Niebla** (`emp-n-25`): fondo de campos en reposo, hover de filas y del botón secundario, cabecera de tablas enmarcadas, avisos neutros, respuesta y motivo citados, zona de archivo.
- **Marfil Nube** (`emp-n-50`): hover de opciones del menú de la cuenta, avisos de éxito, etiquetas de palabras clave, campos deshabilitados.
- **Marfil Vapor** (`emp-n-100`): presionado del botón secundario, divisores entre filas, contador neutro de bandeja.
- **Marfil Hilo** (`emp-n-150`): divisores internos (cabecera de bandeja, bloques, grupos de campos, pie de tabla), contador en reposo fuera del encabezado.
- **Marfil Trazo** (`emp-n-200`): borde del pie de página, encabezado de tabla, avisos de éxito, anillo de los números de paso.
- **Marfil Marco** (`emp-n-300`): borde del botón secundario, de casillas, de la insignia «Rechazado», de las zonas punteadas, línea que une los pasos y barra de desplazamiento.
- **Borde de Campo** (`emp-borde-campo`): borde de todo campo de texto y selector en reposo.
- **Azul Ceniza** (`emp-n-500`): solo texto deshabilitado (3.0:1, no apto para texto legible).
- **Azul Pizarra** (`emp-n-600`): texto secundario: subtítulos, metadatos, ayudas, etiquetas de métrica, encabezados de tabla, placeholders (5.3:1 sobre blanco, 4.6:1 sobre marfil).
- **Azul Grafito** (`emp-n-700`): insignia «Rechazado», botón quieto, notas y texto del proceso de registro (7.1:1 sobre blanco, 6.1:1 sobre marfil).
- **Blanco sobre Azul** (`sobre-azul-texto`, `sobre-azul-tenue`): enlaces del encabezado en reposo (76%) y leyendas y rótulos secundarios dentro del azul (66%).
- **Velos sobre Azul** (`sobre-azul-velo`, `sobre-azul-activo`, `sobre-azul-cuenta`): hover (8–10%), enlace activo (13% con anillo interior de 1px al 8%) y contador (16%) dentro del encabezado.

El negro forma parte de la paleta permitida, pero el sistema no lo usa: la tinta es siempre el azul.

### Named Rules
**La Regla del No Cruce.** Nunca texto café sobre azul ni azul sobre café. Sobre el azul solo va blanco o blanco translúcido; por eso el contador y el enlace activo del encabezado son blancos, y el foco dentro del encabezado cambia su contorno café por blanco.

**La Regla de la Temperatura Dividida.** Superficies y líneas se mezclan con café (marfil, `emp-n-25` a `emp-n-300`); textos se mezclan con azul (`emp-n-500` a `emp-n-700`). No se usan grises neutros ni neutros azulados como superficie.

**La Regla del Café Escaso.** El café marca lo que pide acción o atención: la acción principal, el foco, lo pendiente y el error. Una zona de acción tiene un solo botón primario; lo demás es secundario o quieto.

**La Regla de la Paleta Cerrada.** Ningún color fuera de café, azul, blanco, negro y sus mezclas o transparencias. Sin verdes ni rojos: los colores de estado de Bootstrap están remapeados (`success`/`info` → azul, `warning`/`danger` → café).

## Typography

**Display Font:** Inter (con system-ui, -apple-system, Segoe UI, Roboto, Helvetica Neue, Arial, sans-serif)
**Body Font:** Inter (misma pila)

**Character:** Una sola cara de interfaz, neutra y exacta, que deja hablar a la jerarquía de tamaño, peso y tracking negativo en los tamaños grandes, al modo de Apple. Se instala localmente desde `@fontsource/inter` en 400, 500 y 600; no hay cara decorativa. Inter se conserva a propósito: es una decisión registrada, no un defecto.

### Hierarchy
- **Display** (600, clamp 1.75–2.375rem, 1.1, -0.035em): el lema del proceso de publicación en el registro; ancho máximo 18ch.
- **Cifra** (600, clamp 1.625–2.125rem, 1, -0.04em, tabular): el valor de cada métrica de los paneles.
- **Headline** (600, 1.5rem, 1.25, -0.025em): título de página (`h1`) en las áreas de trabajo.
- **Headline acceso** (600, 1.625rem, -0.03em): título dentro de la tarjeta de acceso y de la pantalla de artículo aprobado.
- **Title** (600, 0.9375rem, -0.01em): títulos de sección, de bloque, de panel de acción, de ajuste, de envío, del historial y leyendas de grupo. Los títulos de bandeja bajan a 0.875rem; el nombre de la revista en el encabezado usa 0.9375rem con -0.015em.
- **Body** (400, 0.875rem, 1.5): texto de interfaz, campos, botones (500), celdas. En la tarjeta de acceso los campos suben a 0.9375rem.
- **Body lectura** (400, 0.9375rem, 1.65): texto largo como el resumen de un artículo; medida máxima 68ch. El texto del proceso de registro usa 0.9375rem con 1.6 y 44ch.
- **Body meta** (400, 0.8125rem): etiquetas de campo (500), etiquetas de métrica (500), enlaces del encabezado (500), metadatos de fila, ayudas, errores de validación (500), botones pequeños.
- **Label** (500, 0.75rem, 0.01em): encabezados de tabla, insignias de estado, notas de métrica, etiquetas de filtros, rol en el menú de la cuenta.
- **Label cuenta** (600, 0.6875rem, cifras tabulares): solo dentro de los contadores de pendientes.

Los títulos llevan `letter-spacing: -0.02em` (o más negativo en los tamaños grandes) y `text-wrap: balance`; los párrafos, `text-wrap: pretty`.

### Named Rules
**La Regla de los Tres Pesos.** Solo 400, 500 y 600. El 600 es para títulos, cifras y el botón grande; el 500 para controles y etiquetas; el 400 para leer.

**La Regla de la Cifra Tabular.** Toda tabla, fecha, folio, contador, métrica y conteo de caracteres usa `font-variant-numeric: tabular-nums` (clase `emp-num`), para que las columnas de datos no bailen.

## Layout

Un solo encabezado global y tres plantillas que comparten tokens:

- **Encabezado global** (acceso, autor y administrador): pegado arriba (`position: sticky`), de 4.5rem de alto mínimo y relleno lateral `clamp(1rem, 2.5vw, 2rem)`. A la izquierda, los logotipos de la UNAM (2.875rem de alto) y la FCA (2.75rem), separados 0.875rem y reducidos a 2.5/2.375rem en teléfonos; desde 768px los acompaña el nombre de la revista con su leyenda, tras una línea blanca al 18% (en el administrador, con seis secciones, el nombre solo aparece desde 1400px). A la derecha, la navegación y el menú de la cuenta. Desde 992px la navegación va en renglón como píldoras; entre 992 y 1199px se ocultan sus íconos y debajo de 1200px el nombre del usuario. Debajo de 992px la navegación pasa a un panel offcanvas azul de 19rem que entra por la derecha, abierto con un botón circular de 2.5rem.
- **Acceso** (iniciar sesión, registro, contraseña): debajo del encabezado, una escena centrada de hasta 74rem con relleno 3rem × 1rem (4rem × 1.5rem desde 576px). Iniciar sesión es una sola tarjeta centrada de 27rem. El registro, desde 992px, son dos columnas (`0.9fr` / `1.1fr`, separación 4.5rem): a la izquierda el proceso de publicación fijo al desplazar (`top: 7rem`), a la derecha la tarjeta de 38rem; en pantallas menores la tarjeta va primero y el proceso debajo. En el encabezado, «Volver al sitio» y el botón de la otra pantalla; un pie institucional centrado de 12px cierra la página.
- **Áreas de trabajo**: contenido centrado de hasta 80rem (70rem en el autor) con relleno 2rem arriba (2.75rem desde 992px), `clamp(1rem, 2.5vw, 2rem)` a los lados y 4rem abajo; pie con borde superior `emp-n-200`, la leyenda institucional y el enlace al sitio.

Patrones de página recurrentes:
- **Encabezado de página**: enlace de regreso opcional, título y descripción a la izquierda (máx. 48rem), acciones a la derecha; 1.75rem de separación con el contenido.
- **Métricas**: franja de tarjetas al inicio de cada panel, debajo del encabezado y 2rem antes del contenido. Dos columnas en teléfono (la última impar ocupa el renglón), tres desde 768px y cinco desde 1200px en el administrador; el autor usa cuatro columnas desde 768px. Separación 0.75rem (1rem desde 768px).
- **Panel de pendientes**: la bandeja de artículos a la izquierda y las de autores y mensajes apiladas a la derecha (`1.45fr` / `1fr` desde 1200px, separación 1.25rem).
- **Detalle**: columna de lectura de hasta 52rem; con acción lateral, `1fr` + 21rem fijo (sticky a 6rem) desde 1200px, separación 2.5rem.
- **Formulario por bloques**: dentro de una tarjeta blanca (relleno 1.5rem; 2rem × 2.25rem desde 768px), cada bloque es título y explicación (16rem; 18rem en ajustes) a la izquierda y campos (máx. 40rem) a la derecha desde 992px, separados por una línea `emp-n-150`.
- **Lista de datos**: término de 11rem y valor a la derecha desde 576px; en teléfono se apilan.

Ritmo de espacio: grupos apretados (0.375rem entre ícono y texto, 0.5rem entre botones) y separación generosa entre secciones (1.75rem a 3rem). El relleno horizontal interno de bandejas, tablas enmarcadas y filas es 1.25rem, constante, para alinear filas y cabecera. Los campos enfocados dejan 6rem de margen de desplazamiento para no quedar bajo el encabezado. Cortes de Bootstrap: 576, 768, 992, 1200 y 1400px.

## Elevation & Depth

El sistema separa capas con sombras en capas, no con bordes. Cada superficie elevada combina un anillo de 1px muy tenue (que define el contorno sin dibujar una línea), una sombra corta de contacto y una sombra amplia y difusa con desplazamiento negativo, todas teñidas del azul de la tinta para que la profundidad se sienta cálida sobre el marfil y nunca gris. Hay tres alturas: tarjeta en reposo, tarjeta levantada (hover) y capa elevada (acceso y menús). Los controles tienen su propia presión: el primario, una sombra cálida teñida de café; el secundario, un píxel; los campos, una sombra interior que los hunde.

### Shadow Vocabulary
- **Tarjeta** (`--emp-sombra-tarjeta`: `0 0 0 1px rgba(0,19,40,.05), 0 1px 2px rgba(0,19,40,.04), 0 8px 24px -12px rgba(0,19,40,.12)`): bandejas, marcos de tabla, historial, paneles de acción, formularios de ajuste y de envío, constancia, métricas y muestra de constancia.
- **Tarjeta alta** (`--emp-sombra-tarjeta-alta`: `0 0 0 1px rgba(0,19,40,.06), 0 2px 4px rgba(0,19,40,.04), 0 16px 32px -14px rgba(0,19,40,.2)`): hover de una métrica enlazada, junto con `translateY(-2px)`.
- **Elevada** (`--emp-sombra-elevada`, también `--emp-sombra-menu`: cuatro capas hasta `0 40px 80px -40px rgba(0,19,40,.22)`): tarjeta de acceso, menú de la cuenta y enlace «Saltar al contenido».
- **Encabezado** (`inset 0 -1px 0 rgba(255,255,255,.06), 0 10px 30px -18px rgba(0,19,40,.7)`): separa el azul del contenido que pasa debajo.
- **Primario** (`inset 0 1px 0 rgba(255,255,255,.14), 0 1px 2px rgba(0,19,40,.16), 0 6px 16px -8px rgba(102,67,0,.55)`; en hover la sombra cálida crece a `0 10px 22px -10px rgba(102,67,0,.6)`): botón primario.
- **Secundario** (`0 1px 2px rgba(0,19,40,.05), inset 0 -1px 0 rgba(0,19,40,.03)`): botón secundario.
- **Campo** (`inset 0 1px 2px rgba(0,19,40,.04)`): campos y selectores en reposo.
- **Anillo de foco** (`--emp-anillo`: `0 0 0 4px rgba(102,67,0,.14)`): campos, casillas, paginación, botones y zona de archivo al enfocarse. El resto de los elementos enfocables usa contorno café de 2px con 2px de separación (blanco dentro del encabezado).

### Named Rules
**La Regla de la Tarjeta sobre Marfil.** Un contenedor principal es blanco, sin borde, con radio de 16px y `--emp-sombra-tarjeta`, sobre el fondo marfil. Nunca se combina un borde visible con una sombra amplia, y la sombra nunca es negra ni gris: siempre teñida de azul (o de café en el botón primario).

**La Regla del Encabezado Sólido.** El encabezado es azul sólido, sin translucidez ni `backdrop-filter`: un filtro volvería al encabezado el bloque contenedor del panel offcanvas (`position: fixed`) y lo recortaría.

## Shapes

Esquinas suaves y escalonadas por tamaño: 10px (`rounded.md`) en todo control (botones, campos, selectores); 12px (`rounded.control-lg`) en el botón grande y en los enlaces del panel offcanvas; 14px (`rounded.menu`) en el menú de la cuenta, cuyas opciones llevan 8px; 16px (`rounded.lg`) en toda tarjeta y contenedor (bandejas, tablas, métricas, paneles de acción, formularios, constancia, estado vacío, muestra de constancia); 20px (`rounded.xl`) en la tarjeta de acceso. Lo pequeño baja: 8px (`rounded.aviso`) en avisos, motivo citado y el cuadro del ícono de archivo; 6px (`rounded.sm`) en etiquetas de palabras clave, paginación y el enlace de salto. La píldora (`rounded.pill`) se reserva a insignias de estado, contadores, enlaces y botones del encabezado y al botón de la cuenta; el círculo, a avatares, números de paso, sellos, puntos de estado y al botón del menú móvil.

Las líneas, cuando aparecen, son de 1px y separan contenido dentro de una tarjeta, no tarjetas entre sí.

**La Regla del Borde Punteado.** El borde punteado significa «espacio por llenar u opcional»: la zona para soltar el PDF, el estado vacío grande y el bloque de coautor. Al llenarse, la zona de archivo pasa a borde sólido.

## Components

### Buttons
Firmes y cálidos, con la presión justa para invitar a tocarlos.
- **Shape:** 10px; el grande (`btn-lg`) 12px, 600, 0.9375rem y 3rem de alto en la tarjeta de acceso.
- **Primary:** fondo café con un degradado blanco de 7% a 0% en el 60% superior, filo de luz interior de 1px y sombra cálida teñida de café; texto blanco 500, relleno 0.5rem × 0.875rem.
- **Hover / Focus:** transición de 180ms de color, fondo, borde y sombra; el fondo pasa a café tostado y la sombra cálida crece. Foco con el anillo café de 4px. Al presionar, `scale(0.98)` con salida exponencial.
- **Secondary:** fondo blanco, borde `emp-n-300`, tinta azul, sombra de un píxel; hover `emp-n-25`, presionado `emp-n-100`. Es el botón por defecto para toda acción que no es la principal.
- **Quieto:** texto `emp-n-700` sin fondo ni borde para acciones discretas en filas (PDF, quitar coautor); en hover se tiñe de café sobre café papel.
- **Link:** texto café sin subrayado que se subraya en hover.
- **Deshabilitado:** opacidad 0.5; el secundario usa texto `emp-n-500` sobre blanco.

### Chips (Insignias de estado)
- **Style:** píldora con punto de 7px a la izquierda y texto de 12px peso 500, borde de 1px.
- **State:** «En revisión / Pendiente» café sobre café papel con borde café bruma; «Aprobado / Contestado» azul sobre velo azul (6%) con borde azul al 14%; «Rechazado» grafito sobre blanco con borde `emp-n-300` y punto hueco. El tipo de punto distingue el estado aun sin color, y el texto siempre lo nombra.
- **Punto de estado:** fuera de la insignia, un punto de 8px (café, azul o hueco grafito) acompaña siempre a una etiqueta de texto, como en las métricas del autor.
- **Etiquetas de palabras clave:** rectángulo de 6px, fondo `emp-n-50`, borde `emp-n-150`, 13px; no son estados y no llevan punto.

### Cards / Containers
- **Corner Style:** 16px.
- **Background:** blanco sobre marfil; `emp-n-25` para cabeceras de tabla, respuesta y motivo citados.
- **Shadow Strategy:** `--emp-sombra-tarjeta` (ver Elevation & Depth).
- **Border:** ninguno; las filas internas se dividen con `emp-n-100` y las secciones internas con `emp-n-150`.
- **Internal Padding:** cabecera de bandeja 1rem × 1.25rem; filas 0.875rem × 1.25rem; panel de acción y formularios de ajuste 1.5rem; formulario de envío y de cursos 1.5rem (2rem × 2.25rem desde 768px).
- **Filas de bandeja:** título en 500 y metadato en 13px `emp-n-600`; a la derecha, fecha tabular y chevron. En hover la fila toma `emp-n-25` y el título se vuelve café.
- **Panel de acción** (dictamen, respuesta): tarjeta lateral fija con título, explicación breve, botón primario a todo lo ancho, un separador «o» con líneas `emp-n-150` y la acción alternativa como secundaria.

### Métricas de los paneles
Por decisión del usuario, ambos paneles abren con una franja de tarjetas de conteo: cinco en el administrador (artículos en revisión, autores por activar, mensajes sin responder, suscriptores activos, cursos visibles) y cuatro en el autor (enviados, en revisión, aprobados, no aprobados). Cada métrica es una tarjeta blanca de 16px con la etiqueta en 13px 500 `emp-n-600` precedida de un ícono café de 14px o de un punto de estado, la cifra grande tabular y una nota de 12px que dice qué significa el número. En el administrador cada métrica enlaza a la lista ya filtrada y en hover se levanta 2px con la sombra alta (220ms); en el autor son informativas y no se mueven.

### Inputs / Fields
- **Style:** borde `emp-borde-campo` de 1px, fondo tenue `emp-n-25`, sombra interior, radio 10px, altura mínima 2.5rem (2.75rem y texto de 15px en la tarjeta de acceso), relleno 0.5rem × 0.75rem. Etiqueta arriba en 13px 500 azul; los opcionales se marcan con «(opcional)» en `emp-n-600`. Ayuda en 13px `emp-n-600`.
- **Hover:** el fondo se aclara a blanco y el borde se oscurece un paso.
- **Focus:** fondo blanco, borde café y anillo café de 4px al 14%; el cursor de texto también es café.
- **Error:** borde café con refuerzo interior de 1px, fondo café papel, y mensaje en 13px peso 500 precedido de un ícono de alerta dibujado en SVG. Si hay más de un error, un aviso arriba de la tarjeta dice cuántos campos revisar.
- **Contraseña:** botón «Mostrar / Ocultar» de texto dentro del campo, a la derecha.
- **Disabled:** fondo `emp-n-50`.
- **Grupos:** en formularios largos (registro), cada grupo lleva una leyenda en Title con línea inferior `emp-n-150`.
- **Zona de archivo:** etiqueta punteada de 12px sobre `emp-n-25` con ícono en cuadro blanco de 8px; hover o arrastre la tiñe de café papel con borde café; con archivo elegido pasa a borde sólido `emp-n-200` sobre blanco.

### Navigation
- **Encabezado:** azul sólido; logotipos a la izquierda que llevan al inicio de cada área. Enlaces de sección como píldoras de 2.25rem en 13px 500, blanco al 76% con ícono al 72% de opacidad; hover con velo blanco al 8% y texto blanco; activo con velo al 13%, anillo interior de 1px y `aria-current="page"`. El administrador muestra seis secciones (Pendientes, Artículos, Autores, Consultorio, Cursos, Newsletter) y el autor dos (Mis artículos, Enviar artículo).
- **Contadores:** las secciones con pendientes llevan su número (blanco al 16% con texto blanco; blanco con texto azul en la sección activa) y texto oculto para lectores de pantalla («3 en revisión»). Solo aparecen si hay algo pendiente.
- **Menú de la cuenta:** botón en píldora con avatar circular de 2rem (inicial en blanco sobre velo al 14%), nombre y flecha que gira 180° al abrir. El panel es blanco, de 16rem, radio de 14px y sombra elevada; arriba, el avatar azul de 2.25rem con nombre y rol, separado por una línea; debajo, «Ver el sitio de la revista» y «Cerrar sesión» con íconos `emp-n-600`. Entra con opacidad y escala desde 0.96.
- **Móvil (debajo de 992px):** la navegación pasa a un panel offcanvas azul de 19rem que entra por la derecha, con su rótulo y botón de cerrar blanco; los enlaces son de 2.75rem, a todo lo ancho y con radio de 12px.
- **Acceso:** en el encabezado, «Volver al sitio» como enlace en píldora (blanco al 76%) y el botón de la otra pantalla como píldora con contorno blanco al 28% que en hover se llena de blanco con texto azul.

### Tablas
- Encabezados en label de 12px `emp-n-600` con borde inferior `emp-n-200`; dentro de un marco, sobre `emp-n-25`. Celdas de 0.875rem de alto de relleno en marcos, cifras tabulares, la última fila sin borde. El primer enlace de cada fila es azul 500 con subrayado `emp-n-300` que se vuelve café en hover. En pantallas menores de 768px la tabla desplaza en horizontal dentro de su marco (ancho mínimo 44rem). Paginación con números de 2rem de ancho, radio 6px y página activa en azul.

### Avisos
- Caja de 8px con borde de 1px, ícono SVG de 16px pintado con `currentColor` y texto azul. Neutro: `emp-n-25` con borde `emp-n-150`; éxito e información: `emp-n-50` con borde `emp-n-200`; atención y error: café papel con borde café bruma e ícono café. Entran con 240ms desde 4px arriba y se pueden cerrar.

### Proceso de publicación (registro)
La columna izquierda del registro: el lema en Display, un párrafo en grafito, cuatro pasos numerados (círculos blancos de 1.75rem con número café tabular y anillo `emp-n-200`, unidos por una línea de 1px `emp-n-300`) y una muestra de constancia en tarjeta blanca de 16px, etiquetada «ejemplo», con folio en 600 y la insignia «Verificable».

### Constancia y artículo aprobado (firma)
El único momento con movimiento propio: un sello circular azul con palomita blanca crece desde 0.6 en 500ms y, 80ms después, la tarjeta de la constancia sube 10px y aparece; usa `@starting-style`, así que solo ocurre al llegar. La constancia es una tarjeta blanca de 16px con sombra de tarjeta, la insignia de estado, el título del artículo en 600 y una lista de registro (folio, fecha) con divisores `emp-n-100`. El folio es el dato protagonista: va en 600 y es verificable públicamente.

### Estado vacío
Contenedor punteado `emp-n-300` de 16px sobre blanco al 60% (deja ver el marfil), centrado: ícono café en círculo blanco de 3rem, título de 17px en 600, explicación de hasta 46ch y el botón primario que resuelve el vacío.

### Páginas de error
Plantilla autónoma, sin Vite ni sesión, que repite en línea los valores del sistema para mostrarse aunque falle el resto de la aplicación: encabezado azul con los dos logotipos, fondo marfil, el mensaje en una tarjeta blanca de 20px con la sombra elevada y el botón primario café de 10px.

## Do's and Don'ts

### Do:
- **Do** poner el fondo de página en marfil `emp-marfil` y apoyar el contenido en tarjetas blancas de 16px con `--emp-sombra-tarjeta`, sin borde.
- **Do** escribir todo texto en azul `#001328` o en un neutro mezclado del azul (`emp-n-600` para secundario, `emp-n-700` para notas); el `emp-n-500` solo para lo deshabilitado.
- **Do** usar neutros cálidos (blanco con café) para superficies, líneas y fondos de campo.
- **Do** mantener el encabezado azul sólido con los logotipos de la UNAM y la FCA a la izquierda y la navegación a la derecha, y mandar la navegación a un offcanvas azul por la derecha debajo de 992px.
- **Do** mostrar cada estado con texto: insignia con punto (café «En revisión / Pendiente», azul «Aprobado / Contestado», grafito con punto hueco «Rechazado») o punto junto a una etiqueta.
- **Do** dejar un solo botón primario café por zona de acción; las alternativas van como secundario o quieto.
- **Do** dar a cada métrica etiqueta, cifra tabular y una nota que explique el número; en el administrador, enlazarla a la lista filtrada.
- **Do** poner las cifras, fechas y folios en números tabulares.
- **Do** mantener las transiciones en 180ms con `cubic-bezier(0.23, 1, 0.32, 1)` y la presión de botón en `scale(0.98)`; reservar el movimiento propio al momento de artículo aprobado.
- **Do** tematizar las superficies del navegador con la paleta: selección en café bruma, cursor café, barra de desplazamiento `emp-n-300`, subrayado con 0.2em de separación.
- **Do** contar lo pendiente en la navegación y en las cabeceras de bandeja, y ordenar las bandejas con lo más antiguo primero.

### Don't:
- **Don't** poner texto café sobre azul ni azul sobre café; dentro del azul solo va blanco o blanco translúcido.
- **Don't** usar verde, rojo ni ningún color fuera de café, azul, blanco, negro y sus mezclas, ni siquiera para éxito o error.
- **Don't** comunicar un estado solo con color o solo con un ícono.
- **Don't** volver al blanco plano: ni fondo de página blanco ni contenedores separados solo por un borde de 1px.
- **Don't** combinar un borde visible con una sombra amplia, ni usar sombras negras o grises.
- **Don't** dar translucidez ni `backdrop-filter` al encabezado.
- **Don't** usar títulos serif decorativos ni una segunda familia tipográfica, ni pesos fuera de 400/500/600.
- **Don't** poner una etiqueta pequeña (kicker) encima de un título, ni etiquetas en mayúsculas espaciadas.
- **Don't** agregar filas de cifras fuera de la franja de métricas de los paneles, ni métricas sin nota que explique el número.
- **Don't** usar la píldora para nada que no sea un estado, un contador o la navegación del encabezado.
