<!-- resources/js/Layouts/AuthLayout.vue -->

<script setup>
import { Link } from "@inertiajs/vue3";
import { IconSchool, IconShieldCheck, IconClock } from "@tabler/icons-vue";
</script>

<template>
    <div class="flex min-h-screen bg-canvas dark:bg-canvas-dark">
        <!-- =====================================================
             PANEL KIRI — IDENTITAS K3S
        ====================================================== -->
        <div
            class="relative hidden overflow-hidden bg-gradient-to-br from-zinc-950 via-zinc-800 to-zinc-950 text-white lg:flex lg:w-5/12 lg:flex-col lg:justify-between px-8 py-10 xl:px-12 xl:py-14"
        >
            <!-- Decorative blur -->
            <div
                class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-zinc-900/20 blur-3xl"
            ></div>

            <div
                class="pointer-events-none absolute -bottom-32 -left-16 h-80 w-80 rounded-full bg-sienna/15 blur-3xl"
            ></div>

            <!-- Subtle glow -->
            <div
                class="pointer-events-none absolute left-1/2 top-1/2 h-96 w-96 -translate-x-1/2 -translate-y-1/2 rounded-full bg-brand/[0.04] blur-3xl"
            ></div>

            <!-- Content -->
            <div class="relative z-10">
                <!-- Logo -->
                <Link
                    :href="route('login')"
                    class="flex w-fit items-center gap-3"
                >
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 backdrop-blur ring-1 ring-white/10"
                    >
                        <IconSchool
                            :size="21"
                            stroke="1.6"
                            class="text-white"
                        />
                    </div>

                    <span class="text-sm font-medium text-white/80">
                        K3S Kecamatan Belitang II
                    </span>
                </Link>

                <!-- Main message -->
                <div class="mt-10">
                    <h1
                        class="font-display text-2xl font-semibold leading-snug xl:text-3xl"
                    >
                        Form Pendataan
                        <br />
                        Sekolah
                    </h1>

                    <p
                        class="mt-3 max-w-sm text-sm leading-relaxed text-white/55"
                    >
                        Satu akun untuk mengelola dan mengirimkan data sekolah
                        Anda ke K3S Kecamatan Belitang II.
                    </p>
                </div>
            </div>

            <!-- Benefits -->
            <div class="relative z-10 mt-12 hidden space-y-5 lg:block">
                <div class="flex items-start gap-3">
                    <div
                        class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-brand/10"
                    >
                        <IconShieldCheck
                            :size="15"
                            stroke="1.7"
                            class="text-brand-light"
                        />
                    </div>

                    <p class="max-w-sm text-sm leading-relaxed text-white/50">
                        Data tersimpan aman dan hanya diakses oleh operator
                        sekolah dan admin K3S.
                    </p>
                </div>

                <div class="flex items-start gap-3">
                    <div
                        class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-brand/10"
                    >
                        <IconClock
                            :size="15"
                            stroke="1.7"
                            class="text-brand-light"
                        />
                    </div>

                    <p class="max-w-sm text-sm leading-relaxed text-white/50">
                        Isi form pendataan kapan saja selama periode pengumpulan
                        masih dibuka.
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <p
                class="relative z-10 mt-12 hidden text-xs text-white/25 lg:block"
            >
                © {{ new Date().getFullYear() }} K3S Kecamatan Belitang II
            </p>
        </div>

        <!-- =====================================================
             PANEL KANAN — AUTH CONTENT
        ====================================================== -->
        <div
            class="relative flex w-full items-center justify-center overflow-hidden px-8 py-10 sm:px-8 lg:w-7/12 lg:px-12 xl:px-20"
        >
            <!-- Mobile decoration -->
            <div
                class="pointer-events-none absolute -right-32 -top-32 h-72 w-72 rounded-full bg-brand/10 blur-3xl lg:hidden"
            ></div>

            <div
                class="pointer-events-none absolute -bottom-32 -left-32 h-72 w-72 rounded-full bg-sienna/10 blur-3xl lg:hidden"
            ></div>

            <!-- Content langsung, TANPA CARD -->
            <div class="relative z-10 w-full max-w-md">
                <!-- Mobile logo -->
                <div class="mb-10 flex items-center justify-center lg:hidden">
                    <Link
                        :href="route('login')"
                        class="flex items-center gap-2.5"
                    >
                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand/10"
                        >
                            <IconSchool
                                :size="20"
                                stroke="1.6"
                                class="text-brand"
                            />
                        </div>

                        <span
                            class="font-display text-lg font-bold text-ink dark:text-ink-dark"
                        >
                            K3S
                        </span>
                    </Link>
                </div>

                <slot />
            </div>
        </div>
    </div>
</template>
