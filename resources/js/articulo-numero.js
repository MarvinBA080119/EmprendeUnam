/**
 * Formulario «Agregar / editar artículo del número» (administrador):
 *  - al elegir un artículo aprobado, llena título, autores, sección y
 *    descripción con sus datos (lo que el editor ya cambió a mano se respeta);
 *  - «Usar el PDF del sistema» deshabilita la carga de un PDF local, para que
 *    nunca se crucen dos archivos;
 *  - sugiere cursos en vivo (solo por título y palabras clave) y permite
 *    agregar otro curso: buscarlo entre los vigentes o registrarlo ahí mismo.
 * Sin JavaScript el formulario funciona igual: el servidor sugiere al guardar
 * y se muestran todos los cursos vigentes.
 */
import { avisar } from './avisos';

const normalizar = (texto) => String(texto ?? '').normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase().trim();

function autollenado(formulario, datos, alCambiar) {
    const select = formulario.querySelector('#campo-arti_id_autor');
    const campos = Array.from(formulario.querySelectorAll('[data-autollenar]'));
    const aviso = formulario.querySelector('[data-autollenado]');
    const textoAviso = aviso?.querySelector('[data-autollenado-texto]');
    const puestos = new Map();

    select.addEventListener('change', () => {
        const articulo = datos[select.value];
        const respetados = [];

        campos.forEach((campo) => {
            // «Editado» = distinto de lo último que puso el autollenado o, si no
            // ha puesto nada, de lo que traía la página. Eso no se pisa.
            const puesto = puestos.get(campo);
            const editado = campo.value !== (puesto ?? campo.defaultValue);

            if (articulo) {
                if (editado && campo.value.trim() !== '') {
                    respetados.push(campo.dataset.autollenar);
                    return;
                }
                campo.value = articulo[campo.dataset.autollenar] ?? '';
                puestos.set(campo, campo.value);
                campo.classList.remove('is-autollenado');
                void campo.offsetWidth;
                campo.classList.add('is-autollenado');
            } else if (puesto !== undefined && !editado) {
                campo.value = '';
                puestos.delete(campo);
            }
        });

        if (aviso) {
            aviso.hidden = !articulo;
            if (articulo) {
                textoAviso.textContent = respetados.length
                    ? 'Llenamos los campos con los datos del artículo; dejamos como estaban los que editaste.'
                    : 'Llenamos título, personas autoras, sección y descripción con los datos del artículo. Puedes ajustarlos.';
            }
        }
        alCambiar();
    });

    campos.forEach((campo) => campo.addEventListener('animationend', () => campo.classList.remove('is-autollenado')));
}

function fuentePdf(formulario, datos) {
    const select = formulario.querySelector('#campo-arti_id_autor');
    const opciones = formulario.querySelector('[data-solo-sistema]');
    const estado = formulario.querySelector('[data-estado-diseno]');
    const bloque = formulario.querySelector('[data-archivo-pdf]');
    const campo = formulario.querySelector('#campo-pdf');
    const ayuda = formulario.querySelector('[data-ayuda-pdf]');
    const ayudaOriginal = ayuda?.textContent.trim();
    if (!opciones || !campo) return () => {};

    const aplicar = () => {
        const articulo = datos[select.value];
        opciones.hidden = !articulo;
        if (articulo) {
            estado.textContent = articulo.diseno
                ? 'Su PDF diseñado ya está listo en la mesa de diseño.'
                : 'Aún no tiene PDF diseñado: se publicará en cuanto la mesa de diseño lo suba.';
        }
        const sistema = Boolean(articulo) && formulario.querySelector('[name=fuente_pdf][value=sistema]')?.checked;
        campo.disabled = sistema;
        if (sistema) campo.value = '';
        bloque.classList.toggle('is-deshabilitado', sistema);
        ayuda.textContent = sistema
            ? 'Se usa el PDF del sistema. Para subir otro, elige «Subir un PDF solo para este número».'
            : ayudaOriginal;
    };

    opciones.addEventListener('change', aplicar);
    aplicar();

    return aplicar;
}

