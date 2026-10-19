/* ==========================================================================
   REVISTA FCA UNAM · premium_ui.js
   1. Filtros desplegables del Acervo Histórico (año y tema, combinables)
   2. Pestañas de áreas del "10 de 10"
   Vanilla JS puro · sin jQuery
   ========================================================================== */

(function () {
  'use strict';

  function init() {
    console.log('[Emprendedores FCA] premium_ui.js inicializado');

    initFiltrosHistorico();
    initAreas();
  }

  /* ----------------------------------------------------------------------
     1. FILTROS DEL HISTÓRICO
     Dos desplegables discretos. El estado de cada uno se combina con AND:
     una edición se muestra sólo si pasa año Y tema.
     ---------------------------------------------------------------------- */
  function initFiltrosHistorico() {
    var grid = document.getElementById('gridHistorico');
    if (!grid) { return; }

    var filtros  = document.querySelectorAll('.filtro');
    var items    = grid.querySelectorAll('[data-ed-anio]');
    /* data-ed-temas trae TODAS las áreas del número, delimitadas por barras:
       "|finanzas|comunicacion|". Se compara por contención y no por igualdad
       porque una edición pertenece a varias áreas a la vez. Las barras de los
       extremos evitan que "comunicacion" case dentro de otro slug más largo. */
    var vacio    = document.getElementById('sinResultados');
    var contador = document.getElementById('contadorEdiciones');

    var estado = { anio: 'todos', tema: 'todos' };

    /* -- Aplicar el filtro al grid -- */
    function aplicar() {
      var visibles = 0;

      for (var i = 0; i < items.length; i++) {
        var item = items[i];
        var temas    = item.getAttribute('data-ed-temas') || '';
        var pasaAnio = (estado.anio === 'todos' || estado.anio === item.getAttribute('data-ed-anio'));
        var pasaTema = (estado.tema === 'todos' || temas.indexOf('|' + estado.tema + '|') !== -1);

        if (pasaAnio && pasaTema) {
          item.classList.remove('oculto');
          visibles++;
        } else {
          item.classList.add('oculto');
        }
      }

      if (vacio) {
        vacio.className = (visibles === 0) ? 'sin-resultados visible' : 'sin-resultados';
      }

      if (contador) {
        contador.textContent = visibles + (visibles === 1 ? ' edición' : ' ediciones');
      }

      console.log('[Histórico] año=' + estado.anio + ' tema=' + estado.tema + ' · ' + visibles + ' visibles');
    }

    /* -- Cerrar todos los desplegables -- */
    function cerrarTodos() {
      for (var i = 0; i < filtros.length; i++) {
        filtros[i].classList.remove('abierto');
        var b = filtros[i].querySelector('.filtro__boton');
        if (b) { b.setAttribute('aria-expanded', 'false'); }
      }
    }

    /* -- Conectar cada desplegable -- */
    for (var f = 0; f < filtros.length; f++) {
      (function (filtro) {

        var boton    = filtro.querySelector('.filtro__boton');
        var etiqueta = filtro.querySelector('.filtro__texto');
        var opciones = filtro.querySelectorAll('.filtro__opcion');
        var grupo    = filtro.getAttribute('data-grupo');   // 'anio' o 'tema'
        var base     = filtro.getAttribute('data-etiqueta'); // 'Año' o 'Tema'

        // Abrir / cerrar
        boton.addEventListener('click', function (e) {
          e.stopPropagation();
          var estabaAbierto = filtro.classList.contains('abierto');
          cerrarTodos();
          if (!estabaAbierto) {
            filtro.classList.add('abierto');
            boton.setAttribute('aria-expanded', 'true');
          }
        });

        // Elegir una opción
        for (var o = 0; o < opciones.length; o++) {
          (function (opcion) {
            opcion.addEventListener('click', function () {
              var valor = opcion.getAttribute('data-valor');

              estado[grupo] = valor;

              for (var k = 0; k < opciones.length; k++) {
                opciones[k].classList.remove('activo');
              }
              opcion.classList.add('activo');

              // El botón muestra el valor elegido y se marca en rojo
              if (valor === 'todos') {
                etiqueta.textContent = base;
                boton.classList.remove('con-valor');
              } else {
                etiqueta.textContent = opcion.textContent;
                boton.classList.add('con-valor');
              }

              cerrarTodos();
              aplicar();
            });
          })(opciones[o]);
        }

      })(filtros[f]);
    }

    // Clic fuera o tecla Escape cierran los desplegables
    document.addEventListener('click', cerrarTodos);

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') { cerrarTodos(); }
    });

    aplicar();
  }

  /* ----------------------------------------------------------------------
     2. ÁREAS DEL 10 DE 10
     ---------------------------------------------------------------------- */
  function initAreas() {
    var tabs = document.querySelectorAll('.area-tab');
    if (tabs.length === 0) { return; }

    function activar(tab) {
      var destino = tab.getAttribute('data-panel');

      for (var i = 0; i < tabs.length; i++) {
        tabs[i].classList.remove('activo');
        tabs[i].setAttribute('aria-selected', 'false');
        tabs[i].setAttribute('tabindex', '-1');
      }

      var paneles = document.querySelectorAll('.area-panel');
      for (var j = 0; j < paneles.length; j++) {
        paneles[j].classList.remove('activo');
        paneles[j].setAttribute('hidden', 'hidden');
      }

      tab.classList.add('activo');
      tab.setAttribute('aria-selected', 'true');
      tab.setAttribute('tabindex', '0');

      var panel = document.getElementById(destino);
      if (panel) {
        panel.classList.add('activo');
        panel.removeAttribute('hidden');
      }

      console.log('[10 de 10] Área activa:', destino);
    }

    for (var k = 0; k < tabs.length; k++) {
      (function (tab, indice) {

        tab.addEventListener('click', function () { activar(tab); });

        // Navegación con flechas del teclado
        tab.addEventListener('keydown', function (e) {
          var siguiente = -1;

          if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
            siguiente = (indice + 1) % tabs.length;
          } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
            siguiente = (indice - 1 + tabs.length) % tabs.length;
          }

          if (siguiente !== -1) {
            e.preventDefault();
            activar(tabs[siguiente]);
            tabs[siguiente].focus();
          }
        });

      })(tabs[k], k);
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
