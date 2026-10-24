/* ==========================================================================
   REVISTA FCA UNAM · interacciones.js
   1. Pestañas verticales de Lineamientos (sin scroll)
   2. Reproductor simulado de Podcasts
   3. Microinteracciones de apoyo
   Vanilla JS puro · sin jQuery
   ========================================================================== */

(function () {
  'use strict';

  function init() {
    console.log('[Emprendedores FCA] interacciones.js inicializado');

    initTabsLineamientos();
    initYouTube();
    initBento();
    initConsultas();
  }

  /* ----------------------------------------------------------------------
     Consultorio: un clic en cualquier parte de la tarjeta abre o cierra la
     respuesta (el <details> sigue funcionando solo, sin JavaScript). No se
     toca si el clic fue en un enlace, en el propio resumen o al seleccionar
     texto de la respuesta abierta.
     ---------------------------------------------------------------------- */
  function initConsultas() {
    document.querySelectorAll('.consulta[data-tarjeta]').forEach(function (tarjeta) {
      var detalle = tarjeta.querySelector('details');
      if (!detalle) { return; }
      tarjeta.addEventListener('click', function (evento) {
        if (evento.target.closest('a, summary, button, .consulta__cuerpo')) { return; }
        if (String(window.getSelection && window.getSelection()).trim() !== '') { return; }
        detalle.open = !detalle.open;
      });
    });
  }

  /* ----------------------------------------------------------------------
     1. PESTAÑAS DE LINEAMIENTOS
     Bootstrap activa los tabs por data-bs-toggle. Aquí sólo añadimos:
     - navegación con flechas del teclado
     - devolver la vista al inicio del panel al cambiar de sección
       (evita quedar a media altura tras leer una pestaña larga)
     ---------------------------------------------------------------------- */
  function initTabsLineamientos() {
    var tabs = document.querySelectorAll('.pills-lux [data-bs-toggle="pill"]');
    if (tabs.length === 0) { return; }

    var contenedor = document.getElementById('bloqueLineamientos');

    for (var i = 0; i < tabs.length; i++) {
      (function (tab, indice) {

        // Al mostrarse una pestaña, alineamos el bloque con la ventana
        tab.addEventListener('shown.bs.tab', function () {
          console.log('[Lineamientos] Sección:', tab.textContent.replace(/\s+/g, ' ').trim());

          if (!contenedor) { return; }

          var arriba = contenedor.getBoundingClientRect().top;

          // Sólo corrige si el bloque quedó por encima del borde superior
          if (arriba < 0) {
            window.scrollTo({
              top: window.scrollY + arriba - 100,
              behavior: 'smooth'
            });
          }
        });

        tab.addEventListener('keydown', function (e) {
          var siguiente = -1;

          if (e.key === 'ArrowDown' || e.key === 'ArrowRight') {
            siguiente = (indice + 1) % tabs.length;
          } else if (e.key === 'ArrowUp' || e.key === 'ArrowLeft') {
            siguiente = (indice - 1 + tabs.length) % tabs.length;
          }

          if (siguiente !== -1) {
            e.preventDefault();
            tabs[siguiente].click();
            tabs[siguiente].focus();
          }
        });

      })(tabs[i], i);
    }
  }

  /* ----------------------------------------------------------------------
     2. PODCASTS DE YOUTUBE · fachada ligera

     Cada .yt-marco[data-yt] muestra sólo la miniatura. El iframe se inserta
     al pulsar (ratón, Enter o Espacio). Así la página no arrastra el peso de
     varios reproductores de YouTube en la carga inicial, y no se envía nada
     a Google hasta que la persona decide ver el video.

     Se usa youtube-nocookie.com y autoplay=1 porque el clic ya fue explícito.
     ---------------------------------------------------------------------- */
  function initYouTube() {
    var marcos = document.querySelectorAll('.yt-marco[data-yt]');
    if (marcos.length === 0) { return; }

    function incrustar(marco) {
      var id = marco.getAttribute('data-yt');
      if (!id || marco.classList.contains('yt-activo')) { return; }

      var iframe = document.createElement('iframe');
      iframe.src = 'https://www.youtube-nocookie.com/embed/' + id +
                   '?autoplay=1&rel=0&modestbranding=1';
      iframe.title = marco.getAttribute('aria-label') || 'Reproductor de YouTube';
      iframe.allow = 'accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture';
      iframe.setAttribute('allowfullscreen', 'allowfullscreen');
      iframe.setAttribute('loading', 'lazy');
      iframe.frameBorder = '0';

      marco.innerHTML = '';
      marco.appendChild(iframe);
      marco.classList.add('yt-activo');
      marco.removeAttribute('role');
      marco.removeAttribute('tabindex');

      console.log('[Podcast] Reproduciendo YouTube:', id);
    }

    for (var i = 0; i < marcos.length; i++) {
      (function (marco) {

        marco.addEventListener('click', function () { incrustar(marco); });

        marco.addEventListener('keydown', function (e) {
          if (e.key === 'Enter' || e.key === ' ' || e.key === 'Spacebar') {
            e.preventDefault();
            incrustar(marco);
          }
        });

      })(marcos[i]);
    }
  }

  /* ----------------------------------------------------------------------
     3. BENTO DE TEMÁTICAS
     El CSS hace la revelación al hover. Aquí sólo se permite el foco por
     teclado para que el detalle también se abra sin ratón.
     ---------------------------------------------------------------------- */
  function initBento() {
    var bloques = document.querySelectorAll('.bento');

    for (var i = 0; i < bloques.length; i++) {
      if (!bloques[i].hasAttribute('tabindex')) {
        bloques[i].setAttribute('tabindex', '0');
      }
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
