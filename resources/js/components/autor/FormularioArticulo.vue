<script setup>
import { computed, nextTick, reactive, ref } from 'vue';

const props = defineProps({
    accion: { type: String, required: true },
    csrf: { type: String, required: true },
    temas: { type: Array, required: true },
    anterior: { type: Object, default: () => ({}) },
    errores: { type: Object, default: () => ({}) },
    pdfMaxKb: { type: Number, required: true },
    maxCoautores: { type: Number, default: 10 },
});

const TOTAL_PALABRAS = 5;

const coautorVacio = () => ({ nombre: '', primer_apellido: '', segundo_apellido: '', correo: '', institucion: '' });

const palabrasPrevias = Array.isArray(props.anterior.palabras) ? props.anterior.palabras : [];
const coautoresPrevios = Array.isArray(props.anterior.coautores) ? props.anterior.coautores : [];

const form = reactive({
    titulo: props.anterior.titulo ?? '',
    tema_id_tema: props.anterior.tema_id_tema ?? '',
    palabras: Array.from({ length: TOTAL_PALABRAS }, (_, i) => palabrasPrevias[i] ?? ''),
    tiene_coautores: Boolean(Number(props.anterior.tiene_coautores ?? 0)) || props.anterior.tiene_coautores === true,
    coautores: coautoresPrevios.length ? coautoresPrevios.map((c) => ({ ...coautorVacio(), ...c })) : [coautorVacio()],
});

const erroresLocales = ref({});
const enviando = ref(false);
const pdfSeleccionado = ref(null);
const formulario = ref(null);

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

const pdfMaxMb = computed(() => Math.round(props.pdfMaxKb / 1024));

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

