<script setup>
// Pinta los segmentos que calcula el servidor; nunca usa v-html.
defineProps({
    segmentos: { type: Array, default: () => [] },
});
</script>

<template>
    <template v-for="(s, i) in segmentos" :key="i">
        <mark v-if="s.marca" class="resaltado">{{ s.texto }}</mark>
        <template v-else>{{ s.texto }}</template>
    </template>
</template>
