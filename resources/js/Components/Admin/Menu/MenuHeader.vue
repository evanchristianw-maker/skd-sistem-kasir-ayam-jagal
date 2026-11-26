<script setup>
import { Link } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    adminName: {
        type: String,
        default: "Administrator",
    },
    currentDate: {
        type: String,
        default: "",
    },
});

const adminInitial = computed(() => {
    const name = props.adminName?.trim();

    return name ? name.charAt(0).toUpperCase() : "A";
});
</script>

<template>
    <header class="app-header">
        <div class="header-container">
            <!-- Brand -->
            <Link href="/dashboard" class="header-brand" aria-label="Kembali ke Dashboard UMKM Ayam Jagal">
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
            </Link>

            <!-- Header Right -->
            <div class="header-right">
                <!-- Date -->
                <div v-if="currentDate" class="header-date" aria-label="Tanggal saat ini">
                    <span class="date-dot"></span>
                    <span>{{ currentDate }}</span>
                </div>

                <!-- Administrator Profile -->
                <div class="profile-button" aria-label="Profil administrator">
                    <div class="profile-avatar-small">
                        {{ adminInitial }}
                    </div>

                    <div class="profile-header-text">
                        <strong>{{ adminName }}</strong>
                        <span>Administrator</span>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>

<style scoped>
.app-header {
    position: sticky;
    top: 0;
    z-index: 100;

    width: 100%;
    min-height: 82px;

    display: flex;
    align-items: center;

    border-bottom: 1px solid #eeeeee;
    background: rgba(255, 255, 255, 0.97);

    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
}

.header-container {
    width: 100%;
    max-width: 1440px;
    min-height: 82px;

    margin: 0 auto;
    padding: 12px 35px;

    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 20px;
}

/* Brand */
.header-brand {
    display: flex;
    align-items: center;
    gap: 12px;

    color: inherit;
    text-decoration: none;

    transition: opacity 0.2s ease;
}

.header-brand:hover {
    opacity: 0.85;
}

.header-brand:focus-visible {
    outline: 3px solid rgba(202, 7, 8, 0.2);
    outline-offset: 5px;
    border-radius: 8px;
}

.brand-logo {
    width: 48px;
    height: 48px;
    flex: 0 0 48px;

    display: flex;
    align-items: center;
    justify-content: center;

    overflow: hidden;

    border-radius: 13px;
    background: #fff0f0;
}

.brand-logo img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.brand-text h1 {
    margin: 0;

    color: #ca0708;

    font-size: 17px;
    font-weight: 900;
    letter-spacing: 0.7px;
    line-height: 1.3;
}

.brand-text span {
    display: block;
    margin-top: 3px;

    color: #777e89;

    font-size: 11px;
    font-weight: 500;
}

/* Header right */
.header-right {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 25px;
}

/* Date */
.header-date {
    display: flex;
    align-items: center;
    gap: 9px;

    color: #626875;

    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
}

.date-dot {
    width: 8px;
    height: 8px;
    flex: 0 0 8px;

    border-radius: 50%;
    background: #179b61;

    box-shadow: 0 0 0 4px rgba(23, 155, 97, 0.1);
}

/* Profile */
.profile-button {
    display: flex;
    align-items: center;
    gap: 11px;
}

.profile-avatar-small {
    width: 42px;
    height: 42px;
    flex: 0 0 42px;

    display: flex;
    align-items: center;
    justify-content: center;

    border: 1px solid #f5caca;
    border-radius: 12px;

    color: #ca0708;
    background: #fff0f0;

    font-size: 16px;
    font-weight: 800;
}

.profile-header-text {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.profile-header-text strong {
    color: #292c32;

    font-size: 13px;
    font-weight: 800;
}

.profile-header-text span {
    color: #777e89;

    font-size: 11px;
    font-weight: 500;
}

/* Tablet */
@media (max-width: 900px) {
    .header-container {
        padding: 12px 22px;
    }

    .header-right {
        gap: 18px;
    }
}

/* Mobile */
@media (max-width: 600px) {
    .app-header {
        min-height: 72px;
    }

    .header-container {
        min-height: 72px;
        padding: 10px 15px;
        gap: 10px;
    }

    .brand-logo {
        width: 40px;
        height: 40px;
        flex-basis: 40px;
        border-radius: 11px;
    }

    .brand-text h1 {
        font-size: 14px;
    }

    .brand-text span {
        font-size: 9px;
    }

    .header-right {
        gap: 12px;
    }

    .header-date {
        display: none;
    }

    .profile-avatar-small {
        width: 37px;
        height: 37px;
        flex-basis: 37px;
        border-radius: 10px;
        font-size: 14px;
    }

    .profile-header-text strong {
        max-width: 105px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-size: 11px;
    }

    .profile-header-text span {
        font-size: 10px;
    }
}

/* Small mobile */
@media (max-width: 360px) {
    .header-container {
        padding: 10px;
    }

    .brand-logo {
        width: 35px;
        height: 35px;
        flex-basis: 35px;
    }

    .brand-text h1 {
        font-size: 12px;
    }

    .brand-text span {
        font-size: 8px;
    }

    .profile-header-text {
        display: none;
    }
}
</style>


