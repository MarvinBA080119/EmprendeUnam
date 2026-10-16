---
name: Revista EmprendeUNAM — Sistema
description: Panel de trabajo sobrio y preciso para autores y equipo editorial, donde lo pendiente se ve primero.
colors:
  emp-cafe: "#664300"
  emp-cafe-hover: "#573900"
  emp-cafe-50: "#f6f4f0"
  emp-cafe-100: "#f0ece6"
  emp-cafe-200: "#e7e1d6"
  emp-azul: "#001328"
  emp-blanco: "#ffffff"
  emp-n-25: "#fafafb"
  emp-n-50: "#f6f7f7"
  emp-n-100: "#edeef0"
  emp-n-150: "#e6e7ea"
  emp-n-200: "#dbdee1"
  emp-n-300: "#ccd0d4"
  emp-n-500: "#8c959e"
  emp-n-600: "#616d7a"
  emp-n-700: "#4c5a68"
  emp-sobre-azul-2: "#b8bdc3"
  emp-linea-sobre-azul: "rgba(255, 255, 255, 0.14)"
typography:
  display:
    fontFamily: "Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif"
    fontSize: "1.75rem"
    fontWeight: 600
    lineHeight: 1.2
    letterSpacing: "-0.03em"
  headline:
    fontFamily: "Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif"
    fontSize: "1.5rem"
    fontWeight: 600
    lineHeight: 1.25
    letterSpacing: "-0.025em"
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
  sm: "0.25rem"
  md: "0.375rem"
  aviso: "0.5rem"
  lg: "0.75rem"
  pill: "999px"
spacing:
  xs: "0.375rem"
  sm: "0.5rem"
  md: "0.75rem"
  contenedor: "1.125rem"
  lg: "1.25rem"
  xl: "1.75rem"
  2xl: "2.5rem"
components:
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
    rounded: "{rounded.md}"
    padding: "0.625rem 0.875rem"
  button-secondary:
    backgroundColor: "{colors.emp-blanco}"
    textColor: "{colors.emp-azul}"
    rounded: "{rounded.md}"
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
    rounded: "{rounded.md}"
    padding: "0.3125rem 0.625rem"
  button-quieto-hover:
    backgroundColor: "{colors.emp-cafe-50}"
    textColor: "{colors.emp-cafe}"
  input:
    backgroundColor: "{colors.emp-blanco}"
    textColor: "{colors.emp-azul}"
    rounded: "{rounded.md}"
    padding: "0.5rem 0.75rem"
    height: "2.375rem"
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
    backgroundColor: "{colors.emp-n-100}"
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
  cuenta-activa:
    backgroundColor: "{colors.emp-cafe}"
    textColor: "{colors.emp-blanco}"
  nav-lateral-enlace:
    backgroundColor: "transparent"
    textColor: "{colors.emp-n-700}"
    rounded: "{rounded.md}"
    padding: "0.375rem 0.5rem"
    height: "2.125rem"
  nav-lateral-enlace-hover:
    backgroundColor: "{colors.emp-n-100}"
    textColor: "{colors.emp-azul}"
  nav-lateral-enlace-activo:
    backgroundColor: "{colors.emp-blanco}"
    textColor: "{colors.emp-azul}"
  nav-lateral:
    backgroundColor: "{colors.emp-n-25}"
    width: "15.5rem"
  contenedor:
    backgroundColor: "{colors.emp-blanco}"
    textColor: "{colors.emp-azul}"
    rounded: "{rounded.lg}"
    padding: "0.75rem 1.125rem"
  aviso:
    backgroundColor: "{colors.emp-n-25}"
    textColor: "{colors.emp-azul}"
    rounded: "{rounded.aviso}"
    padding: "0.75rem 0.875rem"
  aviso-atencion:
    backgroundColor: "{colors.emp-cafe-50}"
    textColor: "{colors.emp-azul}"
  panel-acceso:
    backgroundColor: "{colors.emp-azul}"
    textColor: "{colors.emp-blanco}"
    padding: "3.5rem"
