{{-- Edición del newsletter (newsletter/index.html del sitio original). Diseño propio, con
     estilos y script en línea; solo cambian las partes dinámicas (fuera de los bloques literales). --}}
@verbatim
<!DOCTYPE html>
<!--
  ============================================================================
  NEWSLETTER · EMPRENDEUNAM — FCA UNAM
  "Emprendedores al servicio de la pequeña y mediana empresa"
  PROPUESTA E (rev. 2) — editorial contemporáneo + casa de moda · blanco, negro y rojo

  DE DÓNDE VIENE
  ----------------------------------------------------------------------------
  A: negro a sangre y rojo en grandes superficies. Leía como alerta.
  B: neutros cálidos y mucho aire. Perdió voz: leía como plantilla.
  C: ornamentos, ocres, sellos y grano. Ganó voz pero leía como facsímil.
  D: vuelve a blanco / negro / rojo y busca el carácter en la COMPOSICIÓN y
     en el COMPORTAMIENTO, no en la decoración: retícula visible, numerales
     de gran tamaño, columnas que se fijan mientras la otra avanza, listas
     que reaccionan al cursor y un único bloque rojo en toda la pieza.
  E: conserva D y le suma el lenguaje de una casa de moda (referencia:
     Carolina Herrera): mancheta centrada con el logo, y EL PUNTO (lunar)
     como motivo de sistema: trama, viñeta y fila de lunares.
  rev. 2: logo oficial "Emprende UNAM" (grotesca condensada, rojo y negro);
     la tipografía pasa a Barlow Condensed + Barlow para acompañarlo; los
     logos del encabezado al doble de tamaño en una sola línea; se retira
     el bloque de cierre "Nos leemos el próximo mes".

  CONTENIDO DE MUESTRA
  ----------------------------------------------------------------------------
  Textos, cifras y fechas son de EJEMPLO para maquetación. Cada
  bloque lleva un comentario "CONTENIDO:". Los libros son títulos reales;
  las cubiertas son diseños tipográficos propios.

  RUTAS
  ----------------------------------------------------------------------------
  img/          logos institucionales (junto a este archivo)
  ../index.php  regreso al sitio principal — ajústalo al servidor final

  ÍNDICE DEL ARCHIVO
  ----------------------------------------------------------------------------
  <style>   1 Tokens · 2 Base · 3 Piezas · 4 Encabezado · 5 Portada
            6 Capítulos · 7 Esencial · 8 Datos · 9 Práctico · 10 Comunidad
            11 Cierre y pie · 12 Responsivo
  <body>    ENCABEZADO · PORTADA · I–III · LECCIÓN · PIE
  <script>  progreso, capítulo activo, revelados, contadores, gráfico,
            suscripción, año
  ============================================================================
-->
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
@endverbatim
<meta name="csrf-token" content="{{ csrf_token() }}">
@verbatim
<title>Newsletter | EmprendeUNAM · FCA UNAM</title>
<meta name="description" content="Newsletter mensual de EmprendeUNAM: noticias, datos de mercado, herramientas y comunidad para la pequeña y mediana empresa.">
<meta name="theme-color" content="#FFFFFF">

<!-- Barlow Condensed + Barlow servidas localmente (npm), sin CDN -->
@endverbatim
@vite('resources/js/newsletter.js')
@verbatim

<style>

/* ==========================================================================
   1. TOKENS

   Tres colores y dos grises. El rojo no tiñe superficies: marca. Aparece en
   un único bloque a sangre (la lección de liderazgo) y, fuera de ahí, sólo
   en filetes de 2 px, en un dato por bloque y en las reacciones al cursor.
   ========================================================================== */
:root {
  --blanco: #FFFFFF;
  --papel:  #F4F4F2;   /* sección alterna, gris neutro (no crema) */
  --negro:  #1D1D1B;   /* negro del logo */
  --gris:   #3D3D3A;   /* texto secundario: más oscuro para leerse bien */
  --tenue:  #6B6B67;   /* rótulos y metadatos: antes #A0A09C, no alcanzaba contraste */
  --linea:  #E2E2DF;
  --rojo:   #C2143C;   /* rojo del logo */

  /* --display conserva el nombre de variable --serif en el resto del CSS
     para no reescribir cada regla: ahora apunta a la condensada. */
  --serif: 'Barlow Condensed', 'Arial Narrow', sans-serif-condensed, sans-serif;
  --sans:  'Barlow', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;

  --lenta: .85s cubic-bezier(.16, 1, .3, 1);
  --media: .45s cubic-bezier(.16, 1, .3, 1);
  --corta: .25s cubic-bezier(.16, 1, .3, 1);

  --ancho: 1320px;
  --cab: 140px;       /* encabezado en reposo (logos a 108 px) */
  --cab-min: 88px;    /* encabezado compacto al desplazar (logos a 64 px) */
  --logo: 108px;
  --logo-min: 64px;
}

/* ==========================================================================
   2. BASE
   ========================================================================== */
*, *::before, *::after { box-sizing: border-box; }
html { overflow-x: clip; scroll-behavior: smooth; scroll-padding-top: calc(var(--cab-min) + 20px); }
body {
  margin: 0; overflow-x: clip;
  background: var(--blanco); color: var(--negro);
  font-family: var(--sans); font-size: 1.1rem; font-weight: 500; line-height: 1.7;
  -webkit-font-smoothing: antialiased;
}
h1, h2, h3, h4 { font-family: var(--serif); font-weight: 600; letter-spacing: -.005em; line-height: 1.02; margin: 0; }
p { margin: 0 0 1.1rem; } p:last-child { margin-bottom: 0; }
a { color: inherit; text-decoration: none; transition: color var(--corta); }
img { max-width: 100%; height: auto; display: block; }
ul, ol { margin: 0; padding: 0; list-style: none; }
button { font: inherit; color: inherit; background: none; border: 0; cursor: pointer; }
::selection { background: var(--rojo); color: var(--blanco); }
:focus-visible { outline: 2px solid var(--rojo); outline-offset: 3px; }

.envoltura { width: 100%; max-width: var(--ancho); margin: 0 auto; padding: 0 2.5rem; }

/* ==========================================================================
   3. PIEZAS
   ========================================================================== */

/* Etiqueta: sans apretada, con un cuadro rojo de 6 px como viñeta */
.tag {
  display: inline-flex; align-items: center; gap: .55rem;
  font-size: .82rem; font-weight: 600; letter-spacing: .12em; text-transform: uppercase;
  color: var(--negro);
}
.tag::before { content: ''; width: 7px; height: 7px; border-radius: 50%; background: var(--rojo); flex: none; }
.tag--gris { color: var(--gris); }
.tag--claro { color: rgba(255,255,255,.75); }

/* Cabecera de sección: numeral grande a la izquierda, filete negro debajo */
.sec-cab { display: flex; align-items: flex-end; gap: 1.25rem; padding-bottom: 1rem; border-bottom: 1px solid var(--negro); margin-bottom: 3rem; }
.sec-cab__n { display: inline-flex; align-items: center; gap: .5rem; font-family: var(--sans); font-size: .98rem; font-weight: 600; letter-spacing: .04em; color: var(--rojo); }
.sec-cab__n::before { content: ''; width: 7px; height: 7px; border-radius: 50%; background: var(--rojo); }
.sec-cab__t { font-size: .98rem; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; flex: 1; }
.sec-cab__meta { font-size: .86rem; color: var(--tenue); letter-spacing: .04em; }

.fuente { font-size: .89rem; color: var(--tenue); }

/* Enlace: el filete rojo crece de izquierda a derecha */
.enlace {
  position: relative; display: inline-flex; align-items: center; gap: .55rem;
  font-size: .86rem; font-weight: 600; letter-spacing: .1em; text-transform: uppercase;
  padding-bottom: .55rem;
}
.enlace::before { content: ''; position: absolute; left: 0; right: 0; bottom: 0; height: 1px; background: var(--linea); }
.enlace::after { content: ''; position: absolute; left: 0; right: 0; bottom: 0; height: 2px; background: var(--rojo); transform: scaleX(0); transform-origin: left; transition: transform var(--media); }
.enlace:hover::after { transform: scaleX(1); }
.enlace svg { width: 14px; height: 14px; transition: transform var(--media); }
.enlace:hover svg { transform: translateX(5px); }

.boton {
  display: inline-flex; align-items: center; gap: .6rem;
  padding: 1.1rem 2.2rem; border: 1px solid currentColor;
  font-size: .84rem; font-weight: 600; letter-spacing: .14em; text-transform: uppercase;
  transition: background var(--media), color var(--media), border-color var(--media);
}
.boton--rojo { background: var(--rojo); border-color: var(--rojo); color: var(--blanco); }
.boton--rojo:hover { background: var(--negro); border-color: var(--negro); }
.boton--linea:hover { background: var(--blanco); border-color: var(--blanco); color: var(--negro); }

/* Revelados */
.revela { opacity: 0; transform: translateY(24px); transition: opacity var(--lenta), transform var(--lenta); }
.revela.visible { opacity: 1; transform: none; }
.d1.visible { transition-delay: .07s; } .d2.visible { transition-delay: .14s; }
.d3.visible { transition-delay: .21s; } .d4.visible { transition-delay: .28s; }

@media (prefers-reduced-motion: reduce) {
  html { scroll-behavior: auto; }
  .revela { opacity: 1 !important; transform: none !important; transition: none !important; }
  .marca-roja { background-size: 100% 100% !important; }
}


/* ==========================================================================
   3b. EL PUNTO — motivo de sistema

   El logo nuevo de la revista es un disco rojo. Aquí ese disco se vuelve
   lunar: viñeta de las etiquetas, trama de la portada, fila bajo cada
   capítulo y textura del bloque rojo. Es el mismo recurso que una casa de
   moda usa con su estampado insignia: aparece poco, pero en todas partes.
   ========================================================================== */

/* Trama de lunares reutilizable (CSS puro) */
.lunares {
  background-image: radial-gradient(circle, var(--c-punto, var(--rojo)) var(--r-punto, 2.5px), transparent calc(var(--r-punto, 2.5px) + .5px));
  background-size: var(--paso, 16px) var(--paso, 16px);
}

/* Fila de lunares: separador y remate */
.fila-puntos { display: flex; align-items: center; gap: 10px; }
.fila-puntos i { width: 6px; height: 6px; border-radius: 50%; background: var(--rojo); transform: scale(0); transition: transform .6s cubic-bezier(.34,1.56,.64,1); }
.fila-puntos i:nth-child(3) { width: 12px; height: 12px; }
.visible .fila-puntos i, .fila-puntos.visible i { transform: scale(1); }
.visible .fila-puntos i:nth-child(2), .fila-puntos.visible i:nth-child(2) { transition-delay: .08s; }
.visible .fila-puntos i:nth-child(3), .fila-puntos.visible i:nth-child(3) { transition-delay: .16s; }
.visible .fila-puntos i:nth-child(4), .fila-puntos.visible i:nth-child(4) { transition-delay: .24s; }
.visible .fila-puntos i:nth-child(5), .fila-puntos.visible i:nth-child(5) { transition-delay: .32s; }
@media (prefers-reduced-motion: reduce) { .fila-puntos i { transform: none; transition: none; } }

/* ── Mancheta de casa de moda ──
   Nombre en didona mayúscula muy espaciada, como la firma de una maison.
   "EMPRENDE" en rojo y "UNAM" en negro: el mismo reparto de color que el
   logo de la revista. */
.mancheta { text-align: center; padding-bottom: 1.75rem; border-bottom: 2px solid var(--negro); }
.mancheta__logo { height: clamp(64px, 8vw, 104px); width: auto; margin: 0 auto; }
.oculto { position: absolute !important; width: 1px; height: 1px; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; }
.mancheta__nombre {
  font-family: var(--serif); font-weight: 400; font-size: clamp(1.25rem, 2.6vw, 2rem);
  letter-spacing: .42em; text-transform: uppercase; line-height: 1; margin: 0;
  padding-left: .42em; /* compensa el espaciado final para centrar ópticamente */
}
.mancheta__nombre span { color: var(--rojo); }

/* ── Trama interactiva de la portada ──
   Semitono de lunares rojos que crece hacia la esquina superior derecha.
   Los puntos cercanos al cursor se agrandan: el único juego de la página,
   y ocurre donde no estorba a la lectura. La dibuja el script. */
.portada__titular { position: relative; }
.trama { position: absolute; top: 1.5rem; right: -1rem; bottom: 0; width: 40%; pointer-events: none; z-index: 0; }
.trama svg { width: 100%; height: 100%; display: block; overflow: visible; }
.trama circle { fill: var(--rojo); transform-box: fill-box; transform-origin: center; transition: transform .7s cubic-bezier(.34,1.56,.64,1); }
.portada__titulo { position: relative; z-index: 1; }

/* ==========================================================================
   4. ENCABEZADO
   ========================================================================== */
.cabecera {
  position: fixed; inset: 0 0 auto 0; z-index: 50; height: var(--cab);
  background: rgba(255,255,255,.88);
  backdrop-filter: saturate(150%) blur(14px); -webkit-backdrop-filter: saturate(150%) blur(14px);
  border-bottom: 1px solid var(--linea);   /* separa el encabezado del contenido */
  transition: height var(--media), background var(--media);
}
/* Acompaña siempre la lectura. Al bajar se compacta: logos a tamaño completo
   ocuparían una quinta parte de la pantalla durante todo el recorrido. */
.cabecera--posada { height: var(--cab-min); background: rgba(255,255,255,.95); }
.cabecera__fila { height: 100%; display: flex; align-items: center; justify-content: space-between; gap: 2rem; }

.cabecera__marca { display: flex; align-items: center; flex-wrap: nowrap; gap: 1.1rem; flex: none; }
/* Los archivos de UNAM y FCA son BLANCOS (están hechos para fondo oscuro).
   Sobre el papel blanco hay que invertirlos, igual que hace el sitio
   principal con .logo-invertido, o quedan invisibles. */
