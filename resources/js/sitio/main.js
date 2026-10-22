/* ==========================================================================
   REVISTA EMPRENDEDORES · FCA UNAM
   main.js — Vanilla JS puro (sin jQuery)
   ========================================================================== */

(function () {
  'use strict';

  /* ----------------------------------------------------------------------
     DICCIONARIO DE TRADUCCIÓN (claves = atributo data-i18n en el HTML)
     ---------------------------------------------------------------------- */
  var TEXTOS = {

    es: {
      'nav.inicio': 'Inicio', 'nav.historico': 'Histórico', 'nav.tematicas': 'Temáticas',
      'nav.podcasts': 'Podcasts', 'nav.diezdediez': '10 de 10', 'nav.webinar': 'Webinar',
      'nav.pymehablan': 'Las Pyme Hablan', 'nav.consultorio': 'Consultorio',
      'hero.periodo': 'ENERO - FEBRERO 2026',
      'hero.pdf': 'Descargar el número', 'hero.ebook': 'Ver en eBook',
      'feed.label': 'En este número', 'post.leer': 'Leer artículo',
      'curso.label': 'Curso destacado FCA/UNAM:',
      'widget.busqueda': 'Búsqueda', 'widget.idioma': 'Idioma',
      'widget.informacion': 'Información', 'widget.incluida': 'Incluida en',
      'info.lectores': 'Para lectores', 'info.autores': 'Para autores',
      'cta.titulo': 'Convocatoria abierta',
      'cta.texto': 'Recibimos artículos de divulgación durante todo el año. Envía tu contribución al comité editorial.',
      'cta.boton': 'Enviar artículo',
      'idioma.aviso': 'Idioma actualizado a español.',
      'busqueda.marcador': 'Buscar...'
    },

    en: {
      'nav.inicio': 'Home', 'nav.historico': 'Archive', 'nav.tematicas': 'Topics',
      'nav.podcasts': 'Podcasts', 'nav.diezdediez': '10 of 10', 'nav.webinar': 'Webinar',
      'nav.pymehablan': 'SMEs Speak', 'nav.consultorio': 'Advice desk',
      'hero.periodo': 'JANUARY - FEBRUARY 2026',
      'hero.pdf': 'Download the issue', 'hero.ebook': 'Read as eBook',
      'feed.label': 'In this issue', 'post.leer': 'Read article',
      'curso.label': 'Featured FCA/UNAM program:',
      'widget.busqueda': 'Search', 'widget.idioma': 'Language',
      'widget.informacion': 'Information', 'widget.incluida': 'Indexed in',
      'info.lectores': 'For readers', 'info.autores': 'For authors',
      'cta.titulo': 'Open call for papers',
      'cta.texto': 'We receive outreach articles all year round. Send your contribution to the editorial board.',
      'cta.boton': 'Submit article',
      'idioma.aviso': 'Language switched to English.',
      'busqueda.marcador': 'Search...'
    },

    pt: {
      'nav.inicio': 'Início', 'nav.historico': 'Histórico', 'nav.tematicas': 'Temáticas',
      'nav.podcasts': 'Podcasts', 'nav.diezdediez': '10 de 10', 'nav.webinar': 'Webinar',
      'nav.pymehablan': 'As PME Falam', 'nav.consultorio': 'Consultório',
      'hero.periodo': 'JANEIRO - FEVEREIRO 2026',
      'hero.pdf': 'Baixar a edição', 'hero.ebook': 'Ler em eBook',
      'feed.label': 'Nesta edição', 'post.leer': 'Ler artigo',
      'curso.label': 'Curso destacado FCA/UNAM:',
      'widget.busqueda': 'Busca', 'widget.idioma': 'Idioma',
      'widget.informacion': 'Informação', 'widget.incluida': 'Indexada em',
      'info.lectores': 'Para leitores', 'info.autores': 'Para autores',
      'cta.titulo': 'Convocatória aberta',
      'cta.texto': 'Recebemos artigos de divulgação durante todo o ano. Envie sua contribuição ao comitê editorial.',
      'cta.boton': 'Enviar artigo',
      'idioma.aviso': 'Idioma alterado para português.',
      'busqueda.marcador': 'Buscar...'
    }

  };

  /* ----------------------------------------------------------------------
     INICIALIZACIÓN
     ---------------------------------------------------------------------- */
  function init() {
    console.log('[Emprendedores FCA] main.js inicializado');

    initCabeceraScroll();
    initIdioma();
    initDropdownHover();
    initBusqueda();
    initAnio();
  }

  /* ----------------------------------------------------------------------
     CABECERA: blanca en reposo, negra al desplazar

     La cabecera es sticky y nunca abandona la pantalla. En reposo es blanca
     con filetes negros; en cuanto la lectura arranca vira a negro, se separa
     del contenido y los logos se compactan (el detalle visual lo resuelve
     .cabecera--oscura en styles.css).

     El umbral es deliberadamente corto: el cambio debe acompañar el inicio
     del desplazamiento, no esperar a rebasar el bloque de logos.
     ---------------------------------------------------------------------- */
  function initCabeceraScroll() {
    var cabecera = document.querySelector('.cabecera');
    var logos    = document.querySelector('.bloque-logos');
    if (!cabecera) { return; }

    var escritorio = window.matchMedia('(min-width: 992px)');
    var umbral     = 12;
    var esperando  = false;

    /* En escritorio el navbar se despega justo al rebasar el bloque de logos:
       ese es el umbral natural, y se mide en vez de codificarse a mano para
       que siga siendo correcto si cambia el tamaño de los logos.
       En móvil la cabecera entera permanece, así que basta con arrancar el
       desplazamiento para justificar el cambio de estado. */
    function medir() {
      umbral = (escritorio.matches && logos) ? logos.offsetHeight : 12;
    }

    function evaluar() {
      if (window.scrollY > umbral) {
        cabecera.classList.add('cabecera--oscura');
      } else {
        cabecera.classList.remove('cabecera--oscura');
      }
      esperando = false;
    }

    // requestAnimationFrame evita recalcular en cada píxel de scroll
    function onScroll() {
      if (!esperando) {
        esperando = true;
        window.requestAnimationFrame(evaluar);
      }
    }

    medir();
    evaluar(); // por si la página carga ya desplazada

    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', function () { medir(); evaluar(); });
  }

  /* ----------------------------------------------------------------------
     TRADUCTOR
     ---------------------------------------------------------------------- */
  function traducir(idioma) {
    var diccionario = TEXTOS[idioma];
    if (!diccionario) { return 0; }

    var nodos = document.querySelectorAll('[data-i18n]');
    var cambiados = 0;

    for (var i = 0; i < nodos.length; i++) {
      var clave = nodos[i].getAttribute('data-i18n');
      if (diccionario[clave]) {
        nodos[i].innerHTML = diccionario[clave];
        cambiados++;
      }
    }

    var buscador = document.getElementById('inputBusqueda');
    if (buscador && diccionario['busqueda.marcador']) {
      buscador.setAttribute('placeholder', diccionario['busqueda.marcador']);
    }

    document.documentElement.setAttribute('lang', idioma);
    return cambiados;
  }

  function initIdioma() {
    var select = document.getElementById('selectIdioma');
    if (!select) { return; }

    var inicial = leerParametro('lang');
    if (inicial && TEXTOS[inicial]) {
      select.value = inicial;
      traducir(inicial);
    }

    select.addEventListener('change', function () {
      var idioma = select.value;
      var cambiados = traducir(idioma);

      console.log('[Idioma] ' + idioma + ' · ' + cambiados + ' textos actualizados');

      if (window.history && window.history.replaceState) {
        window.history.replaceState(null, '', '?lang=' + idioma);
      }

      mostrarAviso(TEXTOS[idioma]['idioma.aviso']);
    });
  }

  function mostrarAviso(mensaje) {
    var aviso = document.getElementById('idiomaAviso');
    if (!aviso) { return; }

    aviso.textContent = mensaje;
    aviso.className = 'idioma-aviso visible';

    window.setTimeout(function () {
      aviso.className = 'idioma-aviso';
    }, 3000);
  }

  function leerParametro(nombre) {
    var partes = window.location.search.substring(1).split('&');
    for (var i = 0; i < partes.length; i++) {
      var par = partes[i].split('=');
      if (par[0] === nombre) { return decodeURIComponent(par[1] || ''); }
    }
    return '';
  }

  /* ----------------------------------------------------------------------
     DROPDOWNS AL PASAR EL CURSOR (sólo escritorio)
     ---------------------------------------------------------------------- */
  function initDropdownHover() {
    var esEscritorio = window.matchMedia('(min-width: 992px)');
    var items = document.querySelectorAll('.nav-lux .nav-item.dropdown');

    for (var i = 0; i < items.length; i++) {
      (function (item) {
        var toggle = item.querySelector('.dropdown-toggle');
        if (!toggle) { return; }

        item.addEventListener('mouseenter', function () {
          if (esEscritorio.matches) { toggle.setAttribute('aria-expanded', 'true'); }
        });

        item.addEventListener('mouseleave', function () {
          if (esEscritorio.matches) { toggle.setAttribute('aria-expanded', 'false'); }
        });

        toggle.addEventListener('click', function (e) {
          if (esEscritorio.matches) { e.preventDefault(); }
        });
      })(items[i]);
    }
  }

  /* ----------------------------------------------------------------------
     BÚSQUEDA: el formulario va al buscador (/buscador?q=...); solo se evita
     enviar una búsqueda vacía.
     ---------------------------------------------------------------------- */
  function initBusqueda() {
    var form = document.getElementById('formBusqueda');
    if (!form) { return; }

    form.addEventListener('submit', function (e) {
      var input = document.getElementById('inputBusqueda');
      var termino = input ? input.value.replace(/^\s+|\s+$/g, '') : '';

      if (termino === '') {
        e.preventDefault();
        if (input) { input.focus(); }
      }
    });
  }

  /* ----------------------------------------------------------------------
     AÑO DINÁMICO
     ---------------------------------------------------------------------- */
  /* CUIDADO: este selector debe ser específico.
     Antes usaba [data-anio], el mismo atributo que las tarjetas del Acervo
     Histórico emplean para filtrar. El bucle sobrescribía el textContent de
     las 33 tarjetas y borraba portadas, botones de PDF y eBook.
     El atributo del footer es ahora data-anio-actual, exclusivo del año. */
  function initAnio() {
    var nodos = document.querySelectorAll('[data-anio-actual]');
    var anio = new Date().getFullYear();
    for (var i = 0; i < nodos.length; i++) {
      nodos[i].textContent = anio;
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
