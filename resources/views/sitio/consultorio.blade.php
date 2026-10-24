@extends('layouts.sitio')

{{--
    Consultorio (consultorio.php del sitio original, conectado al sistema).

    ARCHIVO DE CONSULTAS: primero las contestadas desde el sistema (mensajes
    CONTESTADO con su respuesta) y después las del sitio anterior. Nunca se
    muestran correos de los lectores.

    PATRÓN DE LECTURA: las respuestas son largas; cada consulta muestra siempre
    asunto, firma y pregunta, y la respuesta se despliega con <details>, que
    funciona sin JavaScript y es accesible por teclado.
--}}
@php
    $tituloPagina = 'Consultorio';
    $paginaId = 'consultorio';
    $recursos = ['resources/css/sitio/premium_sections.css', 'resources/css/sitio/formularios_media.css', 'resources/js/sitio/interacciones.js'];
    $total = count($consultas);
@endphp

@section('contenido')
<main id="contenido">

    <!-- ══════════════════ ENCABEZADO ══════════════════ -->
    <header class="head-solo">
        <div class="container">
            <h1>Consultorio</h1>
        </div>
    </header>

    <div class="container pb-5">

        <!-- ══════════════════ ENTRADILLA ══════════════════ -->
        <div class="row">
            <div class="col-lg-7">
                <p class="consultorio__entrada">
                    Pregunte a los especialistas cómo mejorar el desempeño de su empresa.
                </p>
                <p class="consultorio__nota">
                    Todas las consultas las revisa el comité editorial. <br> Respondemos
                    en un plazo no mayor a cinco días hábiles.
                </p>
            </div>
            <div class="col-lg-5 d-flex flex-column align-items-lg-end justify-content-end">
                {{-- Atajo al formulario: sin él hay que recorrer todas las consultas. --}}
                <a href="#enviar" class="btn-lux btn-lux--rojo consultorio__atajo"><span>Enviar una consulta</span></a>

                <p class="consultorio__conteo">
                    {{ $total }} {{ $total === 1 ? 'consulta resuelta' : 'consultas resueltas' }}
                </p>
            </div>
        </div>

        <!-- ══════════════════ ARCHIVO DE CONSULTAS ══════════════════ -->
        <section class="consultas">

            @foreach ($consultas as $consulta)

                <article class="consulta" data-tarjeta>

                    <!-- Cabecera: número, estado y firma -->
                    <div class="consulta__cabecera">
                        <span class="consulta__num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="consulta__estado">{{ $consulta['estado'] }}</span>
                    </div>

                    <h2 class="consulta__asunto">{{ $consulta['asunto'] }}</h2>

                    <p class="consulta__firma">
                        {{ $consulta['nombre'] }}@if (! empty($consulta['ubicacion']))<span class="consulta__lugar">{{ $consulta['ubicacion'] }}</span>@endif
                    </p>

                    <!-- La consulta, íntegra -->
                    <p class="consulta__pregunta">{{ $consulta['pregunta'] }}</p>

                    <!-- La respuesta, plegable: funciona sin JavaScript -->
                    <details class="consulta__respuesta">

                        <summary>
                            <span class="consulta__abrir">Respuesta del equipo editorial</span>
                        </summary>

                        <div class="consulta__cuerpo">
                            @foreach ($consulta['respuesta'] as $parrafo)
                                <p>{{ $parrafo }}</p>
                            @endforeach
                        </div>

                    </details>

                </article>

            @endforeach

        </section>

        <!-- ══════════════════ ENVÍO DE CASO ══════════════════ -->
        <section class="consulta-envio" id="enviar">
            <div class="row g-5">

                <!-- Texto -->
                <div class="col-lg-5">

                    <p class="eyebrow mb-0">Interacción</p>

                    <h2 class="consulta-envio__titulo">Envíe su caso</h2>

                    <p class="consulta-envio__texto">
                        Cada caso que llega a esta sección es leído por académicos de la Facultad
                        de Contaduría y Administración, que responden con criterios aplicables a
                        su operación concreta, no con respuestas generales.
                    </p>

                    <p class="consulta-envio__texto">
                        Describa su situación con el mayor detalle posible: el tamaño de su empresa,
                        el sector en el que opera y el problema puntual que enfrenta. Entre más
                        preciso sea el planteamiento, más útil será la respuesta que reciba.
                    </p>

                    <div class="consultorio__contacto">
                        <span>Contacto directo</span>
                        <a href="mailto:{{ $config['contact_email'] }}">{{ $config['contact_email'] }}</a><br>
                        {{ $config['contact_phone'] }}<br>
                        {!! $config['address'] !!}
                    </div>

                </div>

                <!-- Formulario -->
                <div class="col-lg-7">

                    @if (session('consulta_enviada'))
                        <x-sitio.aviso-enviado id="avisoConsulta" etiqueta="Consultorio" titulo="Consulta recibida">
                            <p class="aviso-lux__asunto">«{{ session('consulta_enviada') }}»</p>
                            <p>El comité editorial responderá en un plazo no mayor a cinco días hábiles. Si su consulta
                                se responde, se publicará en esta página con su nombre y lugar, nunca con su correo.</p>
                        </x-sitio.aviso-enviado>
                    @endif

                    <form class="form-lux" id="formConsultorio" method="POST" action="{{ route('consultorio.store') }}" novalidate
                          data-envio-unico>
                        @csrf

                        @if (session('exito'))
                            <p class="form-aviso visible" role="status">{{ session('exito') }}</p>
                        @elseif ($errors->has('general'))
                            <p class="form-aviso visible form-aviso--error" role="alert">{{ $errors->first('general') }}</p>
                        @elseif ($errors->any())
                            <p class="form-aviso visible form-aviso--error" role="alert">Revise los campos marcados antes de enviar.</p>
                        @endif

                        <div class="row g-3">
                            <div class="col-md-6">
                                <x-sitio.campo nombre="nombre" etiqueta="Nombre completo" maxlength="75" requerido autocomplete="name" />
                            </div>
                            <div class="col-md-6">
                                <x-sitio.campo nombre="correo" etiqueta="Correo electrónico" tipo="email" maxlength="254" requerido autocomplete="email" />
                            </div>
                        </div>

                        <x-sitio.campo nombre="lugar" etiqueta="Ciudad o sector (opcional)" maxlength="100" />

                        <x-sitio.campo nombre="asunto" etiqueta="Asunto de la consulta" maxlength="50" requerido />

                        <x-sitio.campo nombre="mensaje" etiqueta="Describa su caso" tipo="textarea" maxlength="700" requerido />

                        {{-- Campo trampa: invisible para las personas; si llega lleno, es un robot. --}}
                        <div class="campo-trampa" aria-hidden="true">
                            <label for="sitioWeb">Sitio web</label>
                            <input type="text" id="sitioWeb" name="sitio_web" tabindex="-1" autocomplete="off">
                        </div>

                        <button type="submit" class="btn-enviar" data-texto-enviando="Enviando consulta…">Enviar consulta</button>

                        <p class="form-nota">
                            Al enviar este formulario, acepta que sus datos sean tratados conforme al
                            <a href="https://www.fca.unam.mx/docs/aviso_privacidad.pdf" target="_blank" rel="noopener">aviso de privacidad</a>
                            de la UNAM. Si su consulta se responde, se publicará con su nombre y lugar, nunca con su correo.
                        </p>

                    </form>

                </div>

            </div>
        </section>

    </div>

</main>
@endsection