.cabecera__marca img { height: var(--logo); width: auto; flex: none; filter: invert(1); transition: height var(--media); }
/* El logo de la revista va a color: no se invierte */
.cabecera__marca img.cabecera__logo { filter: none; height: var(--logo); }
.cabecera--posada .cabecera__marca img { height: var(--logo-min); }
.cabecera__sep { width: 1px; height: calc(var(--logo) * .8); background: var(--linea); flex: none; transition: height var(--media); }
.cabecera--posada .cabecera__sep { height: calc(var(--logo-min) * .8); }
.cabecera__nom { font-size: .94rem; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; line-height: 1.3; }
.cabecera__nom small { display: block; font-size: .74rem; font-weight: 500; letter-spacing: .1em; color: var(--tenue); }

.secciones { display: flex; gap: .35rem; }
.secciones a {
  position: relative; display: flex; align-items: baseline; gap: .45rem; padding: .5rem .85rem;
  font-family: var(--serif); font-size: 1.3rem; font-weight: 600; letter-spacing: .01em;
  color: var(--negro);
  transition: color var(--media), background var(--media);
}
/* Filete rojo que marca la opción activa o bajo el cursor */
.secciones a::after { content: ''; position: absolute; left: .85rem; right: .85rem; bottom: .15rem; height: 2px; background: var(--rojo); transform: scaleX(0); transform-origin: left; transition: transform var(--media); }
.secciones a:hover::after, .secciones a.activo::after { transform: scaleX(1); }
.secciones a b { font-weight: 700; color: var(--rojo); font-size: 1.08rem; transition: color var(--media); }
.secciones a:hover { color: var(--rojo); }
.secciones a.activo { color: var(--negro); }
.secciones a.activo b { color: var(--rojo); }


.progreso { position: absolute; left: 0; bottom: 0; height: 2px; width: 100%; background: var(--rojo); transform-origin: left; transform: scaleX(0); }

.cabecera__toggle { display: none; width: 38px; height: 38px; align-items: center; justify-content: center; }
.cabecera__toggle svg { width: 20px; height: 20px; }

/* ==========================================================================
   5. PORTADA
   ========================================================================== */
.portada { padding: calc(var(--cab) + 3.5rem) 0 0; }

.portada__meta { display: flex; justify-content: space-between; align-items: baseline; gap: 2rem; padding-bottom: 1.25rem; border-bottom: 2px solid var(--negro); }
.portada__meta span { font-size: .86rem; font-weight: 500; letter-spacing: .1em; text-transform: uppercase; color: var(--gris); }

.portada__titular { padding: 3.5rem 0 2.5rem; }
.portada__titulo { font-size: clamp(3rem, 8.4vw, 7.4rem); line-height: .92; letter-spacing: -.01em; max-width: 15ch; }
.portada__titulo em { font-style: italic; }

/* Marcador rojo: una banda que se pinta detrás de la palabra al cargar.
   Es el gesto de rotulador sobre la página impresa, resuelto con
   background-size animado en lugar de un elemento extra. */
.marca-roja {
  background-image: linear-gradient(var(--rojo), var(--rojo));
  background-repeat: no-repeat; background-position: 0 88%;
  background-size: 0% 8px;
  animation: pinta 1.1s cubic-bezier(.16,1,.3,1) .5s forwards;
  padding-bottom: .05em;
}
@keyframes pinta { to { background-size: 100% 8px; } }

.portada__pie { display: grid; grid-template-columns: minmax(0,1.15fr) minmax(0,.85fr); gap: 5rem; padding-bottom: 5rem; align-items: start; }
.portada__bajada { font-size: 1.35rem; line-height: 1.6; color: var(--gris); max-width: 46ch; }
.portada__bajada strong { color: var(--negro); font-weight: 500; }

/* Tres cifras del mes, en fila, con filete superior grueso */
/* Cifras del mes: la coordinación pidió cifras más grandes y legibles */
.cifras-mes { display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem; margin-top: 3rem; }
.cifras-mes div { border-top: 2px solid var(--negro); padding-top: 1rem; }
.cifras-mes span { display: block; font-size: .95rem; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; color: var(--tenue); }
.cifras-mes b { display: block; margin-top: .6rem; font-size: clamp(2.2rem, 3.2vw, 2.9rem); line-height: 1; font-family: var(--serif); font-weight: 700; letter-spacing: -.03em; font-variant-numeric: tabular-nums; }
.cifras-mes i { font-style: normal; font-size: 1.05rem; color: var(--gris); margin-left: .4rem; font-weight: 500; letter-spacing: 0; }

/* Sumario: filas que se desplazan y se subrayan de rojo */
.sumario li a {
  display: grid; grid-template-columns: 2.4rem 1fr auto; align-items: baseline; gap: 1rem;
  padding: .85rem .75rem .85rem 0; border-bottom: 1px solid var(--linea);
  font-size: 1.13rem; color: var(--gris); position: relative;
  transition: color var(--media), padding var(--media);
}
.sumario li:first-child a { border-top: 1px solid var(--linea); }
.sumario li a::after { content: ''; position: absolute; left: 0; bottom: -1px; width: 0; height: 1px; background: var(--rojo); transition: width var(--media); }
.sumario li a .n { font-size: .84rem; font-weight: 600; color: var(--tenue); transition: color var(--media); }
.sumario li a .cap { font-size: .79rem; font-weight: 600; letter-spacing: .08em; color: var(--tenue); }
.sumario li a:hover { color: var(--negro); padding-left: .6rem; }
.sumario li a:hover::after { width: 100%; }
.sumario li a:hover .n { color: var(--rojo); }

/* ==========================================================================
   6. CAPÍTULOS — el numeral se fija mientras el capítulo avanza
   ========================================================================== */
.capitulo { padding: 6rem 0; }
.capitulo--papel { background: var(--papel); }
.capitulo--negro { background: var(--negro); color: rgba(255,255,255,.72); }

.cap-rejilla { display: grid; grid-template-columns: 120px minmax(0,1fr); gap: 3.5rem; }
.cap-rail { position: sticky; top: calc(var(--cab-min) + 3rem); align-self: start; }
.cap-rail__n { font-family: var(--serif); font-weight: 700; font-size: 5rem; line-height: .8; letter-spacing: -.01em; }
.cap-rail__l { margin-top: 1.1rem; padding-top: 1.1rem; position: relative; font-family: var(--serif); font-weight: 600; font-size: 1.14rem; letter-spacing: .18em; text-transform: uppercase; color: var(--negro); }
.cap-rail__l::before { content: ''; position: absolute; left: 0; top: 0; width: 70px; height: 8px; background-image: radial-gradient(circle, var(--rojo) 3px, transparent 3.5px); background-size: 14px 8px; background-repeat: repeat-x; }

.apertura { padding-bottom: 5rem; }
.apertura__tit { font-size: clamp(2rem, 4.2vw, 3.6rem); max-width: 16ch; }
.apertura__tit em { font-style: italic; }
.apertura__txt { margin-top: 1.5rem; font-size: 1.22rem; color: var(--gris); max-width: 52ch; }

.bloque { padding-bottom: 6rem; }
.bloque:last-child { padding-bottom: 0; }

/* ==========================================================================
   7. CAPÍTULO I · ESENCIAL
   ========================================================================== */

