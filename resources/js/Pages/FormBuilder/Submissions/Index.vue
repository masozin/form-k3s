<!-- resources/js/Pages/FormBuilder/Submissions/Index.vue -->
<script setup>
import { ref, watch } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Link, router } from "@inertiajs/vue3";
import {
    IconSearch,
    IconUsers,
    IconSchool,
    IconChevronRight,
    IconInbox,
    IconArrowLeft,
    IconFileText,
    IconFileZip,
} from "@tabler/icons-vue";
import { debounce } from "@/utils/debounce";

const props = defineProps({
    form: Object,
    submissions: Object,
    filters: Object,
    totalSchoolsSubmitted: Number,
});

const search = ref(props.filters.search ?? "");

watch(
    search,
    debounce((val) => {
        router.get(
            route("forms.submissions.index", props.form.id),
            { search: val },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            },
        );
    }, 400),
);

function formatDate(d) {
    if (!d) return "—";
    return new Date(d).toLocaleDateString("id-ID", {
        day: "numeric",
        month: "short",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
}
</script>

<template>
    <AdminLayout>
        <template #header>
            <div class="flex items-center gap-2 text-sm">
                <Link
                    :href="route('forms.index')"
                    class="text-muted hover:text-ink dark:text-muted-dark dark:hover:text-ink-dark"
                    >Form</Link
                >

                <span class="text-muted/50 dark:text-muted-dark/50">/</span>
                <span class="font-medium text-ink dark:text-ink-dark">{{
                    form.title
                }}</span>
            </div>
        </template>

        <div class="mx-auto max-w-5xl">
            <div
                class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h1
                        class="font-display text-2xl font-bold tracking-tight text-ink dark:text-ink-dark"
                    >
                        Daftar Jawaban
                    </h1>
                    <p class="mt-1 text-sm text-muted dark:text-muted-dark">
                        {{ form.title }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <a :href="route('forms.submissions.export', form.id)">
                        <button
                            class="flex items-center gap-1.5 rounded-lg bg-brand px-3 py-2 text-sm font-medium text-white shadow-glow transition hover:bg-brand-dark"
                        >
                            <IconDownload :size="15" stroke="1.75" /> Export
                            Excel
                        </button>
                    </a>
                    <a :href="route('forms.submissions.export-files', form.id)">
                        <button
                            class="flex items-center gap-1.5 rounded-lg border border-border px-3 py-2 text-sm font-medium text-ink transition hover:bg-canvas dark:border-border-dark dark:text-ink-dark dark:hover:bg-zinc-800"
                        >
                            <IconFileZip :size="15" stroke="1.75" /> Download
                            Semua File
                        </button>
                    </a>
                </div>
            </div>

            <!-- Ringkasan -->
            <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div
                    class="rounded-2xl border border-border bg-surface p-5 shadow-premium dark:border-border-dark dark:bg-surface-dark dark:shadow-premium-dark"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand/10 text-brand"
                        >
                            <IconUsers :size="19" stroke="1.75" />
                        </div>
                        <div>
                            <p
                                class="text-2xl font-bold text-ink dark:text-ink-dark"
                            >
                                {{ submissions.total }}
                            </p>
                            <p class="text-xs text-muted dark:text-muted-dark">
                                Total jawaban masuk
                            </p>
                        </div>
                    </div>
                </div>
                <div
                    class="rounded-2xl border border-border bg-surface p-5 shadow-premium dark:border-border-dark dark:bg-surface-dark dark:shadow-premium-dark"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-sienna/10 text-sienna"
                        >
                            <IconSchool :size="19" stroke="1.75" />
                        </div>
                        <div>
                            <p
                                class="text-2xl font-bold text-ink dark:text-ink-dark"
                            >
                                {{ totalSchoolsSubmitted }}
                            </p>
                            <p class="text-xs text-muted dark:text-muted-dark">
                                Sekolah sudah mengisi
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search -->
            <div class="mb-4 relative">
                <IconSearch
                    :size="17"
                    stroke="1.75"
                    class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-muted dark:text-muted-dark"
                />
                <input
                    v-model="search"
                    type="text"
                    placeholder="Cari nama pengisi atau isi jawaban..."
                    class="w-full rounded-xl border-border bg-surface pl-10 text-sm text-ink placeholder:text-muted/60 focus:border-brand focus:ring-brand/20 dark:border-border-dark dark:bg-surface-dark dark:text-ink-dark"
                />
            </div>

            <!-- Empty state -->
            <div
                v-if="submissions.data.length === 0"
                class="flex flex-col items-center rounded-2xl border border-dashed border-border bg-canvas/50 px-6 py-16 text-center dark:border-border-dark dark:bg-zinc-900/30"
            >
                <IconInbox
                    :size="26"
                    stroke="1.5"
                    class="text-muted dark:text-muted-dark"
                />
                <p class="mt-3 text-sm text-muted dark:text-muted-dark">
                    {{
                        search
                            ? "Tidak ada jawaban yang cocok dengan pencarian."
                            : "Belum ada jawaban yang masuk untuk form ini."
                    }}
                </p>
            </div>

            <!-- List -->
            <div v-else class="space-y-3">
                <Link
                    v-for="submission in submissions.data"
                    :key="submission.id"
                    :href="
                        route('forms.submissions.show', [
                            form.id,
                            submission.id,
                        ])
                    "
                    class="group flex items-center justify-between rounded-2xl border border-border bg-surface p-4 shadow-premium transition-shadow duration-200 hover:shadow-glow dark:border-border-dark dark:bg-surface-dark dark:shadow-premium-dark sm:p-5"
                >
                    <div class="min-w-0 flex-1">
                        <div
                            class="flex flex-wrap items-center gap-x-3 gap-y-1"
                        >
                            <p class="font-medium text-ink dark:text-ink-dark">
                                {{
                                    submission.user?.name ??
                                    "Pengguna tidak dikenal"
                                }}
                            </p>
                            <span
                                v-if="submission.user?.school_name"
                                class="flex items-center gap-1 text-xs text-muted dark:text-muted-dark"
                            >
                                <IconSchool :size="12" />
                                {{ submission.user.school_name }}
                            </span>
                        </div>
                        <p
                            class="mt-0.5 text-xs text-muted dark:text-muted-dark"
                        >
                            {{ formatDate(submission.submitted_at) }}
                        </p>

                        <div
                            v-if="submission.preview.length"
                            class="mt-2.5 flex flex-wrap gap-x-4 gap-y-1"
                        >
                            <div
                                v-for="(p, i) in submission.preview"
                                :key="i"
                                class="text-xs"
                            >
                                <span class="text-muted dark:text-muted-dark"
                                    >{{ p.label }}:</span
                                >
                                <span
                                    class="ml-1 font-medium text-ink dark:text-ink-dark"
                                    >{{ p.value }}</span
                                >
                            </div>
                        </div>
                    </div>

                    <IconChevronRight
                        :size="18"
                        class="ml-3 shrink-0 text-muted transition-transform duration-150 group-hover:translate-x-0.5 dark:text-muted-dark"
                    />
                </Link>
            </div>

            <!-- Pagination -->
            <div
                v-if="submissions.links.length > 3"
                class="mt-6 flex flex-wrap items-center justify-center gap-1"
            >
                <template v-for="(link, i) in submissions.links" :key="i">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        v-html="link.label"
                        preserve-scroll
                        class="rounded-lg px-3 py-1.5 text-sm transition"
                        :class="
                            link.active
                                ? 'bg-brand text-white'
                                : 'text-muted hover:bg-canvas dark:text-muted-dark dark:hover:bg-zinc-800'
                        "
                    />
                    <span
                        v-else
                        v-html="link.label"
                        class="rounded-lg px-3 py-1.5 text-sm text-muted/40 dark:text-muted-dark/40"
                    />
                </template>
            </div>
        </div>
    </AdminLayout>
</template>
