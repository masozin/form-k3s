<!-- resources/js/Components/RegisterStepIndicator.vue -->
<script setup>
defineProps({
    currentStep: Number, // 1 | 2
});
</script>

<template>
    <div class="mb-8 flex items-center gap-3">
        <div class="flex items-center gap-2">
            <div
                class="flex h-8 w-8 items-center justify-center rounded-full text-sm font-semibold transition-colors"
                :class="
                    currentStep >= 1
                        ? 'bg-brand text-white'
                        : 'bg-canvas text-muted dark:bg-zinc-800 dark:text-muted-dark'
                "
            >
                1
            </div>
            <span
                class="text-sm font-medium"
                :class="
                    currentStep >= 1
                        ? 'text-ink dark:text-ink-dark'
                        : 'text-muted dark:text-muted-dark'
                "
            >
                Detail Sekolah
            </span>
        </div>

        <div
            class="h-px flex-1"
            :class="
                currentStep >= 2 ? 'bg-brand' : 'bg-border dark:bg-border-dark'
            "
        ></div>

        <div class="flex items-center gap-2">
            <div
                class="flex h-8 w-8 items-center justify-center rounded-full text-sm font-semibold transition-colors"
                :class="
                    currentStep >= 2
                        ? 'bg-brand text-white'
                        : 'bg-canvas text-muted dark:bg-zinc-800 dark:text-muted-dark'
                "
            >
                2
            </div>
            <span
                class="text-sm font-medium"
                :class="
                    currentStep >= 2
                        ? 'text-ink dark:text-ink-dark'
                        : 'text-muted dark:text-muted-dark'
                "
            >
                Detail Akun
            </span>
        </div>
    </div>
</template>
