/**
 * Cierre de sesión por inactividad (autores y administrador).
 *
 * El servidor cierra la sesión tras session.inactividad minutos sin
 * peticiones (ExpiraPorInactividad). Aquí se lleva la misma cuenta en el
 * navegador, compartida entre pestañas con localStorage:
 *  - la actividad real (ratón, teclado, clic, desplazamiento, toque) avisa al
 *    servidor como máximo una vez por minuto, para que la sesión no venza
 *    mientras alguien llena un formulario largo sin recargar la página;
 *  - dos minutos antes del límite aparece un diálogo para seguir trabajando;
 *  - al llegar al límite se cierra la sesión y se vuelve al login con el aviso.
 *
 * La configuración y el formulario de cierre vienen de
 * componentes/sesion-inactiva.blade.php.
 */

const CLAVE_CONTACTO = 'emp:sesion-contacto';
const CLAVE_CIERRE = 'emp:sesion-cerrada';
const MIN_ENTRE_LATIDOS = 60 * 1000;
const MARGEN = 10 * 1000;
const EVENTOS = ['mousemove', 'keydown', 'click', 'scroll', 'touchstart'];

let contactoLocal = Date.now();

function leerAlmacen(clave) {
    try {
        return Number(window.localStorage.getItem(clave)) || 0;
    } catch {
        return 0;
    }
}

function escribirAlmacen(clave, valor) {
    try {
        window.localStorage.setItem(clave, String(valor));
    } catch {
        // almacenamiento bloqueado: cada pestaña lleva su propia cuenta
    }
}

const ultimoContacto = () => Math.max(contactoLocal, leerAlmacen(CLAVE_CONTACTO));

/** Cualquier petición que llegó al servidor renueva la sesión (también las de http.js). */
export function registrarContacto() {
    contactoLocal = Date.now();
    escribirAlmacen(CLAVE_CONTACTO, contactoLocal);
}

function formatear(milisegundos) {
    const total = Math.max(0, Math.ceil(milisegundos / 1000));
    return `${Math.floor(total / 60)}:${String(total % 60).padStart(2, '0')}`;
}

export function vigilarInactividad() {
    const config = document.getElementById('emp-sesion');
    if (!config) {
        return;
    }

    const limite = Number(config.dataset.limite) * 1000;
    const anticipacion = Math.min(Number(config.dataset.aviso) * 1000, limite / 2);
    const urlMantener = config.dataset.mantener;
    const urlLogin = config.dataset.login;
    const formularioCierre = document.getElementById('emp-sesion-cerrar');
    const dialogo = document.getElementById('emp-sesion-aviso');
    const cuenta = dialogo.querySelector('[data-sesion-cuenta]');
    const token = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    let cerrando = false;
    let enviandoLatido = false;

    registrarContacto();

    const irAlLogin = () => {
        cerrando = true;
        window.location.assign(urlLogin);
    };

    async function latido() {
        if (enviandoLatido) {
            return;
        }
        enviandoLatido = true;
        try {
            const respuesta = await fetch(urlMantener, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': token },
            });
            if (respuesta.ok) {
                registrarContacto();
            } else if (respuesta.status === 401 || respuesta.status === 419) {
                irAlLogin();
            }
        } catch {
            // sin conexión: se reintenta con la siguiente actividad
        } finally {
            enviandoLatido = false;
        }
    }

    function cerrarSesion() {
        if (cerrando) {
            return;
        }
        cerrando = true;
        escribirAlmacen(CLAVE_CIERRE, Date.now());
        formularioCierre.submit();
    }

    function actividad() {
        if (cerrando || dialogo.open) {
            return;
        }
        if (Date.now() - ultimoContacto() > MIN_ENTRE_LATIDOS) {
            latido();
        }
    }

    function revisar() {
        if (cerrando) {
            return;
        }
        const restante = ultimoContacto() + limite - MARGEN - Date.now();

        if (restante <= 0) {
            cerrarSesion();
        } else if (restante <= anticipacion) {
            cuenta.textContent = formatear(restante);
            if (!dialogo.open) {
                dialogo.showModal();
                dialogo.querySelector('[data-sesion-seguir]').focus();
            }
        } else if (dialogo.open) {
            // Otra pestaña siguió trabajando.
            dialogo.close();
        }
    }

    const seguir = () => {
        dialogo.close();
        latido();
    };

    EVENTOS.forEach((nombre) => window.addEventListener(nombre, actividad, { passive: true }));
    dialogo.querySelector('[data-sesion-seguir]').addEventListener('click', seguir);
    dialogo.querySelector('[data-sesion-salir]').addEventListener('click', cerrarSesion);
    // Esc en el aviso también significa «aquí sigo».
    dialogo.addEventListener('cancel', (evento) => {
        evento.preventDefault();
        seguir();
    });

    // Cerrar sesión desde el menú (o por inactividad en otra pestaña) cierra todas.
    document.addEventListener('submit', (evento) => {
        if (evento.target.matches?.('form[data-cierra-sesion]')) {
            cerrando = true;
            escribirAlmacen(CLAVE_CIERRE, Date.now());
        }
    });
    window.addEventListener('storage', (evento) => {
        if (evento.key === CLAVE_CIERRE && !cerrando) {
            irAlLogin();
        }
    });

    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') {
            revisar();
        }
    });
    window.setInterval(revisar, 1000);
}
