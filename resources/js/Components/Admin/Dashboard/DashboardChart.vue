<script setup>
import { computed } from "vue";

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

const emit = defineEmits(["period-change", "show-tooltip", "hide-tooltip"]);

// Grafik memiliki data apabila setidaknya ada satu penjualan.
const hasChartData = computed(() => {
    return props.chartPoints.some((point) => Number(point.total) > 0);
});

// Untuk 30 hari, tampilkan label setiap 5 hari agar tidak bertumpuk.
const shouldShowLabel = (index) => {
    if (props.selectedChartPeriod === "30d") {
        return index % 5 === 0 || index === props.chartPoints.length - 1;
    }

    return true;
};

const getLabelStyle = (point) => ({
    left: `${(point.x / props.chartWidth) * 100}%`,
});

const selectPeriod = (value) => {
    emit("period-change", value);
};
</script>

<template>
    <section class="chart-section">
        <div class="chart-card">
            <!-- HEADER -->
            <div class="chart-header">
                <div class="chart-heading">
                    <div class="section-title">
                        
                        <h3>Grafik Penjualan</h3>
                    </div>

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
                            active: selectedChartPeriod === period.value,
                        }"
                        :aria-pressed="selectedChartPeriod === period.value"
                        @click="selectPeriod(period.value)"
                    >
                        {{ period.label }}
                    </button>
                </div>
            </div>

            <!-- RINGKASAN PENJUALAN -->
            <div class="chart-summary">
                <div class="chart-summary-item">
                    <span>Total Penjualan</span>
                    <strong>{{ formatRupiah(totalChartSales) }}</strong>
                </div>

                <div class="chart-summary-item">
                    <span>Rata-rata Harian</span>
                    <strong>{{ formatRupiah(averageChartSales) }}</strong>
                </div>

                <div class="chart-summary-item">
                    <span>Penjualan Tertinggi</span>
                    <strong>{{ formatRupiah(highestChartSales) }}</strong>
                </div>
            </div>

            <!-- AREA GRAFIK -->
            <div class="chart-visual">
                <div v-if="hasChartData" class="chart-canvas">
                    <svg
                        class="sales-chart"
                        :viewBox="`0 0 ${chartWidth} ${chartHeight}`"
                        preserveAspectRatio="none"
                        role="img"
                        aria-label="Grafik penjualan berdasarkan periode"
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
                                    stop-opacity="0.20"
                                />

                                <stop
                                    offset="100%"
                                    stop-color="#CA0708"
                                    stop-opacity="0.01"
                                />
                            </linearGradient>
                        </defs>

                        <!-- GARIS GRID -->
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

                        <!-- AREA GRAFIK -->
                        <path
                            v-if="chartAreaPath"
                            :d="chartAreaPath"
                            fill="url(#salesGradient)"
                        />

                        <!-- GARIS PENJUALAN -->
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

                        <!-- TITIK GRAFIK -->
                        <g v-for="(point, index) in chartPoints" :key="index">
                            <circle
                                :cx="point.x"
                                :cy="point.y"
                                r="9"
                                fill="#CA0708"
                                opacity="0.12"
                            />

                            <circle
                                :cx="point.x"
                                :cy="point.y"
                                r="4.5"
                                fill="#ffffff"
                                stroke="#CA0708"
                                stroke-width="2.5"
                                class="chart-point"
                                tabindex="0"
                                role="button"
                                :aria-label="`${point.label}: ${formatRupiah(point.total)}`"
                                @mouseenter="emit('show-tooltip', point)"
                                @mouseleave="emit('hide-tooltip')"
                                @focus="emit('show-tooltip', point)"
                                @blur="emit('hide-tooltip')"
                                @click="emit('show-tooltip', point)"
                                @keydown.enter.prevent="emit('show-tooltip', point)"
                                @keydown.space.prevent="emit('show-tooltip', point)"
                            />
                        </g>
                    </svg>

                    <!-- TOOLTIP -->
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

                    <!-- LABEL TANGGAL -->
                    <div class="chart-labels">
                        <span
                            v-for="(point, index) in chartPoints"
                            v-show="shouldShowLabel(index)"
                            :key="index"
                            :style="getLabelStyle(point)"
                        >
                            {{ point.label }}
                        </span>
                    </div>
                </div>

                <!-- EMPTY STATE -->
                <div v-else class="chart-empty-state">
                    <div class="empty-chart-icon">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path
                                d="M3 19V5M3 19H21"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            />

                            <path
                                d="M7 15L11 11L14 13L20 6"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                    </div>

                    <h4>Belum Ada Data Penjualan</h4>

                    <p>
                        Grafik akan menampilkan performa penjualan setelah
                        transaksi tersedia.
                    </p>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="chart-footer">
                <div class="chart-legend">
                    Penjualan
                </div>

                <span> Data berdasarkan transaksi penjualan </span>
            </div>
        </div>
    </section>
</template>

<style scoped>
* {
    box-sizing: border-box;
}

.chart-section {
    width: 100%;
    margin-top: 28px;
    animation: fadeUp 0.5s ease both;
}

.chart-card {
    width: 100%;
    padding: 26px;
    border: 1px solid #eceef1;
    border-radius: 16px;
    background: #ffffff;
    box-shadow: 0 3px 14px rgba(20, 25, 35, 0.035);
}

/* HEADER */

.chart-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 20px;
}

.chart-heading {
    min-width: 0;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 10px;
}



.chart-header h3 {
    margin: 0;
    color: #202124;
    font-size: 20px;
    font-weight: 800;
}

.chart-header p {
    margin: 9px 0 0 14px;
    color: #777e89;
    font-size: 13px;
    line-height: 1.5;
}

