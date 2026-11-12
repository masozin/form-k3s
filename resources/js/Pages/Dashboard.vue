<!-- resources/js/Pages/Dashboard.vue -->
<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import StatCard from "@/Components/StatCard.vue";
import Button from "@/Components/Button.vue";
import { Link, usePage } from "@inertiajs/vue3";
import {
    IconSchool,
    IconUsers,
    IconFileText,
    IconRocket,
    IconChecklist,
    IconPencil,
    IconMapPin,
    IconAward,
    IconUserCircle,
    IconClockHour4,
    IconCircleCheck,
    IconCircleDashed,
    IconArrowRight,
    IconTrendingUp,
} from "@tabler/icons-vue";
import { computed } from "vue";

const props = defineProps({
    role: String,
    stats: Object,
    // admin
    recentForms: { type: Array, default: () => [] },
    trendData: { type: Array, default: () => [] },
    // user
    school: { type: Object, default: null },
    activeForms: { type: Array, default: () => [] },
});

const page = usePage();
const user = computed(() => page.props.auth.user);

const greeting = computed(() => {
    const hour = new Date().getHours();
    if (hour < 11) return "Selamat pagi";
    if (hour < 15) return "Selamat siang";
    if (hour < 18) return "Selamat sore";
    return "Selamat malam";
});

const statusBadge = {
    draft: "bg-muted/10 text-muted",
    published: "bg-brand/10 text-brand",
    closed: "bg-sienna/10 text-sienna",
};

function formatDate(d) {
    if (!d) return "—";
    return new Date(d).toLocaleDateString("id-ID", {
        day: "numeric",
        month: "short",
        year: "numeric",
    });
}

const maxTrend = computed(() =>
    Math.max(...props.trendData.map((d) => d.total), 1),
);
</script>