---

# Design System: Revista EmprendeUNAM — Sistema

Este documento cubre el **sistema** (iniciar sesión y registro, área del autor, área del administrador y páginas de error). El sitio público (`layouts/sitio.blade.php`, `resources/css/sitio/*`, `resources/js/sitio*`) conserva su diseño original y no forma parte de este sistema.

## Overview

**Creative North Star: "La mesa de redacción ordenada"**

El sistema es un panel de trabajo sobrio y preciso: un escritorio editorial limpio donde lo que espera acción está arriba y contado, y todo lo demás guarda silencio. La dirección es el estándar de la categoría ejecutado con el acabado de Linear, Stripe Dashboard y Vercel: retícula alineada, escala tipográfica corta y real, bordes de un píxel, estados claros y movimiento mínimo. No hay mundo temático ni guiños decorativos; la seriedad institucional de la UNAM se transmite con orden, no con ornamento.

La densidad es de herramienta de uso diario: cuerpo de 14px, filas de 12px de alto de relleno, contenedores con borde fino sobre fondo blanco. El color trabaja por exclusión: la tinta es azul, los neutros salen del azul mezclado con blanco y el café aparece solo donde algo pide acción o atención. La única superficie azul grande es el panel de las páginas de acceso, que muestra el proceso de publicación y una constancia de ejemplo.

Rechazos confirmados por la dirección: la plantilla de Bootstrap con barra de navegación azul pesada, los títulos serif decorativos y las filas de tarjetas de cifra iguales.

El movimiento es casi invisible: transiciones de 160ms con salida exponencial, botones que ceden a escala 0.98 al presionarse, avisos que entran desde 4px arriba. Hay un solo momento con movimiento propio: la pantalla de artículo aprobado, donde el sello y la constancia aparecen una vez. Todo se apaga con `prefers-reduced-motion`.

**Key Characteristics:**
- Fondo blanco, tinta azul `#001328`, neutros mezclados del azul; café `#664300` escaso y con propósito.
- Inter en tres pesos (400/500/600), números tabulares en todo dato.
- Elevación por borde de 1px; sombras de un píxel solo en controles.
- Radios de 6px en controles y 12px en contenedores; píldora solo para estados y contadores.
- Estados como píldora con punto y texto, nunca solo color; sin verdes ni rojos.
- Contadores de pendientes en la navegación como firma del sistema.

## Colors

Paleta cerrada de cuatro colores (café, azul, blanco y negro) más sus mezclas; los neutros son el azul mezclado con blanco, así que hasta el gris tiene la temperatura de la tinta.

### Primary
- **Café Imprenta** (`emp-cafe`): el color de la acción. Botón primario, enlaces, anillo y contorno de foco, cursor de texto, ícono y contador del elemento activo de la navegación, subrayado de la pestaña activa del autor, estado «En revisión / Pendiente», campos con error y avisos de atención. Contraste sobre blanco ≈ 8.8:1.
- **Café Tostado** (`emp-cafe-hover`): hover y estado presionado del botón primario y de los enlaces.
- **Café Papel** (`emp-cafe-50`): fondo de la insignia «En revisión», de campos inválidos, de avisos de atención y del hover de la zona de archivo y del botón quieto.
- **Café Velado** (`emp-cafe-100`): estado presionado de botones con tinte café.
- **Café Bruma** (`emp-cafe-200`): borde de la insignia «En revisión» y de avisos de atención; fondo de la selección de texto.

### Secondary
- **Azul Tinta** (`emp-azul`): color de todo el texto principal y los títulos, del estado «Aprobado / Contestado», del avatar del usuario y del panel de acceso. Es la tinta, no un acento.

