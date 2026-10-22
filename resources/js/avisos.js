/**
 * Avisos del sistema (autores y administrador):
 *  - avisar(): notificación breve en la esquina, que se cierra sola (los
 *    errores se quedan hasta que se cierran) y se pausa con el puntero o el foco.
 *  - confirmar(): diálogo modal que reemplaza a window.confirm.
 *  - estado de envío en los formularios POST: el botón muestra «Enviando…»
 *    y un segundo clic no vuelve a mandar el formulario.
 *
 * Los mensajes flash del servidor llegan ya pintados por
 * componentes/alertas.blade.php con el mismo marcado; aquí solo se animan y
 * se programan para cerrarse.
 */

const DURACION = { exito: 6000, aviso: 9000, error: 0 };
const TITULO = { exito: 'Listo', aviso: 'Aviso', error: 'No se pudo completar' };

const ICONO_CERRAR =
    '<svg width="12" height="12" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" focusable="false">' +
    '<path d="M3.72 3.72a.75.75 0 0 1 1.06 0L8 6.94l3.22-3.22a.75.75 0 1 1 1.06 1.06L9.06 8l3.22 3.22a.75.75 0 1 1-1.06 1.06L8 9.06l-3.22 3.22a.75.75 0 0 1-1.06-1.06L6.94 8 3.72 4.78a.75.75 0 0 1 0-1.06Z"/></svg>';

const reducirMovimiento = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

function region() {
    let contenedor = document.querySelector('.emp-avisos');
    if (!contenedor) {
        contenedor = document.createElement('div');
        contenedor.className = 'emp-avisos';
        contenedor.setAttribute('aria-live', 'polite');
        document.body.prepend(contenedor);
    }
    return contenedor;
}

function cerrarAviso(aviso) {
    if (aviso.dataset.cerrando) {
        return;
    }
    aviso.dataset.cerrando = '1';
    clearTimeout(aviso._temporizador);
    aviso.classList.add('is-saliendo');
    const quitar = () => aviso.remove();
    if (reducirMovimiento()) {
        setTimeout(quitar, 150);
    } else {
        aviso.addEventListener('transitionend', quitar, { once: true });
        setTimeout(quitar, 400);
    }
}

function programar(aviso) {
    const duracion = Number(aviso.dataset.duracion ?? 0);
    if (!duracion) {
        return;
    }
    let restante = duracion;
    let inicio = 0;
    const correr = () => {
        inicio = Date.now();
        aviso._temporizador = setTimeout(() => cerrarAviso(aviso), restante);
    };
    const pausar = () => {
        clearTimeout(aviso._temporizador);
        restante = Math.max(1500, restante - (Date.now() - inicio));
    };
    aviso.addEventListener('pointerenter', pausar);
    aviso.addEventListener('pointerleave', correr);
    aviso.addEventListener('focusin', pausar);
    aviso.addEventListener('focusout', correr);
    correr();
}

/** Muestra una notificación. tipo: 'exito' | 'aviso' | 'error'. */
export function avisar(texto, tipo = 'exito') {
    const aviso = document.createElement('div');
    aviso.className = `emp-aviso emp-aviso--${tipo}`;
    aviso.setAttribute('role', tipo === 'error' ? 'alert' : 'status');
    aviso.dataset.duracion = String(DURACION[tipo] ?? DURACION.exito);

    const cuerpo = document.createElement('div');
    cuerpo.className = 'emp-aviso__cuerpo';
    const titulo = document.createElement('p');
    titulo.className = 'emp-aviso__titulo';
    titulo.textContent = TITULO[tipo] ?? TITULO.exito;
    const parrafo = document.createElement('p');
    parrafo.className = 'emp-aviso__texto';
    parrafo.textContent = texto;
    cuerpo.append(titulo, parrafo);

    const cerrar = document.createElement('button');
    cerrar.type = 'button';
    cerrar.className = 'emp-aviso__cerrar';
    cerrar.setAttribute('aria-label', 'Cerrar aviso');
    cerrar.innerHTML = ICONO_CERRAR;

    const marca = document.createElement('span');
    marca.className = 'emp-aviso__marca';
    marca.setAttribute('aria-hidden', 'true');

    aviso.append(marca, cuerpo, cerrar);
    region().append(aviso);
    programar(aviso);

    return aviso;
}

/**
 * Pide confirmación con un diálogo modal. Resuelve true si se acepta.
 * destructivo: el foco inicial queda en «Cancelar» (Enter no confirma por error).
 */