function cursos(formulario, datos) {
    const caja = formulario.querySelector('[data-cursos]');
    const contenedor = caja?.querySelector('[data-fichas]');
    if (!contenedor) return () => {};
    const select = formulario.querySelector('#campo-arti_id_autor');
    const titulo = formulario.querySelector('#campo-titulo');
    const camposSugeridos = caja.querySelector('[data-sugeridos-campos]');
    const sinSugerencias = caja.querySelector('[data-sin-sugerencias]');
    const botonAgregar = caja.querySelector('[data-agregar-curso]');
    const fichas = () => Array.from(contenedor.querySelectorAll('[data-ficha]'));

    const pintar = () => {
        const lista = fichas();
        lista.forEach((ficha) => {
            const marcado = ficha.querySelector('input').checked;
            const sugerido = ficha.dataset.sugerido === '1';
            ficha.hidden = !(sugerido || marcado || ficha.dataset.mostrado === '1');
            ficha.querySelector('.emp-cursos-elegir__sugerido').hidden = !sugerido;
            ficha.querySelector('label').classList.toggle('is-sugerido', sugerido);
        });
        // Sugeridos primero; el botón «Agregar otro curso» siempre al final.
        lista.filter((f) => f.dataset.sugerido === '1').concat(lista.filter((f) => f.dataset.sugerido !== '1'))
            .forEach((ficha) => contenedor.insertBefore(ficha, botonAgregar));
        sinSugerencias.hidden = lista.some((f) => !f.hidden);
        camposSugeridos.replaceChildren(...lista.filter((f) => f.dataset.sugerido === '1').map((f) => {
            const oculto = document.createElement('input');
            oculto.type = 'hidden';
            oculto.name = 'sugeridos[]';
            oculto.value = f.dataset.ficha;
            return oculto;
        }));
    };

    contenedor.addEventListener('change', (evento) => {
        const ficha = evento.target.closest('[data-ficha]');
        if (ficha) {
            ficha.dataset.tocado = '1';
            ficha.dataset.mostrado = '1';
        }
    });

    let pendiente = null;
    let temporizador = null;
    const sugerir = async () => {
        if (!titulo.value.trim() && !select.value) return;
        const parametros = new URLSearchParams({ titulo: titulo.value, arti_id_autor: select.value });
        pendiente?.abort();
        pendiente = new AbortController();
        try {
            const respuesta = await fetch(`${formulario.dataset.urlSugerencias}?${parametros}`, {
                headers: { Accept: 'application/json' }, signal: pendiente.signal,
            });
            if (!respuesta.ok) return;
            const ids = new Set((await respuesta.json()).map((curso) => String(curso.id)));
            fichas().forEach((ficha) => {
                const casilla = ficha.querySelector('input');
                const era = ficha.dataset.sugerido === '1';
                const es = ids.has(ficha.dataset.ficha);
                ficha.dataset.sugerido = es ? '1' : '0';
                if (ficha.dataset.tocado) return;
                // Una sugerencia nueva entra marcada; la que deja de serlo sale.
                if (es && !era) casilla.checked = true;
                if (!es && era) {
                    casilla.checked = false;
                    ficha.dataset.mostrado = '0';
                }
            });
            pintar();
        } catch (error) {
            if (error.name !== 'AbortError') throw error;
        }
    };
    titulo?.addEventListener('input', () => {
        clearTimeout(temporizador);
        temporizador = setTimeout(sugerir, 450);
    });

    dialogoCursos(contenedor, botonAgregar, pintar);
    pintar();
    sugerir();

    return sugerir;
}

