<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const page = usePage();

const currentPath = computed(() => page.url.split("?")[0]);

const isDashboardActive = computed(() => currentPath.value === "/dashboard");

const isMenuActive = computed(() => currentPath.value === "/menu");

const isSalesActive = computed(() => currentPath.value === "/sales");

const isFinanceActive = computed(() => currentPath.value === "/finance");

const isRekeningActive = computed(() => currentPath.value === "/rekening-bank");
</script>

<template>
    <nav class="bottom-navigation" aria-label="Navigasi utama">
        <!-- Dashboard -->
        <Link
            href="/dashboard"
            class="nav-item"
            :class="{ active: isDashboardActive }"
            :aria-current="isDashboardActive ? 'page' : undefined"
        >
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
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
            href="/menu"
            class="nav-item"
            :class="{ active: isMenuActive }"
            :aria-current="isMenuActive ? 'page' : undefined"
        >
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
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

        <!-- Penjualan -->
        <Link
            href="/sales"
            class="nav-item"
            :class="{ active: isSalesActive }"
            :aria-current="isSalesActive ? 'page' : undefined"
        >
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
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
        </Link>

        <!-- Keuangan -->
        <Link
            href="/finance"
            class="nav-item"
            :class="{ active: isFinanceActive }"
            :aria-current="isFinanceActive ? 'page' : undefined"
        >
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
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
        </Link>
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
    grid-template-columns: repeat(4, minmax(0, 1fr));
    align-items: center;
    padding: 8px max(calc((100vw - 650px) / 2), 12px);
    border-top: 1px solid #eeeeee;
    background: rgba(255, 255, 255, 0.97);
    box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.035);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
}

.nav-item {
    position: relative;
    min-width: 0;
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
    cursor: pointer;
    transition:
        color 0.2s ease,
        background-color 0.2s ease,
        transform 0.2s ease;
}

.nav-item svg {
    width: 25px;
    height: 25px;
    flex-shrink: 0;
}

.nav-item span {
    font-size: 12px;
    font-weight: 600;
    line-height: 1.2;
}

.nav-item:hover {
    color: #ca0708;
    background: #fff7f7;
}

.nav-item:active {
    transform: scale(0.96);
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
    animation: indicatorAppear 0.25s ease both;
}

@keyframes indicatorAppear {
    from {
        opacity: 0;
        transform: scaleX(0.4);
    }

    to {
        opacity: 1;
        transform: scaleX(1);
    }
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

@media (prefers-reduced-motion: reduce) {
    .nav-item {
        transition: none;
    }

    .nav-item.active::before {
        animation: none;
    }
}
</style>
