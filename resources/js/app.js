import 'bootstrap';
import { montarIslas } from './islas';

// Tipografía de interfaz instalada localmente (sin CDN).
import '@fontsource/inter/latin-400.css';
import '@fontsource/inter/latin-500.css';
import '@fontsource/inter/latin-600.css';

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
