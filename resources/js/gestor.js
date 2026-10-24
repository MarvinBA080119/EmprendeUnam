/**
 * Gestor de portada (administrador):
 *  - vista previa inmediata de la portada elegida (también al arrastrarla);
 *  - estado de cada archivo elegido («Por subir», nombre y tamaño);
 *  - destacados: una posición la ocupa un solo artículo y el esquema del
 *    inicio se actualiza al elegir; también se acomodan arrastrando.
 * Todo funciona sin JavaScript (el servidor valida); esto solo adelanta lo que
 * el editor verá al guardar.
 */

import { dentroDelLimite, peso } from './archivos';

function aceptarArrastre(zona, campo, alCambiar) {
    zona.addEventListener('dragover', (evento) => {
        evento.preventDefault();
        zona.classList.add('is-arrastrando');
    });
    zona.addEventListener('dragleave', () => zona.classList.remove('is-arrastrando'));
    zona.addEventListener('drop', (evento) => {
        evento.preventDefault();
        zona.classList.remove('is-arrastrando');
        if (evento.dataTransfer?.files?.length && dentroDelLimite(campo, evento.dataTransfer.files[0])) {
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

function destacados() {
    const formulario = document.querySelector('[data-form-destacados]');
    const esquema = document.querySelector('[data-esquema]');
    if (!formulario || !esquema) return;
    const conteo = formulario.querySelector('[data-conteo-destacados]');
    const anuncio = formulario.querySelector('[data-anuncio-destacados]');
    const lista = formulario.querySelector('.emp-numero-articulos');
    const requeridos = Number(formulario.dataset.requeridos);

    const radio = (id, posicion) => formulario.querySelector(`input[name="posiciones[${id}]"][value="${posicion}"]`);
    const posicionDe = (id) => formulario.querySelector(`input[name="posiciones[${id}]"]:checked`)?.value ?? '';
    const fila = (id) => formulario.querySelector(`[data-articulo="${id}"]`);

    const dibujar = (cambiada) => {
        let elegidos = 0;
        esquema.querySelectorAll('[data-casilla]').forEach((casilla) => {
            const posicion = casilla.dataset.casilla;
            const marcado = formulario.querySelector(`input[type=radio][value="${posicion}"]:checked`);
            const ocupante = marcado?.closest('[data-articulo]');
            casilla.classList.toggle('is-vacia', !ocupante);
            casilla.querySelector('[data-casilla-titulo]').textContent = ocupante ? ocupante.dataset.titulo : 'Sin elegir';
            const cursos = Number(ocupante?.dataset.cursos ?? 0);
            casilla.querySelector('[data-casilla-cursos]').textContent = ocupante ? `${cursos} ${cursos === 1 ? 'curso' : 'cursos'}` : '';
            // Una casilla ocupada también se arrastra (para intercambiar o sacar del inicio).
            casilla.draggable = Boolean(ocupante);
            casilla.dataset.ocupante = ocupante?.dataset.articulo ?? '';
            if (ocupante) elegidos++;
            if (posicion === cambiada) {
                casilla.classList.add('acaba-de-cambiar');
                setTimeout(() => casilla.classList.remove('acaba-de-cambiar'), 700);
            }
        });
        conteo.textContent = `${elegidos} de ${requeridos} elegidos`;
        conteo.classList.toggle('is-completo', elegidos === requeridos);
    };

    /** Pone el artículo en la posición; el que la ocupaba toma la que deja (o sale del inicio). */
    const colocar = (id, posicion) => {
        const anterior = posicionDe(id);
        if (anterior === posicion) return;
        const ocupante = esquema.querySelector(`[data-casilla="${posicion}"]`)?.dataset.ocupante;
        radio(id, posicion)?.click();
        if (ocupante && ocupante !== id) {
            radio(ocupante, anterior)?.click();
        }
        const rotulo = posicion === ''
            ? 'ya no se muestra en el inicio'
            : `pasó a ${esquema.querySelector(`[data-casilla="${posicion}"]`).dataset.rotulo.toLowerCase()}`;
        anuncio.textContent = `«${fila(id).dataset.titulo}» ${rotulo}.`;
    };

    formulario.addEventListener('change', (evento) => {
        const elegido = evento.target;
        if (elegido.type !== 'radio' || elegido.value === '') {
            dibujar(null);
            return;
        }
        // Cada posición la ocupa un solo artículo: el que la tenía queda fuera del inicio.
        formulario.querySelectorAll(`input[type=radio][value="${elegido.value}"]:checked`).forEach((otro) => {
            if (otro !== elegido) {
                otro.closest('fieldset').querySelector('input[value=""]').checked = true;
            }
        });
        dibujar(elegido.value);
    });

    // Arrastrar y soltar: una fila (por su asa) o una casilla ocupada hacia una
    // casilla; una casilla hacia la lista la saca del inicio. Los botones de
    // posición siguen siendo la forma con teclado y en pantallas táctiles.
    let arrastrado = null;
    // Desplazamiento automático: al arrastrar cerca del borde (bajo el
    // encabezado fijo o abajo), la página se mueve para alcanzar el esquema.
    let velocidad = 0;
    let cuadro = null;
    const desplazar = () => {
        if (!velocidad) {
            cuadro = null;
            return;
        }
        window.scrollBy(0, velocidad);
        cuadro = requestAnimationFrame(desplazar);
    };
    document.addEventListener('dragover', (evento) => {
        if (!arrastrado) return;
        const arriba = (document.querySelector('.emp-cab')?.offsetHeight ?? 0) + 72;
        const abajo = window.innerHeight - 72;
        velocidad = evento.clientY < arriba ? -14 : evento.clientY > abajo ? 14 : 0;
        if (velocidad && !cuadro) cuadro = requestAnimationFrame(desplazar);
    });
    const terminar = () => {
        velocidad = 0;
        arrastrado = null;
        esquema.classList.remove('is-recibiendo');
        lista.classList.remove('is-recibiendo', 'is-destino');
        document.querySelectorAll('.is-destino, .is-arrastrado').forEach((el) => el.classList.remove('is-destino', 'is-arrastrado'));
    };
    const empezar = (evento, id, fuente) => {
        arrastrado = { id, desdeCasilla: fuente.hasAttribute('data-casilla') };
        evento.dataTransfer.effectAllowed = 'move';
        evento.dataTransfer.setData('text/plain', id);
        esquema.classList.add('is-recibiendo');
        if (arrastrado.desdeCasilla) lista.classList.add('is-recibiendo');
        requestAnimationFrame(() => (arrastrado.desdeCasilla ? fuente : fila(id)).classList.add('is-arrastrado'));
    };

    // Todo el recuadro del artículo se arrastra (el asa solo lo indica).
    formulario.querySelectorAll('[data-articulo][draggable]').forEach((elemento) => {
        // Los enlaces y la miniatura de adentro no se arrastran solos: así,
        // empezar a arrastrar sobre ellos mueve la fila completa.
        elemento.querySelectorAll('a, img').forEach((interno) => interno.setAttribute('draggable', 'false'));
        elemento.addEventListener('dragstart', (evento) => {
            if (evento.target !== elemento) return;
            evento.dataTransfer.setDragImage(elemento, 24, 24);
            empezar(evento, elemento.dataset.articulo, elemento);
        });
        elemento.addEventListener('dragend', terminar);
    });

    esquema.querySelectorAll('[data-casilla]').forEach((casilla) => {
        casilla.addEventListener('dragstart', (evento) => {
            if (!casilla.dataset.ocupante) {
                evento.preventDefault();
                return;
            }
            empezar(evento, casilla.dataset.ocupante, casilla);
        });
        casilla.addEventListener('dragend', terminar);
        casilla.addEventListener('dragover', (evento) => {
            if (!arrastrado) return;
            evento.preventDefault();
            evento.dataTransfer.dropEffect = 'move';
            esquema.querySelectorAll('.is-destino').forEach((otra) => otra !== casilla && otra.classList.remove('is-destino'));
            casilla.classList.add('is-destino');
        });
        casilla.addEventListener('dragleave', (evento) => {
            if (!casilla.contains(evento.relatedTarget)) casilla.classList.remove('is-destino');
        });
        casilla.addEventListener('drop', (evento) => {
            evento.preventDefault();
            if (arrastrado) colocar(arrastrado.id, casilla.dataset.casilla);
            terminar();
        });
    });

    lista.addEventListener('dragover', (evento) => {
        if (!arrastrado?.desdeCasilla) return;
        evento.preventDefault();
        lista.classList.add('is-destino');
    });
    lista.addEventListener('dragleave', (evento) => {
        if (!lista.contains(evento.relatedTarget)) lista.classList.remove('is-destino');
    });
    lista.addEventListener('drop', (evento) => {
        if (!arrastrado?.desdeCasilla) return;
        evento.preventDefault();
        colocar(arrastrado.id, '');
        terminar();
    });

    dibujar(null);
}

/** eBook del número: un flipbook generado, ninguno u otro enlace https. */
function ebook() {
    const caja = document.querySelector('[data-ebook]');
    if (!caja) return;
    const origen = caja.querySelector('[data-ebook-origen]');
    const enlace = caja.querySelector('[data-ebook-enlace]');
    origen.hidden = false;

    const aplicar = (cambio) => {
        const otro = origen.value === 'otro';
        enlace.hidden = !otro;
        if (!cambio) return;
        if (otro) {
            if (enlace.value.startsWith('/flipbook/')) enlace.value = '';
            enlace.focus();
        } else {
            enlace.value = origen.value;
        }
    };
    origen.addEventListener('change', () => aplicar(true));
    aplicar(false);
}

export function iniciarGestor() {
    ebook();
    vistaPortada();
    estadoArchivos();
    destacados();
}
