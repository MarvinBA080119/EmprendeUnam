import 'bootstrap';
import { vigilarArchivos } from './archivos';
import { iniciarFlipbook } from './flipbook';
import { iniciarArticuloNumero } from './articulo-numero';
import { iniciarAvisos } from './avisos';
import { iniciarGestor } from './gestor';
import { iniciarMesa } from './mesa';
import { montarIslas } from './islas';
import { vigilarInactividad } from './sesion';

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

// Notificaciones, diálogo de confirmación (form[data-confirmar]) y estado de
// envío de los formularios.
iniciarAvisos();

// Límite de peso de los archivos, antes de enviar.
vigilarArchivos();

// Gestor de portada: vista previa, estado de archivos y destacados.
iniciarGestor();

// Artículo del número: autollenado, PDF del sistema y cursos.
iniciarArticuloNumero();

// Generador de flipbooks: subida, avance y vista previa.
iniciarFlipbook();

// Mesa de diseño: subida del PDF diseñado con avance y arrastrar y soltar.
iniciarMesa();

// Cierre de sesión por inactividad (solo en los paneles con sesión).
vigilarInactividad();

// Páginas con datos de la cuenta o del acceso: si el navegador las restaura de
// su memoria con «Atrás», se vuelven a pedir al servidor (que decide si la
// sesión sigue abierta).
window.addEventListener('pageshow', (evento) => {
    if (evento.persisted && document.body.hasAttribute('data-privada')) {
        window.location.reload();
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
