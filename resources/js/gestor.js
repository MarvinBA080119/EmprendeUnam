/**
 * Gestor de portada (administrador):
 *  - vista previa inmediata de la portada elegida (también al arrastrarla);
 *  - estado de cada archivo elegido («Por subir», nombre y tamaño);
 *  - zona del .zip de PDF maquetados;
 *  - destacados: una posición la ocupa un solo artículo y el esquema del
 *    inicio se actualiza al elegir.
 * Todo funciona sin JavaScript (el servidor valida); esto solo adelanta lo que
 * el editor verá al guardar.
 */

const peso = (bytes) => (bytes >= 1048576 ? `${(bytes / 1048576).toFixed(1)} MB` : `${Math.max(1, Math.round(bytes / 1024))} KB`);

function aceptarArrastre(zona, campo, alCambiar) {
    zona.addEventListener('dragover', (evento) => {
        evento.preventDefault();
        zona.classList.add('is-arrastrando');
    });
    zona.addEventListener('dragleave', () => zona.classList.remove('is-arrastrando'));
    zona.addEventListener('drop', (evento) => {
        evento.preventDefault();
        zona.classList.remove('is-arrastrando');
        if (evento.dataTransfer?.files?.length) {
            campo.files = evento.dataTransfer.files;
            alCambiar();
        }
    });
}

function vistaPortada() {
    const zona = document.querySelector('[data-vista-portada]');
    const campo = document.getElementById('campo-portada');
    if (!zona || !campo) return;
    const imagen = zona.querySelector('img');
    const estado = document.querySelector('[data-portada-estado]');
    let url = null;

    const mostrar = () => {
        const archivo = campo.files?.[0];
        if (!archivo) return;
        if (!/^image\/(jpeg|png|webp)$/.test(archivo.type)) {
            estado.textContent = 'La portada debe ser una imagen JPG, PNG o WebP.';
            return;
        }
        if (url) URL.revokeObjectURL(url);
        url = URL.createObjectURL(archivo);
        imagen.src = url;
        imagen.alt = 'Vista previa de la portada nueva';
        imagen.hidden = false;
        zona.classList.add('tiene-imagen');
        estado.textContent = `Portada nueva: ${archivo.name} · ${peso(archivo.size)}. Se guarda al enviar el formulario.`;
    };

    campo.addEventListener('change', mostrar);
    aceptarArrastre(zona, campo, mostrar);
}

function estadoArchivos() {
    document.querySelectorAll('input[type=file][data-archivo-estado]').forEach((campo) => {
        const insignia = document.querySelector(`[data-estado-de="${campo.id}"]`);
        const detalle = campo.parentElement.querySelector('[data-archivo-detalle]');
        const original = detalle?.textContent;
        campo.addEventListener('change', () => {
            const archivo = campo.files?.[0];
            if (!insignia) return;
            if (archivo) {
                insignia.textContent = insignia.dataset.textoNuevo ?? 'Por subir';
                insignia.className = 'emp-insignia emp-insignia-revision';
                if (detalle) detalle.textContent = `${archivo.name} · ${peso(archivo.size)}. Se sube al guardar.`;
            } else if (detalle) {
                detalle.textContent = original;
            }
        });
    });
}

function zonaZip() {
    const zona = document.querySelector('[data-zona-archivo]');
    const campo = document.getElementById('campo-zip');
    if (!zona || !campo) return;
    const nombre = zona.querySelector('[data-zona-nombre]');
    const mostrar = () => {
        const archivo = campo.files?.[0];
        if (!archivo) return;
        nombre.textContent = `${archivo.name} · ${peso(archivo.size)}`;
        zona.classList.add('tiene-archivo');
    };
    campo.addEventListener('change', mostrar);
    aceptarArrastre(zona, campo, mostrar);
}

function destacados() {
    const formulario = document.querySelector('[data-form-destacados]');
    const esquema = document.querySelector('[data-esquema]');
    if (!formulario || !esquema) return;
    const conteo = formulario.querySelector('[data-conteo-destacados]');
    const requeridos = Number(formulario.dataset.requeridos);

    const dibujar = (cambiada) => {
        let elegidos = 0;
        esquema.querySelectorAll('[data-casilla]').forEach((casilla) => {
            const posicion = casilla.dataset.casilla;
            const marcado = formulario.querySelector(`input[type=radio][value="${posicion}"]:checked`);
            const fila = marcado?.closest('[data-articulo]');
            casilla.classList.toggle('is-vacia', !fila);
            casilla.querySelector('[data-casilla-titulo]').textContent = fila ? fila.dataset.titulo : 'Sin elegir';
            const cursos = Number(fila?.dataset.cursos ?? 0);
            casilla.querySelector('[data-casilla-cursos]').textContent = fila ? `${cursos} ${cursos === 1 ? 'curso' : 'cursos'}` : '';
            if (fila) elegidos++;
            if (posicion === cambiada) {
                casilla.classList.add('acaba-de-cambiar');
                setTimeout(() => casilla.classList.remove('acaba-de-cambiar'), 700);
            }
        });
        conteo.textContent = `${elegidos} de ${requeridos} elegidos`;
        conteo.classList.toggle('is-completo', elegidos === requeridos);
    };

    formulario.addEventListener('change', (evento) => {
        const radio = evento.target;
        if (radio.type !== 'radio' || radio.value === '') {
            dibujar(null);
            return;
        }
        // Cada posición la ocupa un solo artículo: el que la tenía queda fuera del inicio.
        formulario.querySelectorAll(`input[type=radio][value="${radio.value}"]:checked`).forEach((otro) => {
            if (otro !== radio) {
                otro.closest('fieldset').querySelector('input[value=""]').checked = true;
            }
        });
        dibujar(radio.value);
    });

    dibujar(null);
}

export function iniciarGestor() {
    vistaPortada();
    estadoArchivos();
    zonaZip();
    destacados();
}
