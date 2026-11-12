<!-- resources/js/Components/AlertModal.vue -->
<script setup>
import { computed, watch, ref, nextTick, onBeforeUnmount } from "vue";
import { useAlert } from "@/Composables/useAlert";
import {
    IconCheck,
    IconX,
    IconAlertTriangle,
    IconInfoCircle,
} from "@tabler/icons-vue";

const { state, close } = useAlert();
const confirmBtn = ref(null);

// Semua kelas ditulis utuh supaya terbaca oleh Tailwind
const config = computed(
    () =>
        ({
            success: {
                icon: IconCheck,
                ring: "bg-success/15 text-success ring-8 ring-success/10",
                btn: "bg-success hover:brightness-110 focus-visible:ring-success/40",
            },
            error: {
                icon: IconX,
                ring: "bg-danger/15 text-danger ring-8 ring-danger/10",
                btn: "bg-danger hover:brightness-110 focus-visible:ring-danger/40",
            },
            warning: {
                icon: IconAlertTriangle,
                ring: "bg-warning/15 text-warning ring-8 ring-warning/10",
                btn: "bg-warning hover:brightness-110 focus-visible:ring-warning/40",
            },
            info: {
                icon: IconInfoCircle,
                ring: "bg-info/15 text-info ring-8 ring-info/10",
                btn: "bg-info hover:brightness-110 focus-visible:ring-info/40",
            },
        })[state.type] ?? {},
);

function onKeydown(e) {
    if (e.key === "Escape") close(false);
}

watch(
    () => state.open,
    async (open) => {
        if (open) {
            window.addEventListener("keydown", onKeydown);
            await nextTick();
            confirmBtn.value?.focus();
        } else {
            window.removeEventListener("keydown", onKeydown);
        }
    },
);

onBeforeUnmount(() => window.removeEventListener("keydown", onKeydown));
</script>

<template>
    <Teleport to="body">
        <Transition name="alert">
            <div
                v-if="state.open"
                class="alert-overlay fixed inset-0 z-[100] flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
                @click.self="close(false)"
            >
                <div
                    role="alertdialog"
                    aria-modal="true"
                    class="alert-card w-full max-w-sm rounded-2xl border border-border bg-surface p-6 text-center shadow-2xl"
                >
                    <!-- Ikon dengan ring -->
                    <div
                        :key="state.type + state.title"
                        class="alert-icon mx-auto flex h-16 w-16 items-center justify-center rounded-full"
                        :class="config.ring"
                    >
                        <component :is="config.icon" :size="30" stroke="2.2" />
                    </div>

                    <h3 class="mt-5 text-lg font-bold text-ink">
                        {{ state.title }}
                    </h3>

                    <p
                        v-if="state.message"
                        class="mt-1.5 text-sm leading-relaxed text-muted"
                    >
                        {{ state.message }}
                    </p>

                    <div class="mt-6 flex gap-3">
                        <button
                            v-if="state.cancelText"
                            type="button"
                            @click="close(false)"
                            class="flex-1 rounded-xl border-2 border-muted/30 bg-surface px-4 py-2.5 text-sm font-semibold text-ink transition duration-150 hover:border-muted/60 hover:bg-muted/10 active:scale-95"
                        >
                            {{ state.cancelText }}
                        </button>

                        <button
                            ref="confirmBtn"
                            type="button"
                            @click="close(true)"
                            class="flex-1 rounded-xl px-4 py-2.5 text-sm font-semibold text-surface shadow-md transition duration-150 hover:-translate-y-0.5 hover:shadow-lg focus:outline-none focus-visible:ring-4 active:translate-y-0 active:scale-95"
                            :class="config.btn"
                        >
                            {{ state.confirmText }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
/* Overlay fade, kartu zoom + naik sedikit */
.alert-enter-active,
.alert-leave-active {
    transition: opacity 0.2s ease;
}
.alert-enter-from,
.alert-leave-to {
    opacity: 0;
}
.alert-enter-active .alert-card {
    animation: alert-pop 0.32s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.alert-leave-active .alert-card {
    animation: alert-out 0.15s ease-in forwards;
}

/* Ikon membesar dengan efek memantul */
.alert-icon {
    animation: alert-icon-pop 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) 0.1s both;
}

@keyframes alert-pop {
    from {
        opacity: 0;
        transform: translateY(16px) scale(0.92);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}
@keyframes alert-out {
    to {
        opacity: 0;
        transform: translateY(8px) scale(0.96);
    }
}
@keyframes alert-icon-pop {
    from {
        opacity: 0;
        transform: scale(0.3) rotate(-20deg);
    }
    to {
        opacity: 1;
        transform: scale(1) rotate(0);
    }
}
</style>