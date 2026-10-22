<script setup>
import { computed, ref } from 'vue';

/**
 * Zona para elegir o arrastrar un archivo: PDF o, con «word», también .docx.
 * Revisa en el navegador el tamaño y la firma del contenido (%PDF o ZIP de
 * Word; el servidor vuelve a validarlo) y avisa al formulario con «elegido»
 * (archivo y error local).
 */
const props = defineProps({
    nombre: { type: String, required: true },
    maxKb: { type: Number, required: true },
    titulo: { type: String, default: 'Arrastra tu PDF aquí' },
    error: { type: String, default: null },
    word: { type: Boolean, default: false },
});

const aceptar = computed(() => (props.word
    ? 'application/pdf,.pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document,.docx'
    : 'application/pdf,.pdf'));
const tipos = computed(() => (props.word ? 'Word (.docx) o PDF' : 'solo PDF'));

const emit = defineEmits(['elegido']);

const campo = ref(null);
const archivo = ref(null);
const arrastrando = ref(false);

const maxMb = computed(() => Math.round(props.maxKb / 1024));
const peso = computed(() => {
    const bytes = archivo.value?.size ?? 0;
    return bytes >= 1024 * 1024 ? `${(bytes / 1024 / 1024).toFixed(1)} MB` : `${Math.max(1, Math.round(bytes / 1024))} KB`;
});

async function alElegir() {
    const elegido = campo.value?.files?.[0] ?? null;
    archivo.value = elegido;
    if (!elegido) {
        emit('elegido', { archivo: null, error: null });
        return;
    }
    if (elegido.size > props.maxKb * 1024) {
        emit('elegido', { archivo: elegido, error: `El archivo no debe pesar más de ${maxMb.value} MB.` });
        return;
    }
    const inicio = await elegido.slice(0, 4).text();
    const esPdf = inicio === '%PDF';
    const esWord = props.word && inicio === 'PK\u0003\u0004' && /\.docx$/i.test(elegido.name);
    emit('elegido', {
        archivo: elegido,
        error: esPdf || esWord ? null
            : props.word ? 'Sube un documento de Word (.docx) o un PDF.' : 'El archivo no es un PDF válido.',
    });
}

// Arrastrar y soltar: el archivo pasa al mismo campo del formulario.
function alSoltar(evento) {
    arrastrando.value = false;
    const archivos = evento.dataTransfer?.files;
    if (!archivos?.length || !campo.value) return;
    campo.value.files = archivos;
    alElegir();
}

defineExpose({ enfocar: () => campo.value?.focus() });
</script>

<template>
    <div>
        <div class="emp-archivo"
             :class="{ 'is-arrastrando': arrastrando, 'is-invalid': error, 'tiene-archivo': archivo && !error }"
             @dragenter.prevent="arrastrando = true" @dragover.prevent="arrastrando = true"
             @dragleave.prevent="arrastrando = false" @drop.prevent="alSoltar">
            <input :id="nombre" ref="campo" class="emp-archivo__campo" :name="nombre" type="file" :accept="aceptar"
                   required :aria-describedby="`${nombre}-ayuda ${nombre}-error`" @change="alElegir" />
            <label :for="nombre" class="emp-archivo__zona">
                <span class="emp-archivo__icono" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16" focusable="false">
                        <path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5" />
                        <path d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z" />
                    </svg>
                </span>
                <span v-if="archivo" class="emp-archivo__texto">
                    <strong class="text-break">{{ archivo.name }}</strong>
                    <span :id="`${nombre}-ayuda`" class="emp-num">{{ peso }} · <span class="emp-archivo__accion">Cambiar archivo</span></span>
                </span>
                <span v-else class="emp-archivo__texto">
                    <strong>{{ titulo }}</strong>
                    <span :id="`${nombre}-ayuda`">o <span class="emp-archivo__accion">elige un archivo</span> · {{ tipos }}, máximo {{ maxMb }} MB</span>
                </span>
            </label>
        </div>
        <div v-if="error" :id="`${nombre}-error`" class="invalid-feedback d-block">{{ error }}</div>
    </div>
</template>
