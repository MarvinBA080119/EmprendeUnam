import 'bootstrap';
import { montarIslas } from './islas';

// Tipografías instaladas localmente (sin CDN): Geist para la interfaz y
// Newsreader, con tamaño óptico, para los títulos grandes.
import '@fontsource-variable/geist';
import '@fontsource-variable/newsreader/opsz.css';

// Botón Mostrar/Ocultar de los campos de contraseña.
document.addEventListener('click', (evento) => {
    const boton = evento.target.closest('[data-mostrar-clave]');
    if (!boton) {
        return;
    }
    const campo = document.getElementById(boton.dataset.mostrarClave);
    if (!campo) {
        return;
    }
    const visible = campo.type === 'password';
    campo.type = visible ? 'text' : 'password';
    boton.textContent = visible ? 'Ocultar' : 'Mostrar';
    boton.setAttribute('aria-pressed', String(visible));
});

// Formularios que piden confirmación (el texto viene escapado en data-confirmar).
document.addEventListener('submit', (evento) => {
    const formulario = evento.target.closest('form[data-confirmar]');
    if (formulario && !window.confirm(formulario.dataset.confirmar)) {
        evento.preventDefault();
    }
});

// Tras un error de validación, el foco va al primer campo marcado.
const primerInvalido = document.querySelector('.form-control.is-invalid, .form-select.is-invalid');
if (primerInvalido) {
    primerInvalido.focus({ preventScroll: true });
    primerInvalido.scrollIntoView({ block: 'center' });
}

// Sistema (autores y administrador).
montarIslas({
    'formulario-articulo': () => import('./components/autor/FormularioArticulo.vue'),
    'tabla-articulos': () => import('./components/admin/TablaArticulos.vue'),
    'tabla-autores': () => import('./components/admin/TablaAutores.vue'),
    'tabla-mensajes': () => import('./components/admin/TablaMensajes.vue'),
    'tabla-suscriptores': () => import('./components/admin/TablaSuscriptores.vue'),
});