function validar() {
    const e = {};
    if (form.titulo.trim() === '') e.titulo = 'Escribe el título del artículo.';
    if (form.tema_id_tema === '') e.tema_id_tema = 'Elige un tema.';
    form.palabras.forEach((p, i) => {
        if (p.trim() === '') e[`palabras.${i}`] = 'Escribe esta palabra clave.';
    });
    if (palabrasRepetidas.value.size > 0) e.palabras = 'Las 5 palabras clave deben ser distintas.';
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
    <form ref="formulario" :action="accion" method="POST" enctype="multipart/form-data" novalidate @submit="enviar">
        <input type="hidden" name="_token" :value="csrf" />

        <div v-if="hayErroresServidor" class="alert alert-danger" role="alert">
            Revisa los datos marcados. Por seguridad, vuelve a seleccionar el archivo PDF.
        </div>

        <fieldset class="mb-4">
            <legend class="h5">Datos del artículo</legend>

            <div class="mb-3">
                <label for="titulo" class="form-label">Título <span aria-hidden="true">*</span></label>
                <input id="titulo" v-model="form.titulo" name="titulo" type="text" maxlength="200" required
                       class="form-control" :class="{ 'is-invalid': error('titulo') }"
                       aria-describedby="titulo-ayuda titulo-error" />
                <div id="titulo-ayuda" class="form-text">{{ form.titulo.length }} de 200 caracteres.</div>
                <div v-if="error('titulo')" id="titulo-error" class="invalid-feedback">{{ error('titulo') }}</div>
            </div>

            <div class="mb-3">
                <label for="tema" class="form-label">Tema <span aria-hidden="true">*</span></label>
                <select id="tema" v-model="form.tema_id_tema" name="tema_id_tema" required class="form-select"
                        :class="{ 'is-invalid': error('tema_id_tema') }" aria-describedby="tema-error">
                    <option value="" disabled>Elige un tema…</option>
                    <option v-for="tema in temas" :key="tema.id" :value="String(tema.id)">{{ tema.nombre }}</option>
                </select>
                <div v-if="error('tema_id_tema')" id="tema-error" class="invalid-feedback">{{ error('tema_id_tema') }}</div>
            </div>
        </fieldset>

        <fieldset class="mb-4" aria-describedby="palabras-ayuda">
            <legend class="h5">Palabras clave <span aria-hidden="true">*</span></legend>
            <p id="palabras-ayuda" class="form-text mt-0">Escribe exactamente 5 palabras clave distintas, una por campo.</p>
            <div class="row g-2">
                <div v-for="(palabra, i) in form.palabras" :key="i" class="col-12 col-sm-6 col-lg">
                    <label :for="`palabra-${i}`" class="visually-hidden">Palabra clave {{ i + 1 }}</label>
                    <input :id="`palabra-${i}`" v-model="form.palabras[i]" name="palabras[]" type="text" maxlength="100"
                           required class="form-control" :placeholder="`Palabra ${i + 1}`"
                           :class="{ 'is-invalid': error(`palabras.${i}`) || palabrasRepetidas.has(i) }" />
                    <div v-if="error(`palabras.${i}`)" class="invalid-feedback">{{ error(`palabras.${i}`) }}</div>
                </div>
            </div>
            <div v-if="error('palabras') || palabrasRepetidas.size" class="text-danger small mt-2" role="alert">
                {{ palabrasRepetidas.size ? 'Las 5 palabras clave deben ser distintas.' : error('palabras') }}
            </div>
        </fieldset>

        <fieldset class="mb-4">
            <legend class="h5">Coautores</legend>
            <div class="form-check mb-3">
                <input id="tiene-coautores" v-model="form.tiene_coautores" class="form-check-input" type="checkbox"
                       name="tiene_coautores" value="1" />
                <label for="tiene-coautores" class="form-check-label">¿Tiene coautores?</label>
            </div>

            <template v-if="form.tiene_coautores">
                <div v-if="error('coautores')" class="text-danger small mb-2" role="alert">{{ error('coautores') }}</div>
                <div v-for="(coautor, i) in form.coautores" :key="i" class="card emp-tarjeta mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h3 class="h6 mb-0">Coautor {{ i + 1 }}</h3>
                            <button type="button" class="btn btn-outline-primary btn-sm" @click="quitarCoautor(i)">
                                Quitar<span class="visually-hidden"> al coautor {{ i + 1 }}</span>
                            </button>
                        </div>
                        <div class="row g-2">
                            <div v-for="campo in [
                                    { clave: 'nombre', etiqueta: 'Nombre', obligatorio: true, tipo: 'text', col: 'col-md-4' },
                                    { clave: 'primer_apellido', etiqueta: 'Primer apellido', obligatorio: true, tipo: 'text', col: 'col-md-4' },
                                    { clave: 'segundo_apellido', etiqueta: 'Segundo apellido', obligatorio: false, tipo: 'text', col: 'col-md-4' },
                                    { clave: 'correo', etiqueta: 'Correo', obligatorio: true, tipo: 'email', col: 'col-md-6', max: 254 },
                                    { clave: 'institucion', etiqueta: 'Institución', obligatorio: true, tipo: 'text', col: 'col-md-6' },
                                 ]" :key="campo.clave" class="col-12" :class="campo.col">
                                <label :for="`coautor-${i}-${campo.clave}`" class="form-label small mb-1">
                                    {{ campo.etiqueta }}<span v-if="campo.obligatorio" aria-hidden="true"> *</span>
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
                </div>
                <button type="button" class="btn btn-outline-primary" :disabled="form.coautores.length >= maxCoautores"
                        @click="agregarCoautor">
                    Agregar otro coautor
                </button>
            </template>
        </fieldset>

        <fieldset class="mb-4">
            <legend class="h5">Archivo</legend>
            <label for="pdf" class="form-label">Artículo en PDF <span aria-hidden="true">*</span></label>
            <input id="pdf" name="pdf" type="file" accept="application/pdf,.pdf" required class="form-control"
                   :class="{ 'is-invalid': error('pdf') }" aria-describedby="pdf-ayuda pdf-error" @change="alElegirPdf" />
            <div id="pdf-ayuda" class="form-text">Solo PDF, máximo {{ pdfMaxMb }} MB.</div>
            <div v-if="error('pdf')" id="pdf-error" class="invalid-feedback">{{ error('pdf') }}</div>
        </fieldset>

        <div class="alert alert-warning" role="note">
            Una vez enviado, el artículo <strong>no se puede editar ni volver a enviar</strong>. Revisa tus datos antes de continuar.
        </div>

        <button type="submit" class="btn btn-primary" :disabled="enviando">
            {{ enviando ? 'Enviando…' : 'Enviar artículo' }}
        </button>
    </form>
</template>