/* FILTER PERIODE */

.chart-period-options {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-end;
    gap: 7px;
}

.chart-period-button {
    min-height: 38px;
    padding: 8px 13px;
    border: 1px solid #e9eaed;
    border-radius: 8px;
    color: #626875;
    background: #ffffff;
    font-family: inherit;
    font-size: 12px;
    font-weight: 700;
    white-space: nowrap;
    cursor: pointer;
    transition: all 0.2s ease;
}

.chart-period-button:hover {
    color: #ca0708;
    border-color: #f0baba;
    background: #fff7f7;
}

.chart-period-button.active {
    color: #ca0708;
    border-color: #f0c5c5;
    background: #fff0f0;
}

.chart-period-button:focus-visible {
    outline: 3px solid rgba(202, 7, 8, 0.2);
    outline-offset: 2px;
}

/* RINGKASAN */

.chart-summary {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 15px;
    margin: 25px 0;
    padding: 20px 0;
    border-top: 1px solid #f0f0f0;
    border-bottom: 1px solid #f0f0f0;
}

.chart-summary-item {
    display: flex;
    flex-direction: column;
    gap: 9px;
    min-width: 0;
}

.chart-summary-item + .chart-summary-item {
    padding-left: 20px;
    border-left: 1px solid #eeeeee;
}

.chart-summary-item span {
    color: #777e89;
    font-size: 12px;
    font-weight: 500;
}

.chart-summary-item strong {
    color: #25272c;
    font-size: 18px;
    font-weight: 800;
    line-height: 1.4;
    overflow-wrap: anywhere;
}

/* GRAFIK */

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
    animation: lineAppear 0.8s ease both;
}

.chart-point {
    cursor: pointer;
    outline: none;
}

.chart-point:focus-visible {
    stroke-width: 4;
    outline: none;
    filter: drop-shadow(0 0 3px rgba(202, 7, 8, 0.35));
}

/* LABEL TANGGAL */

.chart-labels {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 25px;
}

.chart-labels span {
    position: absolute;
    transform: translateX(-50%);
    color: #777e89;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
}

/* TOOLTIP */

.chart-tooltip {
    position: absolute;
    z-index: 10;
    display: flex;
    flex-direction: column;
    gap: 5px;
    padding: 11px 13px;
    border-radius: 9px;
    color: #ffffff;
    background: #252525;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
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

/* EMPTY STATE */

.chart-empty-state {
    min-height: 260px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 30px 15px;
    text-align: center;
}

.empty-chart-icon {
    width: 58px;
    height: 58px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 15px;
    border-radius: 15px;
    color: #ca0708;
    background: #fff0f0;
}

.empty-chart-icon svg {
    width: 29px;
    height: 29px;
}

.chart-empty-state h4 {
    margin: 0;
    color: #33363d;
    font-size: 16px;
    font-weight: 800;
}

.chart-empty-state p {
    max-width: 340px;
    margin: 9px 0 0;
    color: #858b95;
    font-size: 13px;
    line-height: 1.6;
}

/* FOOTER */

.chart-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-top: 20px;
    padding-top: 16px;
    border-top: 1px solid #f0f0f0;
}

.chart-legend {
    display: flex;
    align-items: center;
    gap: 9px;
    color: #626875;
    font-size: 12px;
    font-weight: 600;
}

.chart-legend::before {
    content: "";
    width: 9px;
    height: 9px;
    flex-shrink: 0;
    border-radius: 50%;
    background: #ca0708;
    box-shadow: 0 0 0 3px #fff0f0;
}



.chart-footer > span {
    color: #858b95;
    font-size: 11px;
}

/* RESPONSIVE */

@media (max-width: 900px) {
    .chart-header {
        flex-direction: column;
        gap: 17px;
    }

    .chart-period-options {
        justify-content: flex-start;
    }

    .chart-period-button {
        font-size: 11px;
    }
}

@media (max-width: 600px) {
    .chart-section {
        margin-top: 24px;
    }

    .chart-card {
        padding: 19px 16px;
        border-radius: 14px;
    }

    .chart-header h3 {
        font-size: 19px;
    }

    .chart-header p {
        font-size: 12px;
    }

    .chart-period-options {
        width: 100%;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 7px;
    }

    .chart-period-button {
        width: 100%;
        min-height: 39px;
        padding: 7px 4px;
        font-size: 10px;
    }

    .chart-summary {
        gap: 9px;
        margin: 20px 0;
        padding: 16px 0;
    }

    .chart-summary-item {
        gap: 7px;
    }

    .chart-summary-item + .chart-summary-item {
        padding-left: 9px;
    }

    .chart-summary-item span {
        min-height: 32px;
        font-size: 10px;
        line-height: 1.5;
    }

    .chart-summary-item strong {
        font-size: 12px;
    }

    .chart-canvas {
        height: 225px;
    }

    .chart-labels span {
        font-size: 10px;
    }

    .chart-footer {
        align-items: flex-start;
        flex-direction: column;
        gap: 10px;
    }
}

@media (max-width: 350px) {
    .chart-card {
        padding-right: 12px;
        padding-left: 12px;
    }

    .chart-summary-item strong {
        font-size: 10px;
    }

    .chart-period-button {
        font-size: 9px;
    }
}

@keyframes fadeUp {
    from {
        opacity: 0;
        transform: translateY(12px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes lineAppear {
    from {
        opacity: 0;
    }

    to {
        opacity: 1;
    }
}

@media (prefers-reduced-motion: reduce) {
    .chart-section,
    .chart-line {
        animation: none;
    }

    .chart-period-button {
        transition: none;
    }
}
</style>