/* 7a. Noticia — la lámina se fija mientras el texto avanza */
.noticia { display: grid; grid-template-columns: minmax(0,1fr) minmax(0,1fr); gap: 4rem; align-items: start; }
.noticia__media { position: sticky; top: calc(var(--cab-min) + 3rem); }
.lamina { position: relative; aspect-ratio: 4/5; overflow: hidden; background: var(--negro); }
.lamina img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
/* Lámina provisional: bloque negro con una curva roja de datos */
.lamina .fondo { position: absolute; inset: 0; background: linear-gradient(160deg, #1c1c1c 0%, #111 100%); transition: transform 2s var(--lenta); }
.noticia:hover .fondo { transform: scale(1.04); }
.lamina .curva { position: absolute; inset: 0; width: 100%; height: 100%; }
.lamina .curva path { fill: none; stroke: var(--rojo); stroke-width: 2.5; stroke-dasharray: 900; stroke-dashoffset: 900; transition: stroke-dashoffset 2.2s var(--lenta) .2s; }
.noticia.visible .curva path { stroke-dashoffset: 0; }
.lamina .malla { position: absolute; inset: 0; background-image: radial-gradient(circle, rgba(255,255,255,.14) 1.2px, transparent 1.7px); background-size: 22px 22px; }
.lamina__tag { position: absolute; left: 1.5rem; top: 1.5rem; background: var(--rojo); color: var(--blanco); font-size: .77rem; font-weight: 600; letter-spacing: .14em; text-transform: uppercase; padding: .5rem .85rem; }
.lamina__pie { position: absolute; left: 1.5rem; right: 1.5rem; bottom: 1.5rem; color: rgba(255,255,255,.55); font-size: .84rem; letter-spacing: .04em; }

.noticia__tit { font-size: clamp(1.9rem, 3.4vw, 3rem); margin: 1.25rem 0 1.75rem; }
.noticia__tit em { font-style: italic; }
.noticia__cuerpo { color: var(--gris); font-size: 1.2rem; line-height: 1.75; }
.noticia__cuerpo .capitular::first-letter {
  float: left; font-family: var(--serif); font-weight: 700; font-size: 4.6rem; line-height: .74;
  padding: .3rem .55rem 0 0; color: var(--negro);
}
/* Cita destacada: filete rojo de 2 px y tipo grande */
.destacada { margin: 2rem 0; padding-left: 1.75rem; border-left: 3px solid var(--rojo); font-family: var(--serif); font-weight: 500; font-size: 1.75rem; line-height: 1.15; color: var(--negro); }
.noticia__pie { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1.25rem; margin-top: 2.25rem; padding-top: 1.25rem; border-top: 1px solid var(--linea); }
.noticia__firma { font-size: .94rem; color: var(--tenue); }
.noticia__firma b { font-weight: 600; color: var(--negro); }

/* 7b. Macro — tres cifras grandes separadas por filetes */
.macro { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0; }
.ind { padding: 0 2.5rem; border-left: 1px solid var(--linea); }
.ind:first-child { padding-left: 0; border-left: 0; }
.ind:last-child { padding-right: 0; }
.ind__rot { font-size: .82rem; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; color: var(--gris); }
.ind__val { display: flex; align-items: baseline; gap: .1rem; margin: 1rem 0 .85rem; font-family: var(--serif); font-weight: 700; font-size: clamp(2.6rem, 4.4vw, 3.6rem); line-height: 1; letter-spacing: -.01em; font-variant-numeric: tabular-nums; }
.ind__val small { font-size: .32em; font-weight: 500; color: var(--tenue); }
.ind__delta { display: inline-flex; align-items: center; gap: .4rem; font-size: .91rem; color: var(--gris); }
.ind__delta svg { width: 9px; height: 9px; flex: none; }
.ind__delta.favorable svg { color: #2E7D4F; } .ind__delta.adverso svg { color: var(--rojo); }
.ind__nota { margin-top: 1.1rem; font-size: 1.01rem; color: var(--tenue); line-height: 1.65; }

/* 7c. Tendencias — lista de filas que reaccionan al cursor */
.tendencias { border-top: 1px solid var(--linea); }
.tend {
  position: relative; display: grid; grid-template-columns: 3.5rem minmax(0,.9fr) minmax(0,1.1fr) 2rem;
  gap: 2rem; align-items: start; padding: 2.5rem 1rem 2.5rem 0;
  border-bottom: 1px solid var(--linea); transition: padding var(--media);
}
/* Filete rojo que crece bajo la fila */
.tend::after { content: ''; position: absolute; left: 0; bottom: -1px; width: 0; height: 1px; background: var(--rojo); transition: width var(--lenta); }
.tend:hover { padding-left: 1.25rem; }
.tend:hover::after { width: 100%; }
.tend__n { font-size: .94rem; font-weight: 600; color: var(--tenue); transition: color var(--media); padding-top: .35rem; }
.tend:hover .tend__n { color: var(--rojo); }
.tend__cat { font-size: .79rem; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; color: var(--tenue); margin-bottom: .6rem; }
.tend__tit { font-size: clamp(1.3rem, 2.1vw, 1.75rem); }
.tend__txt { color: var(--gris); font-size: 1.1rem; }
.tend__ir { justify-self: end; align-self: center; color: var(--tenue); transition: color var(--media), transform var(--media); }
.tend:hover .tend__ir { color: var(--rojo); transform: translateX(5px); }
.tend__ir svg { width: 16px; height: 16px; }

/* ==========================================================================
   8. CAPÍTULO II · DATOS
   ========================================================================== */
.grafico { display: grid; grid-template-columns: minmax(0,.8fr) minmax(0,1.2fr); gap: 4rem; align-items: center; }
.grafico__tit { font-size: clamp(1.8rem, 3vw, 2.6rem); margin-bottom: 1.5rem; }
.grafico__tit em { font-style: italic; }
.grafico__txt { color: var(--gris); }
.grafico__cifra { margin-top: 2.25rem; padding-top: 1.5rem; border-top: 2px solid var(--negro); }
.grafico__cifra > b { display: block; font-family: var(--serif); font-weight: 700; font-size: clamp(2.6rem, 4vw, 3.4rem); line-height: 1; letter-spacing: -.045em; }
.grafico__cifra > span { display: block; margin-top: .7rem; font-size: 1.03rem; color: var(--gris); max-width: 30ch; }

.lienzo { position: relative; background: var(--papel); padding: 2.25rem 2rem 1.5rem; }
.capitulo--papel .lienzo { background: var(--blanco); }
.lienzo__cab { display: flex; justify-content: space-between; align-items: baseline; gap: 1rem; padding-bottom: 1.5rem; }
.lienzo__tit { font-size: .98rem; font-weight: 600; letter-spacing: .02em; }
.lienzo__sub { font-size: .86rem; color: var(--tenue); }
.lienzo svg { width: 100%; height: auto; display: block; overflow: visible; }
.lienzo .rejilla line { stroke: rgba(17,17,17,.1); stroke-width: 1; }
.lienzo .eje text { font: 600 13px var(--sans); fill: var(--tenue); }
.lienzo .barra .forma { fill: #CFCFCA; transition: fill var(--corta); }
.lienzo .barra.actual .forma { fill: var(--rojo); }
.lienzo .barra:hover .forma { fill: var(--negro); }
.lienzo .golpe { fill: transparent; }
.lienzo .valor { font: 700 13px var(--sans); fill: var(--negro); }
.lienzo .barra-crece { transform-box: fill-box; transform-origin: bottom; transform: scaleY(0); transition: transform 1.2s var(--lenta); }
.grafico.visible .barra-crece { transform: scaleY(1); }
.tooltip { position: absolute; pointer-events: none; opacity: 0; transform: translate(-50%,-112%); background: var(--negro); color: var(--blanco); padding: .5rem .8rem; font-size: .86rem; line-height: 1.45; white-space: nowrap; transition: opacity var(--corta); }
.tooltip b { display: block; font-size: 1.14rem; font-weight: 600; }
.lienzo__pie { display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid var(--linea); }
.lienzo details summary { font-size: .84rem; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; color: var(--gris); cursor: pointer; }
.lienzo table { margin-top: .85rem; border-collapse: collapse; width: 100%; font-size: .94rem; }
.lienzo th, .lienzo td { text-align: left; padding: .35rem .5rem; border-bottom: 1px solid var(--linea); font-weight: 400; }
.lienzo th { color: var(--tenue); font-size: .82rem; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; }
.lienzo td:last-child, .lienzo th:last-child { text-align: right; font-variant-numeric: tabular-nums; }

/* 8b. Cifras flash */
.flash { display: grid; grid-template-columns: repeat(4, 1fr); gap: 2.5rem; }
.flash__it { border-top: 2px solid var(--negro); padding-top: 1.5rem; }
.flash__it:nth-child(2) { border-top-color: var(--rojo); }
.flash__n { font-family: var(--serif); font-weight: 700; font-size: clamp(2.4rem, 4vw, 3.4rem); line-height: 1; letter-spacing: -.01em; font-variant-numeric: tabular-nums; }
.flash__n > sup { top: -.05em; font-size: .28em; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; color: var(--rojo); margin-left: .25rem; }
.flash__t { margin-top: 1rem; font-size: 1.06rem; color: var(--gris); }

/* ==========================================================================
   9. CAPÍTULO III · PRÁCTICO
   ========================================================================== */

/* 9a. Herramienta */
.producto { display: grid; grid-template-columns: minmax(0,1fr) minmax(0,1fr); border: 1px solid var(--linea); background: var(--blanco); }
.producto__escena { position: relative; min-height: 460px; background: var(--papel); display: flex; align-items: center; justify-content: center; overflow: hidden; border-right: 1px solid var(--linea); }
.producto__tag { position: absolute; left: 1.5rem; top: 1.5rem; }
/* Tablero kanban moderno: tarjetas blancas con sombra corta */
.kanban { display: flex; gap: 12px; }
.kanban__col { width: 104px; display: flex; flex-direction: column; gap: 9px; }
.kanban__col b { font-size: .67rem; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; color: var(--tenue); margin-bottom: 4px; }
.kanban__t { height: 46px; background: var(--blanco); box-shadow: 0 1px 2px rgba(17,17,17,.07); border-top: 2px solid var(--linea); animation: flota 7s ease-in-out infinite; }
.kanban__t:nth-child(3) { animation-delay: -2.4s; height: 62px; }
.kanban__t:nth-child(4) { animation-delay: -4.8s; }
.kanban__t--rojo { border-top-color: var(--rojo); }
@keyframes flota { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-5px); } }
@media (prefers-reduced-motion: reduce) { .kanban__t { animation: none; } }

.producto__info { padding: 3rem; display: flex; flex-direction: column; justify-content: center; }
.producto__nom { font-size: clamp(2.2rem, 3.6vw, 3.2rem); margin: 1rem 0 .4rem; }
.producto__cat { font-family: var(--serif); font-weight: 500; font-style: italic; font-size: 1.25rem; color: var(--gris); margin-bottom: 1.5rem; }
.producto__txt { color: var(--gris); }
.atributos { display: grid; grid-template-columns: repeat(3,1fr); border-top: 1px solid var(--linea); margin: 2rem 0; }
.atributos div { padding: 1.15rem 0; }
.atributos div + div { padding-left: 1.25rem; border-left: 1px solid var(--linea); }
.atributos dt { font-size: .74rem; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; color: var(--tenue); }
.atributos dd { margin: .45rem 0 0; font-size: 1.05rem; font-weight: 500; }
/* Barras de intensidad */
.intensidad { display: grid; gap: .7rem; margin-bottom: 2rem; }
.intensidad__f { display: grid; grid-template-columns: 8rem 1fr; align-items: center; gap: 1.25rem; font-size: .96rem; color: var(--gris); }
.intensidad__p { display: flex; gap: 4px; }
.intensidad__p i { width: 100%; max-width: 30px; height: 3px; background: var(--linea); }
.intensidad__p i.on { background: var(--negro); }

/* 9b. Lección — el único bloque rojo de la pieza */
.leccion { background: var(--rojo); color: rgba(255,255,255,.82); position: relative; overflow: hidden; }
/* Lunares blancos en la mitad derecha, desvaneciéndose hacia el texto */
.leccion::before {
  content: ''; position: absolute; inset: 0;
  background-image: radial-gradient(circle, rgba(255,255,255,.28) 3px, transparent 3.5px);
  background-size: 22px 22px;
  /* Sólo la esquina inferior derecha, que queda libre bajo la lista */
  -webkit-mask-image: radial-gradient(ellipse 38% 55% at 100% 100%, #000 0%, rgba(0,0,0,.5) 45%, transparent 100%);
          mask-image: radial-gradient(ellipse 38% 55% at 100% 100%, #000 0%, rgba(0,0,0,.5) 45%, transparent 100%);
  pointer-events: none;
}
.leccion__caja { position: relative; }
.leccion .tag::before { background: var(--blanco); }
.leccion__caja { display: grid; grid-template-columns: minmax(0,1.3fr) minmax(0,1fr); gap: 4rem; padding: 5rem 0; align-items: start; }
.leccion__cita { font-family: var(--serif); font-weight: 600; font-size: clamp(2rem, 3.8vw, 3.4rem); line-height: 1.05; color: var(--blanco); margin: 0; }
.leccion__cita em { font-style: italic; }
.leccion__firma { margin-top: 2rem; font-size: .84rem; font-weight: 600; letter-spacing: .12em; text-transform: uppercase; color: rgba(255,255,255,.6); }
.pasos li { display: grid; grid-template-columns: 2rem 1fr; gap: .6rem; padding: 1.1rem 0; border-bottom: 1px solid rgba(255,255,255,.25); font-size: 1.1rem; }
.pasos li:first-child { border-top: 1px solid rgba(255,255,255,.25); }
.pasos li span { font-weight: 600; color: rgba(255,255,255,.6); font-size: .94rem; padding-top: .15rem; }

/* 9c. Preguntas y respuestas */
.qa { display: grid; grid-template-columns: minmax(0,.7fr) minmax(0,1.3fr); gap: 4rem; }
.qa__tit { font-size: clamp(1.8rem, 3vw, 2.6rem); margin-bottom: 1.25rem; }
.qa__tit em { font-style: italic; }
.pregunta { border-bottom: 1px solid var(--linea); }
.pregunta:first-child { border-top: 2px solid var(--negro); }
.pregunta summary { list-style: none; cursor: pointer; display: grid; grid-template-columns: 1fr 26px; gap: 1.5rem; align-items: start; padding: 1.7rem 0; }
.pregunta summary::-webkit-details-marker { display: none; }
.pregunta__c { display: block; margin-bottom: .5rem; font-size: .77rem; font-weight: 600; letter-spacing: .12em; text-transform: uppercase; color: var(--rojo); }
.pregunta__q { font-family: var(--serif); font-weight: 600; font-size: clamp(1.35rem, 2.1vw, 1.7rem); line-height: 1.12; transition: color var(--media); }
.pregunta summary:hover .pregunta__q { color: var(--rojo); }
.signo { position: relative; width: 26px; height: 26px; margin-top: .5rem; }
.signo::before, .signo::after { content: ''; position: absolute; left: 50%; top: 50%; width: 14px; height: 2px; background: var(--negro); transform: translate(-50%,-50%); transition: transform var(--media), background var(--media); }
.signo::after { transform: translate(-50%,-50%) rotate(90deg); }
.pregunta[open] .signo::after { transform: translate(-50%,-50%) rotate(0deg); }
.pregunta[open] .signo::before, .pregunta[open] .signo::after { background: var(--rojo); }
.pregunta__r { padding: 0 3rem 1.75rem 0; color: var(--gris); animation: abre var(--lenta); }
.pregunta__r .experto { display: block; margin-top: 1rem; font-size: .89rem; color: var(--tenue); }
@keyframes abre { from { opacity: 0; transform: translateY(-8px); } }

/* ==========================================================================
   10. CAPÍTULO IV · COMUNIDAD
   ========================================================================== */
.lecturas__cab { display: flex; justify-content: space-between; align-items: flex-end; gap: 2rem; }
.flechas { display: flex; gap: .5rem; }
.flechas button { width: 42px; height: 42px; border: 1px solid var(--linea); display: flex; align-items: center; justify-content: center; color: var(--negro); transition: background var(--media), border-color var(--media), color var(--media); }
.flechas button:hover { background: var(--negro); border-color: var(--negro); color: var(--blanco); }
.flechas svg { width: 15px; height: 15px; }
.carrusel { display: grid; grid-auto-flow: column; grid-auto-columns: minmax(220px, 1fr); gap: 1.5rem; overflow-x: auto; scroll-snap-type: x mandatory; scrollbar-width: none; padding-bottom: .5rem; }
.carrusel::-webkit-scrollbar { display: none; }
.libro { scroll-snap-align: start; }
/* Cubiertas: sólo blanco, negro y rojo */
.libro__cub {
  position: relative; aspect-ratio: 3/4; padding: 1.5rem 1.35rem; overflow: hidden;
  display: flex; flex-direction: column; justify-content: space-between;
  background: var(--c, var(--papel)); color: var(--t, var(--negro));
  transition: transform var(--lenta);
}
.libro:hover .libro__cub { transform: translateY(-8px); }
.libro__cub--lunares { background-image: radial-gradient(circle, rgba(255,255,255,.9) 3px, transparent 3.5px); background-size: 20px 20px; background-position: 10px 10px; }
.libro__cub--lunares { background-image: radial-gradient(circle, rgba(255,255,255,.85) 2.6px, transparent 3.1px); }
.libro__cub--lunares small, .libro__cub--lunares h4 { background: var(--rojo); box-shadow: 0 0 0 .5rem var(--rojo); align-self: flex-start; }
.libro__cub.libro__cub--lunares small { opacity: 1; color: rgba(255,255,255,.8); }
.libro__cub small { font-size: .72rem; font-weight: 600; letter-spacing: .14em; text-transform: uppercase; opacity: .6; }
.libro__cub h4 { font-size: 1.35rem; line-height: 1.1; }
.libro__meta { padding-top: 1.1rem; }
.libro__meta b { display: block; font-weight: 600; font-size: 1.14rem; }
.libro__meta span { font-size: .94rem; color: var(--tenue); }
.libro__meta p { margin-top: .5rem; font-size: 1.01rem; color: var(--gris); }

/* 10b. Bolsa de trabajo */
.bolsa { display: grid; grid-template-columns: minmax(0,.7fr) minmax(0,1.3fr); gap: 4rem; }
.bolsa__tit { font-size: clamp(1.8rem, 3vw, 2.6rem); margin-bottom: 1.25rem; }
.bolsa__tit em { font-style: italic; }
.vacante {
  position: relative; display: grid; grid-template-columns: 1fr auto 28px; gap: 1.5rem; align-items: center;
  padding: 1.6rem .75rem 1.6rem 0; border-bottom: 1px solid var(--linea); transition: padding var(--media);
}
.vacante:first-child { border-top: 2px solid var(--negro); }
.vacante::after { content: ''; position: absolute; left: 0; bottom: -1px; width: 0; height: 1px; background: var(--rojo); transition: width var(--lenta); }
.vacante:hover { padding-left: 1.1rem; }
.vacante:hover::after { width: 100%; }
.vacante__p { font-family: var(--serif); font-weight: 600; font-size: 1.55rem; line-height: 1.1; }
.vacante__p small { display: block; margin-top: .4rem; font-family: var(--sans); font-size: .96rem; color: var(--tenue); letter-spacing: 0; }
.vacante__tags { display: flex; gap: .4rem; flex-wrap: wrap; justify-content: flex-end; }
.vacante__tags span { font-size: .74rem; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; color: var(--gris); background: var(--papel); padding: .35rem .65rem; }
.capitulo--papel .vacante__tags span { background: var(--blanco); }
.vacante__ir { display: flex; align-items: center; justify-content: center; color: var(--tenue); transition: color var(--media), transform var(--media); }
.vacante:hover .vacante__ir { color: var(--rojo); transform: translateX(5px); }
.vacante__ir svg { width: 16px; height: 16px; }

/* 10c. Agenda */
.agenda { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; }
.evento { position: relative; border-top: 2px solid var(--negro); padding-top: 1.5rem; display: flex; flex-direction: column; gap: 1rem; min-height: 270px; transition: border-color var(--media); }
.evento:hover { border-top-color: var(--rojo); }
.evento__fecha { display: flex; align-items: baseline; gap: .5rem; }
.evento__d { font-family: var(--serif); font-weight: 700; font-size: 2.8rem; line-height: 1; letter-spacing: -.01em; font-variant-numeric: tabular-nums; transition: color var(--media); }
.evento:hover .evento__d { color: var(--rojo); }
.evento__m { font-size: .82rem; font-weight: 600; letter-spacing: .12em; text-transform: uppercase; color: var(--tenue); }
.evento__tipo { font-size: .77rem; font-weight: 600; letter-spacing: .12em; text-transform: uppercase; color: var(--gris); }
.evento__tit { font-family: var(--serif); font-weight: 600; font-size: 1.4rem; line-height: 1.1; }
.evento__lugar { margin-top: auto; padding-top: 1rem; border-top: 1px solid var(--linea); font-size: .96rem; color: var(--tenue); display: flex; align-items: center; gap: .45rem; }
.evento__lugar svg { width: 13px; height: 13px; flex: none; }

/* ==========================================================================
   11. CIERRE Y PIE
   ========================================================================== */

/* Mismo pie que el sitio principal (includes/footer.php y su CSS en
   css/styles.css). Se replica aquí porque el newsletter es independiente y
   no carga Bootstrap: la rejilla col-lg-5 / 4 / 3 se resuelve con grid. */
.footer-lux { background: #0A0A0A; color: rgba(255,255,255,.64); padding: 1.75rem 0 0; font-size: .82rem; line-height: 1.6; }
.footer-lux__rejilla { display: grid; grid-template-columns: 5fr 4fr 3fr; gap: 3rem; align-items: start; padding-bottom: 1.25rem; }
.footer-lux p { margin: 0 0 .75rem; text-align: justify; hyphens: auto; -webkit-hyphens: auto; }
.footer-lux p.footer-lux__title { text-align: left; }
.footer-lux__title { font-family: var(--sans); font-size: .62rem; font-weight: 600; letter-spacing: .24em; text-transform: uppercase; color: #FFFFFF; margin-bottom: .5rem !important; }
.footer-lux a { color: rgba(255,255,255,.64); }
.footer-lux a:hover { color: var(--rojo); }
.footer-lux__social { display: flex; }
.footer-lux__social a { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border: 1px solid rgba(255,255,255,.22); margin-right: .4rem; color: #FFFFFF; transition: border-color var(--media), color var(--media); }
.footer-lux__social a:hover { border-color: var(--rojo); color: var(--rojo); }
.footer-lux__social svg { width: 15px; height: 15px; fill: currentColor; }
.footer-lux__marcas { display: flex; flex-wrap: nowrap; align-items: center; justify-content: flex-end; gap: 1rem; }
.logo-footer { height: 92px; width: auto; object-fit: contain; filter: brightness(0) invert(1); opacity: .9; }
.footer-lux__legal { background: #F5F5F5; padding: 1.5rem 0; font-size: .72rem; color: #5A5C60; line-height: 1.7; }
.footer-lux__legal p { margin: 0; }
.footer-lux__legal a { color: #0A0A0A; text-decoration: underline; text-underline-offset: 3px; }
.footer-lux__legal a:hover { color: var(--rojo); }

/* ==========================================================================
   12. RESPONSIVO
   ========================================================================== */
/* Encabezado: con los logos a 108 px, el menú y el botón de suscripción,
   la fila no cabe por debajo de ~1320 px */
@media (max-width: 1320px) {
  :root { --logo: 84px; --cab: 116px; }
  .secciones a { padding: .5rem .6rem; font-size: 1.15rem; }
  .secciones a::after { left: .6rem; right: .6rem; }
}
@media (max-width: 1100px) {
  :root { --logo: 64px; --cab: 96px; }
  .secciones a { padding: .45rem .45rem; font-size: 1.1rem; }
  .secciones a::after { left: .45rem; right: .45rem; }
  .cabecera__sus { padding: .6rem 1rem; font-size: 1.05rem; }
  .cabecera__volver { padding: .52rem .9rem; font-size: 1.05rem; }
  .cabecera__volver svg { width: 15px; height: 15px; }
  .agenda { grid-template-columns: repeat(2, 1fr); gap: 2.5rem 1.5rem; }
  .flash { grid-template-columns: repeat(2, 1fr); }

}
@media (max-width: 991.98px) {
  :root { --cab: 92px; --cab-min: 72px; --logo: 64px; --logo-min: 48px; }
  .trama { width: 46%; top: 0; opacity: .5; }
  .mancheta__nombre { letter-spacing: .3em; padding-left: .3em; }
  .envoltura { padding: 0 1.5rem; }
  .secciones { display: none; }
  .cabecera__nom { display: none; }
  .cabecera__toggle { display: inline-flex; }
  .cabecera.abierta .secciones {
    display: flex; flex-direction: column; gap: .25rem;
    position: absolute; top: 100%; left: 0; right: 0;
    background: var(--blanco); padding: 1rem 1.5rem 1.5rem; border-bottom: 1px solid var(--linea);
  }
  .cabecera.abierta .secciones a { font-size: 1.35rem; padding: .6rem 0; }
  .cabecera.abierta .secciones a::after { left: 0; right: auto; width: 2.5rem; }
  /* Salir del newsletter no es un capítulo más: va al final y con una línea
     que lo separa de los cuatro capítulos y del botón de suscripción. */
  .cabecera.abierta .secciones a.secciones__volver { display: inline-flex; margin-top: .9rem; padding-top: 1.1rem; border-top: 1px solid var(--linea); }
  .cabecera.abierta .secciones a.secciones__volver::after { display: none; }

  .portada { padding-top: calc(var(--cab) + 3rem); }
  .portada__titular { padding: 2.5rem 0 2rem; }
  .portada__pie { grid-template-columns: 1fr; gap: 3rem; padding-bottom: 3.5rem; }
  .cifras-mes { gap: 1.25rem; }

  .capitulo { padding: 4rem 0; }
  .cap-rejilla { grid-template-columns: 1fr; gap: 2rem; }
  .cap-rail { position: static; display: flex; align-items: baseline; gap: 1.25rem; }
  .cap-rail__n { font-size: 2.6rem; }
  .cap-rail__l { margin: 0; padding: 0 0 0 1.25rem; border-top: 0; border-left: 2px solid var(--rojo); }
  .apertura { padding-bottom: 3rem; }
  .bloque { padding-bottom: 4rem; }

  .noticia, .grafico, .qa, .bolsa, .producto, .leccion__caja { grid-template-columns: 1fr; gap: 2.5rem; }
  .noticia__media { position: static; }
  .lamina { aspect-ratio: 4/3; }
  .macro { grid-template-columns: 1fr; gap: 2rem; }
  .ind, .ind:first-child { padding: 0 0 2rem; border-left: 0; border-bottom: 1px solid var(--linea); }
  .ind:last-child { padding-bottom: 0; border-bottom: 0; }
  .tend { grid-template-columns: 2.5rem 1fr; gap: .75rem 1.25rem; }
  .tend__txt { grid-column: 2; }
  .tend__ir { display: none; }
  .producto__escena { min-height: 320px; border-right: 0; border-bottom: 1px solid var(--linea); }
  .producto__info { padding: 2.25rem 1.5rem; }
  .leccion__caja { padding: 3.5rem 0; }
  .footer-lux__rejilla { grid-template-columns: 1fr; gap: 1.5rem; }
  .footer-lux__marcas { justify-content: flex-start; margin-top: .5rem; }
}
@media (max-width: 575.98px) {
  .flash { grid-template-columns: 1fr; gap: 2rem; }
  .agenda { grid-template-columns: 1fr; }
  .evento { min-height: 0; }
  .cifras-mes { grid-template-columns: 1fr; gap: 1rem; }
  .cifras-mes div { display: flex; justify-content: space-between; align-items: baseline; gap: 1rem; padding-top: .75rem; }
  .cifras-mes b { margin-top: 0; font-size: 2.2rem; }
  .vacante { grid-template-columns: 1fr 26px; }
  .vacante__tags { grid-column: 1; grid-row: 2; justify-content: flex-start; margin-top: .75rem; }
  .vacante__ir { grid-row: 1 / span 2; grid-column: 2; }
  .atributos { grid-template-columns: 1fr; }
  .atributos div + div { padding-left: 0; border-left: 0; border-top: 1px solid var(--linea); }
  .pregunta__r { padding-right: 0; }
  .lienzo { padding: 1.5rem 1.1rem 1.25rem; }
  .lienzo__cab, .lienzo__pie { flex-direction: column; align-items: flex-start; gap: .4rem; }
  .portada__meta { flex-direction: column; align-items: flex-start; gap: .4rem; }
  .marca-roja { background-size: 0% 6px; }
  .trama { display: none; }
  :root { --cab: 72px; --cab-min: 60px; --logo: 48px; --logo-min: 40px; }
  .cabecera__marca { gap: .7rem; }

  .mancheta__nombre { font-size: 1.1rem; letter-spacing: .24em; padding-left: .24em; }
      @keyframes pinta { to { background-size: 100% 6px; } }
}

/* En los teléfonos más angostos (320 px) los dos logos y el botón del menú ya
   no caben entre los márgenes: se recortan los logos y el margen lateral. */
@media (max-width: 360px) {
  :root { --logo: 42px; --logo-min: 36px; }
  .envoltura { padding: 0 1.15rem; }
  .cabecera__marca { gap: .55rem; }
}

/* ==========================================================================
   13. SUSCRIPCIÓN — botones y ventana emergente
   ========================================================================== */
.boton-sus {
  display: inline-flex; align-items: center; gap: .6rem; flex: none;
  padding: .8rem 1.5rem; border: 2px solid var(--rojo); background: var(--rojo); color: var(--blanco);
  font-family: var(--serif); font-size: 1.2rem; font-weight: 600; letter-spacing: .01em; line-height: 1;
  cursor: pointer; transition: background var(--media), border-color var(--media), color var(--media), transform var(--media);
}
.boton-sus svg { width: 18px; height: 18px; flex: none; }
.boton-sus:hover { background: var(--negro); border-color: var(--negro); }
.boton-sus:active { transform: translateY(1px); }
.boton-sus[disabled] { opacity: .6; cursor: wait; }
.boton-sus--linea { background: transparent; color: var(--negro); border-color: var(--negro); }
.boton-sus--linea:hover { background: var(--negro); color: var(--blanco); }
.cabecera__sus { padding: .7rem 1.25rem; font-size: 1.15rem; }
.cabecera--posada .cabecera__sus { padding: .55rem 1.1rem; }
.secciones__sus { display: none; }

/* ── Regreso a la revista ──
   El encabezado ya va lleno con los dos logos, los cuatro capítulos y el botón
   de suscripción: un botón más desbordaba la fila en escritorio. La vuelta se
   reparte en tres puntos según dónde esté el lector: la portada (al entrar),
   el menú desplegable (en pantalla chica, a un toque desde cualquier punto)
   y el pie (al terminar la lectura). */
.portada__volver {
  display: inline-flex; align-items: center; justify-content: center; gap: .6rem;
  width: 100%; margin-top: 2rem; padding: .95rem 1.5rem;
  border: 2px solid var(--negro); background: transparent; color: var(--negro);
  font-family: var(--serif); font-size: 1.25rem; font-weight: 600; letter-spacing: .01em; line-height: 1;
  transition: background var(--media), color var(--media);
}
.portada__volver svg { width: 17px; height: 17px; flex: none; transition: transform var(--media); }
.portada__volver:hover { background: var(--negro); color: var(--blanco); }
.portada__volver:hover svg { transform: translateX(-3px); }

/* Desde 600 px el botón deja de ocupar todo el ancho: al lado de un texto de
   lectura, un botón de borde a borde se lee como una barra, no como un botón. */
@media (min-width: 600px) {
  .portada__volver { width: auto; padding: .95rem 1.75rem; }
}

/* La misma opción, con su nombre completo, dentro del menú desplegable */
.secciones a.secciones__volver { display: none; align-items: center; gap: .6rem; font-family: var(--serif); font-size: 1.35rem; font-weight: 600; color: var(--negro); padding: .6rem 0; }
.secciones a.secciones__volver svg { width: 16px; height: 16px; }
.secciones a.secciones__volver:hover { color: var(--rojo); }

/* Y una tercera en el pie, para quien llega al final de la lectura */
.footer-lux__volver { display: inline-flex; align-items: center; gap: .55rem; margin-bottom: 1.1rem; font-family: var(--serif); font-size: 1.15rem; font-weight: 600; letter-spacing: .01em; color: #FFFFFF !important; }
.footer-lux__volver svg { width: 15px; height: 15px; transition: transform var(--media); }
.footer-lux__volver:hover { color: var(--rojo) !important; }
.footer-lux__volver:hover svg { transform: translateX(-3px); }

/* ── Ventana emergente ──
   <dialog> nativo: atrapa el foco, se cierra con Esc y deja el resto de la
   página inerte sin código extra. */
.modal { padding: 0; border: 0; background: transparent; max-width: min(980px, calc(100vw - 2rem)); width: 100%; max-height: calc(100vh - 2rem); overflow: visible; color: var(--negro); }
.modal::backdrop { background: rgba(29,29,27,.72); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); }
.modal[open] { animation: modal-entra .5s cubic-bezier(.16,1,.3,1); }
.modal[open]::backdrop { animation: modal-fondo .4s ease; }
@keyframes modal-entra { from { opacity: 0; transform: translateY(24px) scale(.98); } }
@keyframes modal-fondo { from { opacity: 0; } }
@media (prefers-reduced-motion: reduce) { .modal[open], .modal[open]::backdrop { animation: none; } }

.modal__caja { position: relative; display: grid; grid-template-columns: minmax(0,.85fr) minmax(0,1.15fr); background: var(--blanco); max-height: calc(100vh - 2rem); overflow: auto; box-shadow: 0 40px 80px -30px rgba(0,0,0,.5); }
.modal__cerrar { position: absolute; top: 1rem; right: 1rem; z-index: 2; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; color: var(--negro); transition: color var(--media), transform var(--media); }
.modal__cerrar:hover { color: var(--rojo); transform: rotate(90deg); }
.modal__cerrar svg { width: 22px; height: 22px; }

/* Lado rojo con lunares: el mismo gesto del bloque de liderazgo */
.modal__lado { position: relative; overflow: hidden; background: var(--rojo); color: var(--blanco); padding: 3rem 2.5rem; display: flex; flex-direction: column; justify-content: space-between; gap: 2.5rem; }
.modal__lado::before {
  content: ''; position: absolute; inset: 0; pointer-events: none;
  background-image: radial-gradient(circle, rgba(255,255,255,.26) 3px, transparent 3.5px); background-size: 22px 22px;
  -webkit-mask-image: radial-gradient(ellipse 70% 55% at 100% 100%, #000 0%, transparent 100%);
          mask-image: radial-gradient(ellipse 70% 55% at 100% 100%, #000 0%, transparent 100%);
}
.modal__lado > * { position: relative; }
.modal__eti { display: inline-flex; align-items: center; gap: .55rem; font-size: .85rem; font-weight: 600; letter-spacing: .14em; text-transform: uppercase; color: rgba(255,255,255,.85); margin: 0 0 1.25rem; }
.modal__eti::before { content: ''; width: 8px; height: 8px; border-radius: 50%; background: var(--blanco); }
.modal__tit { font-size: clamp(2.2rem, 4vw, 3.1rem); line-height: .98; color: var(--blanco); margin: 0; }
.modal__tit em { font-style: italic; }
.modal__puntos li { position: relative; padding: .8rem 0 .8rem 1.6rem; border-top: 1px solid rgba(255,255,255,.3); font-size: 1.05rem; line-height: 1.45; color: rgba(255,255,255,.95); }
.modal__puntos li::before { content: ''; position: absolute; left: 0; top: 1.3rem; width: 8px; height: 8px; border-radius: 50%; background: var(--blanco); }

.modal__cuerpo { padding: 3rem 2.75rem 2.5rem; }
.modal__intro { font-size: 1.1rem; color: var(--gris); margin: 0 0 1.5rem; padding-right: 2.5rem; }
.campos { display: grid; grid-template-columns: 1fr 1fr; gap: 1.35rem 1.5rem; }
.campo--ancho { grid-column: 1 / -1; }
.campo label { display: block; font-size: .82rem; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; color: var(--negro); margin-bottom: .35rem; }
.campo input, .campo select {
  width: 100%; font: 500 1.15rem var(--sans); color: var(--negro); background: transparent;
  border: 0; border-bottom: 2px solid var(--linea); border-radius: 0; padding: .55rem 0; outline: 0;
  transition: border-color var(--media);
}
.campo input:hover, .campo select:hover { border-bottom-color: #BDBDB8; }
.campo input:focus, .campo select:focus { border-bottom-color: var(--negro); }
.campo input::placeholder { color: #9A9A95; }
.campo.error input, .campo.error select { border-bottom-color: var(--rojo); }
.campo__error { display: block; min-height: 0; font-size: .9rem; font-weight: 500; color: var(--rojo); margin-top: .35rem; }
.campo__error:empty { display: none; }
.campo__ayuda { display: block; font-size: .88rem; color: var(--tenue); margin-top: .35rem; }

.acepta { display: flex; gap: .7rem; align-items: flex-start; margin-top: 1.5rem; font-size: 1rem; line-height: 1.45; color: var(--gris); cursor: pointer; }
.acepta input { width: 20px; height: 20px; margin: .1rem 0 0; flex: none; accent-color: var(--rojo); }
.acepta a { color: var(--negro); text-decoration: underline; text-underline-offset: 3px; }
.acepta a:hover { color: var(--rojo); }
.acepta.error span { color: var(--rojo); }

/* Campo trampa para robots: fuera de pantalla, no display:none (algunos lo detectan) */
.trampa { position: absolute; left: -9999px; width: 1px; height: 1px; overflow: hidden; }

.form-error { margin: 1.25rem 0 0; padding: .8rem 1rem; background: #FBEAEE; border-left: 3px solid var(--rojo); font-size: 1rem; color: var(--negro); }
.form-error:empty { display: none; }
.form-acciones { display: flex; align-items: center; gap: 1.25rem; margin-top: 1.75rem; flex-wrap: wrap; }
.form-acciones small { font-size: .9rem; color: var(--tenue); }

/* Estado de éxito */
.modal__exito { text-align: left; padding: 1rem 0; }
.modal__exito[hidden] { display: none; }
.exito__marca { width: 64px; height: 64px; border-radius: 50%; background: var(--rojo); color: var(--blanco); display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem; animation: marca-entra .6s cubic-bezier(.34,1.56,.64,1); }
.exito__marca svg { width: 30px; height: 30px; }
@keyframes marca-entra { from { transform: scale(0); } }
.modal__exito h3 { font-size: 2.4rem; line-height: 1; margin: 0 0 .9rem; }
.modal__exito p { font-size: 1.15rem; color: var(--gris); margin: 0 0 .75rem; }

@media (max-width: 991.98px) {
  .cabecera__sus { display: none; }
  .cabecera.abierta .secciones__sus { display: inline-flex; margin-top: .75rem; align-self: flex-start; }
}
@media (max-width: 760px) {
  .modal { max-width: calc(100vw - 1rem); max-height: calc(100vh - 1rem); }
  .modal__caja { grid-template-columns: 1fr; max-height: calc(100vh - 1rem); }
  .modal__lado { padding: 2.25rem 1.5rem 1.75rem; gap: 1.25rem; }
  .modal__puntos { display: none; }
  .modal__tit { font-size: 2.1rem; padding-right: 2.5rem; }
  .modal__cerrar { color: var(--blanco); }
  .modal__cuerpo { padding: 1.75rem 1.5rem 2rem; }
  .modal__intro { padding-right: 0; }
  .campos { grid-template-columns: 1fr; }
}

</style>
</head>
<body>

<!-- ============================================================================
     ENCABEZADO
     ============================================================================ -->
<header class="cabecera" id="cabecera">
  <div class="envoltura cabecera__fila">

    <a href="#portada" class="cabecera__marca" aria-label="Inicio del newsletter">
      <img @endverbatimsrc="{{ asset('img/newsletter/unam_logo.png') }}"@verbatim alt="UNAM">
      <img @endverbatimsrc="{{ asset('img/newsletter/fca-unam-logo.png') }}"@verbatim alt="Facultad de Contaduría y Administración">
      <span class="cabecera__sep"></span>
      <img @endverbatimsrc="{{ asset('img/newsletter/emprende_unam_logo.png') }}"@verbatim alt="Emprende UNAM" class="cabecera__logo">
    </a>

    <nav class="secciones" aria-label="Capítulos de la edición">
      <a href="#esencial"><span>Esencial</span></a>
      <a href="#datos"><span>Datos</span></a>
      <a href="#practico"><span>Práctico</span></a>
      <button type="button" class="boton-sus secciones__sus" data-suscribir><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 8a6 6 0 1 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>Suscribirme</button>
      <!-- En pantalla chica el botón de regreso del encabezado se queda sin
           texto, así que el menú abierto repite la opción con su nombre completo.
           Ajusta esta ruta si el sitio principal cambia de ubicación. -->
      <a @endverbatimhref="{{ route('inicio') }}"@verbatim class="secciones__volver"><svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M13 8H3M7 4 3 8l4 4"/></svg>Volver a la revista</a>
    </nav>

    <button type="button" class="boton-sus cabecera__sus" data-suscribir><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 8a6 6 0 1 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>Suscribirme</button>

    <button class="cabecera__toggle" id="botonMenu" aria-label="Abrir menú" aria-expanded="false">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 8h16M4 16h16"/></svg>
    </button>
  </div>
  <span class="progreso" id="progreso"></span>
</header>

<main>

<!-- ============================================================================
     PORTADA
     ============================================================================ -->
<section class="portada" id="portada">
  <div class="envoltura">


    <div class="portada__titular">
      <div class="trama" id="trama" aria-hidden="true"></div>
      <h1 class="portada__titulo">Emprendedores <em class="marca-roja">al servicio</em> de la pequeña y mediana empresa</h1>
    </div>

    <div class="portada__pie">

      <div class="revela visible">
        <p class="portada__bajada">Manténgase al día con las claves del mercado, las tendencias y lo que ocurre en nuestra comunidad</p>

        <!-- Ajusta esta ruta si el sitio principal cambia de ubicación -->
        <a @endverbatimhref="{{ route('inicio') }}"@verbatim class="portada__volver"><svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M13 8H3M7 4 3 8l4 4"/></svg>Volver a la revista</a>

        <!-- CONTENIDO: tres cifras del mes -->
        <div class="cifras-mes">
          <div><span>Inflación anual</span><b>3.9%<i>−0.2</i></b></div>
          <div><span>Tasa objetivo</span><b>7.00%<i>−25 pb</i></b></div>
          <div><span>Pymes en línea</span><b>37%<i>+3 pts</i></b></div>
        </div>
      </div>

      <nav class="revela visible d1" aria-label="Sumario">
        <p class="tag tag--gris" style="margin-bottom:1.25rem">En esta edición</p>
        <ol class="sumario">
          <li><a href="#noticia"><span class="n">01</span>La noticia<span class="cap">I</span></a></li>
          <li><a href="#agenda"><span class="n">02</span>Agenda de eventos<span class="cap">I</span></a></li>
          <li><a href="#macro"><span class="n">03</span>Resumen macroeconómico<span class="cap">I</span></a></li>
          <li><a href="#tendencias"><span class="n">04</span>Tendencias e innovación<span class="cap">I</span></a></li>
          <li><a href="#grafico"><span class="n">05</span>El gráfico del mes<span class="cap">II</span></a></li>
          <li><a href="#flash"><span class="n">06</span>Cifras flash<span class="cap">II</span></a></li>
          <li><a href="#herramienta"><span class="n">07</span>Herramienta recomendada<span class="cap">III</span></a></li>
          <li><a href="#leccion"><span class="n">08</span>Lección de liderazgo<span class="cap">III</span></a></li>
        </ol>
        <div class="fila-puntos" style="margin-top:1.75rem; justify-content:flex-end" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i></div>
      </nav>

    </div>
  </div>
</section>

<!-- ============================================================================
     I · ESENCIAL
     ============================================================================ -->
<div class="capitulo capitulo--papel" id="esencial" data-seccion>
  <div class="envoltura cap-rejilla">

    <div class="cap-rail">
      <p class="cap-rail__n">01</p>
      <p class="cap-rail__l">Esencial</p>
    </div>

    <div>
      <header class="apertura revela">
        <h2 class="apertura__tit">Lo esencial <em>del mes</em></h2>
        <p class="apertura__txt">El acontecimiento que marcó al mercado, la agenda de nuestra comunidad, el pulso de la economía y las ideas que están cambiando la forma de hacer negocios.</p>
      </header>

      <!-- 01 · LA NOTICIA -->
      <section class="bloque" id="noticia">
        <div class="sec-cab revela"><span class="sec-cab__n">01</span><span class="sec-cab__t">La noticia</span><span class="sec-cab__meta">6 min de lectura</span></div>

        <article class="noticia revela">
          <div class="noticia__media">
            <div class="lamina">
              <!-- Lámina provisional. Para usar fotografía: <img src="img/noticia.jpg" alt="..."> -->
              <div class="fondo"></div>
              <div class="malla"></div>
              <svg class="curva" viewBox="0 0 500 620" preserveAspectRatio="none" aria-hidden="true">
                <path d="M-10 540 C 70 505, 120 440, 180 455 S 290 350, 340 368 S 430 190, 520 150"/>
              </svg>
              <span class="lamina__tag">Comercio exterior</span>
              <span class="lamina__pie">Proveeduría local · Norte y Bajío, 2026</span>
            </div>
          </div>

          <div>
            <!-- CONTENIDO: noticia del mes (texto de muestra) -->
            <h3 class="noticia__tit">El <em>nearshoring</em> abre una segunda ola para los proveedores pyme</h3>
            <div class="noticia__cuerpo">
              <p class="capitular">La llegada de plantas manufactureras al norte y al Bajío ya no sólo demanda naves industriales: exige proveedores locales de empaque, mantenimiento, logística y servicios profesionales. Es ahí donde la pequeña y mediana empresa tiene su oportunidad.</p>
              <p class="destacada">Las pymes que se certifiquen hoy serán las proveedoras de mañana.</p>
              <p>El reto es la certificación: calidad, trazabilidad y facturación en tiempo. Quien llegue con esos tres frentes resueltos entra a una cadena que apenas se está formando, y que en los próximos años definirá a los proveedores de toda una región.</p>
            </div>
            <div class="noticia__pie">
              <span class="noticia__firma">Por <b>Comité editorial</b></span>
              <a href="#" class="enlace">Leer análisis <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 8h10M9 4l4 4-4 4"/></svg></a>
            </div>
          </div>
        </article>
      </section>

      <!-- 02 · AGENDA DE EVENTOS -->
      <section class="bloque" id="agenda">
        <div class="sec-cab revela"><span class="sec-cab__n">02</span><span class="sec-cab__t">Agenda de eventos</span><span class="sec-cab__meta">Octubre — noviembre</span></div>

        <!-- CONTENIDO: eventos (de muestra) -->
        <div class="agenda">
          <article class="evento revela">
            <div class="evento__fecha"><span class="evento__d">08</span><span class="evento__m">Oct</span></div>
            <span class="evento__tipo">Webinar</span>
            <h3 class="evento__tit">Finanzas personales y empresariales: cuándo separarlas</h3>
            <span class="evento__lugar"><svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4"><rect x="2" y="3" width="12" height="9"/><path d="M5 14h6"/></svg>En línea · 17:00 h</span>
          </article>
          <article class="evento revela d1">
            <div class="evento__fecha"><span class="evento__d">16</span><span class="evento__m">Oct</span></div>
            <span class="evento__tipo">Conferencia</span>
            <h3 class="evento__tit">Nearshoring: cómo convertirse en proveedor</h3>
            <span class="evento__lugar"><svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M8 14s5-4.5 5-8A5 5 0 0 0 3 6c0 3.5 5 8 5 8z"/><circle cx="8" cy="6" r="1.6"/></svg>Auditorio FCA, CU</span>
          </article>
          <article class="evento revela d2">
            <div class="evento__fecha"><span class="evento__d">29</span><span class="evento__m">Oct</span></div>
            <span class="evento__tipo">Taller</span>
            <h3 class="evento__tit">Marketing digital con presupuesto limitado</h3>
            <span class="evento__lugar"><svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4"><rect x="2" y="3" width="12" height="9"/><path d="M5 14h6"/></svg>En línea · 12:00 h</span>
          </article>
          <article class="evento revela d3">
            <div class="evento__fecha"><span class="evento__d">12</span><span class="evento__m">Nov</span></div>
            <span class="evento__tipo">Congreso</span>
            <h3 class="evento__tit">Encuentro de emprendimiento e innovación</h3>
            <span class="evento__lugar"><svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M8 14s5-4.5 5-8A5 5 0 0 0 3 6c0 3.5 5 8 5 8z"/><circle cx="8" cy="6" r="1.6"/></svg>Ciudad Universitaria</span>
          </article>
        </div>
      </section>

      <!-- 03 · RESUMEN MACROECONÓMICO -->
      <section class="bloque" id="macro">
        <div class="sec-cab revela"><span class="sec-cab__n">03</span><span class="sec-cab__t">Resumen macroeconómico</span><span class="sec-cab__meta">Cifras de muestra</span></div>

        <!-- CONTENIDO: indicadores. data-valor = cifra; data-dec = decimales -->
        <div class="macro">
          <div class="ind revela">
            <p class="ind__rot">Inflación anual</p>
            <p class="ind__val"><span class="contador" data-valor="3.9" data-dec="1">0</span><small>%</small></p>
            <span class="ind__delta favorable"><svg viewBox="0 0 12 12" fill="currentColor"><path d="M6 10 1 3h10z"/></svg>Bajó 0.2 pts en el mes</span>
            <p class="ind__nota">Tercer mes consecutivo a la baja; los servicios siguen presionando.</p>
          </div>
          <div class="ind revela d1">
            <p class="ind__rot">Tasa de interés objetivo</p>
            <p class="ind__val"><span class="contador" data-valor="7.00" data-dec="2">0</span><small>%</small></p>
            <span class="ind__delta favorable"><svg viewBox="0 0 12 12" fill="currentColor"><path d="M6 10 1 3h10z"/></svg>Recorte de 25 puntos base</span>
            <p class="ind__nota">El crédito para capital de trabajo se abarata de forma gradual.</p>
          </div>
          <div class="ind revela d2">
            <p class="ind__rot">Tasa de desempleo</p>
            <p class="ind__val"><span class="contador" data-valor="2.7" data-dec="1">0</span><small>%</small></p>
            <span class="ind__delta adverso"><svg viewBox="0 0 12 12" fill="currentColor"><path d="M6 2l5 7H1z"/></svg>Subió 0.1 pts en el mes</span>
            <p class="ind__nota">Mercado laboral estrecho: retener talento cuesta más que atraerlo.</p>
          </div>
        </div>
        <p class="fuente revela" style="margin-top:2rem">Cifras de muestra para maquetación · Fuentes sugeridas: INEGI y Banco de México.</p>
      </section>

      <!-- 04 · TENDENCIAS E INNOVACIÓN -->
      <section class="bloque" id="tendencias">
        <div class="sec-cab revela"><span class="sec-cab__n">04</span><span class="sec-cab__t">Tendencias e innovación</span><span class="sec-cab__meta">Tres señales</span></div>

        <!-- CONTENIDO: tres tendencias (texto de muestra) -->
        <div class="tendencias revela">
          <article class="tend">
            <span class="tend__n">01</span>
            <div>
              <p class="tend__cat">Tecnología emergente</p>
              <h3 class="tend__tit">Inteligencia artificial en la atención al cliente</h3>
            </div>
            <p class="tend__txt">Asistentes que responden en WhatsApp, agendan citas y dan seguimiento a pedidos. Para una pyme, el valor no está en la tecnología sino en liberar horas del equipo para vender.</p>
            <span class="tend__ir"><svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 8h10M9 4l4 4-4 4"/></svg></span>
          </article>
          <article class="tend">
            <span class="tend__n">02</span>
            <div>
              <p class="tend__cat">Nuevo modelo de negocio</p>
              <h3 class="tend__tit">Suscripciones en productos cotidianos</h3>
            </div>
            <p class="tend__txt">Café, limpieza, alimento para mascotas: el ingreso recurrente da a la pequeña empresa algo que casi nunca tiene, previsibilidad de flujo.</p>
            <span class="tend__ir"><svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 8h10M9 4l4 4-4 4"/></svg></span>
          </article>
          <article class="tend">
            <span class="tend__n">03</span>
            <div>
              <p class="tend__cat">Disrupción sectorial</p>
              <h3 class="tend__tit">Economía circular en la manufactura ligera</h3>
            </div>
            <p class="tend__txt">Reutilizar mermas y empaques deja de ser sólo una postura ambiental: los grandes compradores empiezan a exigirlo a sus proveedores.</p>
            <span class="tend__ir"><svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 8h10M9 4l4 4-4 4"/></svg></span>
          </article>
        </div>
      </section>
    </div>
  </div>
</div>

<!-- ============================================================================
     II · DATOS Y MERCADO
     ============================================================================ -->
<div class="capitulo" id="datos" data-seccion>
  <div class="envoltura cap-rejilla">

    <div class="cap-rail">
      <p class="cap-rail__n">02</p>
      <p class="cap-rail__l">Datos</p>
    </div>

    <div>
      <header class="apertura revela">
        <h2 class="apertura__tit">Datos y <em>mercado</em></h2>
        <p class="apertura__txt">Una visualización explicada en sencillo y las cifras que vale la pena llevar a la próxima junta.</p>
      </header>

      <!-- 05 · EL GRÁFICO DEL MES -->
      <section class="bloque" id="grafico">
        <div class="sec-cab revela"><span class="sec-cab__n">05</span><span class="sec-cab__t">El gráfico del mes</span><span class="sec-cab__meta">2024–2025</span></div>

        <div class="grafico revela">
          <div>
            <!-- CONTENIDO: lectura del gráfico (texto de muestra) -->
            <h3 class="grafico__tit">Cada vez más pymes <em>venden en línea</em></h3>
            <p class="grafico__txt">En dos años, la proporción de pequeñas y medianas empresas que venden por internet pasó de casi una de cada cinco a más de una de cada tres. La pandemia abrió la puerta; lo que sostiene el crecimiento son los pagos digitales.</p>
            <p class="grafico__cifra"><b>+<span class="contador" data-valor="19" data-dec="0">0</span></b><span>puntos porcentuales ganados en ocho trimestres</span></p>
          </div>

          <figure class="lienzo" style="margin:0">
            <figcaption class="lienzo__cab">
              <span class="lienzo__tit">Pymes que venden en línea</span>
              <span class="lienzo__sub">% del total, por trimestre</span>
            </figcaption>
            <!-- El SVG lo dibuja el script a partir de DATOS_GRAFICO -->
            <svg id="graficoSvg" role="img" aria-label="Gráfico de columnas: pymes que venden en línea, de 18% a 37% entre el primer trimestre de 2024 y el cuarto de 2025"></svg>
            <div class="tooltip" id="tooltip"></div>
            <div class="lienzo__pie">
              <span class="fuente">Datos de muestra para maquetación.</span>
              <details>
                <summary>Ver datos</summary>
                <table id="tablaGrafico"><thead><tr><th>Trimestre</th><th>%</th></tr></thead><tbody></tbody></table>
              </details>
            </div>
          </figure>
        </div>
      </section>

      <!-- 06 · CIFRAS FLASH -->
      <section class="bloque" id="flash">
        <div class="sec-cab revela"><span class="sec-cab__n">06</span><span class="sec-cab__t">Cifras flash</span><span class="sec-cab__meta">El ecosistema en números</span></div>

        <!-- CONTENIDO: cuatro cifras (de muestra) -->
        <div class="flash">
          <div class="flash__it revela"><p class="flash__n"><span class="contador" data-valor="99.8" data-dec="1">0</span><sup>por ciento</sup></p><p class="flash__t">de las unidades económicas del país son micro, pequeñas o medianas.</p></div>
          <div class="flash__it revela d1"><p class="flash__n"><span class="contador" data-valor="7" data-dec="0">0</span><sup>de cada 10</sup></p><p class="flash__t">empleos formales e informales los generan las mipymes.</p></div>
          <div class="flash__it revela d2"><p class="flash__n"><span class="contador" data-valor="52" data-dec="0">0</span><sup>por ciento</sup></p><p class="flash__t">aproximado del PIB nacional lo aportan estas empresas.</p></div>
          <div class="flash__it revela d3"><p class="flash__n"><span class="contador" data-valor="3" data-dec="0">0</span><sup>años</sup></p><p class="flash__t">es la esperanza de vida promedio de un negocio nuevo sin acompañamiento.</p></div>
        </div>
        <p class="fuente revela" style="margin-top:2rem">Cifras de muestra para maquetación · Verificar con INEGI antes de publicar.</p>
      </section>
    </div>
  </div>
</div>

<!-- ============================================================================
     III · PRÁCTICO
     ============================================================================ -->
<div class="capitulo capitulo--papel" id="practico" data-seccion>
  <div class="envoltura cap-rejilla">

    <div class="cap-rail">
      <p class="cap-rail__n">03</p>
      <p class="cap-rail__l">Práctico</p>
    </div>

    <div>
      <header class="apertura revela">
        <h2 class="apertura__tit">Para poner <em>en práctica</em></h2>
        <p class="apertura__txt">Una herramienta que vale la pena probar y una lección para dirigir mejor.</p>
      </header>

      <!-- 07 · HERRAMIENTA RECOMENDADA -->
      <section class="bloque" id="herramienta">
        <div class="sec-cab revela"><span class="sec-cab__n">07</span><span class="sec-cab__t">Herramienta recomendada</span><span class="sec-cab__meta">Selección del mes</span></div>

        <!-- CONTENIDO: herramienta del mes -->
        <article class="producto revela">
          <div class="producto__escena">
            <span class="tag producto__tag">Productividad</span>
            <div class="kanban" aria-hidden="true">
              <div class="kanban__col"><b>Por hacer</b><span class="kanban__t"></span><span class="kanban__t"></span><span class="kanban__t"></span></div>
              <div class="kanban__col"><b>En curso</b><span class="kanban__t kanban__t--rojo"></span><span class="kanban__t"></span></div>
              <div class="kanban__col"><b>Listo</b><span class="kanban__t"></span><span class="kanban__t"></span><span class="kanban__t"></span></div>
            </div>
          </div>
          <div class="producto__info">
            <h3 class="producto__nom">Trello</h3>
            <p class="producto__cat">Tableros kanban para organizar proyectos y equipos</p>
            <p class="producto__txt">Convierte pendientes en tarjetas que avanzan de columna en columna. Ideal para equipos pequeños que coordinan entregas sin necesidad de un sistema complejo.</p>
            <dl class="atributos">
              <div><dt>Costo</dt><dd>Plan gratuito</dd></div>
              <div><dt>Curva</dt><dd>Un día</dd></div>
              <div><dt>Equipo</dt><dd>2 a 15</dd></div>
            </dl>
            <div class="intensidad">
              <div class="intensidad__f">Organización <span class="intensidad__p"><i class="on"></i><i class="on"></i><i class="on"></i><i class="on"></i><i class="on"></i></span></div>
              <div class="intensidad__f">Colaboración <span class="intensidad__p"><i class="on"></i><i class="on"></i><i class="on"></i><i class="on"></i><i></i></span></div>
              <div class="intensidad__f">Reportes <span class="intensidad__p"><i class="on"></i><i class="on"></i><i></i><i></i><i></i></span></div>
            </div>
            <a href="https://trello.com" target="_blank" rel="noopener" class="enlace">Conocer la herramienta <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 8h10M9 4l4 4-4 4"/></svg></a>
          </div>
        </article>
      </section>
    </div>
  </div>
</div>

<!-- 08 · LECCIÓN DE LIDERAZGO — único bloque rojo, a sangre completa -->
<section class="leccion" id="leccion">
  <div class="envoltura leccion__caja revela">
    <div>
      <p class="tag tag--claro" style="margin-bottom:1.5rem"><span style="opacity:.8">08 — Lección de liderazgo</span></p>
      <!-- CONTENIDO: lección del mes (texto de muestra) -->
      <blockquote class="leccion__cita">Delegar no es repartir tareas: es repartir <em>decisiones</em>, y aceptar que otros las tomen distinto a como tú lo harías.</blockquote>
      <p class="leccion__firma">Dirección de equipos</p>
    </div>
    <ol class="pasos">
      <li><span>01</span>Define el resultado esperado, no el procedimiento.</li>
      <li><span>02</span>Acuerda desde el inicio qué decisiones son de la persona.</li>
      <li><span>03</span>Revisa por resultados en fechas fijas, no por supervisión constante.</li>
    </ol>
  </div>
</section>


</main>

<!-- ============================================================================
     SUSCRIPCIÓN — ventana emergente
     Envía a suscribir.php; los datos se guardan en data/ (protegido).
     ============================================================================ -->
<dialog class="modal" id="modalSuscripcion" aria-labelledby="modalTitulo">
  <div class="modal__caja">
    <button type="button" class="modal__cerrar" data-cerrar aria-label="Cerrar">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M5 5l14 14M19 5 5 19"/></svg>
    </button>

    <aside class="modal__lado">
      <div>
        <p class="modal__eti">Newsletter</p>
        <h2 class="modal__tit" id="modalTitulo">Recibe cada edición <em>en tu correo</em></h2>
      </div>
      <ul class="modal__puntos">
        <li>Mantente al día con las claves del mercado</li>
        <li>Ideas y datos para entender mejor los negocios</li>
        <li>Una lectura breve para empezar con lo importante</li>
      </ul>
    </aside>

    <div class="modal__cuerpo">
      @endverbatim
      <form id="formSuscripcion" action="{{ route('newsletter.suscribir') }}" method="post" novalidate>
        @csrf
@verbatim
        <p class="modal__intro">Déjanos tus datos y te avisaremos cada vez que salga una nueva edición.</p>

        <div class="campos">
          <div class="campo">
            <label for="sNombre">Nombre</label>
            <input type="text" id="sNombre" name="nombre" autocomplete="given-name" maxlength="60" required>
            <span class="campo__error" data-error="nombre"></span>
          </div>
          <div class="campo">
            <label for="sApellido">Apellido</label>
            <input type="text" id="sApellido" name="apellido" autocomplete="family-name" maxlength="80" required>
            <span class="campo__error" data-error="apellido"></span>
          </div>
          <div class="campo campo--ancho">
            <label for="sCorreo">Correo electrónico</label>
            <input type="email" id="sCorreo" name="correo" autocomplete="email" maxlength="254" placeholder="nombre@correo.com" required>
            <span class="campo__error" data-error="correo"></span>
          </div>
          <div class="campo campo--ancho">
            <label for="sTema">Tema de interés</label>
@endverbatim
            <select id="sTema" name="tema" required>
              <option value="" selected disabled>Elige un tema</option>
              @foreach ($temas as $tema)
                <option value="{{ $tema }}">{{ $tema }}</option>
              @endforeach
            </select>
@verbatim
            <span class="campo__error" data-error="tema"></span>
          </div>
        </div>

        <div class="trampa" aria-hidden="true">
          <label for="sSitio">Sitio web</label>
          <input type="text" id="sSitio" name="sitio_web" tabindex="-1" autocomplete="off">
        </div>

        <label class="acepta" data-campo="acepta">
          <input type="checkbox" name="acepta" value="1" required>
          <span>He leído y acepto el <a href="https://www.fca.unam.mx/docs/aviso_privacidad.pdf" target="_blank" rel="noopener">aviso de privacidad</a> de la Facultad de Contaduría y Administración.</span>
        </label>

        <p class="form-error" role="alert" aria-live="assertive"></p>

        <div class="form-acciones">
          <button type="submit" class="boton-sus"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 8a6 6 0 1 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg><span data-texto>Suscribirme</span></button>
          <small>Te enviaremos un correo de confirmación.</small>
        </div>
      </form>

      <div class="modal__exito" hidden>
        <span class="exito__marca"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg></span>
        <h3>¡Listo, <span data-nombre></span>!</h3>
        <p data-mensaje></p>
        <p>Revisa tu bandeja de entrada; si no ves el correo de confirmación, busca en la carpeta de spam.</p>
        <div class="form-acciones"><button type="button" class="boton-sus boton-sus--linea" data-cerrar>Seguir leyendo</button></div>
      </div>
    </div>
  </div>
</dialog>

<!-- ============================================================================
     PIE
     ============================================================================ -->
<footer class="footer-lux" role="contentinfo">
  <div class="envoltura footer-lux__rejilla">

    <!-- Identidad -->
    <div>
      <p class="footer-lux__title">Publicación académica</p>
      <p>Revista de divulgación de la Facultad de Contaduría y Administración,
         Universidad Nacional Autónoma de México. Bimestral y de acceso abierto.</p>
      <div class="footer-lux__social">
        <a href="https://www.facebook.com/FCAUNAMOFICIAL/" target="_blank" rel="noopener" aria-label="Facebook de la FCA UNAM"><svg viewBox="0 0 24 24"><path d="M13.5 21v-7.5h2.5l.4-3H13.5V8.4c0-.87.24-1.46 1.5-1.46h1.6V4.34c-.3-.04-1.3-.2-2.44-.2-2.9 0-4.66 1.77-4.66 5.02v2.34H7v3h2.5V21h4z"/></svg></a>
        <a href="https://www.instagram.com/fcaunamoficial/" target="_blank" rel="noopener" aria-label="Instagram de la FCA UNAM"><svg viewBox="0 0 24 24"><path d="M12 7.5A4.5 4.5 0 1 0 12 16.5 4.5 4.5 0 0 0 12 7.5zm0 7.4A2.9 2.9 0 1 1 12 9.1a2.9 2.9 0 0 1 0 5.8zM16.7 6.3a1 1 0 1 0 0 2.1 1 1 0 0 0 0-2.1zM12 4.6c2.4 0 2.7 0 3.6.1.9 0 1.4.2 1.7.3.4.2.7.4 1 .7.3.3.5.6.7 1 .1.3.3.8.3 1.7.1.9.1 1.2.1 3.6s0 2.7-.1 3.6c0 .9-.2 1.4-.3 1.7-.2.4-.4.7-.7 1-.3.3-.6.5-1 .7-.3.1-.8.3-1.7.3-.9.1-1.2.1-3.6.1s-2.7 0-3.6-.1c-.9 0-1.4-.2-1.7-.3-.4-.2-.7-.4-1-.7-.3-.3-.5-.6-.7-1-.1-.3-.3-.8-.3-1.7-.1-.9-.1-1.2-.1-3.6s0-2.7.1-3.6c0-.9.2-1.4.3-1.7.2-.4.4-.7.7-1 .3-.3.6-.5 1-.7.3-.1.8-.3 1.7-.3.9-.1 1.2-.1 3.6-.1zM12 3c-2.4 0-2.8 0-3.7.1-1 0-1.6.2-2.2.4-.6.2-1.1.6-1.6 1.1-.5.5-.8 1-1.1 1.6-.2.6-.4 1.2-.4 2.2C3 9.2 3 9.6 3 12s0 2.8.1 3.7c0 1 .2 1.6.4 2.2.2.6.6 1.1 1.1 1.6.5.5 1 .8 1.6 1.1.6.2 1.2.4 2.2.4.9.1 1.3.1 3.7.1s2.8 0 3.7-.1c1 0 1.6-.2 2.2-.4.6-.2 1.1-.6 1.6-1.1.5-.5.8-1 1.1-1.6.2-.6.4-1.2.4-2.2.1-.9.1-1.3.1-3.7s0-2.8-.1-3.7c0-1-.2-1.6-.4-2.2-.2-.6-.6-1.1-1.1-1.6-.5-.5-1-.8-1.6-1.1-.6-.2-1.2-.4-2.2-.4C14.8 3 14.4 3 12 3z"/></svg></a>
        <a href="https://www.youtube.com/@FCAUNAMOFICIAL" target="_blank" rel="noopener" aria-label="YouTube de la FCA UNAM"><svg viewBox="0 0 24 24"><path d="M21.6 7.2s-.2-1.5-.86-2.16c-.82-.87-1.74-.87-2.16-.92C15.6 4 12 4 12 4s-3.6 0-6.58.12c-.42.05-1.34.05-2.16.92C2.6 5.7 2.4 7.2 2.4 7.2S2.2 9 2.2 10.8v1.4c0 1.8.2 3.6.2 3.6s.2 1.5.86 2.16c.82.87 1.9.84 2.38.94C7.4 19 12 19 12 19s3.6 0 6.58-.13c.42-.05 1.34-.05 2.16-.92.65-.66.86-2.16.86-2.16s.2-1.8.2-3.6v-1.4c0-1.8-.2-3.6-.2-3.6zM9.9 14.6V8.9l5.4 2.86-5.4 2.84z"/></svg></a>
        <a href="https://x.com/FCAUNAMOFICIAL" target="_blank" rel="noopener" aria-label="X de la FCA UNAM"><svg viewBox="0 0 24 24"><path d="M13.6 10.6 20 3h-1.5l-5.6 6.6L8.3 3H3l6.7 9.6L3 21h1.5l6-7 5 7H21l-7.4-10.4zm-2.1 2.4-.7-1L5.3 4.2h2.3l4.5 6.3.7 1 5.9 8.3h-2.3l-4.9-6.8z"/></svg></a>
      </div>
    </div>

    <!-- Contacto -->
    <div>
      <!-- Vuelta a la revista para quien termina la lectura aquí abajo -->
      <a @endverbatimhref="{{ route('inicio') }}"@verbatim class="footer-lux__volver"><svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M13 8H3M7 4 3 8l4 4"/></svg>Volver a la revista</a>
      <p class="footer-lux__title">Contacto</p>
      <p>Circuito Exterior s/n, Ciudad Universitaria<br>Coyoacán, CP 04510, Ciudad de México</p>
      <p><a href="mailto:revistaemprendedores@fca.unam.mx">revistaemprendedores@fca.unam.mx</a><br>+52 (55) 5622 8332</p>
    </div>

    <!-- Escudos -->
    <div class="footer-lux__marcas">
      <img @endverbatimsrc="{{ asset('img/newsletter/unam_gran.png') }}"@verbatim alt="UNAM" class="logo-footer" onerror="this.style.display='none'">
      <img @endverbatimsrc="{{ asset('img/newsletter/logo475-azul.jpg') }}"@verbatim alt="FCA UNAM" class="logo-footer" onerror="this.style.display='none'">
    </div>

  </div>

  <div class="footer-lux__legal">
    <div class="envoltura">
      <p>Hecho en México. D.R. &copy; <span id="anio">2026</span> Universidad Nacional Autónoma de México.
      Esta página puede ser reproducida con fines no lucrativos siempre que no se mutile, se cite la fuente
      completa y su dirección electrónica. De otra forma requiere permiso previo por escrito de la institución.
      <a href="https://www.fca.unam.mx/docs/aviso_privacidad.pdf" target="_blank" rel="noopener">Aviso de privacidad</a> ·
      <a href="https://www.fca.unam.mx/docs/permanentes/seguridad.pdf" target="_blank" rel="noopener">Documento de seguridad</a> ·
      Sitio administrado por el Centro de Informática de la FCA (CIFCA).</p>
    </div>
  </div>
</footer>

<!-- ============================================================================
     SCRIPT
     ============================================================================ -->
<script>
(function () {
  'use strict';

  var menosMovimiento = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  document.getElementById('anio').textContent = new Date().getFullYear();

  /* ── Encabezado ── */
  var cabecera = document.getElementById('cabecera');
  var progreso = document.getElementById('progreso');
  var ultimoY = 0;
  function alDesplazar() {
    var y = window.scrollY;
    var total = document.documentElement.scrollHeight - window.innerHeight;
    progreso.style.transform = 'scaleX(' + (total > 0 ? y / total : 0) + ')';
    cabecera.classList.toggle('cabecera--posada', y > 40);
    ultimoY = y;
  }
  window.addEventListener('scroll', alDesplazar, { passive: true });
  alDesplazar();

  /* ── Menú móvil ── */
  var botonMenu = document.getElementById('botonMenu');
  botonMenu.addEventListener('click', function () {
    var abierto = cabecera.classList.toggle('abierta');
    botonMenu.setAttribute('aria-expanded', String(abierto));
  });
  document.querySelectorAll('.secciones a').forEach(function (a) {
    a.addEventListener('click', function () { cabecera.classList.remove('abierta'); botonMenu.setAttribute('aria-expanded', 'false'); });
  });

  /* ── Capítulo activo ── */
  var enlacesSec = document.querySelectorAll('.secciones a');
  if ('IntersectionObserver' in window) {
    var espia = new IntersectionObserver(function (es) {
      es.forEach(function (e) {
        if (!e.isIntersecting) return;
        enlacesSec.forEach(function (a) { a.classList.toggle('activo', a.getAttribute('href') === '#' + e.target.id); });
      });
    }, { rootMargin: '-45% 0px -50% 0px' });
    document.querySelectorAll('[data-seccion]').forEach(function (s) { espia.observe(s); });
  }

  /* ── Contadores ── */
  function animarContador(el) {
    var fin = parseFloat(el.getAttribute('data-valor'));
    var dec = parseInt(el.getAttribute('data-dec') || '0', 10);
    if (menosMovimiento) { el.textContent = fin.toFixed(dec); return; }
    var inicio = null, dur = 1600;
    function paso(t) {
      if (!inicio) inicio = t;
      var p = Math.min((t - inicio) / dur, 1);
      el.textContent = (fin * (1 - Math.pow(1 - p, 4))).toFixed(dec);
      if (p < 1) requestAnimationFrame(paso);
    }
    requestAnimationFrame(paso);
  }

  /* ── Revelados ── */
  var revelables = document.querySelectorAll('.revela');
  if (!menosMovimiento && 'IntersectionObserver' in window) {
    var obs = new IntersectionObserver(function (es) {
      es.forEach(function (e) {
        if (!e.isIntersecting) return;
        e.target.classList.add('visible');
        e.target.querySelectorAll('.contador').forEach(animarContador);
        obs.unobserve(e.target);
      });
    }, { threshold: .15, rootMargin: '0px 0px -60px 0px' });
    revelables.forEach(function (el) { obs.observe(el); });
    document.querySelectorAll('.revela.visible .contador').forEach(animarContador);
  } else {
    revelables.forEach(function (el) { el.classList.add('visible'); });
    document.querySelectorAll('.contador').forEach(animarContador);
  }

  /* ── Gráfico del mes ──
     CONTENIDO: sustituye estos datos (de muestra) por los de la edición */
  var DATOS_GRAFICO = [
    { t: 'T1 24', v: 18 }, { t: 'T2 24', v: 21 }, { t: 'T3 24', v: 23 }, { t: 'T4 24', v: 26 },
    { t: 'T1 25', v: 29 }, { t: 'T2 25', v: 31 }, { t: 'T3 25', v: 34 }, { t: 'T4 25', v: 37 }
  ];
  var svg = document.getElementById('graficoSvg');
  var tip = document.getElementById('tooltip');
  var tbody = document.querySelector('#tablaGrafico tbody');
  var ns = 'http://www.w3.org/2000/svg';
  function el(n, a) { var e = document.createElementNS(ns, n); for (var k in a) e.setAttribute(k, a[k]); return e; }

  DATOS_GRAFICO.forEach(function (d) {
    var tr = document.createElement('tr');
    tr.innerHTML = '<td>' + d.t.replace(' ', ' 20') + '</td><td>' + d.v + '</td>';
    tbody.appendChild(tr);
  });

  /* Se dibuja al ancho real del contenedor para que el texto de los ejes
     mida siempre 11 px, también en móvil. */
  function dibujarGrafico() {
    var W = Math.max(280, Math.round(svg.parentNode.clientWidth - (window.innerWidth < 576 ? 36 : 64)));
    var H = W < 480 ? 230 : 290;
    var cortas = (W - 32) / DATOS_GRAFICO.length < 46;
    var m = { t: 26, r: 4, b: cortas ? 46 : 30, l: 28 }, maxY = 40;
    var ancho = W - m.l - m.r, alto = H - m.t - m.b;
    var banda = ancho / DATOS_GRAFICO.length, grosor = Math.min(24, banda * .48);
    function y(v) { return m.t + alto - (v / maxY) * alto; }

    svg.setAttribute('viewBox', '0 0 ' + W + ' ' + H);
    while (svg.firstChild) svg.removeChild(svg.firstChild);

    var rejilla = el('g', { 'class': 'rejilla' }), eje = el('g', { 'class': 'eje' });
    [0, 10, 20, 30, 40].forEach(function (v) {
      rejilla.appendChild(el('line', { x1: m.l, x2: W - m.r, y1: y(v), y2: y(v) }));
      var tx = el('text', { x: m.l - 8, y: y(v) + 4, 'text-anchor': 'end' }); tx.textContent = v; eje.appendChild(tx);
    });
    svg.appendChild(rejilla); svg.appendChild(eje);

    DATOS_GRAFICO.forEach(function (d, i) {
      var x = m.l + banda * i + (banda - grosor) / 2, r = 4;
      var ultimo = i === DATOS_GRAFICO.length - 1;
      var g = el('g', { 'class': 'barra' + (ultimo ? ' actual' : '') });
      var crece = el('g', { 'class': 'barra-crece', style: 'transition-delay:' + (i * .06) + 's' });
      // Extremo de datos redondeado (4 px), base recta sobre el eje
      crece.appendChild(el('path', { 'class': 'forma', d: 'M' + x + ',' + y(0) + 'V' + (y(d.v) + r) + 'Q' + x + ',' + y(d.v) + ' ' + (x + r) + ',' + y(d.v) + 'H' + (x + grosor - r) + 'Q' + (x + grosor) + ',' + y(d.v) + ' ' + (x + grosor) + ',' + (y(d.v) + r) + 'V' + y(0) + 'Z' }));
      g.appendChild(crece);

      var cx = m.l + banda * i + banda / 2;
      var lab = el('text', { x: cx, y: cortas ? H - 26 : H - 8, 'text-anchor': 'middle' });
      lab.textContent = cortas ? d.t.split(' ')[0] : d.t; eje.appendChild(lab);
      if (cortas && d.t.indexOf('T1') === 0) {
        var an = el('text', { x: cx - banda / 2 + 2, y: H - 6 }); an.textContent = '20' + d.t.split(' ')[1]; eje.appendChild(an);
      }
      if (ultimo || i === 0) {
        var val = el('text', { 'class': 'valor', x: x + grosor / 2, y: y(d.v) - 9, 'text-anchor': 'middle' });
        val.textContent = d.v + '%'; g.appendChild(val);
      }

      var golpe = el('rect', { 'class': 'golpe', x: m.l + banda * i, y: m.t, width: banda, height: alto });
      g.appendChild(golpe);
      function mostrar() {
        var cs = svg.getBoundingClientRect(), cl = svg.parentNode.getBoundingClientRect(), esc = cs.width / W;
        tip.innerHTML = d.t.replace(' ', ' · 20') + '<b>' + d.v + '%</b>';
        tip.style.left = (cs.left - cl.left + (x + grosor / 2) * esc) + 'px';
        tip.style.top = (cs.top - cl.top + y(d.v) * esc - 6) + 'px';
        tip.style.opacity = 1;
      }
      golpe.addEventListener('mouseenter', mostrar);
      golpe.addEventListener('touchstart', mostrar, { passive: true });
      golpe.addEventListener('mouseleave', function () { tip.style.opacity = 0; });
      svg.appendChild(g);
    });
  }
  dibujarGrafico();
  var anchoPrevio = window.innerWidth, espera;
  window.addEventListener('resize', function () {
    if (window.innerWidth === anchoPrevio) return;
    anchoPrevio = window.innerWidth;
    clearTimeout(espera); espera = setTimeout(function () { dibujarGrafico(); dibujarTrama(); }, 150);
  });


  /* ── Trama de lunares de la portada ──
     Semitono: el radio de cada punto crece hacia la esquina superior
     derecha. Cerca del cursor los puntos se agrandan. */
  var trama = document.getElementById('trama');
  var puntos = [];
  function dibujarTrama() {
    if (!trama || getComputedStyle(trama).display === 'none') return;
    var w = trama.clientWidth, h = trama.clientHeight;
    if (!w || !h) return;
    var paso = 24, cols = Math.floor(w / paso), filas = Math.floor(h / paso);
    var s = el('svg', { viewBox: '0 0 ' + w + ' ' + h, preserveAspectRatio: 'xMaxYMin meet' });
    puntos = [];
    for (var f = 0; f < filas; f++) {
      for (var c = 0; c < cols; c++) {
        var cx = w - (c * paso + paso / 2), cy = f * paso + paso / 2;
        var dx = (w - cx) / w, dy = cy / h;
        var t = 1 - Math.sqrt(dx * dx * 1.1 + dy * dy * .9) * 1.15;
        if (t <= .04) continue;
        var r = Math.max(.7, t * paso * .34);
        var ci = el('circle', { cx: cx.toFixed(1), cy: cy.toFixed(1), r: r.toFixed(2) });
        if (!menosMovimiento) {
          ci.style.transform = 'scale(0)';
          ci.style.transitionDelay = ((1 - t) * .5 + Math.random() * .15).toFixed(2) + 's';
        }
        s.appendChild(ci);
        puntos.push({ n: ci, x: cx, y: cy });
      }
    }
    trama.innerHTML = ''; trama.appendChild(s);
    if (!menosMovimiento) {
      requestAnimationFrame(function () { requestAnimationFrame(function () {
        puntos.forEach(function (p) { p.n.style.transform = 'scale(1)'; });
        setTimeout(function () { puntos.forEach(function (p) { p.n.style.transitionDelay = '0s'; }); }, 1400);
      }); });
    }
  }
  dibujarTrama();

  if (trama && !menosMovimiento && window.matchMedia('(hover: hover)').matches) {
    var portada = document.getElementById('portada'), pend = false, px = -9999, py = -9999;
    function reaccionar() {
      pend = false;
      var b = trama.getBoundingClientRect();
      var lx = px - b.left, ly = py - b.top, radio = 120;
      puntos.forEach(function (p) {
        var d = Math.hypot(p.x - lx, p.y - ly);
        p.n.style.transform = 'scale(' + (d < radio ? 1 + (1 - d / radio) * 1.1 : 1).toFixed(2) + ')';
      });
    }
    portada.addEventListener('pointermove', function (e) {
      px = e.clientX; py = e.clientY;
      if (!pend) { pend = true; requestAnimationFrame(reaccionar); }
    });
    portada.addEventListener('pointerleave', function () { px = py = -9999; requestAnimationFrame(reaccionar); });
  }

  /* ── Suscripción ── */
  var modal = document.getElementById('modalSuscripcion');
  var form  = document.getElementById('formSuscripcion');
  var exito = modal.querySelector('.modal__exito');
  var errorGeneral = form.querySelector('.form-error');
  var botonEnviar  = form.querySelector('button[type=submit]');

  function abrirModal() {
    cabecera.classList.remove('abierta'); botonMenu.setAttribute('aria-expanded', 'false');
    if (typeof modal.showModal === 'function') { modal.showModal(); } else { modal.setAttribute('open', ''); }
    document.documentElement.style.overflow = 'hidden';
    setTimeout(function () { (exito.hidden ? form.nombre : exito.querySelector('[data-cerrar]')).focus(); }, 60);
  }
  function cerrarModal() {
    if (typeof modal.close === 'function') { modal.close(); } else { modal.removeAttribute('open'); }
  }
  modal.addEventListener('close', function () {
    document.documentElement.style.overflow = '';
    /* Tras una suscripción exitosa, la próxima vez vuelve a mostrar el formulario */
    if (!exito.hidden) { form.hidden = false; exito.hidden = true; }
  });
  document.querySelectorAll('[data-suscribir]').forEach(function (b) { b.addEventListener('click', abrirModal); });
  modal.querySelectorAll('[data-cerrar]').forEach(function (b) { b.addEventListener('click', cerrarModal); });
  /* Clic en el fondo oscuro: cierra */
  modal.addEventListener('click', function (e) { if (e.target === modal) { cerrarModal(); } });

  function marcar(campo, texto) {
    var cont = campo === 'acepta' ? form.querySelector('[data-campo=acepta]') : form[campo].closest('.campo');
    cont.classList.toggle('error', !!texto);
    var span = form.querySelector('[data-error=' + campo + ']');
    if (span) { span.textContent = texto || ''; }
  }
  function validar() {
    var errores = {}, soloLetras = /^[A-Za-zÀ-ÖØ-öø-ÿ][A-Za-zÀ-ÖØ-öø-ÿ '.\-]*$/;
    var nombre = form.nombre.value.trim(), apellido = form.apellido.value.trim(), correo = form.correo.value.trim(), tema = form.tema.value.trim();
    if (!nombre) { errores.nombre = 'Escribe tu nombre.'; } else if (!soloLetras.test(nombre)) { errores.nombre = 'Usa sólo letras.'; }
    if (!apellido) { errores.apellido = 'Escribe tu apellido.'; } else if (!soloLetras.test(apellido)) { errores.apellido = 'Usa sólo letras.'; }
    if (!correo) { errores.correo = 'Escribe tu correo.'; } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(correo)) { errores.correo = 'Revisa tu correo; parece incompleto.'; }
    if (!tema) { errores.tema = 'Elige un tema de interés.'; }
    if (!form.acepta.checked) { errores.acepta = 'Es necesario aceptar el aviso de privacidad.'; }
    ['nombre', 'apellido', 'correo', 'tema', 'acepta'].forEach(function (c) { marcar(c, errores[c]); });
    return errores;
  }
  /* Al corregir un campo marcado, el error se retira en cuanto es válido */
  form.addEventListener('input', function (e) {
    if (e.target.name && form.querySelector('.error')) { var err = validar(); if (!err[e.target.name]) { marcar(e.target.name, ''); } }
  });

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    errorGeneral.textContent = '';
    var errores = validar(), primero = Object.keys(errores)[0];
    if (primero) { (primero === 'acepta' ? form.acepta : form[primero]).focus(); return; }

    botonEnviar.disabled = true;
    botonEnviar.querySelector('[data-texto]').textContent = 'Enviando…';
    /* El sistema responde JSON: 200 { ok, mensaje, nombre }, 422 { errors } con
       los errores de validación por campo, 429 si hubo demasiados intentos. */
    var token = document.querySelector('meta[name="csrf-token"]').content;
    fetch(form.action, { method: 'POST', body: new FormData(form), credentials: 'same-origin',
                         headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token } })
      .then(function (r) { return r.json().catch(function () { return {}; }).then(function (d) { return { estado: r.status, d: d }; }); })
      .then(function (res) {
        var d = res.d;
        if (res.estado === 200 && d.ok) {
          exito.querySelector('[data-nombre]').textContent = d.nombre || form.nombre.value.trim().split(' ')[0];
          exito.querySelector('[data-mensaje]').textContent = d.mensaje;
          form.hidden = true; exito.hidden = false;
          exito.querySelector('[data-cerrar]').focus();
          form.reset();
        } else if (res.estado === 422 && d.errors) {
          Object.keys(d.errors).forEach(function (campo) {
            if (form.querySelector('[data-error=' + campo + ']') || campo === 'acepta') { marcar(campo, d.errors[campo][0]); }
            else { errorGeneral.textContent = d.errors[campo][0]; }
          });
        } else if (res.estado === 429) {
          errorGeneral.textContent = 'Demasiados intentos. Espera unos minutos y vuelve a intentarlo.';
        } else if (res.estado === 419) {
          errorGeneral.textContent = 'La página expiró. Recárgala e inténtalo de nuevo.';
        } else {
          errorGeneral.textContent = 'No pudimos guardar tu suscripción en este momento. Inténtalo más tarde.';
        }
      })
      .catch(function () { errorGeneral.textContent = 'No pudimos conectar con el servidor. Revisa tu conexión e inténtalo de nuevo.'; })
      .then(function () { botonEnviar.disabled = false; botonEnviar.querySelector('[data-texto]').textContent = 'Suscribirme'; });
  });
})();
</script>

</body>
</html>
@endverbatim
