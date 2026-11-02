<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";

// ─── Props ────────────────────────────────────────────────────────────────────
// activePage: 'dashboard' | 'menu' | 'penjualan' | 'keuangan'
defineProps({
    activePage: {
        type: String,
        default: "",
    },
});

// ─── Auth ─────────────────────────────────────────────────────────────────────
const page = usePage();

const adminName = computed(() => page.props.auth?.user?.name || "Admin");
const adminEmail = computed(
    () => page.props.auth?.user?.email || "adminayamjagal@gmail.com",
);

// ─── Date ─────────────────────────────────────────────────────────────────────
const currentDate = computed(() =>
    new Date().toLocaleDateString("id-ID", {
        weekday: "long",
        day: "numeric",
        month: "long",
        year: "numeric",
    }),
);

// ─── Popup state ──────────────────────────────────────────────────────────────
const showingProfileMenu = ref(false);
const showingLogoutConfirmation = ref(false);
const showingNotifications = ref(false);

// ─── Notifications ────────────────────────────────────────────────────────────
const notifications = [
    {
        title: "Selamat datang",
        message: "Selamat bekerja di Dashboard UMKM Ayam Jagal.",
        time: "Informasi sistem",
    },
];

const notificationCount = computed(() => notifications.length);

// ─── Handlers ─────────────────────────────────────────────────────────────────
const openProfileMenu = () => {
    showingNotifications.value = false;
    showingProfileMenu.value = !showingProfileMenu.value;
};

const openNotifications = () => {
    showingProfileMenu.value = false;
    showingNotifications.value = !showingNotifications.value;
};

const closePopups = () => {
    showingProfileMenu.value = false;
    showingNotifications.value = false;
    showingLogoutConfirmation.value = false;
};

const openLogoutConfirmation = () => {
    showingProfileMenu.value = false;
    showingLogoutConfirmation.value = true;
};

const cancelLogout = () => {
    showingLogoutConfirmation.value = false;
};

const logout = () => {
    router.post(route("logout"));
};

const handleEscape = (event) => {
    if (event.key === "Escape") closePopups();
};

onMounted(() => window.addEventListener("keydown", handleEscape));
onBeforeUnmount(() => window.removeEventListener("keydown", handleEscape));
</script>

