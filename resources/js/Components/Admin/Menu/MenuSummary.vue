```vue
<script setup>
import { computed } from "vue";

const props = defineProps({
    totalMenus: {
        type: Number,
        default: 0,
    },
    activeMenus: {
        type: Number,
        default: 0,
    },
    inactiveMenus: {
        type: Number,
        default: 0,
    },
});

const summaryCards = computed(() => [
    {
        title: "Total Menu",
        value: props.totalMenus,
        description: "Seluruh produk",
        icon: "menu",
        color: "red",
    },
    {
        title: "Menu Tersedia",
        value: props.activeMenus,
        description: "Siap dijual",
        icon: "available",
        color: "green",
    },
    {
        title: "Menu Tidak Tersedia",
        value: props.inactiveMenus,
        description: "Sedang tidak dijual",
        icon: "unavailable",
        color: "orange",
    },
]);
</script>

<template>
    <section class="summary-section">
        <div class="section-heading">
            <h3>Ringkasan Menu</h3>
            <span>Statistik keseluruhan</span>
        </div>

        <div class="summary-grid">
            <article
                v-for="(card, index) in summaryCards"
                :key="card.title"
                class="summary-card"
                :class="`card-${card.color}`"
                :style="{ '--card-index': index }"
            >
                <div class="summary-card-top">
                    <div class="summary-icon">
                        <!-- Total Menu -->
                        <svg
                            v-if="card.icon === 'menu'"
                            viewBox="0 0 24 24"
                            fill="none"
                            aria-hidden="true"
                        >
                            <rect
                                x="3"
                                y="3"
                                width="18"
                                height="18"
                                rx="3"
                                stroke="currentColor"
                                stroke-width="1.8"
                            />
                            <path
                                d="M8 8H16M8 12H16M8 16H13"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            />
                        </svg>

                        <!-- Menu Tersedia -->
                        <svg
                            v-else-if="card.icon === 'available'"
                            viewBox="0 0 24 24"
                            fill="none"
                            aria-hidden="true"
                        >
                            <circle
                                cx="12"
                                cy="12"
                                r="9"
                                stroke="currentColor"
                                stroke-width="1.8"
                            />
                            <path
                                d="M8 12L10.5 14.5L16 9"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>

                        <!-- Menu Tidak Tersedia -->
                        <svg
                            v-else
                            viewBox="0 0 24 24"
                            fill="none"
                            aria-hidden="true"
                        >
                            <circle
                                cx="12"
                                cy="12"
                                r="9"
                                stroke="currentColor"
                                stroke-width="1.8"
                            />
                            <path
                                d="M9 9L15 15M15 9L9 15"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            />
                        </svg>
                    </div>
                </div>

                <div class="summary-card-bottom">
                    <p>{{ card.title }}</p>
                    <h3>{{ card.value }}</h3>
                    <span>{{ card.description }}</span>
                </div>
            </article>
        </div>
    </section>
</template>

<style scoped>
.summary-section {
    width: 100%;
    margin-bottom: 30px;
    animation: summaryFadeUp 0.55s ease both;
}

/* SECTION HEADING */
.section-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 19px;
}

.section-heading h3 {
    margin: 0;
    color: #202124;
    font-size: 21px;
    font-weight: 800;
}

.section-heading > span {
    color: #777e89;
    font-size: 13px;
}

/* SUMMARY GRID */
.summary-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 19px;
    width: 100%;
}

/* SUMMARY CARD */
.summary-card {
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: space-between;

    width: 100%;
    min-width: 0;
    min-height: 190px;
    padding: 22px;

    overflow: hidden;

    border: 1px solid #ededed;
    border-radius: 17px;

    background: #ffffff;
    box-shadow: 0 4px 15px rgba(20, 20, 20, 0.035);

    animation: summaryFadeUp 0.55s ease both;
    animation-delay: calc(var(--card-index) * 0.08s);

    transition:
        transform 0.25s ease,
        box-shadow 0.25s ease;
}

.summary-card::after {
    content: "";
    position: absolute;
    top: -45px;
    right: -45px;

    width: 120px;
    height: 120px;

    border-radius: 50%;
    opacity: 0.05;
    background: currentColor;

    pointer-events: none;
}

.summary-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.075);
}

/* ICON */
.summary-card-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.summary-icon {
    display: flex;
    align-items: center;
    justify-content: center;

    width: 49px;
    height: 49px;
    flex: 0 0 49px;

    border-radius: 13px;
}

.summary-icon svg {
    width: 27px;
    height: 27px;
}

/* CARD COLORS */
.card-red {
    color: #ca0708;
}

.card-red .summary-icon {
    color: #ca0708;
    background: #fff0f0;
}

.card-green {
    color: #179b61;
}

.card-green .summary-icon {
    color: #179b61;
    background: #e9f8f0;
}

.card-orange {
    color: #e98516;
}

.card-orange .summary-icon {
    color: #e98516;
    background: #fff4e7;
}

/* CARD CONTENT */
.summary-card-bottom {
    margin-top: 18px;
}

.summary-card-bottom p {
    margin: 0 0 8px;

    color: #626875;
    font-size: 14px;
    font-weight: 500;
}

.summary-card-bottom h3 {
    margin: 0;

    color: #202124;
    font-size: 25px;
    font-weight: 800;
    letter-spacing: -0.5px;

    overflow-wrap: anywhere;
}

.summary-card-bottom > span {
    display: block;
    margin-top: 7px;

    color: #777e89;
    font-size: 12px;
}

/* ANIMATION */
@keyframes summaryFadeUp {
    from {
        opacity: 0;
        transform: translateY(15px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* TABLET */
@media (max-width: 900px) {
    .summary-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

/* MOBILE */
@media (max-width: 480px) {
    .summary-grid {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .summary-card {
        min-height: 150px;
        padding: 15px;
    }

    .summary-card-bottom h3 {
        font-size: 20px;
    }

    .section-heading h3 {
        font-size: 17px;
    }

    .section-heading > span {
        font-size: 11px;
    }
}

/* ACCESSIBILITY */
@media (prefers-reduced-motion: reduce) {
    .summary-section,
    .summary-card {
        animation: none;
        transition: none;
    }
}
</style>
```
