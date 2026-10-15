<script setup>
import { computed } from 'vue';

const props = defineProps({
    meta: { type: Object, required: true },
});
const emit = defineEmits(['ir']);

// Páginas visibles: primera, última y dos alrededor de la actual.
const paginas = computed(() => {
    const { pagina, ultima } = props.meta;
    const visibles = new Set([1, ultima, pagina - 1, pagina, pagina + 1].filter((n) => n >= 1 && n <= ultima));
    const lista = [];
    let anterior = 0;
    for (const n of [...visibles].sort((a, b) => a - b)) {
        if (n - anterior > 1) lista.push('…');
        lista.push(n);
        anterior = n;
    }
    return lista;
});
</script>

<template>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
        <p class="small mb-0" aria-live="polite">
            <template v-if="meta.total">Mostrando {{ meta.desde }} a {{ meta.hasta }} de {{ meta.total }}</template>
            <template v-else>Sin resultados</template>
        </p>
        <nav v-if="meta.ultima > 1" aria-label="Paginación">
            <ul class="pagination mb-0">
                <li class="page-item" :class="{ disabled: meta.pagina <= 1 }">
                    <button type="button" class="page-link" :disabled="meta.pagina <= 1" @click="emit('ir', meta.pagina - 1)">
                        Anterior
                    </button>
                </li>
                <li v-for="(n, i) in paginas" :key="i" class="page-item"
                    :class="{ active: n === meta.pagina, disabled: n === '…' }">
                    <span v-if="n === '…'" class="page-link">…</span>
                    <button v-else type="button" class="page-link" :aria-current="n === meta.pagina ? 'page' : null"
                            @click="emit('ir', n)">
                        {{ n }}
                    </button>
                </li>
                <li class="page-item" :class="{ disabled: meta.pagina >= meta.ultima }">
                    <button type="button" class="page-link" :disabled="meta.pagina >= meta.ultima"
                            @click="emit('ir', meta.pagina + 1)">
                        Siguiente
                    </button>
                </li>
            </ul>
        </nav>
    </div>
</template>
