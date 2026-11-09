<script setup>
import { ref, computed } from "vue";
import { router, usePage } from "@inertiajs/vue3";

import AdminHeader from "@/Components/Admin/Shared/AdminHeader.vue";
import AdminLogoutModal from "@/Components/Admin/Shared/AdminLogoutModal.vue";

import MenuBottomNavigation from "@/Components/Admin/Menu/MenuBottomNavigation.vue";
import DashboardSummary from "@/Components/Admin/Dashboard/DashboardSummary.vue";
import DashboardChart from "@/Components/Admin/Dashboard/DashboardChart.vue";
import DashboardTransactions from "@/Components/Admin/Dashboard/DashboardTransactions.vue";

const props = defineProps({
    summary: {
        type: Object,
        default: () => ({
            total_sales: 0,
            transaction_count: 0,
            active_menus: 0,
            inactive_menus: 0,
        }),
    },

    sales_chart: {
        type: Array,
        default: () => [],
    },

    recent_transactions: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();

const adminName = computed(() => {
    return page.props.auth?.user?.name || "Admin";
});

const adminEmail = computed(() => {
    return page.props.auth?.user?.email || "-";
});

const showingLogoutConfirmation = ref(false);
const activeChartPoint = ref(null);
const selectedChartPeriod = ref("7d");

const formatRupiah = (value) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(Number(value) || 0);
};

const formatNumber = (value) => {
    return new Intl.NumberFormat("id-ID").format(Number(value) || 0);
};

const currentDate = computed(() => {
    return new Date().toLocaleDateString("id-ID", {
        weekday: "long",
        day: "numeric",
        month: "long",
        year: "numeric",
    });
});

const summaryCards = computed(() => [
    {
        title: "Total Penjualan",
        value: formatRupiah(props.summary.total_sales),
        description: "Total pendapatan",
        icon: "money",
        color: "red",
    },
    {
        title: "Jumlah Transaksi",
        value: formatNumber(props.summary.transaction_count),
        description: "Total transaksi",
        icon: "transaction",
        color: "orange",
    },
    {
        title: "Menu Aktif",
        value: formatNumber(props.summary.active_menus),
        description: "Menu tersedia",
        icon: "menu",
        color: "green",
    },
    {
        title: "Menu Nonaktif",
        value: formatNumber(props.summary.inactive_menus),
        description: "Menu tidak tersedia",
        icon: "inactive",
        color: "gray",
    },
]);

const chartPeriods = [
    { label: "7 Hari Terakhir", value: "7d" },
    { label: "30 Hari Terakhir", value: "30d" },
    { label: "12 Bulan Terakhir", value: "12m" },
];

const periodDescription = computed(() => {
    const descriptions = {
        "7d": "Performa penjualan selama 7 hari terakhir.",
        "30d": "Performa penjualan selama 30 hari terakhir.",
        "12m": "Performa penjualan selama 12 bulan terakhir.",
    };

    return descriptions[selectedChartPeriod.value];
});

const normalizedSalesData = computed(() => {
    return props.sales_chart
        .map((item) => {
            const rawDate = String(item.date || "");

            const date = new Date(
                rawDate.length === 10 ? `${rawDate}T12:00:00` : rawDate,
            );

            return {
                date,
                total: Number(item.total) || 0,
            };
        })
        .filter((item) => !Number.isNaN(item.date.getTime()))
        .sort((a, b) => a.date - b.date);
});

const dateKey = (date) => {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const day = String(date.getDate()).padStart(2, "0");

    return `${year}-${month}-${day}`;
};

const chartData = computed(() => {
    const today = new Date();
    today.setHours(12, 0, 0, 0);

    const dailySales = new Map();
    const monthlySales = new Map();

    normalizedSalesData.value.forEach((item) => {
        const dailyKey = dateKey(item.date);

        const monthlyKey = `${item.date.getFullYear()}-${String(
            item.date.getMonth() + 1,
        ).padStart(2, "0")}`;

        dailySales.set(
            dailyKey,
            (dailySales.get(dailyKey) || 0) + item.total,
        );

        monthlySales.set(
            monthlyKey,
            (monthlySales.get(monthlyKey) || 0) + item.total,
        );
    });

    if (selectedChartPeriod.value === "12m") {
        return Array.from({ length: 12 }, (_, index) => {
            const monthDate = new Date(
                today.getFullYear(),
                today.getMonth() - 11 + index,
                1,
                12,
            );

            const key = `${monthDate.getFullYear()}-${String(
                monthDate.getMonth() + 1,
            ).padStart(2, "0")}`;

            return {
                label: monthDate.toLocaleDateString("id-ID", {
                    month: "short",
                }),
                total: monthlySales.get(key) || 0,
            };
        });
    }

    const days = selectedChartPeriod.value === "30d" ? 30 : 7;

    return Array.from({ length: days }, (_, index) => {
        const date = new Date(today);
        date.setDate(today.getDate() - (days - 1 - index));

        return {
            label:
                selectedChartPeriod.value === "30d"
                    ? date.toLocaleDateString("id-ID", {
                          day: "numeric",
                          month: "short",
                      })
                    : date.toLocaleDateString("id-ID", {
                          weekday: "short",
                      }),
            total: dailySales.get(dateKey(date)) || 0,
        };
    });
});

const chartWidth = 600;
const chartHeight = 240;

const chartPadding = {
    top: 25,
    right: 20,
    bottom: 35,
    left: 20,
};

const chartPoints = computed(() => {
    const data = chartData.value;

    if (!data.length) return [];

    const maxValue = Math.max(...data.map((item) => item.total), 100000);

    const usableWidth = chartWidth - chartPadding.left - chartPadding.right;

    const usableHeight = chartHeight - chartPadding.top - chartPadding.bottom;

    return data.map((item, index) => ({
        ...item,

        x:
            chartPadding.left +
            (data.length === 1
                ? usableWidth / 2
                : (index / (data.length - 1)) * usableWidth),

        y:
            chartHeight -
            chartPadding.bottom -
            (item.total / maxValue) * usableHeight,
    }));
});

const chartLinePath = computed(() => {
    return chartPoints.value
        .map(
            (point, index) =>
                `${index === 0 ? "M" : "L"} ${point.x} ${point.y}`,
        )
        .join(" ");
});

const chartAreaPath = computed(() => {
    const points = chartPoints.value;

    if (!points.length) return "";

    const first = points[0];
    const last = points[points.length - 1];

    return `
        ${chartLinePath.value}
        L ${last.x} ${chartHeight - chartPadding.bottom}
        L ${first.x} ${chartHeight - chartPadding.bottom}
        Z
    `;
});

const chartGridLines = computed(() => {
    return [0, 1, 2, 3].map((index) => {
        const usableHeight =
            chartHeight - chartPadding.top - chartPadding.bottom;

        return chartPadding.top + (usableHeight / 3) * index;
    });
});

const totalChartSales = computed(() => {
    return chartData.value.reduce((total, item) => total + item.total, 0);
});

const averageChartSales = computed(() => {
    if (!chartData.value.length) return 0;

    return Math.round(totalChartSales.value / chartData.value.length);
});

const highestChartSales = computed(() => {
    if (!chartData.value.length) return 0;

    return Math.max(...chartData.value.map((item) => item.total));
});

const showChartTooltip = (point) => {
    activeChartPoint.value = point;
};

const hideChartTooltip = () => {
    activeChartPoint.value = null;
};

const tooltipStyle = computed(() => {
    if (!activeChartPoint.value) return {};

    return {
        left: `${(activeChartPoint.value.x / chartWidth) * 100}%`,
        top: `${(activeChartPoint.value.y / chartHeight) * 100}%`,
    };
});

const transactions = computed(() => {
    return props.recent_transactions;
});

const formatTransactionDate = (date) => {
    if (!date) return "-";

    const parsedDate = new Date(date);

    if (Number.isNaN(parsedDate.getTime())) return date;

    return parsedDate.toLocaleString("id-ID", {
        day: "2-digit",
        month: "long",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

const transactionStatus = (status) => {
    const normalized = String(status || "").toLowerCase();

    if (["selesai", "success", "completed", "paid"].includes(normalized)) {
        return {
            label: "Selesai",
            class: "status-success",
        };
    }

    if (["pending", "menunggu", "process", "processing"].includes(normalized)) {
        return {
            label: "Diproses",
            class: "status-pending",
        };
    }

    return {
        label: status || "Tidak diketahui",
        class: "status-neutral",
    };
};

const handlePeriodChange = (value) => {
    selectedChartPeriod.value = value;
    activeChartPoint.value = null;
};

const openLogoutConfirmation = () => {
    showingLogoutConfirmation.value = true;
};

const cancelLogout = () => {
    showingLogoutConfirmation.value = false;
};

const logout = () => {
    router.post(route("logout"));
};
</script>

<template>
    <div class="app-layout">
        <AdminHeader
            :admin-name="adminName"
            :admin-email="adminEmail"
            :current-date="currentDate"
            @logout-request="openLogoutConfirmation"
        />

        <main class="dashboard-main">
            <div class="dashboard-container">
                <section class="welcome-section">
                    <div>
                        <h2>Dashboard Admin</h2>
                        <p>
                            Pantau aktivitas dan performa penjualan UMKM Ayam
                            Jagal.
                        </p>
                    </div>

                    <div class="welcome-date">
                        <svg viewBox="0 0 24 24" fill="none">
                            <rect
                                x="3"
                                y="5"
                                width="18"
                                height="16"
                                rx="2"
                                stroke="currentColor"
                                stroke-width="1.7"
                            />
                            <path
                                d="M7 3V7M17 3V7M3 10H21"
                                stroke="currentColor"
                                stroke-width="1.7"
                                stroke-linecap="round"
                            />
                        </svg>

                        <span>{{ currentDate }}</span>
                    </div>
                </section>

                <DashboardSummary :cards="summaryCards" />

                <DashboardChart
                    :chart-periods="chartPeriods"
                    :selected-chart-period="selectedChartPeriod"
                    :period-description="periodDescription"
                    :total-chart-sales="totalChartSales"
                    :average-chart-sales="averageChartSales"
                    :highest-chart-sales="highestChartSales"
                    :chart-width="chartWidth"
                    :chart-height="chartHeight"
                    :chart-points="chartPoints"
                    :chart-line-path="chartLinePath"
                    :chart-area-path="chartAreaPath"
                    :chart-grid-lines="chartGridLines"
                    :active-chart-point="activeChartPoint"
                    :tooltip-style="tooltipStyle"
                    :format-rupiah="formatRupiah"
                    @period-change="handlePeriodChange"
                    @show-tooltip="showChartTooltip"
                    @hide-tooltip="hideChartTooltip"
                />

                <DashboardTransactions
                    :transactions="transactions"
                    :format-transaction-date="formatTransactionDate"
                    :format-rupiah="formatRupiah"
                    :transaction-status="transactionStatus"
                />

                <footer class="dashboard-footer">
                    <p>
                        &copy; {{ new Date().getFullYear() }}
                        <strong>UMKM Ayam Jagal</strong>. All rights reserved.
                    </p>

                    <span>Point of Sale System</span>
                </footer>
            </div>
        </main>

        <MenuBottomNavigation />

        <AdminLogoutModal
            :show="showingLogoutConfirmation"
            @cancel="cancelLogout"
            @confirm="logout"
        />
    </div>
</template>

<style scoped>
* {
    box-sizing: border-box;
}

.app-layout {
    --primary: #ca0708;
    --primary-dark: #a80000;
    --text-primary: #202124;
    --text-secondary: #626875;
    --background: #f5f6f8;

    width: 100%;
    min-height: 100vh;
    min-height: 100dvh;
    margin: 0 auto;
    padding-bottom: 110px;
    overflow-x: hidden;
    color: var(--text-primary);
    background: var(--background);
    font-family: Inter, "Segoe UI", Arial, sans-serif;
}

.dashboard-main {
    width: 100%;
    min-height: calc(100vh - 82px);
    background: var(--background);
}

.dashboard-container {
    width: 100%;
    max-width: 1180px;
    margin: 0 auto;
    padding: 35px 28px 30px;
}

.welcome-section {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 34px;
    animation: welcomeFadeUp 0.5s ease both;
}

.welcome-section h2 {
    margin: 0;
    color: #202124;
    font-size: 28px;
    font-weight: 800;
    letter-spacing: -0.7px;
}

.welcome-section p {
    margin: 10px 0 0;
    color: #626875;
    font-size: 15px;
    line-height: 1.6;
}

.welcome-date {
    min-height: 48px;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 0 15px;
    border: 1px solid #e9e9e9;
    border-radius: 11px;
    color: #555b66;
    background: white;
    font-size: 13px;
    font-weight: 600;
}

.welcome-date svg {
    width: 21px;
    height: 21px;
    color: #ca0708;
    flex-shrink: 0;
}

.dashboard-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-top: 28px;
    padding: 18px 0 5px;
    border-top: 1px solid #e9eaed;
}

.dashboard-footer p,
.dashboard-footer span {
    margin: 0;
    color: #777e89;
    font-size: 12px;
}

.dashboard-footer strong {
    color: #ca0708;
}

@keyframes welcomeFadeUp {
    from {
        opacity: 0;
        transform: translateY(13px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 900px) {
    .dashboard-container {
        padding: 30px 22px;
    }
}

@media (max-width: 600px) {
    .app-layout {
        padding-bottom: 110px;
    }

    .dashboard-container {
        padding: 28px 16px 25px;
    }

    .welcome-section {
        align-items: flex-start;
        margin-bottom: 30px;
    }

    .welcome-section h2 {
        font-size: 26px;
    }

    .welcome-section p {
        max-width: 340px;
        font-size: 14px;
        line-height: 1.7;
    }

    .welcome-date {
        display: none;
    }

    .dashboard-footer {
        align-items: flex-start;
        flex-direction: column;
        gap: 9px;
    }

    .dashboard-footer p,
    .dashboard-footer span {
        font-size: 11px;
    }
}

@media (max-width: 380px) {
    .dashboard-container {
        padding-left: 12px;
        padding-right: 12px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .welcome-section {
        animation: none;
    }
}
</style>