<template>
    <!-- ═══════════════════════════════════════════════════════════════════════
         HEADER
    ════════════════════════════════════════════════════════════════════════ -->
    <header class="app-header">
        <div class="header-container">
            <!-- Brand -->
            <div class="header-brand">
                <div class="brand-logo">
                    <img
                        src="/assets/images/illustrations/ilustrasi-koki.png"
                        alt="Logo Ayam Jagal"
                    />
                </div>

                <div class="brand-text">
                    <h1>AYAM JAGAL</h1>
                    <span>Point of Sale System</span>
                </div>
            </div>

            <!-- Right section -->
            <div class="header-right">
                <!-- Date -->
                <div class="header-date">
                    <span class="date-dot"></span>
                    {{ currentDate }}
                </div>

                <!-- Notification button -->
                <button
                    type="button"
                    class="header-action"
                    aria-label="Notifikasi"
                    @click="openNotifications"
                >
                    <svg viewBox="0 0 24 24" fill="none">
                        <path
                            d="M18 9C18 5.7 15.8 3 12 3C8.2 3 6 5.7 6 9C6 15 3.5 16 3.5 17.5H20.5C20.5 16 18 15 18 9Z"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linejoin="round"
                        />
                        <path
                            d="M10 21H14"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                        />
                    </svg>

                    <span v-if="notificationCount" class="notification-badge">
                        {{ notificationCount }}
                    </span>
                </button>

                <!-- Profile button -->
                <button
                    type="button"
                    class="profile-button"
                    @click="openProfileMenu"
                >
                    <div class="profile-avatar-small">
                        <svg viewBox="0 0 24 24" fill="none">
                            <circle
                                cx="12"
                                cy="8"
                                r="3.5"
                                stroke="currentColor"
                                stroke-width="1.7"
                            />
                            <path
                                d="M5 20C5.8 16.5 8.2 14.5 12 14.5C15.8 14.5 18.2 16.5 19 20"
                                stroke="currentColor"
                                stroke-width="1.7"
                                stroke-linecap="round"
                            />
                        </svg>
                    </div>

                    <div class="profile-header-text">
                        <strong>{{ adminName }}</strong>
                        <span>Administrator</span>
                    </div>

                    <svg class="profile-chevron" viewBox="0 0 24 24" fill="none">
                        <path
                            d="M7 10L12 15L17 10"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Profile dropdown -->
        <Transition name="popup">
            <div
                v-if="showingProfileMenu"
                class="dropdown-backdrop"
                @click.self="showingProfileMenu = false"
            >
                <div class="profile-dropdown">
                    <div class="dropdown-user">
                        <div class="dropdown-avatar">
                            <svg viewBox="0 0 24 24" fill="none">
                                <circle
                                    cx="12"
                                    cy="8"
                                    r="3.5"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                />
                                <path
                                    d="M5 20C5.8 16.5 8.2 14.5 12 14.5C15.8 14.5 18.2 16.5 19 20"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    stroke-linecap="round"
                                />
                            </svg>
                        </div>

                        <div class="dropdown-user-info">
                            <strong>{{ adminName }}</strong>
                            <span>{{ adminEmail }}</span>
                        </div>
                    </div>

                    <div class="dropdown-divider"></div>

                    <button
                        type="button"
                        class="dropdown-logout"
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
                        Keluar dari akun
                    </button>
                </div>
            </div>
        </Transition>

        <!-- Notification dropdown -->
        <Transition name="popup">
            <div
                v-if="showingNotifications"
                class="dropdown-backdrop"
                @click.self="showingNotifications = false"
            >
                <div class="notification-dropdown">
                    <div class="notification-heading">
                        <strong>Notifikasi</strong>
                        <span>{{ notificationCount }} informasi</span>
                    </div>

                    <div
                        v-for="(notification, index) in notifications"
                        :key="index"
                        class="notification-item"
                    >
                        <div class="notification-icon">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path
                                    d="M12 3L14.5 9.5L21 12L14.5 14.5L12 21L9.5 14.5L3 12L9.5 9.5L12 3Z"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    stroke-linejoin="round"
                                />
                            </svg>
                        </div>

                        <div class="notification-content">
                            <strong>{{ notification.title }}</strong>
                            <p>{{ notification.message }}</p>
                            <span>{{ notification.time }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </header>

    <!-- ═══════════════════════════════════════════════════════════════════════
         BOTTOM NAVIGATION
    ════════════════════════════════════════════════════════════════════════ -->
    <nav class="bottom-navigation">
        <!-- Dashboard -->
        <Link
            :href="route('dashboard')"
            class="nav-item"
            :class="{ active: activePage === 'dashboard' }"
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

        <!-- Menu -->
        <Link
            :href="route('menu.index')"
            class="nav-item"
            :class="{ active: activePage === 'menu' }"
        >
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
        </Link>

        <!-- Penjualan (belum tersedia) -->
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

        <!-- Keuangan (belum tersedia) -->
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

    <!-- ═══════════════════════════════════════════════════════════════════════
         LOGOUT CONFIRMATION MODAL
    ════════════════════════════════════════════════════════════════════════ -->
    <Transition name="modal">
        <div
            v-if="showingLogoutConfirmation"
            class="modal-backdrop"
            @click.self="cancelLogout"
        >
            <div
                class="logout-modal"
                role="dialog"
                aria-modal="true"
                aria-labelledby="logout-title"
            >
                <div class="logout-modal-icon">
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

                <h2 id="logout-title">Konfirmasi Keluar Akun</h2>
                <p>Apakah Anda yakin ingin keluar dari akun administrator?</p>

                <div class="logout-modal-actions">
                    <button
                        type="button"
                        class="modal-cancel"
                        @click="cancelLogout"
                    >
                        Batal
                    </button>

                    <button type="button" class="modal-confirm" @click="logout">
                        Keluar Akun
                    </button>
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
/* ─── Header ──────────────────────────────────────────────────────────────── */
.app-header {
    position: sticky;
    top: 0;
    z-index: 100;
    width: 100%;
    min-height: 82px;
    color: white;
    background: linear-gradient(110deg, #a90000, #ca0708 55%, #e92323);
    box-shadow: 0 4px 18px rgba(128, 0, 0, 0.13);
}

.header-container {
    max-width: 1180px;
    min-height: 82px;
    margin: 0 auto;
    padding: 0 28px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
}

.header-brand {
    display: flex;
    align-items: center;
    gap: 13px;
}

.brand-logo {
    width: 54px;
    height: 54px;
    flex: 0 0 54px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    border-radius: 13px;
    background: white;
}

.brand-logo img {
    width: 90%;
    height: 90%;
    object-fit: contain;
}

.brand-text h1 {
    margin: 0;
    font-size: 18px;
    font-weight: 800;
    letter-spacing: 1px;
}

.brand-text span {
    display: block;
    margin-top: 4px;
    color: rgba(255, 255, 255, 0.85);
    font-size: 12px;
}

.header-right {
    display: flex;
    align-items: center;
    gap: 18px;
}

.header-date {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
}

.date-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #ffe08a;
}

.header-action {
    position: relative;
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(255, 255, 255, 0.25);
    border-radius: 12px;
    color: white;
    background: rgba(255, 255, 255, 0.12);
    cursor: pointer;
}

.header-action svg {
    width: 24px;
    height: 24px;
}

.notification-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    min-width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 4px;
    border: 2px solid #ca0708;
    border-radius: 20px;
    color: #ca0708;
    background: white;
    font-size: 11px;
    font-weight: 800;
}

