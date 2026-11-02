<script setup>
// Mengimpor ref untuk mengatur kondisi popup
import { ref } from "vue";

// Mengimpor Link dan router dari Inertia.js
import { Link, router } from "@inertiajs/vue3";

// Mengatur kondisi popup profil dan konfirmasi logout
const showingProfileMenu = ref(false);
const showingLogoutConfirmation = ref(false);

// Data ringkasan dashboard sementara
// Nantinya data ini dapat diganti dengan data dari backend Dito.
const summaryCards = [
    {
        title: "Total Penjualan",
        value: "Rp 1.684.000",
        icon: "money",
        color: "#ffd600",
    },
    {
        title: "Jumlah Transaksi:",
        value: "360",
        icon: "transaction",
        color: "#ff8500",
    },
    {
        title: "Menu Aktif",
        value: "25",
        icon: "menu",
        color: "#00bd35",
    },
    {
        title: "Menu Nonaktif",
        value: "5",
        icon: "inactive",
        color: "#e00000",
    },
];

// Data riwayat transaksi sementara
const transactions = [
    {
        id: "ID-123",
        date: "20 Oktober 2026",
        total: "Rp 145.000",
    },
    {
        id: "ID-124",
        date: "20 Oktober 2026",
        total: "Rp 75.000",
    },
];

// Membuka popup konfirmasi logout
const openLogoutConfirmation = () => {
    showingProfileMenu.value = false;
    showingLogoutConfirmation.value = true;
};

// Menutup popup konfirmasi logout
const cancelLogout = () => {
    showingLogoutConfirmation.value = false;
};

// Fungsi logout pengguna
const logout = () => {
    router.post(route("logout"));
};
</script>

