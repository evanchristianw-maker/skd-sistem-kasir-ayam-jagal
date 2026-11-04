<script setup>
const props = defineProps({
    chartPeriods: {
        type: Array,
        required: true,
    },
    selectedChartPeriod: {
        type: String,
        required: true,
    },
    periodDescription: {
        type: String,
        required: true,
    },
    totalChartSales: {
        type: Number,
        required: true,
    },
    averageChartSales: {
        type: Number,
        required: true,
    },
    highestChartSales: {
        type: Number,
        required: true,
    },
    chartWidth: {
        type: Number,
        required: true,
    },
    chartHeight: {
        type: Number,
        required: true,
    },
    chartPoints: {
        type: Array,
        required: true,
    },
    chartLinePath: {
        type: String,
        default: "",
    },
    chartAreaPath: {
        type: String,
        default: "",
    },
    chartGridLines: {
        type: Array,
        required: true,
    },
    activeChartPoint: {
        type: Object,
        default: null,
    },
    tooltipStyle: {
        type: Object,
        default: () => ({}),
    },
    formatRupiah: {
        type: Function,
        required: true,
    },
});

const emit = defineEmits([
    "period-change",
    "show-tooltip",
    "hide-tooltip",
]);
</script>

<template>
    <section class="chart-section">
        <div class="chart-card">
            <div class="chart-header">
                <div>
                    <h3>Grafik Penjualan</h3>
                    <p>{{ periodDescription }}</p>
                </div>

                <div
                    class="chart-period-options"
                    role="group"
                    aria-label="Periode grafik penjualan"
                >
                    <button
                        v-for="period in chartPeriods"
                        :key="period.value"
                        type="button"
                        class="chart-period-button"
                        :class="{
                            active:
                                selectedChartPeriod === period.value,
                        }"
                        @click="emit('period-change', period.value)"
                    >
                        {{ period.label }}
                    </button>
                </div>
            </div>

            <div class="chart-summary">
                <div class="chart-summary-item">
                    <span>Total Penjualan</span>
                    <strong>
                        {{ formatRupiah(totalChartSales) }}
                    </strong>
                </div>

                <div class="chart-summary-item">
                    <span>Rata-rata Harian</span>
                    <strong>
                        {{ formatRupiah(averageChartSales) }}
                    </strong>
                </div>

                <div class="chart-summary-item">
                    <span>Penjualan Tertinggi</span>
                    <strong>
                        {{ formatRupiah(highestChartSales) }}
                    </strong>
                </div>
            </div>

            <div class="chart-visual">
                <div class="chart-canvas">
                    <svg
                        class="sales-chart"
                        :viewBox="`0 0 ${chartWidth} ${chartHeight}`"
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
                                    stop-color="#CA0708"
                                    stop-opacity="0.25"
                                />
                                <stop
                                    offset="100%"
                                    stop-color="#CA0708"
                                    stop-opacity="0.01"
                                />
                            </linearGradient>
                        </defs>

                        <g class="chart-grid">
                            <line
                                v-for="(line, index) in chartGridLines"
                                :key="index"
                                x1="0"
                                :y1="line"
                                :x2="chartWidth"
                                :y2="line"
                            />
                        </g>

                        <path
                            v-if="chartPoints.length"
                            :d="chartAreaPath"
                            fill="url(#salesGradient)"
                        />

                        <path
                            v-if="chartPoints.length > 1"
                            :d="chartLinePath"
                            fill="none"
                            stroke="#CA0708"
                            stroke-width="3"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="chart-line"
                        />

                        <g
                            v-for="(point, index) in chartPoints"
                            :key="index"
                        >
                            <circle
                                :cx="point.x"
                                :cy="point.y"
                                r="10"
                                fill="#CA0708"
                                opacity="0.12"
                            />

                            <circle
                                :cx="point.x"
                                :cy="point.y"
                                r="5"
                                fill="white"
                                stroke="#CA0708"
                                stroke-width="2.5"
                                class="chart-point"
                                @mouseenter="emit('show-tooltip', point)"
                                @mouseleave="emit('hide-tooltip')"
                                @click="emit('show-tooltip', point)"
                            />
                        </g>
                    </svg>

                    <div
                        v-if="activeChartPoint"
                        class="chart-tooltip"
                        :style="tooltipStyle"
                    >
                        <strong>{{ activeChartPoint.label }}</strong>
                        <span>
                            {{ formatRupiah(activeChartPoint.total) }}
                        </span>
                    </div>

                    <div class="chart-labels">
                        <span
                            v-for="(point, index) in chartPoints"
                            :key="index"
                        >
                            {{ point.label }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="chart-footer">
                <div class="chart-legend">
                    <span></span>
                    Penjualan
                </div>
                <span>Data berdasarkan transaksi harian</span>
            </div>
        </div>
    </section>
</template>

<style scoped>
.chart-section {
    margin-top: 30px;
    animation: fadeUp 0.65s ease both;
}

.chart-card {
    padding: 26px;
    border: 1px solid #ededed;
    border-radius: 17px;
    background: white;
    box-shadow: 0 4px 15px rgba(20, 20, 20, 0.035);
}

.chart-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 15px;
}