.profile-button {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 5px 10px 5px 5px;
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.12);
    cursor: pointer;
}

.profile-avatar-small {
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    color: #ca0708;
    background: white;
}

.profile-avatar-small svg {
    width: 24px;
    height: 24px;
}

.profile-header-text {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 3px;
}

.profile-header-text strong {
    font-size: 13px;
}

.profile-header-text span {
    font-size: 11px;
    color: rgba(255, 255, 255, 0.8);
}

.profile-chevron {
    width: 17px;
    height: 17px;
}

/* ─── Dropdowns ────────────────────────────────────────────────────────────── */
.dropdown-backdrop {
    position: fixed;
    inset: 0;
    z-index: 200;
    display: flex;
    align-items: flex-start;
    justify-content: flex-end;
    padding: 90px max(calc((100vw - 1180px) / 2 + 28px), 18px) 20px;
    background: rgba(20, 20, 20, 0.25);
    backdrop-filter: blur(2px);
}

.profile-dropdown,
.notification-dropdown {
    width: 320px;
    padding: 20px;
    border: 1px solid #eeeeee;
    border-radius: 16px;
    background: white;
    box-shadow: 0 15px 45px rgba(0, 0, 0, 0.16);
}

.dropdown-user {
    display: flex;
    align-items: center;
    gap: 13px;
}

.dropdown-avatar {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ca0708;
    background: #fff0f0;
    border-radius: 12px;
}

.dropdown-avatar svg {
    width: 28px;
    height: 28px;
}

.dropdown-user-info {
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.dropdown-user-info strong {
    color: #252525;
    font-size: 14px;
}

.dropdown-user-info span {
    overflow-wrap: anywhere;
    color: #777777;
    font-size: 12px;
}

.dropdown-divider {
    height: 1px;
    margin: 17px 0;
    background: #eeeeee;
}

.dropdown-logout {
    width: 100%;
    min-height: 48px;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 0 13px;
    color: #ca0708;
    background: #fff3f3;
    border: 1px solid #ffe0e0;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
}

.dropdown-logout svg {
    width: 21px;
    height: 21px;
}

.notification-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
}

.notification-heading strong {
    font-size: 16px;
}

.notification-heading span {
    color: #888888;
    font-size: 12px;
}

.notification-item {
    display: flex;
    gap: 12px;
    padding: 14px 0;
    border-top: 1px solid #eeeeee;
}

.notification-icon {
    width: 40px;
    height: 40px;
    flex: 0 0 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ca0708;
    background: #fff0f0;
    border-radius: 10px;
}

.notification-icon svg {
    width: 23px;
    height: 23px;
}

.notification-content strong {
    font-size: 14px;
}

.notification-content p {
    margin: 5px 0;
    color: #666666;
    font-size: 13px;
    line-height: 1.5;
}

.notification-content span {
    color: #999999;
    font-size: 11px;
}

/* ─── Bottom Navigation ────────────────────────────────────────────────────── */
.bottom-navigation {
    position: fixed;
    bottom: 0;
    left: 0;
    z-index: 90;
    width: 100%;
    min-height: 84px;
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    align-items: center;
    padding: 8px max(calc((100vw - 650px) / 2), 12px);
    border-top: 1px solid #eeeeee;
    background: rgba(255, 255, 255, 0.97);
    box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.035);
    backdrop-filter: blur(12px);
}

