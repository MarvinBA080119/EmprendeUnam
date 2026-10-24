/**
 * Generador de flipbooks (administrador): sube el PDF con avance real, sigue
 * la conversión en el servidor (consulta el estado cada segundo), muestra las
 * páginas convertidas a medida que salen y, al terminar, la vista previa del
 * flipbook. Sin JavaScript el formulario se envía normal y la página indica
 * el número en curso.
 */
import { dentroDelLimite, peso } from './archivos';
import { avisar } from './avisos';

const ETAPAS = ['subir', 'convertir', 'armar'];

export function iniciarFlipbook() {
    const generador = document.querySelector('[data-generador]');
    if (!generador) return;

    const formulario = generador.querySelector('[data-form-flipbook]');
    const campo = formulario.querySelector('#flip-pdf');
    const zona = formulario.querySelector('[data-zona-pdf]');
    const nombre = zona.querySelector('[data-zona-nombre]');
    const detalle = zona.querySelector('[data-zona-detalle]');
    const detalleOriginal = detalle.textContent;
    const error = formulario.querySelector('[data-error-flipbook]');
    const boton = formulario.querySelector('[data-boton-generar]');
    const pasos = generador.querySelector('[data-pasos]');
    const progreso = generador.querySelector('[data-progreso]');
    const textoProgreso = progreso.querySelector('[data-progreso-texto]');
    const cifra = progreso.querySelector('[data-progreso-cifra]');
    const barra = progreso.querySelector('[data-progreso-barra]');
    const tira = generador.querySelector('[data-tira]');
    const vista = generador.querySelector('[data-vista]');
    const marco = vista.querySelector('[data-vista-marco]');
    const abrir = vista.querySelector('[data-vista-abrir]');
    const nota = vista.querySelector('[data-vista-nota]');
    let paginasMostradas = 0;
    let sondeo = null;

    /* ---- Archivo elegido ---- */
    const mostrarArchivo = () => {
        const archivo = campo.files?.[0];
        zona.classList.toggle('tiene-archivo', Boolean(archivo));
        nombre.textContent = archivo ? archivo.name : 'Arrastra el PDF aquí o elige el archivo';
        detalle.textContent = archivo ? `${peso(archivo.size)} · listo para generar` : detalleOriginal;
    };
    campo.addEventListener('change', mostrarArchivo);
    ['dragenter', 'dragover'].forEach((tipo) => zona.addEventListener(tipo, (evento) => {
        evento.preventDefault();
        zona.classList.add('is-arrastrando');
    }));
    ['dragleave', 'drop'].forEach((tipo) => zona.addEventListener(tipo, () => zona.classList.remove('is-arrastrando')));
    zona.addEventListener('drop', (evento) => {
        evento.preventDefault();
        const archivo = evento.dataTransfer?.files?.[0];
        if (archivo && dentroDelLimite(campo, archivo)) {
            campo.files = evento.dataTransfer.files;
            mostrarArchivo();
        }
    });

    /* ---- Pasos y avance ---- */
    const marcarEtapa = (actual, terminado = false) => {
        const indice = ETAPAS.indexOf(actual);
        pasos.querySelectorAll('[data-paso]').forEach((paso) => {
            const posicion = ETAPAS.indexOf(paso.dataset.paso);
            paso.classList.toggle('is-hecho', terminado || posicion < indice);
            paso.classList.toggle('is-actual', !terminado && posicion === indice);
        });
    };
    const avanzar = (fraccion, texto) => {
        const porcentaje = Math.max(0, Math.min(100, Math.round(fraccion * 100)));
        progreso.hidden = false;
        textoProgreso.textContent = texto;
        cifra.textContent = `${porcentaje} %`;
        barra.style.setProperty('--avance', String(porcentaje / 100));
        barra.setAttribute('aria-valuenow', String(porcentaje));
    };
    const ocupado = (si) => {
        boton.disabled = si;
        formulario.setAttribute('aria-busy', String(si));
        generador.classList.toggle('is-trabajando', si);
    };
    const mostrarError = (mensaje) => {
        error.textContent = mensaje;
        error.hidden = false;
        progreso.classList.add('is-error');
        textoProgreso.textContent = mensaje;
        ocupado(false);
        avisar(mensaje, 'error');
    };

    const urlEstado = (numero) => generador.dataset.urlEstado.replace('__N__', numero);
    const urlMiniatura = (numero, pagina) => generador.dataset.urlMiniatura.replace('__N__', numero).replace('__P__', pagina);

    const agregarHojas = (numero, hasta) => {
        for (let pagina = paginasMostradas + 1; pagina <= hasta; pagina++) {
            const hoja = document.createElement('li');
            hoja.className = 'emp-tira-hojas__hoja';
            const imagen = document.createElement('img');
            imagen.alt = `Página ${pagina}`;
            imagen.loading = 'lazy';
            imagen.src = urlMiniatura(numero, pagina);
            const numeroHoja = document.createElement('span');
            numeroHoja.textContent = String(pagina);
            hoja.append(imagen, numeroHoja);
            tira.append(hoja);
        }
        paginasMostradas = Math.max(paginasMostradas, hasta);
        tira.scrollLeft = tira.scrollWidth;
    };

    const terminado = async (estado) => {
        marcarEtapa('armar', true);
        avanzar(1, `Listo: ${estado.total} páginas.`);
        ocupado(false);
        vista.hidden = false;
        const url = `${estado.url_flipbook}/?v=${encodeURIComponent(estado.generado ?? Date.now())}`;
        marco.src = url;
        abrir.href = estado.url_flipbook;
        nota.textContent = `Flipbook del número ${estado.numero} guardado en ${estado.url_flipbook}. Ya puedes elegirlo como eBook en «Portada del sitio».`;
        avisar(`Se generó el flipbook del número ${estado.numero}.`);
        // La galería se actualiza sin recargar la página.
        try {
            const respuesta = await fetch(window.location.pathname, { headers: { Accept: 'text/html' } });
            const html = new DOMParser().parseFromString(await respuesta.text(), 'text/html');
            const nueva = html.querySelector('[data-galeria]');
            if (nueva) document.querySelector('[data-galeria]').replaceWith(nueva);
        } catch {
            // Si falla, la galería se ve al recargar.
        }
    };

    const seguir = (numero) => {
        clearTimeout(sondeo);
        const consultar = async () => {
            try {
                const respuesta = await fetch(urlEstado(numero), { headers: { Accept: 'application/json' } });
                if (!respuesta.ok) throw new Error('estado');
                const estado = await respuesta.json();
                const total = Number(estado.total) || 0;
                const actual = Number(estado.actual) || 0;

                if (estado.estado === 'error') {
                    marcarEtapa('convertir');
                    mostrarError(estado.mensaje ?? 'No se pudo generar el flipbook.');
                    return;
                }
                if (estado.estado === 'listo') {
                    agregarHojas(numero, total);
                    await terminado(estado);
                    return;
                }
                if (estado.estado === 'armando') {
                    marcarEtapa('armar');
                    agregarHojas(numero, total);
                    avanzar(0.97, 'Armando el flipbook…');
                } else {
                    marcarEtapa('convertir');
                    agregarHojas(numero, actual);
                    // La subida ocupa el primer 10 %; la conversión, hasta el 95 %.
                    avanzar(0.1 + (total ? (actual / total) * 0.85 : 0),
                        estado.estado === 'en_cola' ? 'En cola: empieza en un momento…' : `Convirtiendo la página ${actual} de ${total}…`);
                }
                sondeo = setTimeout(consultar, 900);
            } catch {
                sondeo = setTimeout(consultar, 2500);
            }
        };
        consultar();
    };

    /* ---- Envío ---- */
    formulario.addEventListener('submit', (evento) => {
        evento.preventDefault();
        error.hidden = true;
        progreso.classList.remove('is-error');
        const numero = formulario.querySelector('#flip-numero').value.trim();
        if (!/^[0-9]{1,4}$/.test(numero)) {
            mostrarError('Escribe el número de edición, solo con dígitos (por ejemplo 220).');
            return;
        }
        if (!campo.files?.length) {
            mostrarError('Elige el PDF completo de la revista.');
            return;
        }

        tira.replaceChildren();
        paginasMostradas = 0;
        vista.hidden = true;
        marco.removeAttribute('src');
        ocupado(true);
        marcarEtapa('subir');
        avanzar(0, 'Subiendo el PDF…');

        const peticion = new XMLHttpRequest();
        peticion.open('POST', formulario.action);
        peticion.setRequestHeader('Accept', 'application/json');
        peticion.setRequestHeader('X-Subida-Mesa', '1');
        peticion.responseType = 'json';
        peticion.upload.addEventListener('progress', (e) => {
            if (e.lengthComputable) avanzar((e.loaded / e.total) * 0.1, `Subiendo el PDF… ${peso(e.loaded)} de ${peso(e.total)}`);
        });
        peticion.addEventListener('load', () => {
            if (peticion.status === 202) {
                seguir(numero);
                return;
            }
            const cuerpo = peticion.response ?? {};
            const primero = cuerpo.errors ? Object.values(cuerpo.errors)[0]?.[0] : null;
            mostrarError(primero ?? cuerpo.mensaje ?? cuerpo.message ?? 'No se pudo subir el PDF.');
        });
        peticion.addEventListener('error', () => mostrarError('Se perdió la conexión al subir el PDF. Intenta de nuevo.'));
        peticion.send(new FormData(formulario));
    });

    // Al volver a la página con una conversión en curso, se retoma el avance.
    if (generador.dataset.enCurso) {
        ocupado(true);
        seguir(generador.dataset.enCurso);
    }
}