<template>
    <AdminLayout>
        <template #header>
            <h2
                class="font-display text-lg font-semibold text-ink dark:text-ink-dark"
            >
                Dashboard
            </h2>
        </template>

        <!-- Sambutan -->
        <div class="mb-8">
            <h1
                class="font-display text-2xl font-bold tracking-tight text-ink dark:text-ink-dark sm:text-3xl"
            >
                {{ greeting }}, {{ user.name.split(" ")[0] }}
            </h1>
            <p class="mt-1.5 text-sm text-muted dark:text-muted-dark">
                {{
                    role === "admin"
                        ? "Berikut ringkasan aktivitas platform hari ini."
                        : "Berikut ringkasan sekolah dan form kamu."
                }}
            </p>
        </div>

        <!-- ============ ADMIN / OPERATOR ============ -->
        <template v-if="role === 'admin'">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <StatCard
                    label="Sekolah Terdaftar"
                    :value="stats.total_schools"
                    :icon="IconSchool"
                    accent="brand"
                />
                <StatCard
                    label="Total Pengguna"
                    :value="stats.total_users"
                    :icon="IconUsers"
                    accent="brand"
                />
                <StatCard
                    label="Total Form"
                    :value="stats.total_forms"
                    :icon="IconFileText"
                    accent="muted"
                />
                <StatCard
                    label="Form Aktif"
                    :value="stats.active_forms"
                    :icon="IconRocket"
                    accent="sienna"
                    :hint="`dari ${stats.total_forms} total form`"
                />
            </div>

            <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Tren pengisian -->
                <div
                    class="rounded-2xl border border-border bg-surface p-5 shadow-premium dark:border-border-dark dark:bg-surface-dark dark:shadow-premium-dark lg:col-span-2"
                >
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <h3
                                class="font-display text-base font-semibold text-ink dark:text-ink-dark"
                            >
                                Tren Pengisian
                            </h3>
                            <p class="text-xs text-muted dark:text-muted-dark">
                                Total jawaban masuk 7 hari terakhir
                            </p>
                        </div>
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand/10 text-brand"
                        >
                            <IconTrendingUp :size="16" stroke="1.75" />
                        </div>
                    </div>

                    <div class="flex h-40 items-end gap-2">
                        <div
                            v-for="d in trendData"
                            :key="d.date"
                            class="flex flex-1 flex-col items-center gap-2"
                        >
                            <div class="flex w-full flex-1 items-end">
                                <div
                                    class="w-full rounded-t-md bg-brand/80 transition-all duration-300 hover:bg-brand"
                                    :style="{
                                        height: `${(d.total / maxTrend) * 100}%`,
                                        minHeight: d.total > 0 ? '4px' : '2px',
                                    }"
                                    :class="
                                        d.total === 0
                                            ? 'bg-border dark:bg-border-dark'
                                            : ''
                                    "
                                ></div>
                            </div>
                            <span
                                class="text-[10px] text-muted dark:text-muted-dark"
                                >{{ d.date }}</span
                            >
                        </div>
                    </div>
                </div>

                <!-- Total submission -->
                <div
                    class="flex flex-col justify-between rounded-2xl border border-border bg-gradient-to-br from-brand to-brand-dark p-5 text-white shadow-glow"
                >
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/15"
                    >
                        <IconChecklist :size="20" stroke="1.75" />
                    </div>
                    <div>
                        <p class="text-3xl font-bold tracking-tight">
                            {{ stats.total_submissions }}
                        </p>
                        <p class="mt-1 text-sm text-white/80">
                            Total jawaban terkumpul
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form terbaru -->
            <div
                class="mt-6 rounded-2xl border border-border bg-surface p-5 shadow-premium dark:border-border-dark dark:bg-surface-dark dark:shadow-premium-dark"
            >
                <div class="mb-4 flex items-center justify-between">
                    <h3
                        class="font-display text-base font-semibold text-ink dark:text-ink-dark"
                    >
                        Form Terbaru
                    </h3>
                    <Link
                        :href="route('forms.index')"
                        class="flex items-center gap-1 text-sm font-medium text-brand hover:text-brand-dark"
                    >
                        Lihat semua <IconArrowRight :size="14" />
                    </Link>
                </div>

                <div
                    v-if="recentForms.length === 0"
                    class="py-8 text-center text-sm text-muted dark:text-muted-dark"
                >
                    Belum ada form yang dibuat.
                </div>

                <div
                    v-else
                    class="divide-y divide-border dark:divide-border-dark"
                >
                    <Link
                        v-for="form in recentForms"
                        :key="form.id"
                        :href="route('forms.edit', form.id)"
                        class="flex items-center justify-between py-3 transition-colors hover:bg-canvas dark:hover:bg-zinc-800/40 -mx-2 px-2 rounded-lg"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-9 w-9 items-center justify-center rounded-lg bg-canvas text-muted dark:bg-zinc-800 dark:text-muted-dark"
                            >
                                <IconFileText :size="17" stroke="1.75" />
                            </div>
                            <div>
                                <p
                                    class="text-sm font-medium text-ink dark:text-ink-dark"
                                >
                                    {{ form.title }}
                                </p>
                                <p
                                    class="text-xs text-muted dark:text-muted-dark"
                                >
                                    {{ form.submissions_count }} pengisian
                                </p>
                            </div>
                        </div>
                        <span
                            class="rounded-full px-2.5 py-1 text-xs font-medium"
                            :class="statusBadge[form.status]"
                        >
                            {{
                                form.status === "published"
                                    ? "Terbit"
                                    : form.status === "draft"
                                      ? "Draft"
                                      : "Ditutup"
                            }}
                        </span>
                    </Link>
                </div>
            </div>
        </template>

        <!-- ============ USER / GURU ============ -->
        <template v-else>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <StatCard
                    label="Form Aktif"
                    :value="stats.forms_active"
                    :icon="IconRocket"
                    accent="brand"
                />
                <StatCard
                    label="Sudah Diisi"
                    :value="stats.forms_submitted"
                    :icon="IconCircleCheck"
                    accent="brand"
                />
                <StatCard
                    label="Belum Diisi"
                    :value="stats.forms_pending"
                    :icon="IconCircleDashed"
                    accent="sienna"
                />
            </div>

            <!-- Detail Sekolah -->
            <div
                class="mt-6 rounded-2xl border border-border bg-surface p-5 shadow-premium dark:border-border-dark dark:bg-surface-dark dark:shadow-premium-dark"
            >
                <div class="mb-4 flex items-center justify-between">
                    <h3
                        class="font-display text-base font-semibold text-ink dark:text-ink-dark"
                    >
                        Detail Sekolah
                    </h3>
                    <Link
                        v-if="school"
                        :href="route('schools.edit', school.id)"
                    >
                        <Button variant="secondary">
                            <template #icon
                                ><IconPencil :size="15" stroke="1.75"
                            /></template>
                            Edit Sekolah
                        </Button>
                    </Link>
                </div>

                <div
                    v-if="!school"
                    class="py-8 text-center text-sm text-muted dark:text-muted-dark"
                >
                    Kamu belum terhubung ke sekolah mana pun. Hubungi admin
                    untuk mengatur ini.
                </div>

                <div
                    v-else
                    class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2"
                >
                    <div class="flex items-start gap-3">
                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-brand/10 text-brand"
                        >
                            <IconSchool :size="17" stroke="1.75" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-muted dark:text-muted-dark">
                                Nama Sekolah
                            </p>
                            <p
                                class="truncate text-sm font-medium text-ink dark:text-ink-dark"
                            >
                                {{ school.name }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-canvas text-muted dark:bg-zinc-800 dark:text-muted-dark"
                        >
                            <IconAward :size="17" stroke="1.75" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-muted dark:text-muted-dark">
                                NPSN &middot; Akreditasi
                            </p>
                            <p
                                class="text-sm font-medium text-ink dark:text-ink-dark"
                            >
                                {{ school.npsn }} &middot;
                                {{ school.accreditation ?? "—" }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 sm:col-span-2">
                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-canvas text-muted dark:bg-zinc-800 dark:text-muted-dark"
                        >
                            <IconMapPin :size="17" stroke="1.75" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-muted dark:text-muted-dark">
                                Alamat
                            </p>
                            <p
                                class="text-sm font-medium text-ink dark:text-ink-dark"
                            >
                                {{ school.address ?? "—" }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-canvas text-muted dark:bg-zinc-800 dark:text-muted-dark"
                        >
                            <IconUserCircle :size="17" stroke="1.75" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-muted dark:text-muted-dark">
                                Kepala Sekolah
                            </p>
                            <p
                                class="text-sm font-medium text-ink dark:text-ink-dark"
                            >
                                {{ school.principal_name ?? "—" }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-canvas text-muted dark:bg-zinc-800 dark:text-muted-dark"
                        >
                            <IconSchool :size="17" stroke="1.75" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-muted dark:text-muted-dark">
                                Jenjang
                            </p>
                            <p
                                class="text-sm font-medium text-ink dark:text-ink-dark"
                            >
                                {{ school.level ?? "—" }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daftar Form Aktif -->
            <div
                class="mt-6 rounded-2xl border border-border bg-surface p-5 shadow-premium dark:border-border-dark dark:bg-surface-dark dark:shadow-premium-dark"
            >
                <h3
                    class="mb-4 font-display text-base font-semibold text-ink dark:text-ink-dark"
                >
                    Form yang Bisa Diisi
                </h3>

                <div
                    v-if="activeForms.length === 0"
                    class="py-8 text-center text-sm text-muted dark:text-muted-dark"
                >
                    Tidak ada form aktif untuk kamu saat ini.
                </div>

                <div v-else class="space-y-2">
                    <Link
                        v-for="form in activeForms"
                        :key="form.id"
                        :href="route('form.public.show', form.slug)"
                        class="flex items-center justify-between rounded-xl border border-border p-4 transition-colors hover:bg-canvas dark:border-border-dark dark:hover:bg-zinc-800/40"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-9 w-9 items-center justify-center rounded-lg"
                                :class="
                                    form.has_submitted
                                        ? 'bg-brand/10 text-brand'
                                        : 'bg-sienna/10 text-sienna'
                                "
                            >
                                <component
                                    :is="
                                        form.has_submitted
                                            ? IconCircleCheck
                                            : IconCircleDashed
                                    "
                                    :size="17"
                                    stroke="1.75"
                                />
                            </div>
                            <div>
                                <p
                                    class="text-sm font-medium text-ink dark:text-ink-dark"
                                >
                                    {{ form.title }}
                                </p>
                                <p
                                    class="flex items-center gap-1 text-xs text-muted dark:text-muted-dark"
                                >
                                    <IconClockHour4 :size="12" />
                                    Batas: {{ formatDate(form.end_at) }}
                                </p>
                            </div>
                        </div>

                        <span
                            class="rounded-full px-2.5 py-1 text-xs font-medium"
                            :class="
                                form.has_submitted
                                    ? 'bg-brand/10 text-brand'
                                    : 'bg-sienna/10 text-sienna'
                            "
                        >
                            {{
                                form.has_submitted
                                    ? form.allow_multiple_submissions
                                        ? "Sudah diisi · Bisa isi lagi"
                                        : "Sudah diisi"
                                    : "Belum diisi"
                            }}
                        </span>
                    </Link>
                </div>
            </div>
        </template>
    </AdminLayout>
</template>