export function confirmar({ titulo, texto = '', aceptar = 'Confirmar', cancelar = 'Cancelar', destructivo = false } = {}) {
    return new Promise((resolver) => {
        const dialogo = document.createElement('dialog');
        dialogo.className = 'emp-dialogo';
        const id = `emp-dialogo-${Date.now()}`;
        dialogo.setAttribute('aria-labelledby', `${id}-titulo`);
        if (texto) {
            dialogo.setAttribute('aria-describedby', `${id}-texto`);
        }

        const formulario = document.createElement('form');
        formulario.method = 'dialog';
        formulario.className = 'emp-dialogo__cuerpo';

        const encabezado = document.createElement('h2');
        encabezado.className = 'emp-dialogo__titulo';
        encabezado.id = `${id}-titulo`;
        encabezado.textContent = titulo;
        formulario.append(encabezado);

        if (texto) {
            const parrafo = document.createElement('p');
            parrafo.className = 'emp-dialogo__texto';
            parrafo.id = `${id}-texto`;
            parrafo.textContent = texto;
            formulario.append(parrafo);
        }

        const acciones = document.createElement('div');
        acciones.className = 'emp-dialogo__acciones';
        const botonCancelar = document.createElement('button');
        botonCancelar.className = 'btn btn-secondary';
        botonCancelar.value = 'cancelar';
        botonCancelar.textContent = cancelar;
        const botonAceptar = document.createElement('button');
        botonAceptar.className = 'btn btn-primary';
        botonAceptar.value = 'aceptar';
        botonAceptar.textContent = aceptar;
        acciones.append(botonCancelar, botonAceptar);
        formulario.append(acciones);
        dialogo.append(formulario);

        // Clic en el fondo (fuera de la hoja del diálogo) = cancelar.
        dialogo.addEventListener('click', (evento) => {
            if (evento.target === dialogo) {
                dialogo.close('cancelar');
            }
        });
        dialogo.addEventListener('close', () => {
            resolver(dialogo.returnValue === 'aceptar');
            setTimeout(() => dialogo.remove(), 300);
        }, { once: true });

        document.body.append(dialogo);
        dialogo.showModal();
        (destructivo ? botonCancelar : botonAceptar).focus();
    });
}

/** «¿Aprobar el artículo? Esta acción no se puede deshacer.» → título + texto. */
function separarPregunta(mensaje) {
    const fin = mensaje.indexOf('?');
    if (fin === -1 || fin === mensaje.length - 1) {
        return { titulo: mensaje, texto: '' };
    }
    return { titulo: mensaje.slice(0, fin + 1).trim(), texto: mensaje.slice(fin + 1).trim() };
}

function marcarEnviando(formulario, boton) {
    formulario.dataset.enviando = '1';
    formulario.setAttribute('aria-busy', 'true');
    if (boton) {
        boton.dataset.textoOriginal = boton.innerHTML;
        boton.classList.add('is-enviando');
        boton.setAttribute('aria-disabled', 'true');
        boton.textContent = boton.dataset.textoEnviando ?? 'Enviando…';
    }
}

function restaurarFormularios() {
    document.querySelectorAll('form[data-enviando]').forEach((formulario) => {
        delete formulario.dataset.enviando;
        delete formulario.dataset.confirmado;
        formulario.removeAttribute('aria-busy');
        formulario.querySelectorAll('.is-enviando').forEach((boton) => {
            boton.classList.remove('is-enviando');
            boton.removeAttribute('aria-disabled');
            if (boton.dataset.textoOriginal) {
                boton.innerHTML = boton.dataset.textoOriginal;
            }
        });
    });
}

export function iniciarAvisos() {
    document.querySelectorAll('.emp-avisos .emp-aviso').forEach(programar);

    document.addEventListener('click', (evento) => {
        const boton = evento.target.closest('.emp-aviso__cerrar');
        if (boton) {
            cerrarAviso(boton.closest('.emp-aviso'));
        }
    });

    document.addEventListener('submit', async (evento) => {
        const formulario = evento.target;
        if (!(formulario instanceof HTMLFormElement) || evento.defaultPrevented) {
            return;
        }
        if (formulario.method.toLowerCase() !== 'post' || formulario.hasAttribute('data-sin-carga')) {
            return;
        }
        if (formulario.dataset.enviando) {
            evento.preventDefault();
            return;
        }

        const boton = evento.submitter ?? formulario.querySelector('[type=submit]');

        if (formulario.dataset.confirmar && !formulario.dataset.confirmado) {
            evento.preventDefault();
            const { titulo, texto } = separarPregunta(formulario.dataset.confirmar);
            const aceptado = await confirmar({
                titulo,
                texto,
                aceptar: formulario.dataset.confirmarBoton ?? boton?.textContent.trim() ?? 'Confirmar',
                destructivo: formulario.hasAttribute('data-destructivo'),
            });
            if (aceptado) {
                formulario.dataset.confirmado = '1';
                formulario.requestSubmit(boton instanceof HTMLElement && boton.form === formulario ? boton : undefined);
            }
            return;
        }

        marcarEnviando(formulario, boton);
    });

    // Al volver con «Atrás» desde la memoria del navegador, los botones no
    // deben quedarse en «Enviando…».
    window.addEventListener('pageshow', (evento) => {
        if (evento.persisted) {
            restaurarFormularios();
        }
    });
}
