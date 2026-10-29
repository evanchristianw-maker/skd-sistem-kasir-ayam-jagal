<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="relative flex min-h-screen items-center justify-center overflow-hidden bg-slate-50 px-4 py-10">
        <!-- Background Decoration -->
        <div class="absolute -left-24 -top-24 h-72 w-72 rounded-full bg-red-100/70 blur-3xl"></div>
        <div class="absolute -bottom-24 -right-24 h-80 w-80 rounded-full bg-orange-100/70 blur-3xl"></div>

        <div class="relative z-10 w-full max-w-md">
            <!-- Brand -->
            <Link href="/" class="mb-8 flex flex-col items-center text-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-red-600 text-white shadow-lg shadow-red-200">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        class="h-9 w-9"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 3v2m0 14v2M5.64 5.64l1.42 1.42m9.88 9.88 1.42 1.42M3 12h2m14 0h2M5.64 18.36l1.42-1.42m9.88-9.88 1.42-1.42"
                        />
                        <circle cx="12" cy="12" r="6" />
                    </svg>
                </div>

                <h1 class="mt-4 text-2xl font-extrabold tracking-tight text-slate-900">
                    Ayam <span class="text-red-600">Jagal</span>
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Sistem Kasir Berbasis Website
                </p>
            </Link>

            <!-- Content -->
            <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-xl shadow-slate-200/60 sm:p-8">
                <slot />
            </div>

            <!-- Footer -->
            <p class="mt-6 text-center text-xs text-slate-400">
                © {{ new Date().getFullYear() }} UMKM Ayam Jagal
            </p>
        </div>
    </div>
</template>