function dialogoCursos(contenedor, botonAgregar, pintar) {
    const dialogo = document.querySelector('[data-dialogo-cursos]');
    if (!dialogo || !botonAgregar) return;
    const buscador = dialogo.querySelector('[data-buscar-curso]');
    const lista = dialogo.querySelector('[data-lista-cursos]');
    const vacia = dialogo.querySelector('[data-lista-vacia]');
    const pasoBuscar = dialogo.querySelector('[data-paso-buscar]');
    const accionesBuscar = dialogo.querySelector('[data-acciones-buscar]');
    const formulario = dialogo.querySelector('[data-form-curso]');
    const error = dialogo.querySelector('[data-error-curso]');
    botonAgregar.hidden = false;

    const fichas = () => Array.from(contenedor.querySelectorAll('[data-ficha]'));

    const dibujarLista = () => {
        const consulta = normalizar(buscador.value);
        const filas = fichas()
            .filter((f) => !consulta || normalizar(`${f.dataset.nombre} ${f.dataset.institucion}`).includes(consulta))
            .map((ficha) => {
                const marcado = ficha.querySelector('input').checked;
                const fila = document.createElement('li');
                const boton = document.createElement('button');
                boton.type = 'button';
                boton.className = `emp-lista-cursos__opcion${marcado ? ' is-elegido' : ''}`;
                boton.dataset.curso = ficha.dataset.ficha;
                boton.setAttribute('aria-pressed', String(marcado));
                const nombre = document.createElement('span');
                nombre.className = 'emp-lista-cursos__nombre';
                nombre.textContent = ficha.dataset.nombre;
                const institucion = document.createElement('span');
                institucion.className = 'emp-lista-cursos__inst';
                institucion.textContent = ficha.dataset.institucion;
                const estado = document.createElement('span');
                estado.className = 'emp-lista-cursos__estado';
                estado.textContent = marcado ? 'Agregado' : 'Agregar';
                boton.append(nombre, institucion, estado);
                fila.append(boton);
                return fila;
            });
        lista.replaceChildren(...filas);
        vacia.hidden = filas.length > 0;
    };

    const mostrarPaso = (nuevo) => {
        pasoBuscar.hidden = nuevo;
        accionesBuscar.hidden = nuevo;
        formulario.hidden = !nuevo;
        error.hidden = true;
        (nuevo ? formulario.querySelector('[name=nombre]') : buscador).focus();
    };

    botonAgregar.addEventListener('click', () => {
        buscador.value = '';
        dibujarLista();
        dialogo.showModal();
        mostrarPaso(false);
    });
    buscador.addEventListener('input', dibujarLista);
    lista.addEventListener('click', (evento) => {
        const boton = evento.target.closest('[data-curso]');
        if (!boton) return;
        const ficha = contenedor.querySelector(`[data-ficha="${boton.dataset.curso}"]`);
        const casilla = ficha.querySelector('input');
        casilla.checked = !casilla.checked;
        ficha.dataset.tocado = '1';
        ficha.dataset.mostrado = '1';
        pintar();
        dibujarLista();
        lista.querySelector(`[data-curso="${boton.dataset.curso}"]`)?.focus();
    });
    dialogo.querySelector('[data-mostrar-nuevo]').addEventListener('click', () => mostrarPaso(true));
    dialogo.querySelector('[data-volver-buscar]').addEventListener('click', () => mostrarPaso(false));
    dialogo.querySelector('[data-cerrar-dialogo]').addEventListener('click', () => dialogo.close());
    dialogo.addEventListener('click', (evento) => {
        if (evento.target === dialogo) dialogo.close();
    });

    formulario.addEventListener('submit', async (evento) => {
        evento.preventDefault();
        const datos = new FormData(formulario);
        dialogo.querySelector('[data-palabras-nuevo]').value.split(',')
            .map((p) => p.trim()).filter(Boolean)
            .forEach((palabra) => datos.append('palabras[]', palabra));
        const boton = formulario.querySelector('[type=submit]');
        const texto = boton.textContent;
        boton.disabled = true;
        boton.textContent = boton.dataset.textoEnviando;
        error.hidden = true;

        try {
            const respuesta = await fetch(formulario.action, { method: 'POST', body: datos, headers: { Accept: 'application/json' } });
            const cuerpo = await respuesta.json().catch(() => ({}));
            if (!respuesta.ok) {
                const primero = cuerpo.errors ? Object.values(cuerpo.errors)[0]?.[0] : null;
                error.textContent = primero ?? 'No se pudo registrar el curso. Revisa los datos.';
                error.hidden = false;
                return;
            }
            const modelo = contenedor.querySelector('[data-ficha]');
            const ficha = modelo.cloneNode(true);
            const casilla = ficha.querySelector('input');
            const etiqueta = ficha.querySelector('label');
            ficha.dataset.ficha = String(cuerpo.id);
            ficha.dataset.nombre = cuerpo.nombre;
            ficha.dataset.institucion = cuerpo.institucion;
            ficha.dataset.sugerido = '0';
            ficha.dataset.mostrado = '1';
            ficha.dataset.tocado = '1';
            casilla.value = String(cuerpo.id);
            casilla.id = `curso-${cuerpo.id}`;
            casilla.checked = true;
            etiqueta.htmlFor = casilla.id;
            etiqueta.querySelector('span:not([class])').textContent = cuerpo.nombre;
            etiqueta.querySelector('.emp-cursos-elegir__inst').textContent = cuerpo.institucion;
            contenedor.insertBefore(ficha, botonAgregar);
            pintar();
            formulario.reset();
            dialogo.close();
            avisar(`Se registró el curso «${cuerpo.nombre}» y quedó marcado para este artículo.`);
        } finally {
            boton.disabled = false;
            boton.textContent = texto;
        }
    });
}

export function iniciarArticuloNumero() {
    const formulario = document.querySelector('[data-form-articulo-numero]');
    if (!formulario) return;
    const datos = JSON.parse(formulario.querySelector('[data-datos-articulos]')?.textContent || '{}');

    const actualizarPdf = fuentePdf(formulario, datos);
    const sugerir = cursos(formulario, datos);
    autollenado(formulario, datos, () => {
        actualizarPdf();
        sugerir();
    });
}
