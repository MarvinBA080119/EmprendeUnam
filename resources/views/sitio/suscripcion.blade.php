@extends('layouts.sitio')

{{--
    Suscripción al newsletter (suscripcion.php del sitio original).
    Se conservan la portada de la sección y los beneficios con sus textos.
    El formulario usa los campos de la especificación (6.8): nombre, apellido,
    correo, tema de interés (texto libre) y aceptación del aviso.
--}}
@php
    $tituloPagina = 'Suscripción';
    $paginaId = '';
    $recursos = ['resources/css/sitio/premium_sections.css', 'resources/css/sitio/formularios_media.css'];

    $beneficios = [
        ['Hemeroteca completa', 'Acceso ilimitado a todos los números anteriores desde 1995.'],
        ['Webinars exclusivos', 'Sesiones en vivo con autores y expertos de la FCA-UNAM.'],
        ['Reportes de tendencia', 'Boletín mensual con análisis de datos económicos de México.'],
        ['Networking premium', 'Invitaciones a foros presenciales y digitales de emprendimiento.'],
    ];
@endphp

@section('contenido')
<main id="contenido">

    <!-- ══════════════════ ENCABEZADO ══════════════════ -->
    <header class="head-solo">
        <div class="container">
            <p class="eyebrow mb-0">Contenido exclusivo</p>
            <h1>Únete a nuestra comunidad</h1>
        </div>
    </header>

    <div class="container pb-5">

        <div class="row g-5">

            <!-- ═════════ BENEFICIOS ═════════ -->
            <div class="col-lg-4">

                <p class="consultorio__entrada">
                    Acceda a investigaciones profundas, podcasts premium y análisis de mercado
                    exclusivos para nuestra red de académicos y empresarios.
                </p>

                <p class="eyebrow mb-0" style="margin-top: 2.5rem;">Beneficios de suscriptor</p>

                @foreach ($beneficios as [$titulo, $texto])
                    <div class="lineamiento">
                        <h2><span class="lineamiento__num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>{{ $titulo }}</h2>
                        <p>{{ $texto }}</p>
                    </div>
                @endforeach

                <div class="lineamiento lineamiento--compromiso">
                    <h2>Privacidad</h2>
                    <p>
                        EMPRENDEDORES UNAM no está destinado a recopilar PII ni a proteger datos
                        confidenciales. Sus datos están protegidos bajo los términos de privacidad
                        de la UNAM.
                    </p>
                </div>

            </div>

            <!-- ═════════ FORMULARIO ═════════ -->
            <div class="col-lg-8">

                <form class="form-lux" id="formSuscripcion" method="POST" action="{{ route('newsletter.suscribir') }}" novalidate>
                    @csrf

                    @if (session('exito') || session('aviso'))
                        <p class="form-aviso visible" role="status">{{ session('exito') ?? session('aviso') }}</p>
                    @elseif ($errors->has('general'))
                        <p class="form-aviso visible form-aviso--error" role="alert">{{ $errors->first('general') }}</p>
                    @elseif ($errors->any())
                        <p class="form-aviso visible form-aviso--error" role="alert">Revise los campos marcados antes de enviar.</p>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <x-sitio.campo nombre="nombre" etiqueta="Nombre" maxlength="100" requerido autocomplete="given-name" />
                        </div>
                        <div class="col-md-6">
                            <x-sitio.campo nombre="apellido" etiqueta="Apellido" maxlength="100" requerido autocomplete="family-name" />
                        </div>
                    </div>

                    <x-sitio.campo nombre="correo" etiqueta="Correo electrónico" tipo="email" maxlength="254" requerido autocomplete="email" />

                    <x-sitio.campo nombre="tema" etiqueta="Tema de interés (por ejemplo: finanzas, exportación, marketing digital)"
                                   maxlength="120" requerido />

                    <div class="campo-check">
                        <input id="campo-acepta" name="acepta" type="checkbox" value="1" required
                               @checked(old('acepta'))
                               @class(['form-check-input', 'is-invalid' => $errors->has('acepta')])>
                        <label class="form-check-label" for="campo-acepta">
                            He leído y acepto el
                            <a href="https://www.fca.unam.mx/docs/aviso_privacidad.pdf" target="_blank" rel="noopener">aviso de privacidad</a>
                            de la UNAM.
                        </label>
                        @error('acepta')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="campo-trampa" aria-hidden="true">
                        <label for="sitioWeb">Sitio web</label>
                        <input type="text" id="sitioWeb" name="sitio_web" tabindex="-1" autocomplete="off">
                    </div>

                    <button type="submit" class="btn-enviar">Completar registro</button>

                    <p class="form-nota">
                        Te enviaremos un correo para confirmar tu suscripción. Cada aviso incluye un enlace
                        para darte de baja cuando quieras.
                    </p>

                </form>

            </div>

        </div>

    </div>

</main>
@endsection
