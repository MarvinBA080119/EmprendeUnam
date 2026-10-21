<script setup>
import { computed, nextTick, reactive, ref } from 'vue';

const props = defineProps({
    accion: { type: String, required: true },
    csrf: { type: String, required: true },
    temas: { type: Array, required: true },
    anterior: { type: Object, default: () => ({}) },
    errores: { type: Object, default: () => ({}) },
    pdfMaxKb: { type: Number, required: true },
    maxCoautores: { type: Number, default: 1 },
    maxPalabrasResumen: { type: Number, default: 50 },
    urlCancelar: { type: String, default: '' },
});

const TOTAL_PALABRAS = 5;

const coautorVacio = () => ({ nombre: '', primer_apellido: '', segundo_apellido: '', correo: '', institucion: '' });

const palabrasPrevias = Array.isArray(props.anterior.palabras) ? props.anterior.palabras : [];
const coautoresPrevios = Array.isArray(props.anterior.coautores) ? props.anterior.coautores : [];

const form = reactive({
    titulo: props.anterior.titulo ?? '',
    resumen: props.anterior.resumen ?? '',
    tema_id_tema: props.anterior.tema_id_tema ?? '',
    palabras: Array.from({ length: TOTAL_PALABRAS }, (_, i) => palabrasPrevias[i] ?? ''),
    tiene_coautores: Boolean(Number(props.anterior.tiene_coautores ?? 0)) || props.anterior.tiene_coautores === true,
    coautores: coautoresPrevios.length ? coautoresPrevios.map((c) => ({ ...coautorVacio(), ...c })) : [coautorVacio()],
});

const erroresLocales = ref({});
const palabrasFaltantes = ref(new Set());
const totalErroresLocales = ref(0);
const enviando = ref(false);
const pdfSeleccionado = ref(null);
const formulario = ref(null);
const campoPdf = ref(null);
const arrastrando = ref(false);

const hayErroresServidor = Object.keys(props.errores).length > 0;

const error = (clave) => erroresLocales.value[clave] ?? props.errores[clave]?.[0] ?? null;

const normalizar = (texto) => texto.replace(/\s+/g, ' ').trim().toLowerCase();

const palabrasRepetidas = computed(() => {
    const vistas = new Map();
    const repetidas = new Set();
    form.palabras.forEach((p, i) => {
        const n = normalizar(p);
        if (n === '') return;
        if (vistas.has(n)) {
            repetidas.add(i);
            repetidas.add(vistas.get(n));
        } else {
            vistas.set(n, i);
        }
    });
    return repetidas;
});

const palabrasResumen = computed(() => {
    const texto = form.resumen.trim();
    return texto === '' ? 0 : texto.split(/\s+/).length;
});

const pdfMaxMb = computed(() => Math.round(props.pdfMaxKb / 1024));

const pesoPdf = computed(() => {
    const bytes = pdfSeleccionado.value?.size ?? 0;
    return bytes >= 1024 * 1024 ? `${(bytes / 1024 / 1024).toFixed(1)} MB` : `${Math.max(1, Math.round(bytes / 1024))} KB`;
});

function agregarCoautor() {
    if (form.coautores.length < props.maxCoautores) {
        form.coautores.push(coautorVacio());
    }
}

function quitarCoautor(indice) {
    form.coautores.splice(indice, 1);
    if (form.coautores.length === 0) {
        form.coautores.push(coautorVacio());
    }
}

async function alElegirPdf(evento) {
    const archivo = evento.target.files?.[0] ?? null;
    pdfSeleccionado.value = archivo;
    delete erroresLocales.value.pdf;
    if (!archivo) return;

    if (archivo.size > props.pdfMaxKb * 1024) {
        erroresLocales.value.pdf = `El PDF no debe pesar más de ${pdfMaxMb.value} MB.`;
        return;
    }
    const inicio = await archivo.slice(0, 4).text();
    if (inicio !== '%PDF') {
        erroresLocales.value.pdf = 'El archivo no es un PDF válido.';
    }
}

// Arrastrar y soltar: el archivo pasa al mismo campo name="pdf" del formulario.
function alSoltar(evento) {
    arrastrando.value = false;
    const archivos = evento.dataTransfer?.files;
    if (!archivos?.length || !campoPdf.value) return;
    campoPdf.value.files = archivos;
    alElegirPdf({ target: campoPdf.value });
}

