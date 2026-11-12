<!-- resources/js/Components/StatCard.vue -->
<script setup>
defineProps({
    label: String,
    value: [String, Number],
    icon: [Object, Function],
    accent: { type: String, default: "brand" }, // brand | sienna | muted
    hint: { type: String, default: null },
});

const accentClasses = {
    brand: "bg-brand/10 text-brand",
    sienna: "bg-sienna/10 text-sienna",
    muted: "bg-muted/10 text-muted",
};
</script>

<template>
    <div
        class="rounded-2xl border border-border bg-surface p-5 shadow-premium transition-shadow duration-200 hover:shadow-glow dark:border-border-dark dark:bg-surface-dark dark:shadow-premium-dark"
    >
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-medium text-muted dark:text-muted-dark">
                    {{ label }}
                </p>
                <p
                    class="mt-1.5 text-2xl font-bold tracking-tight text-ink dark:text-ink-dark"
                >
                    {{ value }}
                </p>
                <p
                    v-if="hint"
                    class="mt-1 text-xs text-muted dark:text-muted-dark"
                >
                    {{ hint }}
                </p>
            </div>
            <div
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                :class="accentClasses[accent]"
            >
                <component :is="icon" :size="20" stroke="1.75" />
            </div>
        </div>
    </div>
</template>
