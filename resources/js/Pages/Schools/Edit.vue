<!-- resources/js/Pages/Schools/Edit.vue -->
<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import TextInput from "@/Components/TextInput.vue";
import Button from "@/Components/Button.vue";
import AlertModal from "@/Components/AlertModal.vue";
import { useAlert } from "@/Composables/useAlert";
import { Link, useForm, router } from "@inertiajs/vue3";
import {
    IconSchool,
    IconArrowLeft,
    IconDeviceFloppy,
} from "@tabler/icons-vue";

const props = defineProps({
    school: Object,
});

const alert = useAlert();

const form = useForm({
    npsn: props.school.npsn,
    name: props.school.name,
    address: props.school.address,
    level: props.school.level,
    accreditation: props.school.accreditation,
    principal_name: props.school.principal_name,
    principal_nip: props.school.principal_nip,
});

function submit() {
    form.put(route("schools.update", props.school.id), {
        preserveScroll: true,

        // Sukses -> tampilkan alert, setelah OK pindah ke dashboard
        onSuccess: async () => {
            await alert.success(
                "Berhasil disimpan",
                "Data sekolah berhasil diperbarui.",
            );
            router.visit(route("dashboard"));
        },

        // Gagal -> tampilkan alert, setelah OK tetap di halaman ini
        onError: (errors) => {
            const first = Object.values(errors)[0];
            alert.error(
                "Gagal menyimpan",
                first ?? "Periksa kembali isian yang ditandai merah.",
            );
        },
    });
}
</script>

<template>
    <AdminLayout>
        <template #header>
            <div class="flex items-center gap-2 text-sm">
                <Link
                    :href="route('dashboard')"
                    class="text-muted transition hover:text-ink"
                >
                    Dashboard
                </Link>
                <span class="text-muted/50">/</span>
                <span class="font-semibold text-ink">Edit Sekolah</span>
            </div>
        </template>

        <div class="mx-auto max-w-2xl">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-ink">
                        Edit Data Sekolah
                    </h1>
                    <p class="mt-1 text-sm text-muted">
                        Perbarui informasi sekolah kamu di sini.
                    </p>
                </div>
                <Link :href="route('dashboard')">
                    <button
                        type="button"
                        class="flex items-center gap-1.5 rounded-xl border border-border px-3 py-2 text-sm font-medium text-ink transition hover:bg-canvas"
                    >
                        <IconArrowLeft :size="15" stroke="1.75" /> Kembali
                    </button>
                </Link>
            </div>

            <div
                class="rounded-2xl border border-border bg-surface p-6 shadow-premium"
            >
                <form @submit.prevent="submit" class="space-y-5">
                    <div
                        class="flex items-center gap-3 border-b border-border pb-4"
                    >
                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand/10 text-brand"
                        >
                            <IconSchool :size="20" stroke="1.75" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-ink">
                                {{ school.name }}
                            </p>
                            <p class="text-xs text-muted">
                                NPSN {{ school.npsn }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <TextInput
                            v-model="form.npsn"
                            label="NPSN"
                            :error="form.errors.npsn"
                        />

                        <div>
                            <label
                                class="mb-1.5 block text-sm font-medium text-ink"
                                >Jenjang</label
                            >
                            <select
                                v-model="form.level"
                                class="w-full rounded-md border-border bg-surface text-sm text-ink focus:border-brand focus:ring-brand/20"
                                :class="{ 'border-danger': form.errors.level }"
                            >
                                <option value="">Pilih jenjang</option>
                                <option value="SD">SD</option>
                                <option value="SMP">SMP</option>
                                <option value="SMA">SMA</option>
                                <option value="SMK">SMK</option>
                            </select>
                            <p
                                v-if="form.errors.level"
                                class="mt-1.5 text-sm text-danger"
                            >
                                {{ form.errors.level }}
                            </p>
                        </div>
                    </div>

                    <TextInput
                        v-model="form.name"
                        label="Nama Sekolah"
                        :error="form.errors.name"
                    />

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-ink"
                            >Alamat</label
                        >
                        <textarea
                            v-model="form.address"
                            rows="3"
                            class="w-full rounded-md border-border bg-surface text-sm text-ink placeholder:text-muted/60 focus:border-brand focus:ring-brand/20"
                            placeholder="Alamat lengkap sekolah"
                        ></textarea>
                        <p
                            v-if="form.errors.address"
                            class="mt-1.5 text-sm text-danger"
                        >
                            {{ form.errors.address }}
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label
                                class="mb-1.5 block text-sm font-medium text-ink"
                                >Akreditasi</label
                            >
                            <select
                                v-model="form.accreditation"
                                class="w-full rounded-md border-border bg-surface text-sm text-ink focus:border-brand focus:ring-brand/20"
                            >
                                <option value="">Pilih akreditasi</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                            </select>
                        </div>
                        <TextInput
                            v-model="form.principal_nip"
                            label="NIP Kepala Sekolah"
                            placeholder="Opsional"
                            :error="form.errors.principal_nip"
                        />
                    </div>

                    <TextInput
                        v-model="form.principal_name"
                        label="Nama Kepala Sekolah"
                        placeholder="Opsional"
                        :error="form.errors.principal_name"
                    />

                    <div class="flex justify-end border-t border-border pt-5">
                        <Button
                            type="submit"
                            variant="primary"
                            :disabled="form.processing"
                        >
                            <template #icon
                                ><IconDeviceFloppy :size="16" stroke="1.75"
                            /></template>
                            {{
                                form.processing
                                    ? "Menyimpan..."
                                    : "Simpan Perubahan"
                            }}
                        </Button>
                    </div>
                </form>
            </div>
        </div>

        <AlertModal />
    </AdminLayout>
</template>