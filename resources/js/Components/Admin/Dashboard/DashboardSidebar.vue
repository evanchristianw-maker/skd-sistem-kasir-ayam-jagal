<script setup>
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <nav class="bottom-navigation">
        <Link
            :href="route('dashboard')"
            class="nav-item active"
            aria-current="page"
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

        <Link href="/menu" class="nav-item">
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
</template>

<style scoped>
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
    transition: 0.2s ease;
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

@media (max-width: 768px) {
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
}

@media (max-width: 380px) {
    .nav-item span {
        font-size: 11px;
    }
}
</style>