### Neutral
- **Blanco** (`emp-blanco`): fondo de página, de contenedores, del botón secundario y del enlace activo de la barra lateral.
- **Azul Niebla** (`emp-n-25`): la capa tonal más leve: barra lateral, encabezado de tablas enmarcadas, hover de filas, estado vacío, respuesta y motivo citados.
- **Azul Nube** (`emp-n-50`): hover del botón secundario y de la navegación del autor, fondo de etiquetas de palabras clave y de campos deshabilitados.
- **Azul Vapor** (`emp-n-100`): hover de la barra lateral, fondo de la insignia «Aprobado», divisores entre filas de una bandeja.
- **Azul Hilo** (`emp-n-150`): el borde por defecto de todo contenedor, barra y separador (azul + 90% blanco).
- **Azul Trazo** (`emp-n-200`): borde inferior del encabezado de tabla, borde de la insignia «Aprobado» y de la paginación.
- **Azul Marco** (`emp-n-300`): borde de campos y del botón secundario, borde de la insignia «Rechazado» y de las zonas punteadas (azul + 80% blanco).
- **Azul Ceniza** (`emp-n-500`): solo texto deshabilitado y borde de campo en hover (3.0:1, no apto para texto legible).
- **Azul Pizarra** (`emp-n-600`): texto secundario: subtítulos, metadatos, ayudas, encabezados de tabla, placeholders (5.3:1).
- **Azul Grafito** (`emp-n-700`): texto de navegación en reposo, insignia «Rechazado», botón quieto, notas (7.1:1; azul + 30% blanco).
- **Blanco sobre Azul** (`emp-sobre-azul-2`): texto secundario dentro del panel azul de acceso, teñido del propio azul.
- **Línea sobre Azul** (`emp-linea-sobre-azul`): la línea que une los pasos en el panel azul.

El negro forma parte de la paleta permitida, pero el sistema no lo usa: la tinta es siempre el azul.

### Named Rules
**La Regla del No Cruce.** Nunca texto café sobre azul ni azul sobre café. Sobre la superficie azul solo va blanco o `emp-sobre-azul-2`; si un estado debe aparecer ahí, va dentro de una tarjeta blanca (como la constancia de ejemplo).

**La Regla del Café Escaso.** El café marca lo que pide acción o atención: la acción principal, el foco, lo activo, lo pendiente y el error. Una vista tiene un solo botón primario por zona de acción; lo demás es secundario o quieto.

**La Regla de la Paleta Cerrada.** Ningún color fuera de café, azul, blanco, negro y sus mezclas o transparencias. Sin verdes ni rojos: los colores de estado de Bootstrap están remapeados (`success`/`info` → azul, `warning`/`danger` → café).

## Typography

**Display Font:** Inter (con system-ui, -apple-system, Segoe UI, Roboto, Helvetica Neue, Arial, sans-serif)
**Body Font:** Inter (misma pila)

**Character:** Una sola cara de interfaz, neutra y exacta, que deja hablar a la jerarquía de tamaño y peso. Se instala localmente desde `@fontsource/inter` en 400, 500 y 600; no hay cara decorativa. Inter se conserva a propósito aunque el detector la marque como sobreusada: es una superficie de operación con Linear como referencia de acabado, y es una decisión registrada, no un defecto.

### Hierarchy
- **Display** (600, 1.75rem, 1.2, -0.03em): título del panel azul de acceso; ancho máximo 22ch.
- **Headline** (600, 1.5rem, 1.25, -0.025em): título de página (`h1`) en toda vista del sistema y en las páginas de error.
- **Title** (600, 0.9375rem, -0.01em): títulos de sección, de bloque de formulario, de panel de acción, de ajuste, de envío y leyendas de grupo. Los títulos de bandeja bajan a 0.875rem.
- **Body** (400, 0.875rem, 1.5): texto de interfaz, campos, botones (500), etiquetas de campo (500), celdas de tabla.
- **Body lectura** (400, 0.9375rem, 1.65): texto largo como el resumen de un artículo; medida máxima 68ch.
- **Body meta** (400, 0.8125rem): metadatos de fila, ayudas de campo, errores de validación (500), enlaces secundarios (500), botones pequeños.
- **Label** (500, 0.75rem, 0.01em): encabezados de tabla, insignias de estado, grupos de la barra lateral, etiquetas de filtros. Siempre en caja normal, nunca en versalitas ni mayúsculas espaciadas.
- **Label cuenta** (600, 0.6875rem, cifras tabulares): solo dentro de los contadores de pendientes.

