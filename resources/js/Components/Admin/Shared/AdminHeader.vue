<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";

defineProps({
    adminName: {
        type: String,
        default: "Admin",
    },
    adminEmail: {
        type: String,
        default: "adminayamjagal@gmail.com",
    },
    currentDate: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["logout-request"]);

const showingProfileMenu = ref(false);
const showingNotifications = ref(false);

const notifications = [
    {
        title: "Selamat datang",
        message: "Selamat bekerja di Dashboard UMKM Ayam Jagal.",
        time: "Informasi sistem",
    },
];

const notificationCount = notifications.length;

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
};

const openLogoutConfirmation = () => {
    showingProfileMenu.value = false;
    emit("logout-request");
};

const handleEscape = (event) => {
    if (event.key === "Escape") {
        closePopups();
    }
};

onMounted(() => {
    window.addEventListener("keydown", handleEscape);
});

onBeforeUnmount(() => {
    window.removeEventListener("keydown", handleEscape);
});
</script>

<template>
    <header class="app-header">
        <div class="header-container">
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

            <div class="header-right">
                <div class="header-date">
                    <span class="date-dot"></span>
                    {{ currentDate }}
                </div>

                <button
                    type="button"
                    class="header-action"
                    aria-label="Notifikasi"
                    aria-haspopup="true"
                    :aria-expanded="showingNotifications"
                    aria-controls="notification-dropdown"
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

                    <span
                        v-if="notificationCount"
                        class="notification-badge"
                    >
                        {{ notificationCount }}
                    </span>
                </button>

                <button
                    type="button"
                    class="profile-button"
                    aria-label="Menu profil administrator"
                    aria-haspopup="true"
                    :aria-expanded="showingProfileMenu"
                    aria-controls="profile-dropdown"
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

                    <svg
                        class="profile-chevron"
                        viewBox="0 0 24 24"
                        fill="none"
                    >
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

        <Transition name="popup">
            <div
                v-if="showingProfileMenu"
                class="dropdown-backdrop"
                @click.self="showingProfileMenu = false"
            >
                <div
                    id="profile-dropdown"
                    class="profile-dropdown"
                    aria-label="Menu profil administrator"
                >
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

        <Transition name="popup">
            <div
                v-if="showingNotifications"
                class="dropdown-backdrop"
                @click.self="showingNotifications = false"
            >
                <div
                    id="notification-dropdown"
                    class="notification-dropdown"
                    role="region"
                    aria-label="Daftar notifikasi"
                >
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
</template>

<style scoped>
* {
    box-sizing: border-box;
}

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

.header-action:hover,
.profile-button:hover {
    background: rgba(255, 255, 255, 0.22);
}

.header-action:focus-visible,
.profile-button:focus-visible,
.dropdown-logout:focus-visible {
    outline: 3px solid #ffe08a;
    outline-offset: 3px;
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

.popup-enter-active,
.popup-leave-active {
    transition: opacity 0.2s ease;
}

.popup-enter-from,
.popup-leave-to {
    opacity: 0;
}

.popup-enter-active .profile-dropdown,
.popup-enter-active .notification-dropdown {
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
}
</style>