.chart-header h3 {
    margin: 0;
    color: #202124;
    font-size: 21px;
    font-weight: 800;
}

.chart-header p {
    margin: 8px 0 0;
    color: #777e89;
    font-size: 14px;
}

.chart-period-options {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 16px;
}

.chart-period-button {
    min-height: 42px;
    padding: 9px 14px;
    color: #626875;
    border: 1px solid #eeeeee;
    border-radius: 9px;
    background: #fafafa;
    font-family: inherit;
    font-size: 13px;
    font-weight: 700;
    white-space: nowrap;
    cursor: pointer;
    transition:
        color 0.2s ease,
        background 0.2s ease,
        border-color 0.2s ease;
}

.chart-period-button.active {
    color: #ca0708;
    border-color: rgba(202, 7, 8, 0.25);
    background: #fff0f0;
}

.chart-period-button:focus-visible {
    outline: 3px solid rgba(202, 7, 8, 0.2);
    outline-offset: 2px;
}

.chart-summary {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 15px;
    margin: 24px 0;
    padding: 19px 0;
    border-top: 1px solid #f0f0f0;
    border-bottom: 1px solid #f0f0f0;
}

.chart-summary-item {
    display: flex;
    flex-direction: column;
    gap: 9px;
}

.chart-summary-item + .chart-summary-item {
    padding-left: 20px;
    border-left: 1px solid #eeeeee;
}

.chart-summary-item span {
    color: #777e89;
    font-size: 13px;
}

.chart-summary-item strong {
    color: #26282c;
    font-size: 18px;
    font-weight: 800;
}

.chart-visual {
    position: relative;
    width: 100%;
}

.chart-canvas {
    position: relative;
    width: 100%;
    height: 270px;
    padding-bottom: 36px;
}

.sales-chart {
    display: block;
    width: 100%;
    height: calc(100% - 36px);
    overflow: visible;
}

.chart-grid line {
    stroke: #ededed;
    stroke-width: 1;
    stroke-dasharray: 4 5;
}

.chart-line {
    stroke-dasharray: 1200;
    stroke-dashoffset: 1200;
    animation: lineAppear 1.3s ease forwards;
}

.chart-point {
    cursor: pointer;
}

.chart-point:hover {
    r: 8;
}

.chart-labels {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    display: flex;
    justify-content: space-between;
    gap: 5px;
}

.chart-labels span {
    flex: 1;
    color: #777e89;
    text-align: center;
    font-size: 12px;
    font-weight: 600;
}

.chart-tooltip {
    position: absolute;
    z-index: 10;
    display: flex;
    flex-direction: column;
    gap: 5px;
    padding: 11px 14px;
    border-radius: 9px;
    color: white;
    background: #242424;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.16);
    transform: translate(-50%, -120%);
    pointer-events: none;
    white-space: nowrap;
}

.chart-tooltip strong {
    font-size: 12px;
}

.chart-tooltip span {
    font-size: 13px;
    font-weight: 700;
}

.chart-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 18px;
    padding-top: 17px;
    border-top: 1px solid #f0f0f0;
}

.chart-legend {
    display: flex;
    align-items: center;
    gap: 9px;
    color: #626875;
    font-size: 13px;
}

.chart-legend span {
    width: 22px;
    height: 4px;
    border-radius: 10px;
    background: #ca0708;
}

.chart-footer > span {
    color: #777e89;
    font-size: 12px;
}

@media (max-width: 768px) {
    .chart-header {
        flex-direction: column;
        gap: 13px;
    }

    .chart-header p {
        font-size: 13px;
        line-height: 1.6;
    }

    .chart-period-options {
        gap: 7px;
    }

    .chart-period-button {
        min-height: 40px;
        padding: 8px 11px;
        font-size: 12px;
    }

    .chart-summary {
        gap: 9px;
        margin: 19px 0;
        padding: 16px 0;
    }

    .chart-summary-item {
        gap: 8px;
    }

    .chart-summary-item + .chart-summary-item {
        padding-left: 9px;
    }

    .chart-summary-item span {
        min-height: 34px;
        font-size: 11px;
        line-height: 1.5;
    }

    .chart-summary-item strong {
        font-size: 13px;
        line-height: 1.4;
        overflow-wrap: anywhere;
    }

    .chart-canvas {
        height: 225px;
    }

    .chart-labels span {
        font-size: 11px;
    }

    .chart-footer {
        align-items: flex-start;
        flex-direction: column;
        gap: 10px;
    }

    .chart-legend {
        font-size: 13px;
    }

    .chart-footer > span {
        font-size: 11px;
    }
}

@media (max-width: 380px) {
    .chart-card {
        padding-left: 12px;
        padding-right: 12px;
    }

    .chart-summary-item strong {
        font-size: 11px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .chart-section,
    .chart-line,
    .chart-card,
    .chart-card *,
    .chart-card *::before,
    .chart-card *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}
</style>
