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
* {
    box-sizing: border-box;
}

.summary-section {
    width: 100%;
    margin-bottom: 30px;
    animation: summaryFadeUp 0.5s ease both;
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
    letter-spacing: -0.4px;
}

.section-heading > span {
    color: #777e89;
    font-size: 13px;
}

/* SUMMARY GRID */
.summary-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 18px;
    width: 100%;
}

/* SUMMARY CARD */
.summary-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-width: 0;
    min-height: 178px;
    padding: 20px 16px;
    border: 1px solid #eceef1;
    border-radius: 16px;
    background: #ffffff;
    box-shadow: 0 3px 12px rgba(20, 25, 35, 0.035);
    text-align: center;
    animation: summaryFadeUp 0.5s ease both;
    animation-delay: calc(var(--card-index) * 0.08s);
    transition:
        transform 0.2s ease,
        box-shadow 0.2s ease;
}

.summary-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 22px rgba(20, 25, 35, 0.07);
}

/* ICON */
.summary-card-top {
    display: flex;
    align-items: center;
    justify-content: center;
}

.summary-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 43px;
    height: 43px;
    flex-shrink: 0;
    margin-bottom: 17px;
    border-radius: 12px;
}

.summary-icon svg {
    width: 22px;
    height: 22px;
}

/* COLOR VARIANTS */
.card-red .summary-icon {
    color: #ca0708;
    background: #fff0f0;
}

.card-orange .summary-icon {
    color: #e58a20;
    background: #fff5e8;
}

.card-green .summary-icon {
    color: #199b62;
    background: #eaf8f1;
}

/* CARD CONTENT */
.summary-card-bottom {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
    min-width: 0;
}

.summary-card-bottom p {
    margin: 0 0 7px;
    color: #747b87;
    font-size: 13px;
    font-weight: 600;
    line-height: 1.4;
}

.summary-card-bottom h3 {
    margin: 0;
    color: #202124;
    font-size: clamp(19px, 1.7vw, 25px);
    font-weight: 800;
    line-height: 1.3;
    letter-spacing: -0.7px;
    overflow-wrap: anywhere;
}

.summary-card-bottom > span {
    display: block;
    margin-top: 7px;
    color: #9298a2;
    font-size: 11px;
    font-weight: 500;
    line-height: 1.4;
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
        gap: 16px;
    }

    .summary-card {
        min-height: 170px;
    }

    .summary-card-bottom h3 {
        font-size: 23px;
    }
}

/* MOBILE */
@media (max-width: 600px) {
    .summary-section {
        margin-bottom: 24px;
    }

    .summary-grid {
        gap: 12px;
    }

    .summary-card {
        min-height: 160px;
        padding: 17px 10px;
        border-radius: 14px;
    }

    .summary-icon {
        width: 40px;
        height: 40px;
        margin-bottom: 14px;
        border-radius: 11px;
    }

    .summary-icon svg {
        width: 21px;
        height: 21px;
    }

    .summary-card-bottom p {
        margin-bottom: 6px;
        font-size: 11px;
    }

    .summary-card-bottom h3 {
        font-size: clamp(17px, 4.5vw, 21px);
    }

    .summary-card-bottom > span {
        margin-top: 5px;
        font-size: 10px;
    }

    .section-heading h3 {
        font-size: 17px;
    }

    .section-heading > span {
        font-size: 11px;
    }
}

/* SMALL MOBILE */
@media (max-width: 350px) {
    .summary-grid {
        gap: 9px;
    }

    .summary-card {
        min-height: 150px;
        padding: 14px 8px;
    }

    .summary-icon {
        width: 36px;
        height: 36px;
        margin-bottom: 12px;
    }

    .summary-card-bottom h3 {
        font-size: 16px;
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