.nav-item {
    position: relative;
    min-height: 62px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 7px;
    border: none;
    border-radius: 12px;
    color: #777e89;
    background: transparent;
    text-decoration: none;
    font-family: inherit;
    transition: 0.2s ease;
    cursor: pointer;
}

.nav-item svg {
    width: 25px;
    height: 25px;
}

.nav-item span {
    font-size: 12px;
    font-weight: 600;
}

.nav-item.active {
    color: #ca0708;
    background: #fff0f0;
}

.nav-item.active::before {
    content: "";
    position: absolute;
    top: -8px;
    width: 25px;
    height: 4px;
    border-radius: 10px;
    background: #ca0708;
}

.nav-item:disabled {
    cursor: not-allowed;
    opacity: 0.65;
}

/* ─── Logout Modal ─────────────────────────────────────────────────────────── */
.modal-backdrop {
    position: fixed;
    inset: 0;
    z-index: 300;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    background: rgba(20, 20, 20, 0.58);
    backdrop-filter: blur(4px);
}

.logout-modal {
    width: 100%;
    max-width: 420px;
    padding: 32px 28px 27px;
    border: 1px solid #eeeeee;
    border-radius: 19px;
    background: white;
    text-align: center;
    box-shadow: 0 20px 65px rgba(0, 0, 0, 0.22);
}

.logout-modal-icon {
    width: 65px;
    height: 65px;
    margin: 0 auto 19px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ca0708;
    background: #fff0f0;
    border-radius: 17px;
}

.logout-modal-icon svg {
    width: 31px;
    height: 31px;
}

.logout-modal h2 {
    margin: 0;
    color: #242424;
    font-size: 21px;
    font-weight: 800;
}

.logout-modal p {
    margin: 12px 0 25px;
    color: #626875;
    font-size: 14px;
    line-height: 1.7;
}

.logout-modal-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

.logout-modal-actions button {
    min-height: 48px;
    border-radius: 10px;
    font-family: inherit;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
}

.modal-cancel {
    color: #60656f;
    border: 1px solid #e5e6e9;
    background: white;
}

.modal-confirm {
    color: white;
    border: 1px solid #ca0708;
    background: #ca0708;
}

/* ─── Transitions ──────────────────────────────────────────────────────────── */
.popup-enter-active,
.popup-leave-active,
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.2s ease;
}

.popup-enter-from,
.popup-leave-to,
.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.popup-enter-active .profile-dropdown,
.popup-enter-active .notification-dropdown,
.modal-enter-active .logout-modal {
    animation: popupAppear 0.22s ease;
}

@keyframes popupAppear {
    from {
        opacity: 0;
        transform: translateY(-8px) scale(0.97);
    }

    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* ─── Responsive ───────────────────────────────────────────────────────────── */
@media (max-width: 600px) {
    .app-header,
    .header-container {
        min-height: 76px;
    }

    .header-container {
        padding: 0 15px;
    }

    .brand-logo {
        width: 48px;
        height: 48px;
        flex-basis: 48px;
        border-radius: 12px;
    }

    .brand-text h1 {
        font-size: 16px;
    }

    .brand-text span {
        font-size: 11px;
    }

    .header-right {
        gap: 10px;
    }

    .header-date {
        display: none;
    }

    .header-action {
        width: 43px;
        height: 43px;
    }

    .profile-button {
        padding: 3px;
        border: none;
        background: transparent;
    }

    .profile-avatar-small {
        width: 43px;
        height: 43px;
    }

    .profile-header-text,
    .profile-chevron {
        display: none;
    }

    .dropdown-backdrop {
        padding: 85px 12px 15px;
    }

    .profile-dropdown,
    .notification-dropdown {
        width: min(320px, 100%);
    }

    .bottom-navigation {
        min-height: 88px;
        padding: 8px 7px;
    }

    .nav-item {
        min-height: 67px;
        gap: 7px;
    }

    .nav-item svg {
        width: 26px;
        height: 26px;
    }

    .nav-item span {
        font-size: 12px;
    }

    .logout-modal {
        max-width: 360px;
        padding: 28px 20px 24px;
    }

    .logout-modal h2 {
        font-size: 19px;
    }

    .logout-modal p {
        font-size: 13px;
    }
}

@media (max-width: 380px) {
    .nav-item span {
        font-size: 11px;
    }
}

@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}
</style>

