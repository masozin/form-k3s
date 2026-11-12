<!-- resources/js/Pages/Auth/Register.vue -->
<script setup>
import { ref, computed, watch } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

import AuthLayout from "@/Layouts/AuthLayout.vue";
import TextInput from "@/Components/TextInput.vue";
import Button from "@/Components/Button.vue";
import RegisterStepIndicator from "@/Components/RegisterStepIndicator.vue";

import {
    IconUserPlus,
    IconArrowRight,
    IconArrowLeft,
    IconCheck,
} from "@tabler/icons-vue";

const step = ref(1);

const form = useForm({
    school_npsn: "",
    school_name: "",
    school_address: "",
    school_level: "",
    school_accreditation: "",
    principal_name: "",
    principal_nip: "",

    name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

/*
|--------------------------------------------------------------------------
| Jika backend mengembalikan error sekolah, otomatis kembali ke Step 1.
|--------------------------------------------------------------------------
*/
watch(
    () => form.errors,
    (errors) => {
        const step1Fields = [
            "school_npsn",
            "school_name",
            "school_address",
            "school_level",
            "school_accreditation",
            "principal_name",
            "principal_nip",
        ];

        if (step1Fields.some((field) => errors[field])) {
            step.value = 1;
        }
    },
    { deep: true },
);

/*
|--------------------------------------------------------------------------
| Validasi sederhana Step 1
|--------------------------------------------------------------------------
*/
const step1Valid = computed(() => {
    return (
        form.school_npsn.trim() && form.school_name.trim() && form.school_level
    );
});

/*
|--------------------------------------------------------------------------
| Step navigation
|--------------------------------------------------------------------------
*/
function goToStep2() {
    step.value = 2;
}

function goToStep1() {
    step.value = 1;
}

/*
|--------------------------------------------------------------------------
| Submit
|--------------------------------------------------------------------------
*/
function submit() {
    form.post(route("register"), {
        onFinish: () => {
            form.reset("password", "password_confirmation");
        },
    });
}
</script>

<template>
    <Head title="Daftar" />

    <AuthLayout>
        <div class="w-full max-w-md lg:max-w-lg">
            <!-- HEADER -->
            <div>
                <div
                    class="mb-5 flex h-11 w-11 items-center justify-center rounded-2xl bg-brand/10"
                >
                    <IconUserPlus :size="21" stroke="1.7" class="text-brand" />
                </div>

                <h2
                    class="font-display text-2xl font-bold tracking-tight text-ink dark:text-ink-dark sm:text-3xl"
                >
                    Daftar sebagai Operator
                </h2>

                <p
                    class="mt-2 max-w-md text-sm leading-6 text-muted dark:text-muted-dark"
                >
                    Buat akun operator untuk mengelola dan mengirimkan data
                    sekolah melalui portal K3S.
                </p>
            </div>

            <!-- STEP INDICATOR -->
            <div class="mt-7">
                <RegisterStepIndicator :current-step="step" />
            </div>

            <!-- STEP 1: Detail Sekolah -->
            <div v-show="step === 1" class="mt-8 space-y-6">
                <div class="space-y-5">
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <TextInput
                            v-model="form.school_npsn"
                            label="NPSN"
                            placeholder="Contoh: 12345678"
                            :error="form.errors.school_npsn"
                        />

                        <div>
                            <label
                                class="mb-1.5 block text-sm font-medium text-ink dark:text-ink-dark"
                            >
                                Jenjang
                            </label>

                            <select
                                v-model="form.school_level"
                                class="w-full rounded-xl border border-border bg-surface px-3.5 py-2.5 text-sm text-ink outline-none transition focus:border-brand focus:ring-2 focus:ring-brand/10 dark:border-border-dark dark:bg-surface-dark dark:text-ink-dark"
                                :class="{
                                    'border-sienna': form.errors.school_level,
                                }"
                            >
                                <option value="">Pilih jenjang</option>
                                <option value="SD">SD</option>
                                <option value="SMP">SMP</option>
                                <option value="SMA">SMA</option>
                                <option value="SMK">SMK</option>
                            </select>

                            <p
                                v-if="form.errors.school_level"
                                class="mt-1.5 text-xs text-sienna"
                            >
                                {{ form.errors.school_level }}
                            </p>
                        </div>
                    </div>

                    <TextInput
                        v-model="form.school_name"
                        label="Nama Sekolah"
                        placeholder="Contoh: SD Negeri 01 Toto Rejo"
                        :error="form.errors.school_name"
                    />

                    <div>
                        <label
                            class="mb-1.5 block text-sm font-medium text-ink dark:text-ink-dark"
                        >
                            Alamat Sekolah
                        </label>

                        <textarea
                            v-model="form.school_address"
                            rows="3"
                            class="w-full resize-none rounded-xl border border-border bg-surface px-3.5 py-2.5 text-sm text-ink outline-none transition placeholder:text-muted/50 focus:border-brand focus:ring-2 focus:ring-brand/10 dark:border-border-dark dark:bg-surface-dark dark:text-ink-dark dark:placeholder:text-muted-dark/50"
                            :class="{
                                'border-sienna': form.errors.school_address,
                            }"
                            placeholder="Alamat lengkap sekolah"
                        ></textarea>

                        <p
                            v-if="form.errors.school_address"
                            class="mt-1.5 text-xs text-sienna"
                        >
                            {{ form.errors.school_address }}
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label
                                class="mb-1.5 block text-sm font-medium text-ink dark:text-ink-dark"
                            >
                                Akreditasi
                            </label>

                            <select
                                v-model="form.school_accreditation"
                                class="w-full rounded-xl border border-border bg-surface px-3.5 py-2.5 text-sm text-ink outline-none transition focus:border-brand focus:ring-2 focus:ring-brand/10 dark:border-border-dark dark:bg-surface-dark dark:text-ink-dark"
                                :class="{
                                    'border-sienna':
                                        form.errors.school_accreditation,
                                }"
                            >
                                <option value="">Pilih akreditasi</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                            </select>

                            <p
                                v-if="form.errors.school_accreditation"
                                class="mt-1.5 text-xs text-sienna"
                            >
                                {{ form.errors.school_accreditation }}
                            </p>
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
                        placeholder="Nama lengkap kepala sekolah"
                        :error="form.errors.principal_name"
                    />
                </div>

                <Button
                    variant="primary"
                    type="button"
                    class="w-full justify-center"
                    :disabled="!step1Valid"
                    @click="goToStep2"
                >
                    Lanjut ke Detail Akun
                    <template #icon>
                        <IconArrowRight :size="17" stroke="1.8" />
                    </template>
                </Button>
            </div>

            <!-- STEP 2: Detail Akun -->
            <form
                v-show="step === 2"
                class="mt-8 space-y-5"
                @submit.prevent="submit"
            >
                <div
                    class="mb-6 rounded-2xl border border-brand/15 bg-brand/5 p-4 dark:border-brand/20 dark:bg-brand/10"
                >
                    <div class="flex items-start gap-3">
                        <div
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand/10"
                        >
                            <IconCheck
                                :size="16"
                                stroke="2"
                                class="text-brand"
                            />
                        </div>
                        <div>
                            <p
                                class="text-sm font-semibold text-ink dark:text-ink-dark"
                            >
                                Data sekolah selesai
                            </p>
                            <p
                                class="mt-0.5 text-xs leading-5 text-muted dark:text-muted-dark"
                            >
                                Sekarang buat akun yang akan digunakan untuk
                                masuk ke portal.
                            </p>
                        </div>
                    </div>
                </div>

                <TextInput
                    v-model="form.name"
                    label="Nama Lengkap"
                    placeholder="Nama operator"
                    :error="form.errors.name"
                />
                <TextInput
                    v-model="form.email"
                    type="email"
                    label="Alamat Email"
                    placeholder="nama@sekolah.sch.id"
                    :error="form.errors.email"
                />
                <TextInput
                    v-model="form.password"
                    type="password"
                    label="Kata Sandi"
                    placeholder="Buat kata sandi"
                    :error="form.errors.password"
                />
                <TextInput
                    v-model="form.password_confirmation"
                    type="password"
                    label="Konfirmasi Kata Sandi"
                    placeholder="Ulangi kata sandi"
                    :error="form.errors.password_confirmation"
                />

                <div class="flex gap-3 pt-3">
                    <Button
                        variant="secondary"
                        type="button"
                        class="shrink-0"
                        @click="goToStep1"
                    >
                        <template #icon
                            ><IconArrowLeft :size="17" stroke="1.8"
                        /></template>
                        Kembali
                    </Button>

                    <Button
                        variant="primary"
                        type="submit"
                        class="flex-1 justify-center"
                        :disabled="form.processing"
                    >
                        <template #icon
                            ><IconUserPlus :size="17" stroke="1.8"
                        /></template>
                        {{
                            form.processing
                                ? "Mendaftarkan..."
                                : "Daftar Sekarang"
                        }}
                    </Button>
                </div>
            </form>

            <div
                class="mt-8 border-t border-border pt-6 text-center dark:border-border-dark"
            >
                <p class="text-sm text-muted dark:text-muted-dark">
                    Sudah memiliki akun?
                    <Link
                        :href="route('login')"
                        class="ml-1 font-semibold text-brand transition hover:text-brand-dark"
                    >
                        Masuk di sini
                    </Link>
                </p>
            </div>
        </div>
    </AuthLayout>
</template>
