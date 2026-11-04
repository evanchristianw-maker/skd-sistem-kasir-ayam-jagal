<script setup>
defineProps({
    cards: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <section class="summary-section">
        <div class="section-heading">
            <h3>Ringkasan Penjualan</h3>
            <span>Statistik keseluruhan</span>
        </div>

        <div class="summary-grid">
            <article
                v-for="(card, index) in cards"
                :key="card.title"
                class="summary-card"
                :class="`card-${card.color}`"
                :style="{ '--card-index': index }"
            >
                <div class="summary-card-top">
                    <div class="summary-icon">
                        <svg
                            v-if="card.icon === 'money'"
                            viewBox="0 0 24 24"
                            fill="none"
                        >
                            <circle
                                cx="12"
                                cy="12"
                                r="9"
                                stroke="currentColor"
                                stroke-width="1.8"
                            />
                            <path
                                d="M15 9.5C14.5 8.5 13.5 8 12 8C10.5 8 9.5 8.7 9.5 10C9.5 11.2 10.5 11.5 12 12C13.5 12.5 14.5 13 14.5 14.2C14.5 15.4 13.5 16 12 16C10.5 16 9.5 15.5 9 14.5M12 6V18"
                                stroke="currentColor"
                                stroke-width="1.6"
                                stroke-linecap="round"
                            />
                        </svg>

                        <svg
                            v-else-if="card.icon === 'transaction'"
                            viewBox="0 0 24 24"
                            fill="none"
                        >
                            <rect
                                x="4"
                                y="3"
                                width="16"
                                height="18"
                                rx="2"
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

                        <svg v-else viewBox="0 0 24 24" fill="none">
                            <rect
                                x="3"
                                y="4"
                                width="18"
                                height="16"
                                rx="2"
                                stroke="currentColor"
                                stroke-width="1.8"
                            />
                            <path
                                d="M3 10H21M9 10V20"
                                stroke="currentColor"
                                stroke-width="1.8"
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
.summary-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 19px;
}

.summary-card {
    position: relative;
    min-height: 190px;
    padding: 22px;
    overflow: hidden;
    border: 1px solid #ededed;
    border-radius: 17px;
    background: white;
    box-shadow: 0 4px 15px rgba(20, 20, 20, 0.035);
    animation: summaryFadeUp 0.55s ease both;
    animation-delay: calc(var(--card-index) * 0.08s);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
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
}

.summary-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.075);
}

.summary-card-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.summary-icon {
    width: 49px;
    height: 49px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 13px;
}

.summary-icon svg {
    width: 27px;
    height: 27px;
}

.card-red {
    color: #ca0708;
}

.card-red .summary-icon {
    color: #ca0708;
    background: #fff0f0;
}

.card-orange {
    color: #e98516;
}

.card-orange .summary-icon {
    color: #e98516;
    background: #fff4e7;
}

.card-green {
    color: #179b61;
}

.card-green .summary-icon {
    color: #179b61;
    background: #e9f8f0;
}

.card-gray {
    color: #747b88;
}

.card-gray .summary-icon {
    color: #747b88;
    background: #f0f1f4;
}

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

@media (max-width: 900px) {
    .summary-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 480px) {
    .summary-grid {
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
}
</style>
