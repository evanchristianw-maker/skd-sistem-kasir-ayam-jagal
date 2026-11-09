<script setup>
defineProps({
    cards: {
        type: Array,
        default: () => [],
    },
});


</script>

<template>
    <section class="summary-section" aria-label="Ringkasan statistik">
        <div class="summary-grid">
            <article
                v-for="(card, index) in cards"
                :key="card.title"
                class="summary-card"
                :class="[
                    `summary-${card.color || 'red'}`,
                    `summary-card-${index + 1}`,
                ]"
            >
                <!-- ICON -->
                <div class="card-icon">
                    <!-- Total Penjualan -->
                    <svg
                        v-if="card.icon === 'money'"
                        viewBox="0 0 24 24"
                        fill="none"
                    >
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
                            d="M12 9V15M14.5 10.5C14.5 9.67 13.38 9 12 9C10.62 9 9.5 9.67 9.5 10.5C9.5 11.33 10.62 12 12 12C13.38 12 14.5 12.67 14.5 13.5C14.5 14.33 13.38 15 12 15C10.62 15 9.5 14.33 9.5 13.5"
                            stroke="currentColor"
                            stroke-width="1.6"
                            stroke-linecap="round"
                        />
                    </svg>

                    <!-- Jumlah Transaksi -->
                    <svg
                        v-else-if="card.icon === 'transaction'"
                        viewBox="0 0 24 24"
                        fill="none"
                    >
                        <path
                            d="M7 3H14L19 8V20C19 20.55 18.55 21 18 21H6C5.45 21 5 20.55 5 20V5C5 3.9 5.9 3 7 3Z"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linejoin="round"
                        />
                        <path
                            d="M13 3V9H19M8 13H16M8 17H14"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                        />
                    </svg>

                    <!-- Menu Aktif -->
                    <svg
                        v-else-if="card.icon === 'menu'"
                        viewBox="0 0 24 24"
                        fill="none"
                    >
                        <path
                            d="M4 7H20M4 12H20M4 17H20"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                        />
                        <circle cx="7" cy="7" r="1" fill="currentColor" />
                        <circle cx="7" cy="12" r="1" fill="currentColor" />
                        <circle cx="7" cy="17" r="1" fill="currentColor" />
                    </svg>

                    <!-- Menu Nonaktif -->
                    <svg v-else viewBox="0 0 24 24" fill="none">
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

                <!-- CONTENT -->
                <div class="card-content">
                    <p class="card-title">
                        {{ card.title }}
                    </p>

                    <h3 class="card-value">
                        {{ card.value }}
                    </h3>

                    <p class="card-description">
                        {{ card.description }}
                    </p>
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
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 18px;
    width: 100%;
}

/* CARD */
.summary-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;

    min-width: 0;
    min-height: 178px;
    padding: 20px 16px;

    background: #ffffff;
    border: 1px solid #eceef1;
    border-radius: 16px;

    box-shadow: 0 3px 12px rgba(20, 25, 35, 0.035);

    text-align: center;

    transition:
        transform 0.2s ease,
        box-shadow 0.2s ease;
    animation: dashboardCardEnter 0.55s ease both;
}

.summary-card:nth-child(1) {
    animation-delay: 0s;
}

.summary-card:nth-child(2) {
    animation-delay: 0.1s;
}

.summary-card:nth-child(3) {
    animation-delay: 0.2s;
}

.summary-card:nth-child(4) {
    animation-delay: 0.3s;
}

.summary-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 22px rgba(20, 25, 35, 0.07);
}

/* ICON */
.card-icon {
    display: flex;
    align-items: center;
    justify-content: center;

    width: 43px;
    height: 43px;
    flex-shrink: 0;

    margin-bottom: 17px;

    border-radius: 12px;
}

.card-icon svg {
    width: 22px;
    height: 22px;
}

/* CONTENT */
.card-content {
    display: flex;
    flex-direction: column;
    align-items: center;

    width: 100%;
    min-width: 0;
}

.card-title {
    margin: 0 0 7px;

    color: #747b87;

    font-size: 13px;
    font-weight: 600;
    line-height: 1.4;
}

.card-value {
    margin: 0;

    color: #202124;

    font-size: clamp(19px, 1.7vw, 25px);
    font-weight: 800;
    line-height: 1.3;
    letter-spacing: -0.7px;

    overflow-wrap: anywhere;
}

.card-description {
    margin: 7px 0 0;

    color: #9298a2;

    font-size: 11px;
    font-weight: 500;
    line-height: 1.4;
}

@keyframes dashboardCardEnter {
    from {
        opacity: 0;
        transform: translateY(18px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* COLOR VARIANTS */
.summary-red .card-icon {
    color: #ca0708;
    background: #fff0f0;
}

.summary-orange .card-icon {
    color: #e58a20;
    background: #fff5e8;
}

.summary-green .card-icon {
    color: #199b62;
    background: #eaf8f1;
}

.summary-gray .card-icon {
    color: #737b88;
    background: #f0f1f3;
}

/* TABLET */
@media (max-width: 1050px) {
    .summary-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .summary-card {
        min-height: 170px;
    }

    .card-value {
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
        min-height: 170px;
        padding: 17px 10px;
        border-radius: 14px;
    }

    .card-icon {
        width: 40px;
        height: 40px;
        margin-bottom: 14px;
        border-radius: 11px;
    }

    .card-icon svg {
        width: 21px;
        height: 21px;
    }

    .card-title {
        margin-bottom: 6px;
        font-size: 11px;
    }

    .card-value {
        font-size: clamp(17px, 4.5vw, 21px);
    }

    .card-description {
        margin-top: 5px;
        font-size: 10px;
    }
}

/* SMALL MOBILE */
@media (max-width: 350px) {
    .summary-grid {
        gap: 9px;
    }

    .summary-card {
        min-height: 155px;
        padding: 14px 8px;
    }

    .card-icon {
        width: 36px;
        height: 36px;
        margin-bottom: 12px;
    }

    .card-value {
        font-size: 16px;
    }
}

/* ACCESSIBILITY */
@media (prefers-reduced-motion: reduce) {
    .summary-card {
        transition: none;
    }
}
</style>