<template>
    <div class="app-layout">
        <!-- Header utama aplikasi -->
        <header class="app-header">
            <div class="header-left">
                <!-- Tombol kembali -->
                <button
                    type="button"
                    class="back-button"
                    @click="$inertia.visit(route('dashboard'))"
                    aria-label="Kembali ke dashboard"
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

                <h1>Dashboard</h1>
            </div>

            <div class="header-actions">
                <!-- Tombol notifikasi -->
                <button
                    type="button"
                    class="header-icon notification-button"
                    aria-label="Notifikasi"
                >
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M12 22C13.1 22 14 21.1 14 20H10C10 21.1 10.9 22 12 22ZM18 16V11C18 7.93 16.37 5.36 13.5 4.68V4C13.5 3.17 12.83 2.5 12 2.5S10.5 3.17 10.5 4V4.68C7.63 5.36 6 7.92 6 11V16L4 18V19H20V18L18 16Z"
                        />
                    </svg>
                    <span class="notification-dot"></span>
                </button>

                <!-- Profil pengguna -->
                <div class="profile-wrapper">
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

                    <!-- Popup profil -->
                    <div
                        v-if="showingProfileMenu"
                        class="popup-backdrop profile-backdrop"
                        @click.self="showingProfileMenu = false"
                    >
                        <div class="profile-popup">
                            <div class="profile-details">
                                <div class="profile-avatar">
                                    <svg viewBox="0 0 24 24" fill="none">
                                        <circle
                                            cx="12"
                                            cy="12"
                                            r="10"
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
                                            d="M5.5 19C6.5 16.2 9 14.5 12 14.5C15 14.5 17.5 16.2 18.5 19"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            stroke-linecap="round"
                                        />
                                    </svg>
                                </div>

                                <div class="profile-user-text">
                                    <p class="profile-name">Admin</p>
                                    <p class="profile-email">
                                        adminayamjagal@gmail.com
                                    </p>
                                </div>
                            </div>

                            <button
                                type="button"
                                class="profile-logout-button"
                                @click="openLogoutConfirmation"
                            >
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path
                                        d="M10 17L15 12L10 7M15 12H3M12 3H19C20.1 3 21 3.9 21 5V19C21 20.1 20.1 21 19 21H12"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                                Keluar Akun
                            </button>
                        </div>
                    </div>

                    <!-- Popup konfirmasi logout -->
                    <div
                        v-if="showingLogoutConfirmation"
                        class="popup-backdrop confirmation-backdrop"
                        @click.self="cancelLogout"
                    >
                        <div class="logout-confirmation">
                            <div class="confirmation-icon">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path
                                        d="M10 17L15 12L10 7M15 12H3M12 3H19C20.1 3 21 3.9 21 5V19C21 20.1 20.1 21 19 21H12"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            </div>

                            <h2>Konfirmasi Keluar Akun</h2>

                            <p>
                                Apakah anda yakin ingin keluar<br />
                                dari akun?
                            </p>

                            <div class="confirmation-actions">
                                <button
                                    type="button"
                                    class="cancel-logout-button"
                                    @click="cancelLogout"
                                >
                                    Batal
                                </button>

                                <button
                                    type="button"
                                    class="confirm-logout-button"
                                    @click="logout"
                                >
                                    Keluar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Konten utama Dashboard -->
        <main class="app-main">
            <div class="dashboard-content">
                <!-- Ringkasan penjualan -->
                <section class="summary-section">
                    <h2 class="section-title">Ringkasan Penjualan</h2>

                    <div class="summary-grid">
                        <div
                            v-for="card in summaryCards"
                            :key="card.title"
                            class="summary-card"
                        >
                            <div
                                class="summary-icon"
                                :style="{ backgroundColor: card.color }"
                            >
                                <!-- Ikon total penjualan -->
                                <svg
                                    v-if="card.icon === 'money'"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                >
                                    <circle
                                        cx="12"
                                        cy="12"
                                        r="9"
                                        stroke="white"
                                        stroke-width="1.8"
                                    />
                                    <path
                                        d="M14.5 9.5C14 8.5 13 8 12 8C10.8 8 10 8.7 10 9.5C10 10.5 11 11 12.2 11.4C13.5 11.8 14.5 12.3 14.5 13.5C14.5 14.5 13.5 15.2 12 15.2C10.8 15.2 9.8 14.7 9.3 13.8M12 6.5V17.5"
                                        stroke="white"
                                        stroke-width="1.5"
                                        stroke-linecap="round"
                                    />
                                </svg>

                                <!-- Ikon jumlah transaksi -->
                                <svg
                                    v-else-if="card.icon === 'transaction'"
                                    viewBox="0 0 24 24"
                                    fill="white"
                                >
                                    <path
                                        d="M7 3H9L10 6H14L15 3H17L16 7H18L20 20H4L6 7H8L7 3Z"
                                    />
                                    <path
                                        d="M10 9V14M14 9V14"
                                        stroke="#ff8500"
                                        stroke-width="1.5"
                                        stroke-linecap="round"
                                    />
                                </svg>

                                <!-- Ikon menu aktif -->
                                <svg
                                    v-else-if="card.icon === 'menu'"
                                    viewBox="0 0 24 24"
                                    fill="white"
                                >
                                    <rect
                                        x="5"
                                        y="8"
                                        width="14"
                                        height="12"
                                        rx="2"
                                    />
                                    <path
                                        d="M9 8V6C9 4.3 10.3 3 12 3C13.7 3 15 4.3 15 6V8"
                                        stroke="white"
                                        stroke-width="2"
                                        fill="none"
                                    />
                                </svg>

                                <!-- Ikon menu nonaktif -->
                                <svg v-else viewBox="0 0 24 24" fill="white">
                                    <rect
                                        x="5"
                                        y="8"
                                        width="14"
                                        height="12"
                                        rx="2"
                                    />
                                    <path
                                        d="M9 8V6C9 4.3 10.3 3 12 3C13.7 3 15 4.3 15 6V8"
                                        stroke="white"
                                        stroke-width="2"
                                        fill="none"
                                    />
                                </svg>
                            </div>

                            <p class="summary-label">
                                {{ card.title }}
                            </p>

                            <h3 class="summary-value">
                                {{ card.value }}
                            </h3>
                        </div>
                    </div>
                </section>

                <!-- Grafik penjualan -->
                <section class="chart-section">
                    <div class="chart-card">
                        <h2 class="chart-title">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M3 19H21V21H3V19ZM5 11H9V18H5V11ZM11 6H15V18H11V6ZM17 2H21V18H17V2Z"
                                />
                            </svg>
                            Grafik Penjualan (7 Hari Terakhir)
                        </h2>

                        <div class="chart-container">
                            <svg
                                class="sales-chart"
                                viewBox="0 0 300 190"
                                preserveAspectRatio="none"
                            >
                                <defs>
                                    <linearGradient
                                        id="salesGradient"
                                        x1="0"
                                        y1="0"
                                        x2="0"
                                        y2="1"
                                    >
                                        <stop
                                            offset="0%"
                                            stop-color="#e00000"
                                            stop-opacity="0.95"
                                        />
                                        <stop
                                            offset="100%"
                                            stop-color="#ffcccc"
                                            stop-opacity="0.05"
                                        />
                                    </linearGradient>
                                </defs>

                                <!-- Garis horizontal -->
                                <g class="chart-grid">
                                    <line x1="0" y1="0" x2="300" y2="0" />
                                    <line x1="0" y1="47.5" x2="300" y2="47.5" />
                                    <line x1="0" y1="95" x2="300" y2="95" />
                                    <line
                                        x1="0"
                                        y1="142.5"
                                        x2="300"
                                        y2="142.5"
                                    />
                                    <line x1="0" y1="190" x2="300" y2="190" />
                                </g>

                                <!-- Garis vertikal -->
                                <g class="chart-grid">
                                    <line x1="0" y1="0" x2="0" y2="190" />
                                    <line x1="50" y1="0" x2="50" y2="190" />
                                    <line x1="100" y1="0" x2="100" y2="190" />
                                    <line x1="150" y1="0" x2="150" y2="190" />
                                    <line x1="200" y1="0" x2="200" y2="190" />
                                    <line x1="250" y1="0" x2="250" y2="190" />
                                    <line x1="300" y1="0" x2="300" y2="190" />
                                </g>

                                <!-- Area warna grafik -->
                                <path
                                    d="M0 188
                                    C15 165 25 125 38 125
                                    C50 125 65 180 78 180
                                    C94 180 105 115 120 112
                                    C135 109 145 155 160 150
                                    C175 145 185 75 200 70
                                    C215 65 230 125 245 125
                                    C260 125 265 40 280 40
                                    C290 40 295 75 300 95
                                    L300 190 L0 190 Z"
                                    fill="url(#salesGradient)"
                                />

                                <!-- Garis utama grafik -->
                                <path
                                    d="M0 188
                                    C15 165 25 125 38 125
                                    C50 125 65 180 78 180
                                    C94 180 105 115 120 112
                                    C135 109 145 155 160 150
                                    C175 145 185 75 200 70
                                    C215 65 230 125 245 125
                                    C260 125 265 40 280 40
                                    C290 40 295 75 300 95"
                                    fill="none"
                                    stroke="#e00000"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                />
                            </svg>
                        </div>
                    </div>
                </section>

                <!-- Riwayat transaksi -->
                <section class="history-section">
                    <div class="history-heading">
                        <h2>Riwayat Transaksi</h2>
                        <button type="button" class="see-all-button">
                            Lihat Semua &gt;
                        </button>
                    </div>

                    <div class="transaction-list">
                        <div
                            v-for="transaction in transactions"
                            :key="transaction.id"
                            class="transaction-card"
                        >
                            <div class="transaction-left">
                                <p>Transaksi #{{ transaction.id }}</p>
                                <p class="transaction-date">
                                    Tanggal: {{ transaction.date }}
                                </p>
                            </div>

                            <div class="transaction-right">
                                <p>Total:</p>
                                <p class="transaction-total">
                                    {{ transaction.total }}
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>

        <!-- Navigasi bawah -->
        <nav class="bottom-navigation">
            <!-- Navigasi Dashboard -->
            <Link :href="route('dashboard')" class="nav-item active">
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

            <!-- Navigasi Menu -->
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
    background: #f4f4f4;
    display: flex;
    flex-direction: column;
    font-family: Arial, Helvetica, sans-serif;
    position: relative;
    overflow-x: hidden;
}