Los títulos llevan `letter-spacing: -0.02em` y `text-wrap: balance`; los párrafos, `text-wrap: pretty`.

### Named Rules
**La Regla de los Tres Pesos.** Solo 400, 500 y 600. El 600 es para títulos y cifras, el 500 para controles y etiquetas, el 400 para leer.

**La Regla de la Cifra Tabular.** Toda tabla, fecha, folio, contador y conteo de caracteres usa `font-variant-numeric: tabular-nums` (clase `emp-num`), para que las columnas de datos no bailen.

## Layout

Tres plantillas comparten tokens y cambian de estructura según el uso:

- **Acceso** (iniciar sesión, registro, contraseña): retícula de dos columnas a partir de 992px (`1fr` y `0.82fr`). A la izquierda, la marca y «Volver al sitio» arriba, el formulario centrado en una columna de 23.5rem (376px; 38rem en el registro) y el pie institucional abajo. A la derecha, el panel azul con contenido fijo (`position: sticky`, 100vh) y relleno de 3.5rem (4.5rem × 5rem desde 1400px). Debajo de 992px solo queda la columna, y el formulario arranca arriba en lugar de flotar al centro.
- **Administrador**: barra lateral de 15.5rem (248px) fija al desplazar desde 992px; debajo se vuelve panel desplegable de 17.5rem con una barra superior translúcida de 3.25rem. El área de trabajo llega a 76rem con relleno de 1.5rem × 1.25rem (2.25rem × 2.5rem desde 992px).
- **Autor**: barra superior fija, translúcida, con fila de 68rem centrada; debajo de 768px la navegación baja a un segundo renglón a todo lo ancho. Contenido centrado de 68rem con relleno de 2rem × 1.25rem (2.5rem × 1.5rem desde 768px).

Patrones de página recurrentes:
- **Encabezado de página**: enlace de regreso opcional, título y descripción a la izquierda (máx. 48rem), acciones alineadas a la derecha; 1.75rem de separación con el contenido.
- **Panel de pendientes**: la bandeja de artículos a la izquierda y las de autores y mensajes apiladas a la derecha (`1.45fr` / `1fr` desde 1200px, separación 1.25rem); un resumen en línea con borde superior cierra la página.
- **Detalle**: columna de lectura de hasta 52rem; con acción lateral, `1fr` + 21rem fijo desde 1200px, separación 2.5rem.
- **Formulario por bloques**: cada bloque es título y explicación (16rem; 18rem en ajustes) a la izquierda y campos (máx. 40rem) a la derecha desde 992px, separados por un borde de 1px.
- **Lista de datos**: término de 11rem y valor a la derecha desde 576px; en teléfono se apilan.

Ritmo de espacio: grupos apretados (0.375rem entre ícono y texto, 0.5rem entre botones) y separación generosa entre secciones (1.75rem a 3rem). El relleno horizontal interno de bandejas y tablas enmarcadas es 1.125rem, constante, para que el texto de las filas quede alineado con el de la cabecera. Los campos enfocados dejan 6rem de margen de desplazamiento para no quedar bajo la barra fija. Cortes de Bootstrap: 576, 768, 992, 1200 y 1400px.

## Elevation & Depth

