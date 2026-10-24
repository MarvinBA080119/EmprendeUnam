/**
 * Mesa de diseño: al elegir (o soltar) el PDF diseñado sobre un artículo, se
 * sube en ese momento y la fila muestra el avance real de la subida. Al
 * terminar, la página se recarga con el aviso y la fila resaltada.
 * Sin JavaScript, cada fila es un formulario normal con su botón «Enviar».
 */
import { peso } from './archivos';
import { confirmar } from './avisos';

function subir(fila, formulario, archivo) {
    const estado = fila.querySelector('[data-subida]');
    const nombre = estado.querySelector('[data-subida-nombre]');
    const porcentaje = estado.querySelector('[data-subida-porcentaje]');
    const barra = estado.querySelector('[data-subida-barra]');
    const maximo = Number(formulario.closest('[data-mesa]').dataset.maxKb) * 1024;

    const mostrar = (texto, avance, error = false) => {
        estado.hidden = false;
        estado.classList.toggle('is-error', error);
        nombre.textContent = texto;
        porcentaje.textContent = avance === null ? '' : `${Math.round(avance * 100)} %`;
        barra.style.setProperty('--avance', String(avance ?? 0));
    };

    if (archivo.type && archivo.type !== 'application/pdf' && !/\.pdf$/i.test(archivo.name)) {
        mostrar(`«${archivo.name}» no es un PDF. Exporta el diseño a PDF.`, null, true);
        return;
    }
    if (maximo && archivo.size > maximo) {
        mostrar(`«${archivo.name}» pesa ${peso(archivo.size)}; el máximo es ${peso(maximo)}.`, null, true);
        return;
    }

    const datos = new FormData(formulario);
    datos.set('pdf', archivo);

    const peticion = new XMLHttpRequest();
    peticion.open('POST', formulario.action);
    peticion.setRequestHeader('Accept', 'application/json');
    peticion.setRequestHeader('X-Subida-Mesa', '1');
    peticion.responseType = 'json';

    fila.classList.add('is-subiendo');
    mostrar(`Subiendo ${archivo.name} · ${peso(archivo.size)}`, 0);

    peticion.upload.addEventListener('progress', (evento) => {
        if (evento.lengthComputable) {
            mostrar(`Subiendo ${archivo.name} · ${peso(archivo.size)}`, evento.loaded / evento.total);
        }
    });

    peticion.addEventListener('load', () => {
        if (peticion.status === 200 && peticion.response?.url) {
            mostrar(`${archivo.name} · listo`, 1);
            window.location.assign(peticion.response.url);
            return;
        }
        fila.classList.remove('is-subiendo');
        const mensaje = peticion.response?.mensaje
            ?? (peticion.status === 413 ? 'El PDF es más grande de lo que acepta el servidor.' : 'No se pudo subir el PDF. Intenta de nuevo.');
        mostrar(mensaje, null, true);
    });

    peticion.addEventListener('error', () => {
        fila.classList.remove('is-subiendo');
        mostrar('Se perdió la conexión. Revisa tu red e intenta de nuevo.', null, true);
    });

    peticion.send(datos);
}

async function elegir(fila, archivo) {
    const formulario = fila.querySelector('[data-subir]');
    if (!formulario || !archivo || fila.classList.contains('is-subiendo')) return;

    if (fila.classList.contains('is-disenado')) {
        const aceptado = await confirmar({
            titulo: `¿Reemplazar el PDF de «${fila.dataset.titulo}»?`,
            texto: 'El PDF anterior se borra y se publica el nuevo.',
            aceptar: 'Reemplazar',
        });
        if (!aceptado) return;
    }

    subir(fila, formulario, archivo);
}

export function iniciarMesa() {
    const mesa = document.querySelector('[data-mesa]');
    if (!mesa) return;

    mesa.querySelectorAll('[data-sin-js]').forEach((boton) => { boton.hidden = true; });
    mesa.querySelectorAll('[data-subir]').forEach((formulario) => formulario.setAttribute('data-sin-carga', ''));

    mesa.addEventListener('change', (evento) => {
        const campo = evento.target;
        if (!(campo instanceof HTMLInputElement) || campo.type !== 'file') return;
        const archivo = campo.files?.[0];
        campo.value = '';
        elegir(campo.closest('[data-mesa-fila]'), archivo);
    });

    // Arrastrar un archivo a la ventana marca las filas que lo aceptan; la que
    // queda bajo el puntero se tiñe y, al soltar, recibe el PDF.
    let profundidad = 0;
    const limpiar = () => {
        profundidad = 0;
        mesa.classList.remove('is-arrastrando');
        mesa.querySelectorAll('.is-destino').forEach((fila) => fila.classList.remove('is-destino'));
    };
    const traeArchivos = (evento) => Array.from(evento.dataTransfer?.types ?? []).includes('Files');

    window.addEventListener('dragenter', (evento) => {
        if (!traeArchivos(evento)) return;
        profundidad++;
        mesa.classList.add('is-arrastrando');
    });
    window.addEventListener('dragleave', () => {
        profundidad = Math.max(0, profundidad - 1);
        if (profundidad === 0) limpiar();
    });
    window.addEventListener('dragover', (evento) => {
        if (!traeArchivos(evento)) return;
        evento.preventDefault();
        const fila = evento.target.closest?.('[data-mesa-fila]');
        const acepta = fila?.querySelector('[data-subir]');
        evento.dataTransfer.dropEffect = acepta ? 'copy' : 'none';
        mesa.querySelectorAll('.is-destino').forEach((otra) => { if (otra !== fila) otra.classList.remove('is-destino'); });
        if (acepta) fila.classList.add('is-destino');
    });
    window.addEventListener('drop', (evento) => {
        if (!traeArchivos(evento)) return;
        evento.preventDefault();
        const fila = evento.target.closest?.('[data-mesa-fila]');
        limpiar();
        if (fila?.querySelector('[data-subir]')) {
            elegir(fila, evento.dataTransfer.files[0]);
        }
    });

    // La fila recién diseñada queda a la vista al volver.
    mesa.querySelector('.is-recien')?.scrollIntoView({ block: 'center' });
}
