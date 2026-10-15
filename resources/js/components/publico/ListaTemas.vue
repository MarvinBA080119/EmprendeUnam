<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    temas: { type: Array, required: true },
});

const filtro = ref('');

// Comparación sin mayúsculas ni acentos.
const normalizar = (texto) =>
    texto.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();

const temasFiltrados = computed(() => {
    const termino = normalizar(filtro.value.trim());
    return termino === ''
        ? props.temas
        : props.temas.filter((tema) => normalizar(tema.nombre).includes(termino));
});
</script>

<template>
    <div>
        <label for="filtro-temas" class="form-label">Filtrar temas</label>
        <input
            id="filtro-temas"
            v-model="filtro"
            type="search"
            class="form-control mb-3"
            placeholder="Escribe parte del nombre…"
            autocomplete="off"
        />
        <p class="text-body-secondary small" aria-live="polite">
            {{ temasFiltrados.length }} de {{ temas.length }} temas
        </p>
        <ul class="list-group">
            <li v-for="tema in temasFiltrados" :key="tema.id" class="list-group-item">
                {{ tema.nombre }}
            </li>
        </ul>
    </div>
</template>