function validar() {
    const e = {};
    if (form.titulo.trim() === '') e.titulo = 'Escribe el título del artículo.';
    if (form.resumen.trim() === '') e.resumen = 'Escribe el resumen del artículo.';
    else if (palabrasResumen.value > props.maxPalabrasResumen) e.resumen = `El resumen no debe tener más de ${props.maxPalabrasResumen} palabras.`;
    if (form.tema_id_tema === '') e.tema_id_tema = 'Elige un tema.';
    // Un solo mensaje para el grupo; cada campo vacío solo se marca.
    const faltantes = new Set(form.palabras.map((p, i) => (p.trim() === '' ? i : null)).filter((i) => i !== null));
    palabrasFaltantes.value = faltantes;
    if (faltantes.size > 0) {
        e.palabras = faltantes.size === 1
            ? 'Falta 1 palabra clave; escribe cinco distintas.'
            : `Faltan ${faltantes.size} palabras clave; escribe cinco distintas.`;
    } else if (palabrasRepetidas.value.size > 0) {
        e.palabras = 'Las 5 palabras clave deben ser distintas.';
    }
    if (form.tiene_coautores) {
        form.coautores.forEach((c, i) => {
            for (const campo of ['nombre', 'primer_apellido', 'correo', 'institucion']) {
                if (c[campo].trim() === '') e[`coautores.${i}.${campo}`] = 'Este dato es obligatorio.';
            }
            if (c.correo.trim() !== '' && !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(c.correo.trim())) {
                e[`coautores.${i}.correo`] = 'Escribe un correo válido.';
            }
        });
    }
    if (!pdfSeleccionado.value) e.pdf = 'Selecciona el archivo PDF del artículo.';
    else if (erroresLocales.value.pdf) e.pdf = erroresLocales.value.pdf;
    erroresLocales.value = e;
    totalErroresLocales.value = Object.keys(e).length - (e.palabras ? 1 : 0) + palabrasFaltantes.value.size
        + (e.palabras && palabrasFaltantes.value.size === 0 ? 1 : 0);
    return Object.keys(e).length === 0;
}

async function enviar(evento) {
    if (enviando.value) {
        evento.preventDefault();
        return;
    }
    if (!validar()) {
        evento.preventDefault();
        await nextTick();
        formulario.value?.querySelector('.is-invalid')?.focus();
        return;
    }
    enviando.value = true;
}
</script>

