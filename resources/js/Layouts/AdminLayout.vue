<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import { routeExists, safeRoute } from "@/utils/safeRoute";

import {
    IconSchool,
    IconFileText,
    IconChartBar,
    IconSettings,
    IconChevronDown,
    IconMenu2,
    IconX,
    IconLogout,
    IconSun,
    IconMoon,
    IconUser,
    IconLayoutDashboard,
} from "@tabler/icons-vue";

const page = usePage();
const user = page.props.auth.user;

const mobileOpen = ref(false);
const profileOpen = ref(false);
const isDark = ref(false);
const isScrolled = ref(false);

/*
|--------------------------------------------------------------------------
| Navigation
|--------------------------------------------------------------------------
*/

const nav = computed(() =>
    [
        {
            label: "Dashboard",
            name: "dashboard",
            icon: IconLayoutDashboard,
        },
        {
            label: "Sekolah",
            name: "schools.index",
            icon: IconSchool,
        },
        {
            label: "Form",
            name: "forms.index",
            icon: IconFileText,
        },
        {
            label: "Laporan",
            name: "reports.index",
            icon: IconChartBar,
        },
    ]
        .filter((item) => routeExists(item.name))
        .map((item) => ({
            ...item,
            href: safeRoute(item.name),
            active:
                item.name === "dashboard"
                    ? route().current("dashboard")
                    : route().current(item.name.replace(".index", ".*")),
        })),
);

const roleLabel = computed(() => {
    const labels = {
        admin: "Administrator",
        operator: "Operator Sekolah",
        guru: "Guru",
        user: "Pengguna",
    };
    return labels[user.role] ?? user.role;
});

const settingsExists = computed(() => routeExists("settings.index"));
const profileExists = computed(() => routeExists("profile.edit"));

/*
|--------------------------------------------------------------------------
| Theme
|--------------------------------------------------------------------------
*/

const applyTheme = (dark) => {
    isDark.value = dark;

    if (dark) {
        document.documentElement.classList.add("dark");
        localStorage.setItem("theme", "dark");
    } else {
        document.documentElement.classList.remove("dark");
        localStorage.setItem("theme", "light");
    }
};

const toggleTheme = () => {
    applyTheme(!isDark.value);
};

/*
|--------------------------------------------------------------------------
| Scroll
|--------------------------------------------------------------------------
*/

const handleScroll = () => {
    isScrolled.value = window.scrollY > 10;
};

/*
|--------------------------------------------------------------------------
| Close dropdown
|--------------------------------------------------------------------------
*/

const handleClickOutside = (event) => {
    if (!event.target.closest(".profile-menu")) {
        profileOpen.value = false;
    }
};

onMounted(() => {
    const saved = localStorage.getItem("theme");

    const prefersDark = window.matchMedia(
        "(prefers-color-scheme: dark)",
    ).matches;

    applyTheme(saved === "dark" || (!saved && prefersDark));

    window.addEventListener("scroll", handleScroll, {
        passive: true,
    });

    document.addEventListener("click", handleClickOutside);

    handleScroll();
});

onUnmounted(() => {
    window.removeEventListener("scroll", handleScroll);
    document.removeEventListener("click", handleClickOutside);
});
</script>