/* Header utama: z-index dinaikkan agar popup tidak tertutup konten */
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
    z-index: 100;
}

/* Bagian kiri header */
.header-left {
    display: flex;
    align-items: center;
    gap: 10px;
}

.header-left h1 {
    font-size: 16px;
    font-weight: 600;
    margin: 0;
}

/* Tombol kembali */
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

.back-button svg {
    width: 21px;
    height: 21px;
}

/* Ikon bagian kanan header */
.header-actions {
    display: flex;
    align-items: center;
    gap: 13px;
}

.header-icon {
    width: 25px;
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

.header-icon svg {
    width: 19px;
    height: 19px;
}

/* Indikator notifikasi */
.notification-dot {
    position: absolute;
    top: 3px;
    right: 2px;
    width: 5px;
    height: 5px;
    background: #ffd700;
    border-radius: 50%;
}

/* Posisi tombol profil */
.profile-wrapper {
    position: static;
}

/* Popup profil dan konfirmasi logout */
.popup-backdrop {
    position: fixed;
    inset: 0;
    z-index: 110;
    display: flex;
    justify-content: center;
    padding: 40px 16px 20px;
    background: rgba(0, 0, 0, 0.88);
}

/* Popup profil berada di bagian atas */
.profile-backdrop {
    align-items: flex-start;
}

/* Kotak popup profil */
.profile-popup {
    width: 100%;
    max-width: 257px;
    height: fit-content;
    padding: 13px 8px 15px;
    border-radius: 8px;
    background: #ffffff;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

/* Informasi pengguna */
.profile-details {
    display: flex;
    align-items: center;
    gap: 9px;
    min-height: 39px;
    margin-bottom: 7px;
    padding: 0 1px;
}

/* Ikon profil */
.profile-avatar {
    width: 29px;
    height: 29px;
    flex: 0 0 29px;
    color: #111111;
}

.profile-avatar svg {
    width: 100%;
    height: 100%;
}

/* Nama dan email pengguna */
.profile-user-text {
    min-width: 0;
}

.profile-user-text p {
    margin: 0;
    line-height: 17px;
}

.profile-name {
    color: #111111;
    font-size: 14px;
    font-weight: 500;
}

.profile-email {
    color: #888888;
    font-size: 13px;
    white-space: nowrap;
}

/* Tombol Keluar Akun */
.profile-logout-button {
    width: 100%;
    height: 23px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    border: 1.5px solid #e00000;
    border-radius: 8px;
    color: #e00000;
    background: #ffffff;
    font-family: inherit;
    font-size: 13px;
    cursor: pointer;
}

.profile-logout-button svg {
    width: 17px;
    height: 17px;
}

/* Popup konfirmasi berada di tengah layar */
.confirmation-backdrop {
    align-items: center;
    padding-top: 20px;
}

/* Kotak konfirmasi logout */
.logout-confirmation {
    width: 100%;
    max-width: 258px;
    height: fit-content;
    padding: 14px 8px 16px;
    border-radius: 8px;
    background: #ffffff;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

/* Ikon logout */
.confirmation-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 22px;
    margin-bottom: 6px;
    color: #e00000;
}

.confirmation-icon svg {
    width: 21px;
    height: 21px;
}

/* Judul konfirmasi */
.logout-confirmation h2 {
    margin: 0 0 10px;
    color: #111111;
    font-size: 17px;
    font-weight: 700;
}

/* Pesan konfirmasi */
.logout-confirmation p {
    margin: 0 0 17px;
    color: #111111;
    font-size: 13px;
    line-height: 1.35;
}

/* Tombol Batal dan Keluar */
.confirmation-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 7px;
}

.confirmation-actions button {
    height: 38px;
    border-radius: 10px;
    font-family: inherit;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
}

/* Tombol Batal */
.cancel-logout-button {
    border: 1px solid #555555;
    color: #543b2c;
    background: #ffffff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

/* Tombol Keluar */
.confirm-logout-button {
    border: 1px solid #e00000;
    color: #ffffff;
    background: linear-gradient(180deg, #ff0808, #d00000);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

/* Mengatur area konten utama */
.app-main {
    width: 100%;
    flex: 1;
    padding-bottom: 76px;
    position: relative;
    z-index: 2;
    margin-top: -10px;
}

/* Panel konten dengan lengkungan halus */
.dashboard-content {
    width: 100%;
    min-height: 100%;
    background: #f4f4f4;

    /* Lengkungan horizontal dan vertikal */
    border-radius: 58px 58px 0 0 / 48px 48px 0 0;

    margin-top: 0;
    padding: 16px 14px 15px;
    position: relative;
    z-index: 2;
}

/* Warna merah di belakang lengkungan */
.app-main::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 48px;
    background: #ca0708;
    z-index: 1;
}

/* Judul ringkasan */
.section-title {
    font-size: 12px;
    font-weight: 700;
    color: #111111;
    margin: 0 0 10px 5px;
}

/* Grid kartu statistik */
.summary-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
}

/* Kartu statistik */
.summary-card {
    height: 70px;
    background: #ffffff;
    border-radius: 17px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 3px rgba(0, 0, 0, 0.2);
}

/* Ikon statistik */
.summary-icon {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 5px;
}

.summary-icon svg {
    width: 15px;
    height: 15px;
}

/* Label statistik */
.summary-label {
    font-size: 11px;
    color: #999999;
    margin: 0 0 3px;
    line-height: 13px;
}

/* Angka statistik */
.summary-value {
    font-size: 14px;
    font-weight: 700;
    color: #111111;
    margin: 0;
    line-height: 17px;
}

/* Jarak grafik */
.chart-section {
    margin-top: 17px;
}

/* Kartu grafik */
.chart-card {
    width: 100%;
    background: #ffffff;
    border-radius: 17px;
    padding: 15px 12px 11px;
    box-shadow: 0 2px 3px rgba(0, 0, 0, 0.2);
}

/* Judul grafik */
.chart-title {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 700;
    color: #111111;
    margin: 0 0 12px;
    white-space: nowrap;
}

.chart-title svg {
    width: 20px;
    height: 20px;
}

/* Area grafik */
.chart-container {
    width: 100%;
    height: 166px;
    border: 1px solid #eeeeee;
    overflow: hidden;
}

/* SVG grafik */
.sales-chart {
    display: block;
    width: 100%;
    height: 100%;
}

/* Garis bantu grafik */
.chart-grid line {
    stroke: #e9e9e9;
    stroke-width: 0.8;
}

/* Jarak riwayat transaksi */
.history-section {
    margin-top: 15px;
}

/* Judul riwayat transaksi */
.history-heading {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 0 5px 10px;
}

.history-heading h2 {
    font-size: 12px;
    font-weight: 700;
    color: #111111;
    margin: 0;
}

/* Tombol lihat semua */
.see-all-button {
    border: none;
    background: transparent;
    font-size: 10px;
    color: #111111;
    cursor: pointer;
    padding: 0;
}

/* Daftar transaksi */
.transaction-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

/* Kartu transaksi */
.transaction-card {
    min-height: 44px;
    width: 100%;
    background: #ffffff;
    border-radius: 10px;
    padding: 7px 11px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 2px 3px rgba(0, 0, 0, 0.18);
}

/* Informasi transaksi */
.transaction-left p,
.transaction-right p {
    font-size: 10px;
    line-height: 15px;
    color: #111111;
    margin: 0;
}

.transaction-date {
    margin-top: 2px !important;
}

/* Total pembayaran */
.transaction-right {
    text-align: right;
}

.transaction-total {
    margin-top: 2px !important;
}

/* Navigasi bawah */
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

/* Setiap item navigasi */
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

/* Ikon navigasi */
.nav-item svg {
    width: 20px;
    height: 20px;
}

/* Teks navigasi */
.nav-item span {
    font-size: 9px;
    font-weight: 500;
}

/* Navigasi aktif */
.nav-item.active {
    color: #ca0708;
    background: #fff0f0;
}

/* Navigasi yang belum tersedia */
.nav-item:disabled {
    opacity: 1;
    cursor: default;
}

/* Penyesuaian layar HP kecil */
@media (max-width: 380px) {
    .dashboard-content {
        padding-left: 12px;
        padding-right: 12px;
    }

    .summary-grid {
        gap: 10px;
    }

    .summary-card {
        height: 68px;
    }

    .chart-card {
        padding-left: 9px;
        padding-right: 9px;
    }

    .chart-title {
        font-size: 11px;
    }

    .profile-popup,
    .logout-confirmation {
        max-width: 258px;
    }
}

/* Penyesuaian tampilan desktop */
@media (min-width: 768px) {
    .app-layout {
        min-height: 917px;
    }
}
</style>
