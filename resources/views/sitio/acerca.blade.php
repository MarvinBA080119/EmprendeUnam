@extends('layouts.sitio')

@php
    $tituloPagina = 'Nuestra revista';
    $paginaId = '';
    $recursos = ['resources/css/sitio/institucional_suizo.css'];
@endphp

@section('contenido')
<main id="contenido" class="suizo">

    <!-- Marca de agua de fondo -->
    <span class="marca-agua" aria-hidden="true">FCA UNAM</span>

    <div class="container">
        <div class="row">

            <!-- ══════════════ COLUMNA FIJA ══════════════ -->
            <div class="col-lg-4">
                <div class="columna-fija">
                    <p class="rotulo">EmprendeUNAM</p>
                    <h1 class="titulo-gigante">Nuestra <em>revista</em></h1>
                    <div class="filete-rojo"></div>
                </div>
            </div>

            <!-- ══════════════ COLUMNA QUE FLUYE ══════════════ -->
            {{-- Todos los párrafos al mismo cuerpo. Antes el primero
                     llevaba .entrada-mayor (1.5rem frente a 1.15rem del resto)
                     y la coordinación editorial pidió unificarlo: el salto de
                     tamaño hacía leer la entradilla como un destacado y el
                     resto como letra pequeña. --}}
            <div class="col-lg-7 offset-lg-1">
                <div class="texto-fluye" style="padding-top: 5rem;">

                    <p>
                        Somos una revista de divulgación científica, de periodicidad bimestral y acceso
                        abierto, que tiene por objetivo ser un puente entre el conocimiento académico y
                        las necesidades de las pequeñas y medianas empresas, así como de las personas
                        emprendedoras, por medio de la difusión de contenidos que fortalezcan la toma de
                        decisiones para el desarrollo de las organizaciones.
                    </p>

                    <p>
                        En 1987, cuando la palabra emprendimiento apenas comenzaba a recorrer las aulas y
                        las organizaciones de México, la línea editorial de la entonces revista
                        <em>Emprendedores</em> decidió adelantarse a su tiempo. <em>Emprendedores</em> nació
                        con la convicción de que una idea compartida podía convertirse en empresa, una
                        empresa en comunidad y una comunidad en desarrollo. La revista nació con la certeza
                        de que la universidad de la nación transforma la realidad cuando el conocimiento
                        encuentra a quienes, día tras día, construyen la economía del país con su trabajo.
                    </p>

                    <p>
                        Desde entonces, reunimos el pensamiento de especialistas, docentes, personas
                        dedicadas a la investigación y profesionales de la administración, contaduría,
                        informática, negocios internacionales y otras disciplinas afines, plasmando en las
                        páginas de la revista las tendencias y mejores prácticas en materia de innovación,
                        sustentabilidad, finanzas, economía, tecnología, talento humano, emprendimiento y
                        experiencias de quienes forman empresas en distintos sectores.
                    </p>

                </div>
            </div>

        </div>
    </div>

    <!-- ══════════════ BLOQUE ROJO DE IMPACTO ══════════════ -->
    {{-- La frase destacada es la que el texto entregado por la coordinación
             editorial trae en cursivas, palabra por palabra. --}}
    <section class="bloque-rojo">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 offset-lg-4">
                    <p class="bloque-rojo__texto">
                        Emprender consiste en crear una empresa, en imaginar un futuro posible
                        y comenzar a construir un legado.
                    </p>
                    <p class="bloque-rojo__pie">Nuestro propósito</p>
                </div>
            </div>
        </div>
    </section>

    <div class="container">
        <div class="row">

            <div class="col-lg-4">
                <p class="rotulo rotulo--negro">2026</p>
            </div>

            <div class="col-lg-7 offset-lg-1">
                <div class="texto-fluye">

                    <p>
                        La transformación de <em>Emprendedores</em> se dio en 2026 cuando su nombre
                        cambió a <strong>EmprendeUNAM</strong>, pero su convicción se afirmó: reconocer
                        a todas las personas que emprenden, dirigen, integran y fortalecen las Pyme, ese
                        tejido cotidiano donde se entrelazan la economía, el trabajo y la vida social.
                    </p>

                    <p>
                        La esencia permanece, aunque la identidad cambia para evidenciar que la Universidad
                        dialoga con su tiempo y reafirma su compromiso con la divulgación del conocimiento,
                        la innovación y el emprendimiento.
                    </p>

                    <p>
                        Los más de 220 números publicados demuestran nuestra visión de que emprender no
                        sólo consiste en crear una empresa; también es la decisión de imaginar un futuro
                        posible y comenzar, desde el presente, a construirlo. De esta manera, permanece
                        el propósito de acompañar, desde la reflexión y la experiencia, a quienes
                        construyen organizaciones competitivas, responsables y humanistas.
                    </p>

                </div>
            </div>

        </div>

        <!-- ══════════════ MÉTRICAS ══════════════ -->
        <section class="row g-5" style="margin-top: 5rem;">
            @foreach ($metricas as $m)
                <div class="col-md-4">
                    <div class="cifra-suiza">
                        <p class="cifra-suiza__num">{!! $m['cifra'] !!}</p>
                        <p>{!! $m['texto'] !!}</p>
                    </div>
                </div>
            @endforeach
        </section>

        <!-- ══════════════ COMITÉ EDITORIAL ══════════════ -->
        <section class="row" style="margin-top: 6rem;">

            <div class="col-lg-4">
                <p class="rotulo rotulo--negro">Directorio</p>
                <h2 class="titulo-gigante" style="font-size: clamp(2rem, 4vw, 3rem);">Directorio <em>Editorial</em></h2>
                <div class="filete-rojo"></div>
            </div>

            <div class="col-lg-7 offset-lg-1">

                @foreach ($directorio as $g => $grupo)
                    <p class="rotulo" style="margin-top: {{ $g === 0 ? '4rem' : '3.5rem' }};">{{ $grupo['rotulo'] }}</p>

                    @foreach ($grupo['personas'] as $persona)
                        <div class="miembro" tabindex="0">
                            <p class="miembro__nombre">{{ $persona['nombre'] }}</p>
                            <p class="miembro__cargo">{{ $persona['cargo'] }}</p>
                        </div>
                    @endforeach
                @endforeach

            </div>

        </section>

        <!-- ══════════════ DATOS LEGALES ══════════════ -->
        <section class="legales">
            <div class="row g-5">

                <div class="col-md-4">
                    <strong>ISSN</strong>
                    <p>0187-7828<br>Otorgado por el Instituto Nacional del Derecho de Autor, Secretaría de Cultura.</p>
                </div>

                <div class="col-md-4">
                    <strong>Certificado de reserva de derechos</strong>
                    <p>04-2009-071412482400-203<br>Otorgado por el Instituto Nacional del Derecho de Autor, Secretaría de Cultura.</p>
                </div>

                <div class="col-md-4">
                    <strong>Sitio</strong>
                    <p><a href="https://emprendedores.unam.mx/" target="_blank" rel="noopener">emprendedores.unam.mx</a></p>
                </div>

            </div>
        </section>

    </div>

</main>
@endsection