El sistema es plano y separa capas con bordes de 1px y tonos del azul, no con sombras. Los contenedores no tienen sombra. Lo único elevado son los controles, con una sombra de un píxel que les da cuerpo sin despegarlos, y las capas flotantes. Las barras fijas usan blanco translúcido con desenfoque para que el contenido que pasa debajo se intuya sin competir.

### Shadow Vocabulary
- **Presión del primario** (`box-shadow: 0 1px 2px rgba(0, 19, 40, 0.12)`): botón primario.
- **Presión del secundario** (`box-shadow: 0 1px 2px rgba(0, 19, 40, 0.06)`): botón secundario y enlace activo de la barra lateral (junto con un anillo de 1px `emp-n-150`).
- **Presión del campo** (`box-shadow: 0 1px 2px rgba(0, 19, 40, 0.04)`): campos de texto y selectores.
- **Capa flotante** (`--emp-sombra-menu`): elementos que flotan sobre la página; hoy, el enlace «Saltar al contenido».
- **Anillo de foco** (`--emp-anillo`): campos, casillas, paginación y zona de archivo al enfocarse. El resto de los elementos enfocables usa contorno café de 2px con 2px de separación.

### Named Rules
**La Regla del Borde Primero.** Un contenedor se separa con borde `emp-n-150` de 1px y radio de 12px; nunca con sombra. Si algo necesita más presencia, sube de tono (`emp-n-25`), no de sombra.

## Shapes

Esquinas suaves y consistentes: 6px (`rounded.md`) en todo control (botones, campos, enlaces de navegación, etiquetas, paginación, botones de ícono), 8px (`rounded.aviso`) en avisos y cajas citadas, 12px (`rounded.lg`) en todo contenedor (bandejas, tablas enmarcadas, paneles de acción, formularios de ajuste, constancia, zona de archivo, estado vacío). La píldora (`rounded.pill`) se reserva a las insignias de estado y los contadores; el círculo, al avatar, a los números de paso y a los sellos. Los bordes son siempre de 1px.

**La Regla del Borde Punteado.** El borde punteado significa «espacio por llenar u opcional»: la zona para soltar el PDF, el estado vacío grande y el bloque de coautor. Al llenarse, la zona de archivo pasa a borde sólido.

## Components

### Buttons
Contenidos y firmes, como en una herramienta de oficina bien hecha.
- **Shape:** esquinas de 6px en todos los tamaños.
- **Primary:** fondo café, texto blanco, peso 500, relleno 0.5rem × 0.875rem; en las páginas de acceso va a todo lo ancho con relleno vertical de 0.625rem y 0.9375rem de texto.
- **Hover / Focus:** transición de 160ms de color, fondo, borde y sombra; el fondo pasa a café tostado. El foco se muestra con el anillo café de 3px. Al presionar, el botón cede a `scale(0.98)` con salida exponencial.
- **Secondary:** fondo blanco, borde `emp-n-300`, tinta azul; hover `emp-n-50`, presionado `emp-n-100`. Es el botón por defecto para toda acción que no es la principal, incluido «Rechazar artículo».
- **Quieto:** texto `emp-n-700` sin fondo ni borde para acciones discretas en filas (descargar PDF, quitar coautor); en hover se tiñe de café sobre café papel.
- **Link:** texto café sin subrayado que se subraya en hover (p. ej., «Limpiar filtros»).
- **Deshabilitado:** opacidad 0.5; el secundario usa texto `emp-n-500` sobre blanco.

### Chips (Insignias de estado)
- **Style:** píldora con punto de 7px a la izquierda y texto de 12px peso 500, borde de 1px.
- **State:** «En revisión / Pendiente» café sobre café papel con borde café bruma; «Aprobado / Contestado» azul sobre `emp-n-100` con borde `emp-n-200`; «Rechazado» grafito sobre blanco con borde `emp-n-300` y punto hueco. El tipo de punto distingue el estado aun sin color, y el texto siempre lo nombra.
- **Etiquetas de palabras clave:** rectángulo de 6px, fondo `emp-n-50`, borde `emp-n-150`, 13px; no son estados y no llevan punto.

