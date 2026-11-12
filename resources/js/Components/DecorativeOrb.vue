<!-- resources/js/Components/DecorativeOrb.vue -->
<script setup>
defineProps({
    color: { type: String, default: "emerald" }, // emerald | sienna
    class: { type: String, default: "" },
});
</script>

<template>
    <div
        class="pointer-events-none absolute rounded-full blur-3xl opacity-30"
        :class="[
            color === 'emerald' ? 'bg-brand-light' : 'bg-sienna-light',
            $props.class,
        ]"
    />
</template>
