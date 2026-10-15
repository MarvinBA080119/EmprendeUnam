@extends('layouts.sitio')

@php
    $tituloPagina = 'Temáticas';
    $paginaId = 'tematicas';
    $recursos = ['resources/css/sitio/institucional_suizo.css'];
@endphp

@section('contenido')
<main id="contenido" class="suizo">

    <!-- Marca de agua de fondo -->
    <span class="marca-agua" aria-hidden="true">FCA UNAM</span>

    <!-- ══════════════ ENCABEZADO ══════════════ -->
    <div class="container">
        <div class="row" style="padding: 5rem 0 3rem;">
            <div class="col-lg-7">
                <h1 class="titulo-gigante">Temáticas</h1>
                <div class="filete-rojo"></div>
            </div>
        </div>
    </div>

    <div class="container pb-5">
        <div class="row g-5">

            <!-- ══════════════ ÍNDICE DE CATEGORÍAS ══════════════ -->
            <div class="col-lg-4">
                {{-- El índice va dentro de un <details>. En escritorio el resumen
                         se oculta y la lista queda siempre visible; en móvil arranca
                         plegado mostrando la categoría en curso, para no obligar a
                         recorrer trece entradas antes de llegar al primer artículo.
                         El atributo open viaja en el HTML y lo retira el script de
                         abajo sólo cuando la pantalla es estrecha: así la lista sigue
                         desplegada si el navegador no ejecuta JavaScript. --}}
                <details class="tem-indice" id="indiceTematicas" open>

                    <summary class="tem-indice__resumen">
                        <span class="tem-indice__resumen-eti">Categoría</span>
                        <span class="tem-indice__resumen-val">{{ $datos_cat['nombre'] }}</span>
                    </summary>

                    <p class="rotulo rotulo--negro">Categorías</p>

                    <ul class="tem-indice__lista">
                        @foreach ($categorias as $slug => $cat)
                            <li @class(['activo' => $slug === $cat_activa])>
                                <a href="{{ route('tematicas', ['cat' => $slug]) }}">
                                    <span class="tem-indice__num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                    <span class="tem-indice__nombre">{{ $cat['nombre'] }}</span>
                                    <span class="tem-indice__conteo">{{ count($cat['articulos']) }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>

                </details>
            </div>

            <!-- ══════════════ ARTÍCULOS DEL ÁREA ══════════════ -->
            <div class="col-lg-8">

                <div class="tem-cabecera">
                    <h2 class="tem-cabecera__titulo">{{ $datos_cat['nombre'] }}</h2>
                    <span class="tem-cabecera__conteo">
                        {{ $n_art }} {{ $n_art === 1 ? 'artículo' : 'artículos' }}
                    </span>
                </div>

                @if ($n_art === 0)

                    <p class="tem-vacio">
                        Aún no hay artículos publicados en esta categoría.
                        Es un área abierta a colaboraciones: consulte los
                        <a href="{{ route('lineamientos') }}">lineamientos para autores</a>.
                    </p>

                @else

                    {{-- El acervo se agrupa por número: recorrer 74 fichas
                             seguidas sin puntos de referencia es ilegible, y el
                             número de la revista es el dato que permite ubicar
                             y pedir el texto. El rótulo se pinta al cambiar de
                             edición, no en cada ficha. --}}
                    @foreach ($articulos as $art)

                        @if ($art['nuevo_numero'])
                            <p class="tem-edicion">
                                <span class="tem-edicion__num">No. {{ (int) $art['numero'] }}</span>
                                <span class="tem-edicion__periodo">{{ trim($art['bimestre'] . ' ' . $art['anio']) }}</span>
                            </p>
                        @endif

                        <article class="tem-articulo">

                            <h3 class="tem-articulo__titulo">{{ $art['titulo'] }}</h3>

                            <p class="tem-articulo__meta">
                                @if ($art['autor'] !== '')
                                    <strong>{{ $art['autor'] }}</strong>
                                @endif
                                @if ($art['seccion'] !== '')
                                    @if ($art['autor'] !== '')&nbsp;·&nbsp;@endif
                                    {{ $art['seccion'] }}
                                @endif
                            </p>

                        </article>

                    @endforeach

                @endif

            </div>

        </div>
    </div>

</main>

<script>
/* En pantallas estrechas el índice arranca plegado. Se evalúa al cargar y en
   cada cambio de tamaño, pero sin volver a plegarlo si la persona ya lo abrió. */
(function () {
    var indice = document.getElementById('indiceTematicas');
    if (!indice) { return; }

    var estrecha = window.matchMedia('(max-width: 991.98px)');
    var tocado   = false;

    indice.addEventListener('toggle', function () { tocado = true; });

    function ajustar() {
        if (tocado) { return; }
        if (estrecha.matches) {
            indice.removeAttribute('open');
        } else {
            indice.setAttribute('open', 'open');
        }
    }

    ajustar();
    window.addEventListener('resize', ajustar);
})();
</script>
@endsection
