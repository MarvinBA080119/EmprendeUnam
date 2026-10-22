@extends('layouts.sitio')

{{-- Confirmación y baja del newsletter (enlaces de los correos, sin sesión). --}}
@php
    $tituloPagina = 'Newsletter';
    $paginaId = '';
    $recursos = ['resources/css/sitio/premium_sections.css', 'resources/css/sitio/formularios_media.css'];
    $correo = isset($persona) ? $persona->correo?->comu_valor_comunicacion : null;
@endphp

@section('contenido')
<main id="contenido">

    <header class="head-solo">
        <div class="container">
            <p class="eyebrow mb-0">Newsletter</p>
            <h1>
                @switch($estado)
                    @case('confirmada') ¡Suscripción confirmada! @break
                    @case('ya_confirmada') Tu suscripción ya estaba confirmada @break
                    @case('confirmar_baja') ¿Dejar de recibir el newsletter? @break
                    @case('baja') Listo, {{ $persona->pers_nombre }} @break
                    @case('ya_baja') Ya estabas dado de baja @break
                    @case('vencido') El enlace de confirmación venció @break
                    @default Este enlace ya no es válido
                @endswitch
            </h1>
        </div>
    </header>

    <div class="container pb-5">
        <div class="row">
            <div class="col-lg-8">
                <div class="form-lux">
                    @switch($estado)
                        @case('confirmada')
                        @case('ya_confirmada')
                            <p class="consultorio__entrada">
                                Gracias, {{ $persona->pers_nombre }}. Te escribiremos a <strong>{{ $correo }}</strong>
                                cada vez que publiquemos una nueva edición.
                            </p>
                            <a href="{{ route('newsletter') }}" class="btn-enviar">Leer el newsletter</a>
                            @break

                        @case('confirmar_baja')
                            <p class="consultorio__entrada">
                                {{ $persona->pers_nombre }}, vas a cancelar los avisos de nuevas ediciones que llegan a
                                <strong>{{ $correo }}</strong>.
                            </p>
                            <form method="POST" action="{{ route('newsletter.baja.confirmar', $token) }}">
                                @csrf
                                <button type="submit" class="btn-enviar">Sí, darme de baja</button>
                            </form>
                            @break

                        @case('baja')
                            <p class="consultorio__entrada">
                                Ya no recibirás avisos de nuevas ediciones en <strong>{{ $correo }}</strong>.
                            </p>
                            <p>El newsletter sigue siendo de acceso libre: puedes leerlo cuando quieras y volver a suscribirte.</p>
                            <a href="{{ route('newsletter') }}" class="btn-lux"><span>Volver al newsletter</span></a>
                            @break

                        @case('ya_baja')
                            <p class="consultorio__entrada">El correo <strong>{{ $correo }}</strong> no recibe avisos del newsletter.</p>
                            <a href="{{ route('suscripcion') }}" class="btn-lux"><span>Volver a suscribirme</span></a>
                            @break

                        @case('vencido')
                            <p class="consultorio__entrada">
                                Por seguridad, el enlace vale {{ config('emprende.newsletter_horas_confirmacion') }} horas.
                                Tu suscripción con <strong>{{ $correo }}</strong> sigue pendiente de confirmación:
                                vuelve a suscribirte y te enviaremos un enlace nuevo.
                            </p>
                            <a href="{{ route('suscripcion') }}" class="btn-enviar">Enviarme un enlace nuevo</a>
                            @break

                        @default
                            <p class="consultorio__entrada">
                                Es posible que esté incompleto o que ya se haya usado. Copia el enlace completo desde el
                                correo o escríbenos a <a href="mailto:revistaemprendedores@fca.unam.mx">revistaemprendedores@fca.unam.mx</a>.
                            </p>
                    @endswitch
                </div>
            </div>
        </div>
    </div>

</main>
@endsection
