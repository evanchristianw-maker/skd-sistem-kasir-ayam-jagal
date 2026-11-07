<script setup>
import { router } from "@inertiajs/vue3";

const pageTitle = "Transaksi Penjualan";

const logout = () => {
    router.post(route("logout"));
};
</script>

<template>
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    {{ pageTitle }}
                </h1>

                <p class="mt-2 text-gray-500">
                    Halaman {{ pageTitle }} UMKM Ayam Jagal.
                </p>
            </div>

            <button
                @click="logout"
                class="rounded-lg bg-red-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-700"
            >
                Logout
            </button>
        </div>
    </div>
</template>