<template>
    <div
        class="min-h-screen bg-[#f7f8fc] font-sans text-slate-900 antialiased transition-colors duration-300 dark:bg-[#09090b] dark:text-white"
    >
        <!-- ========================================================= -->
        <!-- BACKGROUND -->
        <!-- ========================================================= -->

        <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
            <!-- Main glow -->
            <div
                class="absolute left-1/2 top-[-180px] h-[420px] w-[700px] -translate-x-1/2 rounded-full bg-indigo-500/[0.07] blur-[120px] dark:bg-indigo-500/[0.10]"
            ></div>

            <!-- Secondary glow -->
            <div
                class="absolute right-[-150px] top-[30%] h-[350px] w-[350px] rounded-full bg-violet-500/[0.05] blur-[110px] dark:bg-violet-500/[0.06]"
            ></div>

            <!-- Grid -->
            <div
                class="absolute inset-0 opacity-[0.35] dark:opacity-[0.12]"
                style="
                    background-image:
                        linear-gradient(
                            rgba(100, 116, 139, 0.06) 1px,
                            transparent 1px
                        ),
                        linear-gradient(
                            90deg,
                            rgba(100, 116, 139, 0.06) 1px,
                            transparent 1px
                        );
                    background-size: 48px 48px;
                "
            ></div>
        </div>

        <!-- ========================================================= -->
        <!-- TOP NAVIGATION -->
        <!-- ========================================================= -->

        <header
            class="sticky top-0 z-50 border-b transition-all duration-300"
            :class="
                isScrolled
                    ? 'border-slate-200/70 bg-white/80 shadow-[0_8px_30px_rgba(15,23,42,0.06)] backdrop-blur-2xl dark:border-zinc-800/70 dark:bg-zinc-950/80 dark:shadow-black/20'
                    : 'border-transparent bg-transparent'
            "
        >
            <div
                class="mx-auto flex h-[68px] max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8"
            >
                <!-- ================================================= -->
                <!-- BRAND -->
                <!-- ================================================= -->

                <Link href="/" class="group flex shrink-0 items-center gap-3">
                    <!-- Logo -->
                    <div
                        class="relative flex h-10 w-10 items-center justify-center overflow-hidden rounded-[13px] bg-gradient-to-br from-indigo-500 via-indigo-600 to-violet-600 text-white shadow-[0_8px_25px_rgba(79,70,229,0.28)] transition-all duration-300 group-hover:-translate-y-0.5 group-hover:shadow-[0_12px_30px_rgba(79,70,229,0.35)]"
                    >
                        <div class="absolute inset-0 bg-white/10"></div>

                        <IconSchool :size="20" stroke="1.8" class="relative" />
                    </div>

                    <!-- Brand text -->
                    <div class="hidden sm:block">
                        <div
                            class="text-[15px] font-bold tracking-[-0.02em] text-slate-900 dark:text-white"
                        >
                            K3S Portal
                        </div>

                        <div
                            class="text-[10px] font-medium uppercase tracking-[0.16em] text-slate-400 dark:text-zinc-500"
                        >
                            Management System
                        </div>
                    </div>
                </Link>

                <!-- ================================================= -->
                <!-- DESKTOP NAVIGATION -->
                <!-- ================================================= -->

                <nav
                    class="absolute left-1/2 hidden -translate-x-1/2 items-center rounded-2xl border border-slate-200/70 bg-white/60 p-1 shadow-sm backdrop-blur-xl md:flex dark:border-zinc-800/80 dark:bg-zinc-900/60"
                >
                    <Link
                        v-for="item in nav"
                        :key="item.label"
                        :href="item.href"
                        class="group relative flex items-center gap-2 rounded-xl px-4 py-2.5 text-[13px] font-semibold transition-all duration-200"
                        :class="
                            item.active
                                ? 'bg-slate-900 text-white shadow-sm dark:bg-white dark:text-slate-900'
                                : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white'
                        "
                    >
                        <component
                            :is="item.icon"
                            :size="16"
                            :stroke="item.active ? 2 : 1.7"
                        />

                        <span>
                            {{ item.label }}
                        </span>
                    </Link>
                </nav>

                <!-- ================================================= -->
                <!-- RIGHT ACTIONS -->
                <!-- ================================================= -->

                <div class="flex items-center gap-1.5">
                    <!-- Settings -->
                    <Link
                        v-if="settingsExists"
                        :href="safeRoute('settings.index')"
                        class="hidden h-9 w-9 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 md:flex dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white"
                        title="Pengaturan"
                    >
                        <IconSettings :size="18" stroke="1.7" />
                    </Link>

                    <!-- Theme -->
                    <button
                        type="button"
                        @click="toggleTheme"
                        class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white"
                        :title="
                            isDark
                                ? 'Gunakan mode terang'
                                : 'Gunakan mode gelap'
                        "
                    >
                        <IconSun v-if="isDark" :size="18" stroke="1.7" />

                        <IconMoon v-else :size="18" stroke="1.7" />
                    </button>

                    <!-- Divider -->
                    <div
                        class="mx-2 hidden h-6 w-px bg-slate-200 dark:bg-zinc-800 sm:block"
                    ></div>

                    <!-- ================================================= -->
                    <!-- PROFILE -->
                    <!-- ================================================= -->

                    <div class="profile-menu relative">
                        <button
                            type="button"
                            @click.stop="profileOpen = !profileOpen"
                            class="group flex items-center gap-2 rounded-xl p-1 transition hover:bg-slate-100 dark:hover:bg-zinc-800"
                        >
                            <!-- Avatar -->
                            <div
                                class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500/15 to-violet-500/20 text-sm font-bold text-indigo-600 ring-1 ring-indigo-500/10 dark:text-indigo-400 dark:ring-indigo-400/10"
                            >
                                {{ user.name.charAt(0).toUpperCase() }}
                            </div>

                            <!-- User -->
                            <div
                                class="hidden max-w-[130px] flex-col items-start lg:flex"
                            >
                                <span
                                    class="w-full truncate text-[12px] font-bold text-slate-800 dark:text-zinc-100"
                                >
                                    {{ user.name }}
                                </span>

                                <span
                                    class="w-full truncate text-[10px] text-slate-400 dark:text-zinc-500"
                                >
                                    {{ roleLabel }}
                                </span>
                            </div>

                            <IconChevronDown
                                :size="14"
                                class="mr-1 text-slate-400 transition-transform duration-200 dark:text-zinc-500"
                                :class="{
                                    'rotate-180': profileOpen,
                                }"
                            />
                        </button>

                        <!-- ================================================= -->
                        <!-- PROFILE DROPDOWN -->
                        <!-- ================================================= -->

                        <Transition
                            enter-active-class="transition duration-200 ease-out"
                            leave-active-class="transition duration-150 ease-in"
                            enter-from-class="translate-y-2 scale-95 opacity-0"
                            leave-to-class="translate-y-2 scale-95 opacity-0"
                        >
                            <div
                                v-if="profileOpen"
                                class="absolute right-0 mt-2 w-64 origin-top-right overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-[0_20px_50px_rgba(15,23,42,0.12)] dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-black/40"
                            >
                                <!-- User header -->
                                <div
                                    class="border-b border-slate-100 px-4 py-4 dark:border-zinc-800"
                                >
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-sm font-bold text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400"
                                        >
                                            {{
                                                user.name
                                                    .charAt(0)
                                                    .toUpperCase()
                                            }}
                                        </div>

                                        <div class="min-w-0">
                                            <p
                                                class="truncate text-sm font-bold text-slate-900 dark:text-white"
                                            >
                                                {{ user.name }}
                                            </p>

                                            <p
                                                class="truncate text-xs text-slate-400 dark:text-zinc-500"
                                            >
                                                {{ user.email }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Menu -->
                                <div class="p-1.5">
                                    <Link
                                        v-if="profileExists"
                                        :href="safeRoute('profile.edit')"
                                        class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50 hover:text-slate-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white"
                                        @click="profileOpen = false"
                                    >
                                        <IconUser :size="17" stroke="1.7" />

                                        Profil Akun
                                    </Link>

                                    <Link
                                        :href="safeRoute('logout')"
                                        method="post"
                                        as="button"
                                        class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-red-500 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10"
                                    >
                                        <IconLogout :size="17" stroke="1.7" />

                                        Keluar
                                    </Link>
                                </div>
                            </div>
                        </Transition>
                    </div>

                    <!-- Mobile -->
                    <button
                        type="button"
                        @click="mobileOpen = true"
                        class="ml-1 flex h-9 w-9 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 md:hidden dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white"
                    >
                        <IconMenu2 :size="20" stroke="1.7" />
                    </button>
                </div>
            </div>
        </header>

        <!-- ========================================================= -->
        <!-- MOBILE OVERLAY -->
        <!-- ========================================================= -->

        <Transition
            enter-active-class="transition-opacity duration-300"
            leave-active-class="transition-opacity duration-200"
            enter-from-class="opacity-0"
            leave-to-class="opacity-0"
        >
            <div
                v-if="mobileOpen"
                class="fixed inset-0 z-[60] bg-slate-950/40 backdrop-blur-sm md:hidden"
                @click="mobileOpen = false"
            ></div>
        </Transition>

        <!-- ========================================================= -->
        <!-- MOBILE DRAWER -->
        <!-- ========================================================= -->

        <Transition
            enter-active-class="transition-transform duration-300 ease-out"
            leave-active-class="transition-transform duration-200 ease-in"
            enter-from-class="translate-x-full"
            leave-to-class="translate-x-full"
        >
            <aside
                v-if="mobileOpen"
                class="fixed inset-y-0 right-0 z-[70] flex w-[300px] max-w-[85vw] flex-col border-l border-slate-200 bg-white shadow-2xl dark:border-zinc-800 dark:bg-zinc-950 md:hidden"
            >
                <!-- Drawer header -->
                <div
                    class="flex h-[68px] items-center justify-between border-b border-slate-100 px-5 dark:border-zinc-800"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-white"
                        >
                            <IconSchool :size="18" stroke="1.7" />
                        </div>

                        <span class="font-bold text-slate-900 dark:text-white">
                            K3S Portal
                        </span>
                    </div>

                    <button
                        type="button"
                        @click="mobileOpen = false"
                        class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-500 hover:bg-slate-100 dark:text-zinc-400 dark:hover:bg-zinc-800"
                    >
                        <IconX :size="20" />
                    </button>
                </div>

                <!-- Mobile navigation -->
                <nav class="flex-1 space-y-1 p-4">
                    <div
                        class="mb-3 px-3 text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400 dark:text-zinc-600"
                    >
                        Menu Utama
                    </div>

                    <Link
                        v-for="item in nav"
                        :key="item.label"
                        :href="item.href"
                        class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition"
                        :class="
                            item.active
                                ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900'
                                : 'text-slate-600 hover:bg-slate-100 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white'
                        "
                        @click="mobileOpen = false"
                    >
                        <component
                            :is="item.icon"
                            :size="19"
                            :stroke="item.active ? 2 : 1.7"
                        />

                        {{ item.label }}
                    </Link>

                    <div class="my-4 h-px bg-slate-100 dark:bg-zinc-800"></div>

                    <Link
                        v-if="settingsExists"
                        :href="safeRoute('settings.index')"
                        class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-100 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white"
                        @click="mobileOpen = false"
                    >
                        <IconSettings :size="19" stroke="1.7" />

                        Pengaturan
                    </Link>
                </nav>

                <!-- Mobile user -->
                <div class="border-t border-slate-100 p-4 dark:border-zinc-800">
                    <div
                        class="flex items-center gap-3 rounded-xl bg-slate-50 p-3 dark:bg-zinc-900"
                    >
                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-500/10 font-bold text-indigo-600 dark:text-indigo-400"
                        >
                            {{ user.name.charAt(0).toUpperCase() }}
                        </div>

                        <div class="min-w-0">
                            <p
                                class="truncate text-sm font-bold text-slate-800 dark:text-white"
                            >
                                {{ user.name }}
                            </p>

                            <p
                                class="truncate text-xs text-slate-400 dark:text-zinc-500"
                            >
                                {{ user.email }}
                            </p>
                        </div>
                    </div>
                </div>
            </aside>
        </Transition>

        <!-- ========================================================= -->
        <!-- PAGE CONTENT -->
        <!-- ========================================================= -->

        <main
            class="mx-auto min-h-[calc(100vh-68px)] max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-10"
        >
            <!-- Page header -->
            <div v-if="$slots.header" class="mb-8">
                <slot name="header" />
            </div>

            <!-- Content -->
            <slot />
        </main>
    </div>
</template>
