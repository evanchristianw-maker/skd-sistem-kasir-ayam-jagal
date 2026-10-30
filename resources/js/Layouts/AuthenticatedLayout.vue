<script setup>
// Mengimpor ref untuk mengatur kondisi dropdown
import { ref } from "vue";

// Mengimpor Link dan router dari Inertia.js
import { Link, router } from "@inertiajs/vue3";

// Mengatur judul dan halaman aktif berdasarkan halaman yang menggunakan layout
const props = defineProps({
    // Judul yang ditampilkan pada header
    title: {
        type: String,
        default: "Dashboard",
    },

    // Menentukan navigasi yang sedang aktif
    activePage: {
        type: String,
        default: "dashboard",
    },
});

// Mengatur kondisi dropdown profil pengguna
const showingProfileMenu = ref(false);

// Mengatur fungsi tombol kembali
const goBack = () => {
    // Memeriksa apakah pengguna memiliki riwayat halaman sebelumnya
    if (window.history.length > 1) {
        window.history.back();
    } else {
        // Jika tidak ada riwayat, kembali ke Dashboard
        router.visit(route("dashboard"));
    }
};

// Mengatur fungsi logout pengguna
const logout = () => {
    router.post(route("logout"));
};
</script>

<template>
    <div class="app-layout">
        <!-- Header utama aplikasi -->
        <header class="app-header">
            <!-- Bagian kiri header -->
            <div class="header-left">
                <!-- Tombol kembali ditampilkan selain di Dashboard -->
                <button
                    v-if="props.activePage !== 'dashboard'"
                    type="button"
                    class="back-button"
                    @click="goBack"
                    aria-label="Kembali"
                >
                    <svg viewBox="0 0 24 24" fill="none">
                        <path
                            d="M19 12H5M12 19L5 12L12 5"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>
                </button>

                <!-- Menampilkan judul halaman secara dinamis -->
                <h1>{{ props.title }}</h1>
            </div>

            <!-- Bagian kanan header -->
            <div class="header-actions">
                <!-- Tombol notifikasi -->
                <button
                    type="button"
                    class="header-icon"
                    aria-label="Notifikasi"
                >
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M12 22C13.1 22 14 21.1 14 20H10C10 21.1 10.9 22 12 22ZM18 16V11C18 7.93 16.37 5.36 13.5 4.68V4C13.5 3.17 12.83 2.5 12 2.5S10.5 3.17 10.5 4V4.68C7.63 5.36 6 7.92 6 11V16L4 18V19H20V18L18 16Z"
                        />
                    </svg>

                    <!-- Titik indikator notifikasi -->
                    <span class="notification-dot"></span>
                </button>

                <!-- Bagian profil pengguna -->
                <div class="profile-wrapper">
                    <!-- Tombol untuk membuka dropdown profil -->
                    <button
                        type="button"
                        class="header-icon"
                        @click="showingProfileMenu = !showingProfileMenu"
                        :aria-expanded="showingProfileMenu"
                        aria-label="Profil pengguna"
                    >
                        <svg viewBox="0 0 24 24" fill="none">
                            <circle
                                cx="12"
                                cy="12"
                                r="9"
                                stroke="currentColor"
                                stroke-width="1.8"
                            />

                            <circle
                                cx="12"
                                cy="9"
                                r="3"
                                stroke="currentColor"
                                stroke-width="1.8"
                            />

                            <path
                                d="M6.5 18C7.5 15.8 9.5 14.5 12 14.5C14.5 14.5 16.5 15.8 17.5 18"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            />
                        </svg>
                    </button>

                    <!-- Dropdown menu profil -->
                    <div v-if="showingProfileMenu" class="profile-dropdown">
                        <!-- Menu menuju halaman profil -->
                        <Link
                            :href="route('profile.edit')"
                            class="dropdown-item"
                        >
                            Profil Saya
                        </Link>

                        <!-- Tombol keluar dari aplikasi -->
                        <button
                            type="button"
                            class="dropdown-item logout-item"
                            @click="logout"
                        >
                            Keluar
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Konten utama setiap halaman -->
        <main class="app-main">
            <!-- Slot untuk menampilkan konten Dashboard, Menu, Laporan, dan lainnya -->
            <slot />
        </main>

        <!-- Navigasi utama bagian bawah -->
        <nav class="bottom-navigation">
            <!-- Navigasi Dashboard -->
            <Link
                :href="route('dashboard')"
                class="nav-item"
                :class="{ active: props.activePage === 'dashboard' }"
            >
                <svg viewBox="0 0 24 24" fill="none">
                    <path
                        d="M3 10L12 3L21 10V20C21 20.55 20.55 21 20 21H4C3.45 21 3 20.55 3 20V10Z"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linejoin="round"
                    />

                    <path
                        d="M9 21V13H15V21"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linejoin="round"
                    />
                </svg>

                <span>Dashboard</span>
            </Link>

            <!-- Navigasi Kelola Menu -->
            <!-- Sementara dinonaktifkan karena route belum dibuat -->
            <button type="button" class="nav-item" disabled>
                <svg viewBox="0 0 24 24" fill="none">
                    <rect
                        x="4"
                        y="4"
                        width="16"
                        height="16"
                        rx="2"
                        stroke="currentColor"
                        stroke-width="1.8"
                    />

                    <path
                        d="M4 10H20M4 15H20M10 4V20M15 4V20"
                        stroke="currentColor"
                        stroke-width="1.5"
                    />
                </svg>

                <span>Menu</span>
            </button>

            <!-- Navigasi Penjualan -->
            <!-- Sementara dinonaktifkan karena route belum dibuat -->
            <button type="button" class="nav-item" disabled>
                <svg viewBox="0 0 24 24" fill="none">
                    <path
                        d="M12 3V12H21"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                    />

                    <path
                        d="M19.5 15A8.5 8.5 0 1 1 9 3.5"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                    />
                </svg>

                <span>Penjualan</span>
            </button>

            <!-- Navigasi Keuangan -->
            <!-- Sementara dinonaktifkan karena route belum dibuat -->
            <button type="button" class="nav-item" disabled>
                <svg viewBox="0 0 24 24" fill="none">
                    <rect
                        x="3"
                        y="5"
                        width="18"
                        height="14"
                        rx="2"
                        stroke="currentColor"
                        stroke-width="1.8"
                    />

                    <path
                        d="M7 15H11M7 11H17"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                    />
                </svg>

                <span>Keuangan</span>
            </button>
        </nav>
    </div>