### Cards / Containers
- **Corner Style:** 12px.
- **Background:** blanco; tonos `emp-n-25` para cabeceras de tabla, estados vacíos y texto citado.
- **Shadow Strategy:** ninguna (ver Elevation & Depth).
- **Border:** 1px `emp-n-150`; filas internas divididas con `emp-n-100`.
- **Internal Padding:** cabecera de bandeja 0.875rem × 1.125rem; filas 0.75rem × 1.125rem; panel de acción y formularios de ajuste 1.25rem.
- **Filas de bandeja:** título en 500 y metadato en 13px `emp-n-600`; a la derecha, fecha tabular y chevron. En hover la fila toma `emp-n-25` y el título se vuelve café.
- **Panel de acción** (dictamen, respuesta): contenedor lateral fijo con título, explicación breve, botón primario a todo lo ancho, un separador «o» con líneas `emp-n-150` y la acción alternativa como secundaria.

### Inputs / Fields
- **Style:** borde `emp-n-300` de 1px, fondo blanco, radio 6px, altura mínima 2.375rem, relleno 0.5rem × 0.75rem, sombra de un píxel. Etiqueta arriba en 500 azul; los opcionales se marcan con «(opcional)» en `emp-n-600`, los obligatorios no se marcan. Ayuda en 13px `emp-n-600`.
- **Hover:** el borde sube a `emp-n-500`.
- **Focus:** borde café y anillo café de 3px al 22%; el cursor de texto también es café.
- **Error:** borde café con refuerzo interior de 1px, fondo café papel, y mensaje en 13px peso 500 precedido de un ícono de alerta dibujado en SVG. Tras un envío fallido, el foco va al primer campo marcado y un aviso arriba dice cuántos campos revisar.
- **Contraseña:** botón «Mostrar / Ocultar» de texto dentro del campo, a la derecha.
- **Disabled:** fondo `emp-n-50`.
- **Zona de archivo:** etiqueta punteada de 12px con ícono en cuadro blanco de 8px; hover o arrastre la tiñe de café papel con borde café; con archivo elegido pasa a borde sólido `emp-n-200` sobre blanco.

### Navigation
- **Barra lateral (administrador):** fondo `emp-n-25` con borde derecho `emp-n-150`. Grupos («Trabajo editorial», «Difusión») en label de 12px `emp-n-600`. Enlaces de 2.125rem con ícono SVG de 16px, texto 500 `emp-n-700`; hover `emp-n-100` con tinta azul; activo en tarjeta blanca con anillo de 1px, ícono café y contador café. Al pie, el enlace al sitio público y el bloque de usuario (avatar azul con inicial, nombre, rol, botón de salir).
- **Barra superior (autor):** blanco translúcido con borde inferior; marca, dos enlaces y usuario. Enlace activo con tinta azul y una línea café de 2px pegada al borde inferior de la barra.
- **Móvil:** la barra lateral se vuelve panel desplegable desde la izquierda; la del autor baja la navegación a un segundo renglón y oculta el nombre del usuario en teléfonos.
- **Marca:** «Revista EmprendeUNAM» en 15px peso 600 y la leyenda «UNAM · Facultad de Contaduría y Administración» en 12px `emp-n-600`. No hay logotipo.

### Tablas
- Encabezados en label de 12px `emp-n-600` con borde inferior `emp-n-200`; dentro de un marco, sobre `emp-n-25`. Celdas de 0.75rem, cifras tabulares, la última fila sin borde. El primer enlace de cada fila es azul 500 con subrayado `emp-n-300` que se vuelve café en hover. En pantallas menores de 768px la tabla desplaza en horizontal dentro de su marco (ancho mínimo 44rem). Paginación con números de 2rem de ancho, radio 6px y página activa en azul.

