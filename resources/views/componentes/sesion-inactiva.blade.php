{{-- Cierre de sesión por inactividad (resources/js/sesion.js): límite en
     segundos, el formulario que cierra la sesión con el motivo y el diálogo que
     avisa dos minutos antes. --}}
<div id="emp-sesion" hidden
     data-limite="{{ \App\Http\Middleware\ExpiraPorInactividad::segundos() }}"
     data-aviso="120"
     data-mantener="{{ route('sesion.mantener') }}"
     data-login="{{ route('login') }}">
    <form id="emp-sesion-cerrar" method="POST" action="{{ route('logout') }}" data-sin-carga>
        @csrf
        <input type="hidden" name="motivo" value="inactividad">
    </form>
</div>

<dialog class="emp-dialogo" id="emp-sesion-aviso" aria-labelledby="emp-sesion-titulo" aria-describedby="emp-sesion-texto">
    <div class="emp-dialogo__cuerpo">
        <h2 class="emp-dialogo__titulo" id="emp-sesion-titulo">¿Sigues ahí?</h2>
        <p class="emp-dialogo__texto" id="emp-sesion-texto">
            Por seguridad, tu sesión se cerrará en <strong class="emp-dialogo__cuenta" data-sesion-cuenta>2:00</strong>
            por inactividad. Lo que no hayas guardado se perderá.
        </p>
        <div class="emp-dialogo__acciones">
            <button type="button" class="btn btn-secondary" data-sesion-salir>Cerrar sesión</button>
            <button type="button" class="btn btn-primary" data-sesion-seguir>Seguir trabajando</button>
        </div>
    </div>
</dialog>
