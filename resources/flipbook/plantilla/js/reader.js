/**
 * ============================================================================
 * EMPRENDEDORES — Reader Engine (StPageFlip Edition · v4)
 * ============================================================================
 * js/reader.js | jQuery 1.12.4 + StPageFlip 2.0.7 (MIT License)
 *
 * 100% legal para distribución comercial.
 *
 * v4 (sobre el motor corregido de Consultorio Fiscal):
 *   ✅ Zoom con hoja en ALTA RESOLUCIÓN: al ampliar se cambia la imagen de
 *      las hojas visibles por su versión de img/hd/ (data-hd)
 *   ✅ Teclas + / − para el zoom
 *   ✅ Doble toque fiable: sólo cuenta toques reales (sin arrastre), así dos
 *      swipes rápidos ya no disparan el zoom
 *   ✅ Miniaturas ligeras (img/thumbs/) que se piden al abrir el panel
 *   ✅ aria-hidden de los paneles sincronizado con su estado
 *
 * Heredado de Consultorio (v3):
 * CORRECCIONES v3:
 *   ✅ Spread real de 2 páginas en el contenido (antes mostraba 1)
 *   ✅ Robustez en Android gama media (sin destroy/recreate frágil)
 *   ✅ Resize estable con debounce + recreación controlada
 *   ✅ Altura calculada sin depender de 100vh (barra URL móvil)
 *   ✅ Preloader que nunca bloquea más de lo necesario
 *   ✅ Manejo defensivo de errores de la librería
 * ============================================================================
 */

