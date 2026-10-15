@extends('layouts.sitio')

{{--
    Envío de artículos (envio-articulos.php del sitio original).

    El formulario del sitio anterior solo confirmaba en pantalla. El envío real
    lo hace el sistema: el autor se registra, el administrador activa su cuenta
    y desde su panel envía el PDF con sus 5 palabras clave y coautores. Esta
    página conserva los lineamientos y lleva a ese flujo.

    Dos lineamientos se ajustaron a las reglas del sistema (ver PENDIENTES.md):
    «Formato» (PDF de hasta 7 MB, no Word) y «Revisión por pares» (el dictamen
    lo emite el comité editorial; no hay revisores externos, RN-07).
--}}
@php
    $tituloPagina = 'Envío de artículos';
    $paginaId = '';
    $recursos = ['resources/css/sitio/premium_sections.css', 'resources/css/sitio/formularios_media.css'];
    $maxMb = (int) round(config('emprende.pdf_max_kb') / 1024);
@endphp

@section('contenido')
<main id="contenido">

    <!-- ══════════════════ ENCABEZADO ══════════════════ -->
    <header class="head-solo">
        <div class="container">
            <h1>Envío de artículos</h1>
        </div>
    </header>

    <div class="container pb-5">

        <div class="row g-5">

            <!-- ═════════ LINEAMIENTOS ═════════ -->
            <div class="col-lg-4">

                <p class="eyebrow mb-0">Lineamientos editoriales</p>

                <div class="lineamiento">
                    <h2>Originalidad</h2>
                    <p>Los artículos deben ser inéditos y no estar postulados simultáneamente en otras revistas.</p>
                </div>

                <div class="lineamiento">
                    <h2>Dictamen editorial</h2>
                    <p>Todo trabajo será sometido a un riguroso proceso de evaluación por el comité editorial.</p>
                </div>

                <div class="lineamiento">
                    <h2>Formato</h2>
                    <p>Sube tu documento en formato PDF (máximo {{ $maxMb }} MB), omitiendo datos de identificación en el archivo.</p>
                </div>

                <div class="lineamiento lineamiento--compromiso">
                    <h2>Compromiso UNAM</h2>
                    <p>
                        Todos los manuscritos son revisados por el comité editorial. Te notificaremos
                        por correo el dictamen de tu artículo; si es aprobado, podrás descargar tu
                        constancia desde tu panel de autor.
                    </p>
                </div>

                <p class="mt-4 mb-0">
                    <a href="{{ route('lineamientos') }}" class="post__more">Ver lineamientos completos</a>
                </p>

            </div>

            <!-- ═════════ CÓMO ENVIAR ═════════ -->
            <div class="col-lg-8">

                <div class="form-lux">

                    <p class="eyebrow mb-0">Cómo enviar su artículo</p>

                    <ol class="pasos-envio">
                        <li>
                            <strong>Registre su cuenta de autor</strong>
                            con su nombre, institución y correo electrónico.
                        </li>
                        <li>
                            <strong>Espere la activación.</strong>
                            El administrador revisa cada registro y le avisa por correo cuando su cuenta está activa.
                        </li>
                        <li>
                            <strong>Envíe su artículo desde su panel:</strong>
                            título, tema, cinco palabras clave, coautores (si los tiene) y el archivo PDF.
                        </li>
                        <li>
                            <strong>Dé seguimiento.</strong>
                            En su panel verá el estatus (En revisión, Aprobado o Rechazado) y, si se aprueba,
                            descargará su constancia con folio verificable.
                        </li>
                    </ol>

                    <div class="acciones-envio">
                        @auth
                            <a href="{{ route('autor.articulos.create') }}" class="btn-enviar">Enviar artículo</a>
                            <a href="{{ route('autor.panel') }}" class="btn-lux"><span>Ir a mi panel</span></a>
                        @else
                            <a href="{{ route('registro') }}" class="btn-enviar">Registrarme como autor</a>
                            <a href="{{ route('login') }}" class="btn-lux"><span>Ya tengo cuenta</span></a>
                        @endauth
                    </div>

                    <p class="form-nota">
                        Al enviar su artículo, acepta que sus datos y documento sean tratados conforme a las
                        políticas editoriales y el
                        <a href="https://www.fca.unam.mx/docs/aviso_privacidad.pdf" target="_blank" rel="noopener">aviso de privacidad</a>
                        de la UNAM.
                    </p>

                </div>

            </div>

        </div>

    </div>

</main>
@endsection
