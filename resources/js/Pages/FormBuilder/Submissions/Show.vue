<!-- resources/js/Pages/FormBuilder/Submissions/Show.vue -->
<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { formatNumber } from "@/utils/numberFormat";
import { Link } from "@inertiajs/vue3";
import {
    IconArrowLeft,
    IconUser,
    IconSchool,
    IconMail,
    IconCalendar,
    IconFile,
    IconDownload,
    IconHash,
} from "@tabler/icons-vue";
import { computed } from "vue";

const props = defineProps({
    form: Object,
    submission: Object,
});

const answerableItems = computed(() => {
    const loose = (props.form.fields ?? []).map((f) => ({
        ...f,
        sectionTitle: null,
    }));
    const sectioned = (props.form.sections ?? []).flatMap((s) =>
        (s.fields ?? []).map((f) => ({ ...f, sectionTitle: s.title })),
    );
    return [...loose, ...sectioned].sort((a, b) => a.sort_order - b.sort_order);
});

function formatDate(d) {
    if (!d) return "—";
    return new Date(d).toLocaleDateString("id-ID", {
        day: "numeric",
        month: "long",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
}

function answerFor(field) {
    if (field.type === "file") {
        return props.submission.files?.[field.name] ?? null;
    }
    const val = props.submission.data?.[field.name];
    if (field.type === "date" && val) {
        return formatDate(
            parseISO(val),
            field.config?.date_format || "DD/MM/YYYY",
        );
    }
    if (
        field.type === "number" &&
        val !== null &&
        val !== undefined &&
        field.config?.number_format
    ) {
        return formatNumber(val, field.config.number_format);
    }

    if (Array.isArray(val)) return val.length ? val.join(", ") : null;
    return val || null;
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
                <Link
                    :href="route('forms.submissions.index', form.id)"
                    class="text-muted hover:text-ink dark:text-muted-dark dark:hover:text-ink-dark"
                    >{{ form.title }}</Link
                >
                <span class="text-muted/50 dark:text-muted-dark/50">/</span>
                <span class="font-medium text-ink dark:text-ink-dark"
                    >Detail Jawaban</span
                >
            </div>
        </template>

        <div class="mx-auto max-w-3xl">
            <Link
                :href="route('forms.submissions.index', form.id)"
                class="mb-4 flex items-center gap-1.5 text-sm font-medium text-muted hover:text-ink dark:text-muted-dark dark:hover:text-ink-dark"
            >
                <IconArrowLeft :size="16" /> Kembali ke Daftar Jawaban
            </Link>

            <!-- Header pengisi -->
            <div
                class="mb-6 rounded-2xl border border-border bg-surface p-5 shadow-premium dark:border-border-dark dark:bg-surface-dark dark:shadow-premium-dark"
            >
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand/10 text-sm font-bold text-brand"
                        >
                            {{
                                (submission.user?.name ?? "?")
                                    .charAt(0)
                                    .toUpperCase()
                            }}
                        </div>
                        <div>
                            <p
                                class="font-semibold text-ink dark:text-ink-dark"
                            >
                                {{
                                    submission.user?.name ??
                                    "Pengguna tidak dikenal"
                                }}
                            </p>
                            <div
                                class="mt-1 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-muted dark:text-muted-dark"
                            >
                                <span
                                    v-if="submission.user?.email"
                                    class="flex items-center gap-1"
                                    ><IconMail :size="12" />
                                    {{ submission.user.email }}</span
                                >
                                <span
                                    v-if="submission.user?.school_name"
                                    class="flex items-center gap-1"
                                    ><IconSchool :size="12" />
                                    {{ submission.user.school_name }}</span
                                >
                                <span class="flex items-center gap-1"
                                    ><IconCalendar :size="12" />
                                    {{
                                        formatDate(submission.submitted_at)
                                    }}</span
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="mt-4 flex items-center gap-1.5 rounded-lg bg-canvas px-3 py-2 text-xs text-muted dark:bg-zinc-900 dark:text-muted-dark"
                >
                    <IconHash :size="13" />
                    <span class="font-mono">{{
                        submission.submission_token
                    }}</span>
                </div>
            </div>

            <!-- Jawaban -->
            <div class="space-y-3">
                <template
                    v-for="(field, index) in answerableItems"
                    :key="field.id"
                >
                    <!-- Judul section, tampil sekali di awal grupnya -->
                    <div
                        v-if="
                            field.sectionTitle &&
                            (index === 0 ||
                                answerableItems[index - 1].sectionTitle !==
                                    field.sectionTitle)
                        "
                        class="pt-4 pb-1 first:pt-0"
                    >
                        <h3
                            class="font-display text-sm font-semibold uppercase tracking-wide text-muted dark:text-muted-dark"
                        >
                            {{ field.sectionTitle }}
                        </h3>
                    </div>

                    <!-- Heading/paragraph ditampilkan sebagai konteks, bukan jawaban -->
                    <div v-if="field.type === 'heading'" class="pt-2">
                        <h4
                            class="font-display text-base font-semibold text-ink dark:text-ink-dark"
                        >
                            {{ field.label }}
                        </h4>
                    </div>
                    <p
                        v-else-if="field.type === 'paragraph'"
                        class="text-sm text-muted dark:text-muted-dark"
                    >
                        {{ field.label }}
                    </p>

                    <!-- Jawaban field biasa -->
                    <div
                        v-else
                        class="rounded-xl border border-border bg-surface p-4 dark:border-border-dark dark:bg-surface-dark"
                    >
                        <p
                            class="text-xs font-medium text-muted dark:text-muted-dark"
                        >
                            {{ field.label }}
                        </p>
                        <a
                            v-if="field.type === 'file' && answerFor(field)"
                            :href="answerFor(field).url"
                            target="_blank"
                            class="mt-1.5 flex items-center gap-2 text-sm font-medium text-brand hover:text-brand-dark"
                        >
                            <IconFile :size="16" stroke="1.75" />
                            {{ answerFor(field).original_name }}
                            <span
                                class="text-xs text-muted dark:text-muted-dark"
                                >({{ answerFor(field).size }})</span
                            >
                            <IconDownload :size="14" stroke="1.75" />
                        </a>

                        <!-- Jawaban teks/angka/tanggal/pilihan -->
                        <p
                            v-else
                            class="mt-1.5 text-sm text-ink dark:text-ink-dark"
                            :class="{
                                'italic text-muted dark:text-muted-dark':
                                    !answerFor(field),
                            }"
                        >
                            {{ answerFor(field) ?? "Tidak dijawab" }}
                        </p>
                    </div>
                </template>
            </div>
        </div>
    </AdminLayout>
</template>
