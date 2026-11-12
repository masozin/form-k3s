<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import AuthLayout from "@/Layouts/AuthLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import Button from "@/Components/Button.vue";

import { Head, Link, useForm } from "@inertiajs/vue3";

import { IconArrowRight, IconLock, IconMail } from "@tabler/icons-vue";

defineProps({
    canResetPassword: {
        type: Boolean,
    },

    status: {
        type: String,
    },
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <AuthLayout>
        <Head title="Masuk" />

        <!-- Header -->
        <div>
            <div
                class="mb-5 flex h-11 w-11 items-center justify-center rounded-2xl bg-brand/10"
            >
                <IconLock :size="21" stroke="1.7" class="text-brand" />
            </div>

            <h2
                class="font-display text-2xl font-bold tracking-tight text-ink dark:text-ink-dark sm:text-3xl"
            >
                Selamat datang kembali
            </h2>

            <p class="mt-2 text-sm leading-6 text-muted dark:text-muted-dark">
                Masuk untuk melanjutkan pengelolaan data sekolah.
            </p>
        </div>

        <!-- Status -->
        <div
            v-if="status"
            class="mt-6 rounded-xl border border-brand/20 bg-brand/5 px-4 py-3 text-sm text-brand dark:bg-brand/10"
        >
            {{ status }}
        </div>

        <!-- Form -->
        <form class="mt-8 space-y-5" @submit.prevent="submit">
            <!-- Email -->
            <div>
                <InputLabel for="email" value="Alamat Email" />

                <div class="relative mt-1.5">
                    <IconMail
                        :size="18"
                        stroke="1.6"
                        class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-muted/50 dark:text-muted-dark/50"
                    />

                    <TextInput
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="block w-full pl-10"
                        placeholder="nama@sekolah.sch.id"
                        required
                        autofocus
                        autocomplete="username"
                    />
                </div>

                <InputError
                    class="mt-1.5 text-sienna"
                    :message="form.errors.email"
                />
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between">
                    <InputLabel for="password" value="Kata Sandi" />

                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-xs font-medium text-brand transition hover:text-brand-dark"
                    >
                        Lupa kata sandi?
                    </Link>
                </div>

                <div class="relative mt-1.5">
                    <IconLock
                        :size="18"
                        stroke="1.6"
                        class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-muted/50 dark:text-muted-dark/50"
                    />

                    <TextInput
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="block w-full pl-10"
                        placeholder="Masukkan kata sandi"
                        required
                        autocomplete="current-password"
                    />
                </div>

                <InputError
                    class="mt-1.5 text-sienna"
                    :message="form.errors.password"
                />
            </div>

            <!-- Remember -->
            <label class="flex cursor-pointer items-center gap-2.5">
                <Checkbox name="remember" v-model:checked="form.remember" />

                <span class="text-sm text-muted dark:text-muted-dark">
                    Ingat saya
                </span>
            </label>

            <!-- Submit -->
            <Button
                variant="primary"
                type="submit"
                class="w-full justify-center"
                :disabled="form.processing"
            >
                {{ form.processing ? "Sedang masuk..." : "Masuk ke Portal" }}

                <template #icon>
                    <IconArrowRight :size="17" stroke="1.8" />
                </template>
            </Button>
        </form>

        <!-- Register -->
        <div class="mt-8 border-t border-border pt-6 dark:border-border-dark">
            <p class="text-center text-sm text-muted dark:text-muted-dark">
                Belum memiliki akun?

                <Link
                    :href="route('register')"
                    class="ml-1 font-semibold text-brand transition hover:text-brand-dark"
                >
                    Daftar sebagai Operator
                </Link>
            </p>
        </div>
    </AuthLayout>
</template>