<template>
    <form ref="formulario" class="emp-envio-form" :action="accion" method="POST" enctype="multipart/form-data" novalidate @submit="enviar">
        <input type="hidden" name="_token" :value="csrf" />

        <div v-if="totalErroresLocales > 0" class="alert alert-danger" role="alert">
            <span class="alert-texto">
                {{ totalErroresLocales === 1 ? 'Revisa el campo marcado.' : `Revisa los ${totalErroresLocales} campos marcados.` }}
            </span>
        </div>
        <div v-else-if="hayErroresServidor" class="alert alert-danger" role="alert">
            <span class="alert-texto">Revisa los datos marcados. Por seguridad, vuelve a seleccionar el archivo PDF.</span>
        </div>

        <section class="emp-bloque" aria-labelledby="bloque-datos">
            <div class="emp-bloque__texto">
                <h2 id="bloque-datos" class="emp-bloque__titulo">Datos del artículo</h2>
                <p>Así aparecerá en la revista y en el buscador del sitio.</p>
            </div>
            <div class="emp-bloque__campos">
                <div class="mb-3">
                    <label for="titulo" class="form-label">Título</label>
                    <input id="titulo" v-model="form.titulo" name="titulo" type="text" maxlength="200" required
                           class="form-control" :class="{ 'is-invalid': error('titulo') }"
                           aria-describedby="titulo-ayuda titulo-error" />
                    <div id="titulo-ayuda" class="form-text emp-num">{{ form.titulo.length }} de 200 caracteres.</div>
                    <div v-if="error('titulo')" id="titulo-error" class="invalid-feedback">{{ error('titulo') }}</div>
                </div>

                <div class="mb-3">
                    <label for="resumen" class="form-label">Resumen</label>
                    <textarea id="resumen" v-model="form.resumen" name="resumen" rows="4" maxlength="500" required
                              class="form-control" :class="{ 'is-invalid': error('resumen') || palabrasResumen > maxPalabrasResumen }"
                              aria-describedby="resumen-ayuda resumen-error"></textarea>
                    <div id="resumen-ayuda" class="form-text emp-num" aria-live="polite">
                        {{ palabrasResumen }} de {{ maxPalabrasResumen }} palabras como máximo.
                    </div>
                    <div v-if="error('resumen')" id="resumen-error" class="invalid-feedback">{{ error('resumen') }}</div>
                </div>

                <div class="mb-0">
                    <label for="tema" class="form-label">Tema</label>
                    <select id="tema" v-model="form.tema_id_tema" name="tema_id_tema" required class="form-select"
                            :class="{ 'is-invalid': error('tema_id_tema') }" aria-describedby="tema-error">
                        <option value="" disabled>Elige un tema…</option>
                        <option v-for="tema in temas" :key="tema.id" :value="String(tema.id)">{{ tema.nombre }}</option>
                    </select>
                    <div v-if="error('tema_id_tema')" id="tema-error" class="invalid-feedback">{{ error('tema_id_tema') }}</div>
                </div>
            </div>
        </section>

        <section class="emp-bloque" aria-labelledby="bloque-palabras" aria-describedby="palabras-ayuda">
            <div class="emp-bloque__texto">
                <h2 id="bloque-palabras" class="emp-bloque__titulo">Palabras clave</h2>
                <p id="palabras-ayuda">Exactamente cinco, distintas, una por campo. Clasifican tu artículo en el buscador.</p>
            </div>
            <div class="emp-bloque__campos">
                <div class="row g-2">
                    <div v-for="(palabra, i) in form.palabras" :key="i" class="col-12 col-sm-6">
                        <label :for="`palabra-${i}`" class="visually-hidden">Palabra clave {{ i + 1 }}</label>
                        <input :id="`palabra-${i}`" v-model="form.palabras[i]" name="palabras[]" type="text" maxlength="100"
                               required class="form-control" :placeholder="`Palabra ${i + 1}`"
                               :class="{ 'is-invalid': error(`palabras.${i}`) || palabrasRepetidas.has(i) || (palabrasFaltantes.has(i) && form.palabras[i].trim() === '') }" />
                        <div v-if="error(`palabras.${i}`)" class="invalid-feedback">{{ error(`palabras.${i}`) }}</div>
                    </div>
                </div>
                <div v-if="error('palabras') || palabrasRepetidas.size" class="invalid-feedback d-block" role="alert">
                    {{ erroresLocales.palabras ?? (palabrasRepetidas.size ? 'Las 5 palabras clave deben ser distintas.' : error('palabras')) }}
                </div>
            </div>
        </section>

        <section class="emp-bloque" aria-labelledby="bloque-coautor">
            <div class="emp-bloque__texto">
                <h2 id="bloque-coautor" class="emp-bloque__titulo">Coautor <span class="emp-opcional">(opcional)</span></h2>
                <p>Hasta dos personas autoras por artículo: tú y, si lo hay, un coautor.</p>
            </div>
            <div class="emp-bloque__campos">
                <div class="form-check form-switch mb-0">
                    <input id="tiene-coautores" v-model="form.tiene_coautores" class="form-check-input" type="checkbox"
                           role="switch" name="tiene_coautores" value="1" />
                    <label for="tiene-coautores" class="form-check-label">Tiene coautor</label>
                </div>

                <template v-if="form.tiene_coautores">
                    <div v-if="error('coautores')" class="invalid-feedback d-block" role="alert">{{ error('coautores') }}</div>
                    <div v-for="(coautor, i) in form.coautores" :key="i" class="emp-coautor">
                        <div v-if="maxCoautores > 1" class="d-flex justify-content-between align-items-center mb-2">
                            <h3 class="h6 mb-0">Coautor {{ i + 1 }}</h3>
                            <button type="button" class="btn btn-quieto btn-sm" @click="quitarCoautor(i)">
                                Quitar<span class="visually-hidden"> al coautor {{ i + 1 }}</span>
                            </button>
                        </div>
                        <div class="row gx-3">
                            <div v-for="campo in [
                                    { clave: 'nombre', etiqueta: 'Nombre', obligatorio: true, tipo: 'text', col: 'col-sm-6' },
                                    { clave: 'primer_apellido', etiqueta: 'Primer apellido', obligatorio: true, tipo: 'text', col: 'col-sm-6' },
                                    { clave: 'segundo_apellido', etiqueta: 'Segundo apellido', obligatorio: false, tipo: 'text', col: 'col-sm-6' },
                                    { clave: 'institucion', etiqueta: 'Institución', obligatorio: true, tipo: 'text', col: 'col-sm-6' },
                                    { clave: 'correo', etiqueta: 'Correo', obligatorio: true, tipo: 'email', col: '', max: 254 },
                                 ]" :key="campo.clave" class="col-12 mb-3" :class="campo.col">
                                <label :for="`coautor-${i}-${campo.clave}`" class="form-label">
                                    {{ campo.etiqueta }}<span v-if="!campo.obligatorio" class="emp-opcional"> (opcional)</span>
                                </label>
                                <input :id="`coautor-${i}-${campo.clave}`" v-model="coautor[campo.clave]"
                                       :name="`coautores[${i}][${campo.clave}]`" :type="campo.tipo"
                                       :maxlength="campo.max ?? 100" :required="campo.obligatorio" class="form-control"
                                       :class="{ 'is-invalid': error(`coautores.${i}.${campo.clave}`) }" />
                                <div v-if="error(`coautores.${i}.${campo.clave}`)" class="invalid-feedback">
                                    {{ error(`coautores.${i}.${campo.clave}`) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <button v-if="maxCoautores > 1" type="button" class="btn btn-secondary btn-sm"
                            :disabled="form.coautores.length >= maxCoautores" @click="agregarCoautor">
                        Agregar otro coautor
                    </button>
                </template>
            </div>
        </section>

        <section class="emp-bloque" aria-labelledby="bloque-archivo">
            <div class="emp-bloque__texto">
                <h2 id="bloque-archivo" class="emp-bloque__titulo">Archivo</h2>
                <p>El artículo completo en PDF, de máximo {{ pdfMaxMb }} MB.</p>
            </div>
            <div class="emp-bloque__campos">
                <div class="emp-archivo"
                     :class="{ 'is-arrastrando': arrastrando, 'is-invalid': error('pdf'), 'tiene-archivo': pdfSeleccionado && !error('pdf') }"
                     @dragenter.prevent="arrastrando = true" @dragover.prevent="arrastrando = true"
                     @dragleave.prevent="arrastrando = false" @drop.prevent="alSoltar">
                    <input id="pdf" ref="campoPdf" class="emp-archivo__campo" name="pdf" type="file" accept="application/pdf,.pdf"
                           required aria-describedby="pdf-ayuda pdf-error" @change="alElegirPdf" />
                    <label for="pdf" class="emp-archivo__zona">
                        <span class="emp-archivo__icono" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16" focusable="false">
                                <path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5" />
                                <path d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z" />
                            </svg>
                        </span>
                        <span v-if="pdfSeleccionado" class="emp-archivo__texto">
                            <strong class="text-break">{{ pdfSeleccionado.name }}</strong>
                            <span id="pdf-ayuda" class="emp-num">{{ pesoPdf }} · <span class="emp-archivo__accion">Cambiar archivo</span></span>
                        </span>
                        <span v-else class="emp-archivo__texto">
                            <strong>Arrastra tu PDF aquí</strong>
                            <span id="pdf-ayuda">o <span class="emp-archivo__accion">elige un archivo</span> · solo PDF, máximo {{ pdfMaxMb }} MB</span>
                        </span>
                    </label>
                </div>
                <div v-if="error('pdf')" id="pdf-error" class="invalid-feedback d-block">{{ error('pdf') }}</div>
            </div>
        </section>

        <div class="emp-envio-acciones">
            <p class="emp-envio-acciones__nota">Una vez enviado, el artículo no se puede editar ni volver a enviar.</p>
            <div class="emp-envio-acciones__botones">
                <a v-if="urlCancelar" :href="urlCancelar" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary" :disabled="enviando">
                    {{ enviando ? 'Enviando…' : 'Enviar artículo' }}
                </button>
            </div>
        </div>
    </form>
</template>
