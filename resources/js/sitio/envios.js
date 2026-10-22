/*
 * Formularios del sitio público (consultorio, suscripción):
 *  - form[data-envio-unico]: al enviar, el botón muestra «Enviando…» y un
 *    segundo clic no vuelve a mandar el formulario;
 *  - dialog[data-abrir-al-cargar]: la ventana de confirmación que el servidor
 *    pinta tras un envío correcto se abre como modal al cargar la página.
 */

function restaurar() {
    document.querySelectorAll('form[data-enviando]').forEach((formulario) => {
        delete formulario.dataset.enviando;
        formulario.removeAttribute('aria-busy');
        formulario.querySelectorAll('.is-enviando').forEach((boton) => {
            boton.classList.remove('is-enviando');
            boton.removeAttribute('aria-disabled');
            boton.textContent = boton.dataset.textoOriginal ?? boton.textContent;
        });
    });
}

export function iniciarEnvios() {
    document.addEventListener('submit', (evento) => {
        const formulario = evento.target;
        if (!(formulario instanceof HTMLFormElement) || !formulario.hasAttribute('data-envio-unico') || evento.defaultPrevented) {
            return;
        }
        if (formulario.dataset.enviando) {
            evento.preventDefault();
            return;
        }
        formulario.dataset.enviando = '1';
        formulario.setAttribute('aria-busy', 'true');

        const boton = evento.submitter ?? formulario.querySelector('[type=submit]');
        if (boton) {
            boton.dataset.textoOriginal = boton.textContent;
            boton.textContent = boton.dataset.textoEnviando ?? 'Enviando…';
            boton.classList.add('is-enviando');
            boton.setAttribute('aria-disabled', 'true');
        }
    });

    window.addEventListener('pageshow', (evento) => {
        if (evento.persisted) {
            restaurar();
        }
    });

    document.querySelectorAll('dialog[data-abrir-al-cargar]').forEach((dialogo) => {
        // Clic fuera de la ventana = cerrar.
        dialogo.addEventListener('click', (evento) => {
            if (evento.target === dialogo) {
                dialogo.close();
            }
        });
        dialogo.showModal();
    });
}
