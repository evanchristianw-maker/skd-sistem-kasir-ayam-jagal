<script setup>
import AppNavbar from "@/Components/AppNavbar.vue";

defineProps({
    /**
     * Menentukan item navigasi yang aktif.
     * Nilai yang valid: 'dashboard' | 'menu' | 'penjualan' | 'keuangan'
     */
    activePage: {
        type: String,
        default: "",
    },
});
</script>

<template>
    <div class="admin-layout">
        <!-- Shared header + bottom nav -->
        <AppNavbar :active-page="activePage" />

        <!-- Page content -->
        <main class="layout-main">
            <slot />
        </main>
    </div>
</template>

<style scoped>
* {
    box-sizing: border-box;
}

.admin-layout {
    --primary: #ca0708;
    --primary-dark: #a80000;
    --text-primary: #202124;
    --text-secondary: #626875;
    --background: #f5f6f8;

    width: 100%;
    min-height: 100vh;
    min-height: 100dvh;
    margin: 0 auto;
    /* Ruang bawah untuk bottom navigation (84px) */
    padding-bottom: 100px;
    overflow-x: hidden;
    color: var(--text-primary);
    background: var(--background);
    font-family: Inter, "Segoe UI", Arial, sans-serif;
}

.layout-main {
    width: 100%;
}

@media (max-width: 600px) {
    .admin-layout {
        padding-bottom: 100px;
    }
}
</style>