### Avisos
- Caja de 8px con borde de 1px, ícono SVG de 16px a la izquierda pintado con `currentColor` y texto azul. Éxito e información: `emp-n-50` con borde `emp-n-200`; atención y error: café papel con borde café bruma e ícono café. Entran con 240ms desde 4px arriba y se pueden cerrar.

### Contadores de pendientes (firma)
Píldora de 1.25rem de alto con la cifra tabular en 11px peso 600: `emp-n-150` con tinta azul en reposo, café con texto blanco en el enlace activo, y versión neutra (`emp-n-100`, grafito) junto a los títulos de bandeja. Solo aparecen si hay algo pendiente, y llevan texto oculto para lectores de pantalla («3 en revisión»).

### Constancia y artículo aprobado (firma)
El único momento con movimiento propio: un sello circular azul con palomita blanca crece desde 0.6 en 500ms y, 80ms después, la tarjeta de la constancia sube 10px y aparece; usa `@starting-style`, así que solo ocurre al llegar. La tarjeta es un contenedor blanco de 12px con la insignia de estado, el título del artículo en 600 y una lista de registro (folio, fecha) con divisores `emp-n-100`. El folio es el dato protagonista: va en 600 y es verificable públicamente.

### Estado vacío
Contenedor punteado de 12px sobre `emp-n-25`, centrado: ícono café en círculo blanco de 3rem, título de 17px en 600, explicación de hasta 46ch y el botón primario que resuelve el vacío.

### Páginas de error
Plantilla autónoma, sin Vite ni sesión, que repite los valores del sistema en línea (Inter local, tinta azul, borde `emp-n-150`, título de 24px, botón café de 6px) para mostrarse aunque falle el resto de la aplicación.

## Do's and Don'ts

### Do:
- **Do** escribir todo texto en azul `#001328` o en un neutro mezclado del azul (`emp-n-600` para secundario, `emp-n-700` para navegación); el `emp-n-500` solo para lo deshabilitado.
- **Do** separar contenedores con borde de 1px `emp-n-150` y radio de 12px, y los controles con radio de 6px.
- **Do** mostrar cada estado como insignia con punto y texto: café para «En revisión / Pendiente», azul para «Aprobado / Contestado», grafito con punto hueco para «Rechazado».
- **Do** dejar un solo botón primario café por zona de acción; las alternativas van como secundario o quieto.
- **Do** poner las cifras, fechas y folios en números tabulares.
- **Do** mantener las transiciones en 160ms con `cubic-bezier(0.23, 1, 0.32, 1)` y la presión de botón en `scale(0.98)`; reservar el movimiento propio al momento de artículo aprobado.
- **Do** tematizar las superficies del navegador con la paleta: selección en café bruma, cursor café, barra de desplazamiento `emp-n-300`, subrayado con 0.2em de separación.
- **Do** contar lo pendiente en la navegación y en las cabeceras de bandeja, y ordenar las bandejas con lo más antiguo primero.

### Don't:
- **Don't** poner texto café sobre azul ni azul sobre café; sobre el panel azul solo va blanco o `emp-sobre-azul-2`.
- **Don't** usar verde, rojo ni ningún color fuera de café, azul, blanco, negro y sus mezclas, ni siquiera para éxito o error.
- **Don't** comunicar un estado solo con color o solo con un ícono.
- **Don't** pintar la barra de navegación de azul sólido; el azul como superficie se reserva al panel de acceso.
- **Don't** usar títulos serif decorativos ni una segunda familia tipográfica, ni pesos fuera de 400/500/600.
- **Don't** armar páginas con filas de tarjetas de cifra iguales; los totales van en línea, como lista de datos.
- **Don't** poner una etiqueta pequeña (kicker) encima de un título, ni etiquetas en mayúsculas espaciadas.
- **Don't** dar sombra a los contenedores ni combinar borde con una sombra amplia.
- **Don't** usar la píldora para nada que no sea un estado o un contador.