;(function ($, W, D) {
    'use strict';

    /* ==================================================================
       CONSTANTES
       ================================================================== */
    var C = {
        BP:            768,    // breakpoint móvil/desktop
        WHEEL_WAIT:    450,    // ms entre cambios con scroll
        SWIPE_MIN:     40,     // px mínimos para swipe
        PAGE_RATIO:    0.7071, // fallback A4; se recalcula con la portada real
        ZOOM_TIP_MS:   1800,
        PANEL_LOCK:    350,
        FLIP_DURATION: 700,
        MIN_PAGE_W:    300,    // px mínimos por hoja para admitir pliego doble
        RESIZE_DEBOUNCE: 250,  // ms debounce para resize
        PRELOAD_PRIMARY: 4,    // páginas críticas a decodificar antes de mostrar
        PRELOAD_TIMEOUT: 4000, // ms máximo de espera del preloader
        PRELOAD_MIN:     450   // ms mínimos en pantalla antes de empezar a salir
    };

    var total  = (W.CF && W.CF.total) || 0;
    var revNum = (W.CF && W.CF.num)   || 0;
    var BK_KEY = 'cf_bookmarks_' + revNum;

    /* ==================================================================
       ESTADO
       ================================================================== */
    var S = {
        ready:           false,
        portrait:        false,   // true = 1 página (móvil); false = 2 páginas (desktop)
        zoomLevel:       0,
        wheelOk:         true,
        dragging:        false,
        dx0: 0, dy0: 0, sl0: 0, st0: 0,
        tx0: 0, ty0: 0,
        searchOpen:      false,
        indexOpen:       false,
        panelOpenedAt:   0,
        bookmarks:       {},
        isTransitioning: false,
        lastW:           0,       // último ancho de viewport (para detectar cambios reales)
        lastH:           0,
        resizeTimer:     null
    };

    var pageFlip = null;
    var ORIGINAL_HTML = null;     // snapshot del DOM de páginas
    var T_ARRANQUE = (W.Date && W.Date.now) ? W.Date.now() : 0;  // para la permanencia mínima

    /* ==================================================================
       CACHE DOM
       ================================================================== */
    var $mag, $stage, $pre, $zw, $tip,
        $bPrev, $bNext, $bZoom, $bFirst, $bSearch, $bIndex, $bBookmark,
        $pgNow, $pgMax,
        $sPanel, $sInput, $sGo, $sClose, $sThumbs,
        $iPanel, $iClose, $iLinks,
        $overlay;

    /* ==================================================================
       HELPERS
       ================================================================== */
    function currentPage() {
        if (!S.ready || !pageFlip) return 1;
        try { return pageFlip.getCurrentPageIndex() + 1; }
        catch (e) { return 1; }
    }

    /** Altura real disponible — usa visualViewport si existe (móvil) */
    function viewportSize() {
        var vv = W.visualViewport;
        var wW = vv ? vv.width  : (W.innerWidth  || D.documentElement.clientWidth  || 360);
        var wH = vv ? vv.height : (W.innerHeight || D.documentElement.clientHeight || 640);
        return { w: wW, h: wH };
    }

    /* ==================================================================
       1. MEDIR ESPACIO  →  dimensiones de UNA página
       ==================================================================
       StPageFlip recibe el tamaño de UNA página. En landscape (desktop)
       muestra 2 lado a lado automáticamente; en portrait (móvil) muestra 1.
       El truco para que el "spread" se vea completo es calcular el ancho
       de página tal que 2 páginas + alto quepan en pantalla.
       ================================================================== */
    /* ── Cuándo se muestra UNA sola página ────────────────────────────────
       El estándar decide sólo por ancho (< 768px). Eso deja dos casos
       incómodos de leer:

         · Tableta en vertical (768x1024): entra en modo dos páginas y cada
           una queda en ~380px de ancho dentro de una pantalla que sobra por
           arriba y por abajo. El texto sale diminuto.
         · Teléfono en horizontal (844x390): dos páginas de ~200px.

       Se añaden dos condiciones al ancho, que es lo que de verdad determina
       si un pliego doble se lee bien:

         1. Si la pantalla es más alta que ancha, una sola página.
         2. Si cada hoja bajaría de MIN_PAGE_W de ancho, una sola página:
            vale más una hoja legible que dos que no lo son.

       Vive en una sola función porque measure() y handleResize() tienen que
       responder lo mismo; si divergen, girar el dispositivo deja de recrear
       el pliego y el visor se queda en el modo anterior.                    */
    function isPortraitMode(vp, availW, availH) {
        if (vp.w < C.BP)  return true;
        if (vp.h > vp.w)  return true;
        if (availW != null && availH != null) {
            var pageW = Math.min(Math.round(availH * C.PAGE_RATIO),
                                 Math.floor(availW / 2));
            if (pageW < C.MIN_PAGE_W) return true;
        }
        return false;
    }

    function measure() {
        var vp   = viewportSize();
        var hdrH = (D.getElementById('hdr')     || {}).offsetHeight || 50;
        var tbH  = (D.getElementById('toolbar')  || {}).offsetHeight || 48;
        var availW = Math.max(200, vp.w - 8);
        var availH = Math.max(160, vp.h - hdrH - tbH - 8);

        var portrait = isPortraitMode(vp, availW, availH);

        // Altura candidata = todo el alto disponible
        var pageH = availH;
        var pageW = Math.round(pageH * C.PAGE_RATIO);

        if (portrait) {
            // 1 sola página: limitar por ancho disponible
            if (pageW > availW) {
                pageW = availW;
                pageH = Math.round(pageW / C.PAGE_RATIO);
            }
        } else {
            // 2 páginas lado a lado: el spread no puede exceder availW
            if (pageW * 2 > availW) {
                pageW = Math.floor(availW / 2);
                pageH = Math.round(pageW / C.PAGE_RATIO);
            }
        }

        return {
            w:        Math.max(pageW, 200),
            h:        Math.max(pageH, 260),
            portrait: portrait
        };
    }

    /* ==================================================================
       2. SNAPSHOT / RESTORE del DOM de páginas
       ==================================================================
       StPageFlip.destroy() ELIMINA el elemento raíz (#magazine) del DOM.
       Por eso guardamos (a) el HTML interno original de las páginas y
       (b) una referencia al padre estable (#zoom-wrap), para poder
       reconstruir #magazine limpio antes de cada recreación.
       ================================================================== */
    function snapshotPages() {
        if (ORIGINAL_HTML === null) {
            var mag = D.getElementById('magazine');
            if (mag) ORIGINAL_HTML = mag.innerHTML;
        }
    }

    /** Garantiza que exista un #magazine limpio dentro de #zoom-wrap,
        con las páginas originales restauradas. Devuelve el elemento. */
    function rebuildMagazine() {
        var parent = D.getElementById('zoom-wrap');
        var mag = D.getElementById('magazine');
        if (!mag) {
            // destroy() lo eliminó: lo recreamos
            mag = D.createElement('div');
            mag.id = 'magazine';
            parent.appendChild(mag);
        }
        // Restaurar páginas originales (limpia cualquier wrapper de StPageFlip)
        if (ORIGINAL_HTML !== null) {
            mag.innerHTML = ORIGINAL_HTML;
        }
        return mag;
    }

    /* ==================================================================
       3. CREAR INSTANCIA STPAGEFLIP
       ================================================================== */
    function createInstance(dim, startIndex) {
        // Reconstruir #magazine limpio (necesario tras destroy en resize)
        var el = rebuildMagazine();
        $mag = $(el);  // re-cachear referencia jQuery

        pageFlip = new W.St.PageFlip(el, {
            width:            dim.w,
            height:           dim.h,
            size:             'fixed',
            minWidth:         200,
            maxWidth:         3000,
            minHeight:        260,
            maxHeight:        3200,
            maxShadowOpacity: 0.5,
            showCover:        true,          // portada y contraportada solas
            mobileScrollSupport: false,
            usePortrait:      dim.portrait,  // true=1 pág, false=2 págs
            startPage:        startIndex || 0,
            drawShadow:       true,
            flippingTime:     C.FLIP_DURATION,
            useMouseEvents:   true,
            swipeDistance:    C.SWIPE_MIN,
            clickEventForward: true,
            disableFlipByClick: false
        });

        var loadFn = (typeof pageFlip.loadFromHTML === 'function')
            ? 'loadFromHTML' : 'loadFromHtml';
        pageFlip[loadFn](el.querySelectorAll('.pg'));

        pageFlip.on('flip', function (e) {
            onPageTurned((e.data | 0) + 1);
        });
        pageFlip.on('changeState', function (e) {
            if (e.data === 'flipping')      S.isTransitioning = true;
            else if (e.data === 'read')     S.isTransitioning = false;
        });

        S.portrait = dim.portrait;
    }

    /* ==================================================================
       4. INIT
       ================================================================== */
    function initPageFlip() {
        if (!W.St || !W.St.PageFlip) {
            console.error('[Reader] StPageFlip no disponible.');
            // Fallback: mostrar primera imagen estática para no dejar pantalla negra
            $mag.css({ 'overflow': 'auto' });
            return false;
        }
        if (total === 0) return false;

        snapshotPages();
        var dim = measure();
        try {
            createInstance(dim, 0);
        } catch (err) {
            console.error('[Reader] Error al crear StPageFlip:', err);
            return false;
        }

        S.ready = true;
        S.lastW = viewportSize().w;
        S.lastH = viewportSize().h;
        refreshUI(1);
        return true;
    }

    function onPageTurned(pg) {
        refreshUI(pg);
        if (S.zoomLevel > 0) upgradeVisibleToHD(pg);
        var look = S.portrait ? pg + 1 : pg + 2;
        if (look <= total) {
            ensureDecoded(look);
            if (!S.portrait && look + 1 <= total) ensureDecoded(look + 1);
        }
    }

    /* ==================================================================
       5. RESIZE  (debounce + recreación solo si cambió de verdad)
       ================================================================== */
    function handleResize() {
        if (!S.ready) return;

        var vp = viewportSize();
        // Ignorar microcambios (barra URL Android sube/baja ~50-100px de alto)
        var dwChanged = Math.abs(vp.w - S.lastW) > 2;
        var hdrH = (D.getElementById('hdr')    || {}).offsetHeight || 50;
        var tbH  = (D.getElementById('toolbar') || {}).offsetHeight || 48;
        var portraitNow = isPortraitMode(vp,
                              Math.max(200, vp.w - 8),
                              Math.max(160, vp.h - hdrH - tbH - 8));
        var portraitChanged = portraitNow !== S.portrait;

        // Solo recreamos si cambió el ancho significativamente o cambió de modo.
        // Los cambios de SOLO altura (barra URL) NO recrean → evita parpadeos y cuelgues en Android.
        if (!dwChanged && !portraitChanged) return;

        if (S.zoomLevel > 0) zoomReset();
        closeAllPanels();

        var pg  = currentPage();
        var dim = measure();

        S.lastW = vp.w;
        S.lastH = vp.h;
        S.isTransitioning = true;
        $zw.addClass('is-swapping');   // fade sobre el padre estable

        W.setTimeout(function () {
            // Destruir instancia previa de forma segura (esto elimina #magazine)
            if (pageFlip) {
                try { pageFlip.destroy(); } catch (e) {}
                pageFlip = null;
            }
            // createInstance() reconstruye #magazine limpio dentro de #zoom-wrap
            try {
                createInstance(dim, pg - 1);
                refreshUI(pg);
            } catch (err) {
                console.error('[Reader] resize recreate falló:', err);
            }

            requestAnimationFrame(function () {
                $zw.removeClass('is-swapping');
                W.setTimeout(function () { S.isTransitioning = false; }, 300);
            });
        }, 200);
    }

    function onResize() {
        if (S.resizeTimer) W.clearTimeout(S.resizeTimer);
        S.resizeTimer = W.setTimeout(handleResize, C.RESIZE_DEBOUNCE);
    }

    /* ==================================================================
       6. UI
       ================================================================== */
    /* ------------------------------------------------------------------
       CENTRADO DEL PLIEGO
       ------------------------------------------------------------------
       Con showCover activo, la portada y —según la paginación— la hoja
       final se muestran SOLAS. StPageFlip las coloca en una mitad del
       bloque de dos páginas, así que el libro queda descuadrado respecto
       a la pantalla: la portada aparece corrida a la derecha.

       Aquí sólo se decide en qué mitad quedó la hoja; el desplazamiento
       (media página al lado contrario) lo hace el CSS con las clases
       .pliego-solo-der / .pliego-solo-izq sobre #stage.

       Reparto de pliegos con showCover:  [0] · [1,2] · [3,4] · …
       ------------------------------------------------------------------ */
    function ajustarCentrado(pg) {
        if (!$stage || !$stage.length) return;

        $stage.removeClass('pliego-solo-der pliego-solo-izq');

        // Retrato: una sola página, el flex del escenario ya la centra.
        if (S.portrait) return;
        // Con zoom manda el desplazamiento manual del usuario.
        if (S.zoomLevel > 0) return;

        var idx = (pg | 0) - 1;
        if (idx < 0) idx = 0;

        // Portada sola → ocupa la mitad DERECHA del bloque.
        if (idx === 0) {
            $stage.addClass('pliego-solo-der');
            return;
        }

        // Hoja final sin pareja → ocupa la mitad IZQUIERDA del bloque.
        var izq = (idx % 2 === 1) ? idx : (idx - 1);
        if (izq + 1 > total - 1) {
            $stage.addClass('pliego-solo-izq');
        }
    }

    function refreshUI(pg) {
        $pgNow.text(pg);
        $bPrev.attr('aria-hidden', pg <= 1     ? 'true' : 'false');
        $bNext.attr('aria-hidden', pg >= total ? 'true' : 'false');
        if ($sThumbs && $sThumbs.length) {
            $sThumbs.removeClass('is-current')
                    .filter('[data-pg="' + pg + '"]').addClass('is-current');
        }
        $bBookmark.toggleClass('is-bookmarked', !!S.bookmarks[pg]);
        ajustarCentrado(pg);
    }

    /* ==================================================================
       7. PRE-DECODE de imágenes
       ================================================================== */
    function findImg(pg) {
        var sel = '.pg[data-pg="' + pg + '"] .pg-img';
        return D.querySelector('#magazine ' + sel)
            || D.querySelector('.stf__wrapper ' + sel)
            || (D.querySelectorAll('.pg-img')[pg - 1] || null);
    }

    function ensureDecoded(pg) {
        var el = findImg(pg);
        if (!el) return Promise.resolve();
        if (el.complete && el.naturalWidth > 0) {
            return el.decode ? el.decode().catch(function () {}) : Promise.resolve();
        }
        return new Promise(function (resolve) {
            var done = false;
            function fin() {
                if (done) return; done = true;
                if (el.decode) el.decode().then(resolve).catch(resolve);
                else resolve();
            }
            el.addEventListener('load', fin, { once: true });
            el.addEventListener('error', fin, { once: true });
            if (el.loading === 'lazy') el.loading = 'eager';
            W.setTimeout(fin, 900);
        });
    }

    /* ------------------------------------------------------------------
       ALTA RESOLUCIÓN PARA EL ZOOM
       ------------------------------------------------------------------
       La hoja normal (img/) mide 1275 px de ancho: sobra para leer el
       pliego, pero a 3x en un monitor la hoja ocupa ~1700 px y se ve
       blanda. Cada <img> trae en data-hd su versión de 2040 px; al ampliar
       se cambia sólo en las hojas visibles, y únicamente cuando la nueva ya
       está decodificada, para que no haya un parpadeo en blanco.

       Una hoja que ya pasó a HD se queda así (el navegador la tiene en
       caché). Tras un resize el DOM se restaura con las hojas normales y el
       siguiente zoom las vuelve a subir.
       ------------------------------------------------------------------ */
    function upgradeToHD(pg) {
        if (pg < 1 || pg > total) return;
        var el = findImg(pg);
        if (!el) return;
        var hd = el.getAttribute('data-hd');
        if (!hd || el.getAttribute('data-hd-on') === '1') return;
        el.setAttribute('data-hd-on', '1');
        var pre = new Image();
        pre.onload = function () {
            var swap = function () { el.src = hd; };
            if (pre.decode) pre.decode().then(swap, swap); else swap();
        };
        pre.onerror = function () { el.removeAttribute('data-hd-on'); };
        pre.src = hd;
    }

    function upgradeVisibleToHD(pg) {
        pg = pg || currentPage();
        upgradeToHD(pg);
        // En pliego doble StPageFlip reporta la hoja IZQUIERDA; la otra visible
        // es la siguiente (con la portada sola, la 2 queda lista para el giro).
        if (!S.portrait) upgradeToHD(pg + 1);
    }

    /* ==================================================================
       8. NAVEGACIÓN
       ================================================================== */
    function goPage(pg) {
        if (!S.ready || !pageFlip) return;
        pg = parseInt(pg, 10);
        if (isNaN(pg) || pg < 1 || pg > total) return;
        if (pg === currentPage()) return;
        ensureDecoded(pg);
        if (!S.portrait) { ensureDecoded(pg + 1); ensureDecoded(pg - 1); }
        try { pageFlip.flip(pg - 1); } catch (e) {}
        if (S.zoomLevel > 0) zoomCenterScroll();
    }

    function goNext() {
        if (!S.ready || !pageFlip || S.isTransitioning) return;
        try { pageFlip.flipNext(); } catch (e) {}
        if (S.zoomLevel > 0) zoomCenterScroll();
    }
    function goPrev() {
        if (!S.ready || !pageFlip || S.isTransitioning) return;
        try { pageFlip.flipPrev(); } catch (e) {}
        if (S.zoomLevel > 0) zoomCenterScroll();
    }

    function bindNav() {
        $bPrev.on('click',  function (e) { e.preventDefault(); goPrev(); });
        $bNext.on('click',  function (e) { e.preventDefault(); goNext(); });
        $bFirst.on('click', function (e) { e.preventDefault(); goPage(1); });
    }

    function bindKeys() {
        $(D).on('keydown', function (e) {
            if (e.keyCode === 27) { closeAllPanels(); if (S.zoomLevel > 0) zoomReset(); return; }
            if (S.searchOpen || S.indexOpen) return;
            var tag = (e.target.tagName || '').toLowerCase();
            if (tag === 'input' || tag === 'textarea') return;
            if (e.keyCode === 37) { e.preventDefault(); goPrev(); }
            if (e.keyCode === 39) { e.preventDefault(); goNext(); }
            if (e.keyCode === 36) { e.preventDefault(); goPage(1); }
            if (e.keyCode === 35) { e.preventDefault(); goPage(total); }
            // + / − (teclado principal y numérico) para el zoom
            if (!e.ctrlKey && !e.metaKey) {
                if (e.key === '+' || e.key === '=' || e.keyCode === 107) { e.preventDefault(); zoomToLevel(S.zoomLevel + 1); }
                if (e.key === '-' || e.key === '_' || e.keyCode === 109) { e.preventDefault(); zoomToLevel(S.zoomLevel - 1); }
            }
        });
    }

    /* ==================================================================
       9. WHEEL
       ================================================================== */
    function bindWheel() {
        var ev = ('onwheel' in D) ? 'wheel'
               : ('onmousewheel' in D) ? 'mousewheel' : 'DOMMouseScroll';
        $stage[0].addEventListener(ev, function (e) {
            if (e.ctrlKey) return;  // Ctrl+rueda = zoom (lo maneja bindZoom)
            if (S.zoomLevel > 0 || !S.ready || S.searchOpen || S.indexOpen || S.isTransitioning) return;
            if (!S.wheelOk) return;
            var delta = (e.deltaY !== undefined) ? e.deltaY
                      : (e.wheelDelta !== undefined) ? -e.wheelDelta : (e.detail || 0);
            if (!delta) return;
            e.preventDefault();
            S.wheelOk = false;
            if (delta > 0) goNext(); else goPrev();
            W.setTimeout(function () { S.wheelOk = true; }, C.WHEEL_WAIT);
        }, { passive: false });
    }

    /* ==================================================================
       10. SWIPE
       ==================================================================
       Sin zoom lo gestiona StPageFlip, que ya distingue arrastre de toque.

       CON zoom NO se pasa página al deslizar: el mismo gesto es el que sirve
       para recorrer la hoja ampliada, y hacerlo cambiar de página convertía
       cualquier paneo horizontal que terminara a más de 40 px del origen en
       un salto de hoja. Es el choque que hacía el zoom incómodo.

       Con zoom activo, para cambiar de página están las flechas de la barra,
       las teclas ← →, y alejar con doble toque.
       ================================================================== */
    function bindSwipe() {
        /* Intencionadamente vacío. Se conserva la función porque el arranque
           la llama junto al resto de bindings; documentarlo aquí evita que
           alguien "arregle" la ausencia volviendo a introducir el conflicto. */
    }

    /* ==================================================================
       11. ZOOM  (mejorado)
       ==================================================================
       - 3 niveles: 0 = normal, 1 = 1.8x, 2 = 3x
       - Zoom centrado en el PUNTO donde el usuario hace click / doble-tap
       - Pinch-to-zoom con dos dedos en móvil
       - Arrastre para desplazarse cuando hay zoom
       - Ctrl + rueda para zoom fino en desktop
       El zoom se aplica con transform:scale sobre #zoom-wrap y el
       desplazamiento con scroll de #stage.
       ================================================================== */
    var ZOOM_SCALES = [1, 1.8, 3];

    function currentScale() { return ZOOM_SCALES[S.zoomLevel] || 1; }

    /** Aplica el nivel de zoom actual, opcionalmente anclado en (px,py),
        que son coordenadas de cliente (viewport).

        ── Por qué hay margen de compensación ──────────────────────────────
        `transform:scale()` NO cambia la caja de layout: el elemento sigue
        midiendo lo mismo para el padre. Con el origen al centro (el que traía
        el estándar) la mitad escalada se sale por arriba y por la izquierda,
        y esa zona no es alcanzable con scroll porque el scroll no llega a
        valores negativos. Resultado: con zoom sólo se podía recorrer la mitad
        inferior derecha de la página.

        Se corrige anclando el origen en la esquina superior izquierda y
        añadiendo margen derecho e inferior de (escala-1)·tamaño. El margen sí
        cuenta para el área desplazable del padre, así que #stage pasa a tener
        exactamente el ancho y alto del pliego escalado y se puede recorrer
        entero.                                                              */
    function applyZoom(px, py) {
        var el = $zw[0], st = $stage[0];

        // Anclaje: posición relativa (0..1) del punto DENTRO del pliego, medida
        // con la escala que hay ahora, antes de tocar nada.
        var pre = el.getBoundingClientRect();
        var rx, ry;
        if (px != null && pre.width && py != null && pre.height) {
            rx = (px - pre.left) / pre.width;
            ry = (py - pre.top)  / pre.height;
        } else {
            /* Sin punto (botón de la barra): se ancla en el centro, salvo
               cuando la hoja va sola en el pliego. Con showCover, la portada
               ocupa la mitad derecha del bloque de dos páginas y la última
               hoja suelta la izquierda; anclar al centro dejaba al lector
               mirando la mitad vacía. Se reutiliza la misma señal que ya
               calcula ajustarCentrado(). */
            rx = $stage.hasClass('pliego-solo-der') ? 0.75
               : $stage.hasClass('pliego-solo-izq') ? 0.25
               : 0.5;
            ry = 0.5;
        }
        rx = Math.min(1, Math.max(0, rx));
        ry = Math.min(1, Math.max(0, ry));

        $bZoom.removeClass('is-active is-active-2');
        $zw.removeClass('is-zoomed-1 is-zoomed-2');

        if (S.zoomLevel === 0) {
            $stage.removeClass('zoom-on');
            $tip.removeClass('show');
            el.style.transform       = '';
            el.style.transformOrigin = '';
            el.style.marginRight     = '';
            el.style.marginBottom    = '';
            st.scrollLeft = 0; st.scrollTop = 0;
            return;
        }

        var scale = currentScale();
        // offsetWidth/Height NO incluyen el transform: son las medidas reales
        var w = el.offsetWidth, h = el.offsetHeight;

        $bZoom.addClass(S.zoomLevel === 1 ? 'is-active' : 'is-active-2');
        $stage.addClass('zoom-on');

        el.style.transformOrigin = '0 0';
        el.style.transform       = 'scale(' + scale + ')';
        el.style.marginRight     = ((scale - 1) * w) + 'px';
        el.style.marginBottom    = ((scale - 1) * h) + 'px';

        $tip.addClass('show');
        W.setTimeout(function () { $tip.removeClass('show'); }, C.ZOOM_TIP_MS);

        upgradeVisibleToHD();

        // El scroll se fija cuando el navegador ya recalculó el área con el
        // margen nuevo; un rAF doble basta y evita el setTimeout a ciegas.
        W.requestAnimationFrame(function () {
            W.requestAnimationFrame(function () {
                scrollToRatio(rx, ry, w * scale, h * scale);
            });
        });
    }

    /** Lleva el punto relativo (rx,ry) del pliego al centro del viewport,
        sin salirse de los límites del área desplazable. */
    function scrollToRatio(rx, ry, fullW, fullH) {
        var st = $stage[0];
        var maxL = Math.max(0, fullW - st.clientWidth);
        var maxT = Math.max(0, fullH - st.clientHeight);
        st.scrollLeft = Math.min(maxL, Math.max(0, rx * fullW - st.clientWidth  / 2));
        st.scrollTop  = Math.min(maxT, Math.max(0, ry * fullH - st.clientHeight / 2));
    }

    /* ------------------------------------------------------------------
       Recentrado tras pasar de página con el zoom puesto.
       ------------------------------------------------------------------
       En el estándar esta función se INVOCA en goPage/goNext/goPrev pero no
       está declarada en ninguna parte: navegar con zoom lanzaba
       «zoomCenterScroll is not defined» y abortaba el resto del manejador.
       Aquí queda definida.

       Al cambiar de hoja se recentra en horizontal —la hoja nueva es otra— y
       se sube al principio de la página, que es por donde se empieza a leer.
       ------------------------------------------------------------------ */
    function zoomCenterScroll() {
        if (S.zoomLevel === 0) return;
        var el = $zw[0], scale = currentScale();
        W.requestAnimationFrame(function () {
            scrollToRatio(0.5, 0, el.offsetWidth * scale, el.offsetHeight * scale);
        });
    }

    function zoomToggle(px, py) {
        if (!S.ready) return;
        S.zoomLevel = (S.zoomLevel + 1) % 3;
        applyZoom(px, py);
    }
    function zoomReset() { S.zoomLevel = 0; applyZoom(); }

    /** Zoom directo a un nivel concreto centrado en un punto */
    function zoomToLevel(level, px, py) {
        if (!S.ready) return;
        S.zoomLevel = Math.max(0, Math.min(2, level));
        applyZoom(px, py);
    }

    function bindZoom() {
        // Botón de la toolbar: cicla niveles, centrado en el medio
        $bZoom.on('click', function (e) { e.preventDefault(); zoomToggle(); });

        // Doble click / doble tap sobre el libro: zoom al punto
        $stage[0].addEventListener('dblclick', function (e) {
            e.preventDefault();
            zoomToggle(e.clientX, e.clientY);
        });

        /* Doble toque. Antes se contaba como «toque» cualquier touchend, de
           modo que dos swipes de página seguidos (< 300 ms) activaban el zoom.
           Ahora un toque sólo cuenta si fue con un dedo, corto (< 250 ms) y
           casi sin desplazamiento (< 12 px); y el segundo toque tiene que caer
           cerca del primero (< 40 px). */
        var tap0 = null, lastTap = null;
        $stage[0].addEventListener('touchstart', function (e) {
            if (e.touches.length !== 1) { tap0 = null; return; }
            var t = e.touches[0];
            tap0 = { x: t.clientX, y: t.clientY, t: Date.now() };
        }, { passive: true });
        $stage[0].addEventListener('touchend', function (e) {
            if (!tap0 || e.touches.length || !e.changedTouches.length) { tap0 = null; return; }
            var t = e.changedTouches[0], now = Date.now();
            var isTap = (now - tap0.t) < 250 &&
                        Math.abs(t.clientX - tap0.x) < 12 &&
                        Math.abs(t.clientY - tap0.y) < 12;
            tap0 = null;
            if (!isTap) { lastTap = null; return; }
            if (lastTap && (now - lastTap.t) < 300 &&
                Math.abs(t.clientX - lastTap.x) < 40 &&
                Math.abs(t.clientY - lastTap.y) < 40) {
                e.preventDefault();
                lastTap = null;
                zoomToggle(t.clientX, t.clientY);
                return;
            }
            lastTap = { x: t.clientX, y: t.clientY, t: now };
        }, { passive: false });

        // Ctrl + rueda: zoom fino en desktop
        var wheelEv = ('onwheel' in D) ? 'wheel' : 'mousewheel';
        $stage[0].addEventListener(wheelEv, function (e) {
            if (!e.ctrlKey) return;       // solo con Ctrl (gesto de zoom)
            e.preventDefault();
            var delta = (e.deltaY !== undefined) ? e.deltaY : -e.wheelDelta;
            if (delta < 0) zoomToLevel(S.zoomLevel + 1, e.clientX, e.clientY);
            else           zoomToLevel(S.zoomLevel - 1, e.clientX, e.clientY);
        }, { passive: false });
    }

    /* ==================================================================
       12. DRAG (pan) + PINCH-TO-ZOOM con zoom activo
       ================================================================== */
    function bindDrag() {
        var el = $stage[0];

        /* ---- Mouse: arrastrar para desplazar ---- */
        el.addEventListener('mousedown', function (e) {
            if (S.zoomLevel === 0) return;
            S.dragging = true;
            S.dx0 = e.clientX; S.dy0 = e.clientY;
            S.sl0 = el.scrollLeft; S.st0 = el.scrollTop;
            $stage.addClass('is-grabbing');
            e.preventDefault();
        });
        el.addEventListener('mousemove', function (e) {
            if (!S.dragging) return;
            el.scrollLeft = S.sl0 - (e.clientX - S.dx0);
            el.scrollTop  = S.st0 - (e.clientY - S.dy0);
        });
        function endDrag() { S.dragging = false; $stage.removeClass('is-grabbing'); }
        el.addEventListener('mouseup',    endDrag);
        el.addEventListener('mouseleave', endDrag);

        /* ---- Touch: 1 dedo = pan; 2 dedos = pinch-zoom ---- */
        var pinchStartDist = 0;
        var pinchStartLevel = 0;

        function dist(t1, t2) {
            var dx = t1.clientX - t2.clientX, dy = t1.clientY - t2.clientY;
            return Math.sqrt(dx * dx + dy * dy);
        }

        el.addEventListener('touchstart', function (e) {
            if (e.touches.length === 2) {
                // Inicio de pinch
                pinchStartDist = dist(e.touches[0], e.touches[1]);
                pinchStartLevel = S.zoomLevel;
                S.dragging = false;
            } else if (e.touches.length === 1 && S.zoomLevel > 0) {
                // Inicio de pan
                S.dragging = false;
                S.dx0 = e.touches[0].clientX; S.dy0 = e.touches[0].clientY;
                S.sl0 = el.scrollLeft; S.st0 = el.scrollTop;
            }
        }, { passive: true });

        el.addEventListener('touchmove', function (e) {
            if (e.touches.length === 2 && pinchStartDist > 0) {
                // Pinch: comparar distancia actual con inicial
                var d = dist(e.touches[0], e.touches[1]);
                var ratio = d / pinchStartDist;
                var midX = (e.touches[0].clientX + e.touches[1].clientX) / 2;
                var midY = (e.touches[0].clientY + e.touches[1].clientY) / 2;
                if (ratio > 1.25 && S.zoomLevel < 2) {
                    zoomToLevel(pinchStartLevel + 1, midX, midY);
                    pinchStartDist = d;  // resetear para el siguiente paso
                } else if (ratio < 0.8 && S.zoomLevel > 0) {
                    zoomToLevel(pinchStartLevel - 1, midX, midY);
                    pinchStartDist = d;
                }
                return;
            }
            if (e.touches.length === 1 && S.zoomLevel > 0) {
                var t = e.touches[0];
                var dx = Math.abs(t.clientX - S.dx0), dy = Math.abs(t.clientY - S.dy0);
                if (dx > 6 || dy > 6) {
                    S.dragging = true;
                    el.scrollLeft = S.sl0 - (t.clientX - S.dx0);
                    el.scrollTop  = S.st0 - (t.clientY - S.dy0);
                }
            }
        }, { passive: true });

        el.addEventListener('touchend', function (e) {
            if (e.touches.length < 2) pinchStartDist = 0;
        }, { passive: true });
    }

    /* ==================================================================
       13. PANEL BÚSQUEDA
       ================================================================== */
    /* Las miniaturas no se piden al cargar la revista: traen data-src y se
       activan la primera vez que se abre el panel (y con loading=lazy sólo
       bajan las que quedan a la vista). */
    var thumbsArmed = false;
    function armThumbs() {
        if (thumbsArmed) return;
        thumbsArmed = true;
        $sPanel.find('img[data-src]').each(function () {
            this.src = this.getAttribute('data-src');
            this.removeAttribute('data-src');
        });
    }

    function searchOpenFn() {
        closeAllPanels();
        armThumbs();
        S.searchOpen = true; S.panelOpenedAt = Date.now();
        $sPanel.addClass('is-open').attr('aria-hidden', 'false');
        $overlay.addClass('is-open');
        refreshThumbBookmarks();
        // Lleva la miniatura de la hoja actual a la vista
        var cur = $sThumbs.filter('.is-current')[0];
        if (cur && cur.scrollIntoView) { try { cur.scrollIntoView({ block: 'nearest' }); } catch (e) {} }
        W.setTimeout(function () { try { $sInput.focus(); } catch (e) {} }, 320);
    }
    function searchCloseFn() {
        S.searchOpen = false;
        $sPanel.removeClass('is-open').attr('aria-hidden', 'true');
        $overlay.removeClass('is-open');
        try { $sInput.blur(); } catch (e) {}
    }
    function bindSearch() {
        $bSearch.on('click', function (e) { e.preventDefault(); e.stopPropagation(); S.searchOpen ? searchCloseFn() : searchOpenFn(); });
        $sClose.on('click touchend', function (e) { e.preventDefault(); e.stopPropagation(); searchCloseFn(); });
        $sGo.on('click', function (e) { e.preventDefault(); goPage($sInput.val()); searchCloseFn(); });
        $sInput.on('keydown', function (e) { if (e.keyCode === 13) { e.preventDefault(); goPage($sInput.val()); searchCloseFn(); } });
        $sThumbs.on('click', function (e) { e.preventDefault(); goPage($(this).data('pg')); searchCloseFn(); });
    }

    /* ==================================================================
       14. PANEL ÍNDICE
       ================================================================== */
    function indexOpenFn() {
        closeAllPanels();
        S.indexOpen = true; S.panelOpenedAt = Date.now();
        $iPanel.addClass('is-open').attr('aria-hidden', 'false');
        $overlay.addClass('is-open');
    }
    function indexCloseFn() {
        S.indexOpen = false;
        $iPanel.removeClass('is-open').attr('aria-hidden', 'true');
        $overlay.removeClass('is-open');
    }
    function bindIndex() {
        $bIndex.on('click', function (e) { e.preventDefault(); e.stopPropagation(); S.indexOpen ? indexCloseFn() : indexOpenFn(); });
        $iClose.on('click touchend', function (e) { e.preventDefault(); e.stopPropagation(); indexCloseFn(); });
        $iLinks.on('click', function (e) { e.preventDefault(); goPage($(this).data('pg')); indexCloseFn(); });
    }

    function closeAllPanels() {
        if (S.searchOpen) searchCloseFn();
        if (S.indexOpen)  indexCloseFn();
    }
    function bindOverlay() {
        $overlay.on('click touchend', function (e) {
            e.preventDefault(); e.stopPropagation();
            if (Date.now() - S.panelOpenedAt < C.PANEL_LOCK) return;
            closeAllPanels();
        });
    }

    /* ==================================================================
       15. MARCADORES
       ================================================================== */
    function bkLoad() {
        try { var raw = W.localStorage.getItem(BK_KEY); if (raw) S.bookmarks = JSON.parse(raw); }
        catch (e) { S.bookmarks = {}; }
    }
    function bkSave() {
        try { W.localStorage.setItem(BK_KEY, JSON.stringify(S.bookmarks)); } catch (e) {}
    }
    function bkToggle(pg) {
        pg = String(pg);
        if (S.bookmarks[pg]) delete S.bookmarks[pg]; else S.bookmarks[pg] = true;
        bkSave();
        $bBookmark.toggleClass('is-bookmarked', !!S.bookmarks[pg]);
        refreshThumbBookmarks();
    }
    function refreshThumbBookmarks() {
        if (!$sThumbs || !$sThumbs.length) return;
        $sThumbs.each(function () {
            var $t = $(this);
            $t.toggleClass('is-bookmarked', !!S.bookmarks[String($t.data('pg'))]);
        });
    }
    function bindBookmarks() {
        bkLoad();
        $bBookmark.on('click', function (e) { e.preventDefault(); if (!S.ready) return; bkToggle(currentPage()); });
    }

    /* ==================================================================
       16. ANTI-COPIA
       ================================================================== */
    function bindAntiCopy() {
        $(D).on('contextmenu', function (e) { e.preventDefault(); return false; });
        $(D).on('keydown', function (e) {
            if (e.ctrlKey && (e.keyCode === 83 || e.keyCode === 80)) { e.preventDefault(); return false; }
            if (e.ctrlKey && e.keyCode === 67) {
                var tag = (e.target.tagName || '').toLowerCase();
                if (tag !== 'input' && tag !== 'textarea') { e.preventDefault(); return false; }
            }
            if (e.keyCode === 44) { e.preventDefault(); return false; }
        });
        // En Windows ImprPant sólo emite keyup
        $(D).on('keyup', function (e) {
            if (e.keyCode === 44) { e.preventDefault(); return false; }
        });
        $(D).on('dragstart', function (e) {
            if (e.target.tagName === 'IMG') { e.preventDefault(); return false; }
        });
    }

    /* ==================================================================
       17. PRELOADER
       ==================================================================
       En desktop hacemos warmup progresivo de todas las páginas.
       En móvil (o equipos con poca memoria) NO: dejamos que el lazy-load
       nativo + el prefetch puntual alrededor de la página actual gestionen
       la memoria. Decodificar decenas de hojas a la vez agota la RAM en gama media.
       ================================================================== */
    function deviceIsLowMemory() {
        // navigator.deviceMemory: GB de RAM aproximados (Chrome/Android)
        var mem = (W.navigator && W.navigator.deviceMemory) || 0;
        if (mem && mem <= 4) return true;
        // Heurística de respaldo: pantalla pequeña = tratar como móvil
        return viewportSize().w < C.BP;
    }

    /* ------------------------------------------------------------------
       SALIDA DEL PRELOADER — fundido cruzado
       ------------------------------------------------------------------
       No es un corte: son tres tiempos encadenados sobre un mismo velo.

         1. El wordmark se retira solo, antes que el fondo.
         2. La revista empieza a subir DEBAJO del velo todavía opaco.
            Aquí se cruzan las dos capas.
         3. El velo negro se disuelve encima, ya sin nada que sostener.

       Es sólo opacidad. Se probó con desenfoque de entrada y se descartó:
       leía como transición de diapositiva, no como una página cargando.
       ------------------------------------------------------------------ */
    function salidaPreloader() {
        var mq = W.matchMedia && W.matchMedia('(prefers-reduced-motion: reduce)');
        if (mq && mq.matches) {
            $pre.hide();
            $stage.addClass('revealed reveal-done');
            $('body').removeClass('cargando');
            return;
        }

        $pre.addClass('is-leaving');
        W.setTimeout(function () {
            $stage.addClass('revealed');
            $('body').removeClass('cargando');
        }, 70);
        W.setTimeout(function () { $pre.addClass('is-out'); },      120);
        W.setTimeout(function () {
            $pre.hide();
            $stage.addClass('reveal-done');
        }, 550);
    }

    function preloadAndGo() {
        var $imgs = $('#magazine .pg-img');
        var primary = Math.min(C.PRELOAD_PRIMARY, $imgs.length);
        var finished = false, ratioDone = false;
        var lowMem = deviceIsLowMemory();

        function go() {
            if (finished) return; finished = true;
            var ok = initPageFlip();
            /* Con caché o conexión rápida las páginas están listas en 300 ms y la
               pantalla de carga apenas parpadea. Se le garantiza un mínimo en
               pantalla para que el wordmark se lea y la salida no atropelle. */
            var transcurrido = ((W.Date && W.Date.now) ? W.Date.now() : 0) - T_ARRANQUE;
            var espera = Math.max(120, C.PRELOAD_MIN - transcurrido);
            W.setTimeout(salidaPreloader, espera);
            // Warmup masivo SOLO en equipos con memoria suficiente
            if (ok && !lowMem) warmup(primary);
        }

        function warmup(start) {
            var idx = start, BATCH = 2;
            function batch() {
                if (idx >= $imgs.length) return;
                var arr = [];
                for (var i = 0; i < BATCH && idx < $imgs.length; i++, idx++) arr.push(ensureDecoded(idx + 1));
                Promise.all(arr).then(function () {
                    if (W.requestIdleCallback) W.requestIdleCallback(batch, { timeout: 2500 });
                    else W.setTimeout(batch, 260);
                });
            }
            if (W.requestIdleCallback) W.requestIdleCallback(batch, { timeout: 2500 });
            else W.setTimeout(batch, 320);
        }

        if (primary === 0) { go(); return; }

        var proms = [];
        $imgs.slice(0, primary).each(function (i) {
            var imgEl = this, src = $(this).attr('src');
            proms.push(new Promise(function (resolve) {
                var off = new Image();
                off.onload = function () {
                    if (!ratioDone && i === 0 && off.naturalWidth && off.naturalHeight) {
                        C.PAGE_RATIO = off.naturalWidth / off.naturalHeight;
                        ratioDone = true;
                    }
                    if (imgEl.decode) imgEl.decode().then(resolve).catch(resolve);
                    else resolve();
                };
                off.onerror = resolve;
                off.src = src;
                if (imgEl.loading === 'lazy') imgEl.loading = 'eager';
            }));
        });
        Promise.all(proms).then(go);
        W.setTimeout(go, C.PRELOAD_TIMEOUT);  // fallback duro
    }

    /* ==================================================================
       18. DOCUMENT READY
       ================================================================== */
    $(function () {
        $mag       = $('#magazine');
        $stage     = $('#stage');
        $pre       = $('#preloader');
        $zw        = $('#zoom-wrap');
        $tip       = $('#zoom-tip');
        $bPrev     = $('#bt-prev');
        $bNext     = $('#bt-next');
        $bZoom     = $('#bt-zoom');
        $bFirst    = $('#bt-first');
        $bSearch   = $('#bt-search');
        $bIndex    = $('#bt-index');
        $bBookmark = $('#bt-bookmark');
        $pgNow     = $('#pg-now');
        $pgMax     = $('#pg-max');
        $sPanel    = $('#search-panel');
        $sInput    = $('#sp-input');
        $sGo       = $('#sp-go');
        $sClose    = $('#sp-close');
        $sThumbs   = $('.sp-thumb');
        $iPanel    = $('#index-panel');
        $iClose    = $('#ip-close');
        $iLinks    = $('.ipanel__link');
        $overlay   = $('#panel-overlay');

        $pgMax.text(total);

        /* La cabecera y la barra se atenúan mientras dura la carga, para que
           entren con la revista y no aparezcan nítidas sobre un velo. La clase
           la pone el JS, no el HTML: si el motor fallara, la interfaz queda
           visible en lugar de invisible. */
        $('body').addClass('cargando');

        bindNav(); bindKeys(); bindSearch(); bindIndex(); bindOverlay();
        bindWheel(); bindSwipe(); bindZoom(); bindDrag();
        bindBookmarks(); bindAntiCopy();

        $sPanel.on('click touchend', function (e) { e.stopPropagation(); });
        $iPanel.on('click touchend', function (e) { e.stopPropagation(); });
    });

    /* ==================================================================
       19. WINDOW LOAD
       ================================================================== */
    $(W).on('load', function () {
        // Salvavidas: pase lo que pase con la carga, la interfaz se destapa
        W.setTimeout(function () { $('body').removeClass('cargando'); }, 8000);
        preloadAndGo();
        $(W).on('resize orientationchange', onResize);
        if (W.visualViewport) {
            // Solo escuchamos cambios de ANCHO en visualViewport (no scroll/zoom del SO)
            W.visualViewport.addEventListener('resize', onResize);
        }
    });

})(jQuery, window, document);