</template>

<style scoped>
/* Mengatur keseluruhan layout aplikasi */
.app-layout {
    width: 100%;
    max-width: 412px;
    min-height: 100vh;
    min-height: 100dvh;
    margin: 0 auto;
    background: #f5f5f5;
    display: flex;
    flex-direction: column;
    font-family: Arial, Helvetica, sans-serif;
    position: relative;
    overflow-x: hidden;
}

/* Mengatur header utama berwarna merah */
.app-header {
    width: 100%;
    height: 68px;
    min-height: 68px;
    background: #ca0708;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 14px;
    position: relative;
    z-index: 10;
}

/* Mengatur susunan bagian kiri header */
.header-left {
    display: flex;
    align-items: center;
    gap: 10px;
}

/* Mengatur tampilan judul halaman */
.header-left h1 {
    font-size: 16px;
    font-weight: 600;
    margin: 0;
}

/* Mengatur tombol kembali */
.back-button {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    border: none;
    color: #ffffff;
    padding: 0;
    cursor: pointer;
}

/* Mengatur ukuran ikon tombol kembali */
.back-button svg {
    width: 21px;
    height: 21px;
}

/* Mengatur susunan ikon bagian kanan header */
.header-actions {
    display: flex;
    align-items: center;
    gap: 13px;
}

/* Mengatur tombol ikon header */
.header-icon {
    width: 28px;
    height: 28px;
    background: transparent;
    border: none;
    color: #ffffff;
    padding: 2px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    cursor: pointer;
}

/* Mengatur ukuran ikon header */
.header-icon svg {
    width: 19px;
    height: 19px;
}

/* Mengatur titik indikator notifikasi */
.notification-dot {
    position: absolute;
    top: 3px;
    right: 2px;
    width: 5px;
    height: 5px;
    background: #ffd700;
    border-radius: 50%;
}

/* Mengatur posisi dropdown profil */
.profile-wrapper {
    position: relative;
}

/* Mengatur tampilan dropdown profil */
.profile-dropdown {
    position: absolute;
    top: 38px;
    right: 0;
    width: 145px;
    background: #ffffff;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    overflow: hidden;
}

/* Mengatur setiap item dropdown */
.dropdown-item {
    display: block;
    width: 100%;
    padding: 12px;
    color: #333333;
    font-size: 12px;
    text-align: left;
    text-decoration: none;
    background: #ffffff;
    border: none;
    cursor: pointer;
}

/* Memberikan efek ketika item dropdown diarahkan */
.dropdown-item:hover {
    background: #f5f5f5;
}

/* Memberikan warna merah pada tombol logout */
.logout-item {
    color: #ca0708;
}

/* Mengatur area konten setiap halaman */
.app-main {
    width: 100%;
    flex: 1;
    padding-bottom: 72px;
}

/* Mengatur navigasi bagian bawah */
.bottom-navigation {
    position: fixed;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 100%;
    max-width: 412px;
    height: 62px;
    background: #ffffff;
    border-top: 1px solid #dddddd;
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    align-items: center;
    z-index: 20;
    box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.06);
}

/* Mengatur tampilan setiap item navigasi */
.nav-item {
    height: 48px;
    margin: 0 5px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 3px;
    color: #667085;
    background: transparent;
    border: none;
    border-radius: 13px;
    text-decoration: none;
    cursor: pointer;
}

/* Mengatur ukuran ikon navigasi */
.nav-item svg {
    width: 20px;
    height: 20px;
}

/* Mengatur ukuran tulisan navigasi */
.nav-item span {
    font-size: 9px;
    font-weight: 500;
}

/* Memberikan tampilan khusus pada navigasi aktif */
.nav-item.active {
    color: #ca0708;
    background: #fff0f0;
}

/* Mengatur tampilan navigasi yang belum tersedia */
.nav-item:disabled {
    opacity: 1;
    cursor: default;
}

/* Menyesuaikan tampilan layout pada desktop */
@media (min-width: 768px) {
    .app-layout {
        min-height: 917px;
    }
}
</style>
