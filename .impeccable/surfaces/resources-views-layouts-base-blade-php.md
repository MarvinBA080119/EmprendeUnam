---
version: 1
slug: "resources-views-layouts-base-blade-php"
primary_target: "resources/views/layouts/base.blade.php"
related_targets: ["resources/views/layouts/publico.blade.php","resources/views/layouts/admin.blade.php","resources/views/layouts/autor.blade.php","resources/css/emprende.css"]
---

# Sistema EmprendeUNAM (login, panel del autor, panel del administrador)

Modo: operate. Público: equipo editorial (1–3 personas, escritorio, uso diario) y autores académicos (uso esporádico). Tarea: activar cuentas, dictaminar artículos, atender el consultorio; el autor envía su artículo y descarga su constancia. Fuera de alcance: el sitio público (layouts/sitio), que conserva su diseño.

Restricciones: paleta cerrada #664300 / #001328 / blanco / negro y sus mezclas; nunca café sobre azul; estados siempre con texto, sin verdes ni rojos. Controles estándar de Bootstrap 5 rehechos con los tokens.

Dirección elegida: el estándar de la categoría (salida canónica), a la altura de Linear, Stripe Dashboard y Vercel.

## Direction contract

THESIS: Un panel de trabajo sobrio y preciso donde lo pendiente se ve primero; rechaza la plantilla de Bootstrap con barra azul pesada, títulos serif decorativos y tarjetas de cifra iguales.

OWN-WORLD: Fondo blanco; neutros mezclados del azul #001328 (bordes 1px, fondos de barra lateral, texto secundario); texto principal en tinta azul; café #664300 solo para la acción principal, el foco y el elemento activo. Inter en pesos 400/500/600 con números tabulares; radios de 6px en controles y 12px en contenedores; elevación por borde, sombra solo en menús.

STORY: El editor entra y ve de inmediato cuántas cuentas, artículos y mensajes esperan, y los resuelve en pocos clics; el autor sabe en qué estado está su artículo y qué sigue.

FIRST VIEWPORT: Login: columna centrada de 360px con el nombre de la revista, título, dos campos, botón café a todo lo ancho y enlaces discretos. Panel: barra lateral de 248px con navegación y contadores de pendientes; encabezado de página con título y acciones a la derecha; contenido con la bandeja de pendientes arriba.

FORM: canon (salida estándar), elegido por el usuario sobre la dirección asignada; seed:403bb47850e80751. Firma: contadores de pendientes en la navegación y estados como píldora con punto y texto; movimiento de 150–200ms con ease-out y presión de botón a escala 0.98.

FINISH: unreviewed and undocumented is unfinished; this build ends with the finish review, the verdict, DESIGN.md, and every shipping raster carrying its provenance
