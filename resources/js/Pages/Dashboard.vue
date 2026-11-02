<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";

const props = defineProps({
    summary: {
        type: Object,
        default: () => ({
            total_sales: 1684000,
            transaction_count: 360,
            active_menus: 25,
            inactive_menus: 5,
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
    return page.props.auth?.user?.email || "adminayamjagal@gmail.com";
});

const showingProfileMenu = ref(false);
const showingLogoutConfirmation = ref(false);
const showingNotifications = ref(false);
const activeChartPoint = ref(null);

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

const selectedChartPeriod = ref("7d");

const chartPeriods = [
    { label: "7 Hari Terakhir", value: "7d" },
    { label: "30 Hari Terakhir", value: "30d" },
    { label: "12 Bulan Terakhir", value: "12m" },
];

const periodDescription = computed(() => {
    if (selectedChartPeriod.value === "30d") {
        return "Performa penjualan selama 30 hari terakhir.";
    }

    if (selectedChartPeriod.value === "12m") {
        return "Performa penjualan selama 12 bulan terakhir.";
    }

    return "Performa penjualan selama 7 hari terakhir.";
});

const demoChartData = () => {
    const today = new Date();
    const values = [250000, 420000, 180000, 530000, 350000, 680000, 450000];

    return Array.from({ length: 365 }, (_, index) => {
        const date = new Date(today);
        date.setHours(12, 0, 0, 0);
        date.setDate(today.getDate() - (364 - index));

        return {
            date: date.toISOString().slice(0, 10),
            total: values[index % values.length],
        };
    });
};

const normalizedSalesData = computed(() => {
    const source = props.sales_chart.length
        ? props.sales_chart
        : demoChartData();

    return source
        .map((item) => {
            const date = new Date(`${item.date}T12:00:00`);

            return {
                date,
                total: Number(item.total) || 0,
            };
        })
        .filter((item) => !Number.isNaN(item.date.getTime()))
        .sort((a, b) => a.date - b.date);
});

const chartData = computed(() => {
    const today = new Date();
    today.setHours(12, 0, 0, 0);

    if (selectedChartPeriod.value === "12m") {
        const months = [];

        for (let offset = 11; offset >= 0; offset--) {
            const monthDate = new Date(
                today.getFullYear(),
                today.getMonth() - offset,
                1,
                12,
            );
            const year = monthDate.getFullYear();
            const month = monthDate.getMonth();
            const total = normalizedSalesData.value
                .filter(
                    (item) =>
                        item.date.getFullYear() === year &&
                        item.date.getMonth() === month,
                )
                .reduce((sum, item) => sum + item.total, 0);

            months.push({
                label: monthDate.toLocaleDateString("id-ID", {
                    month: "short",
                }),
                total,
            });
        }

        return months;
    }

    const days = selectedChartPeriod.value === "30d" ? 30 : 7;
    const result = [];

    for (let offset = days - 1; offset >= 0; offset--) {
        const date = new Date(today);
        date.setDate(today.getDate() - offset);
        const key = date.toDateString();
        const total = normalizedSalesData.value
            .filter((item) => item.date.toDateString() === key)
            .reduce((sum, item) => sum + item.total, 0);

        result.push({
            label:
                selectedChartPeriod.value === "30d"
                    ? date.toLocaleDateString("id-ID", {
                          day: "numeric",
                          month: "short",
                      })
                    : date.toLocaleDateString("id-ID", { weekday: "short" }),
            total,
        });
    }

    return result;
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
        .map((point, index) => {
            return `${index === 0 ? "M" : "L"} ${point.x} ${point.y}`;
        })
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

const demoTransactions = [
    {
        id: "ID-123",
        created_at: "2026-10-20T10:30:00",
        total: 145000,
        status: "selesai",
    },
    {
        id: "ID-124",
        created_at: "2026-10-20T11:15:00",
        total: 75000,
        status: "selesai",
    },
    {
        id: "ID-125",
        created_at: "2026-10-20T12:20:00",
        total: 95000,
        status: "selesai",
    },
];

const transactions = computed(() => {
    return props.recent_transactions.length
        ? props.recent_transactions
        : demoTransactions;
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

const notifications = [
    {
        title: "Selamat datang",
        message: "Selamat bekerja di Dashboard UMKM Ayam Jagal.",
        time: "Informasi sistem",
    },
];

const notificationCount = computed(() => notifications.length);

const openProfileMenu = () => {
    showingNotifications.value = false;
    showingProfileMenu.value = !showingProfileMenu.value;
};

const openNotifications = () => {
    showingProfileMenu.value = false;
    showingNotifications.value = !showingNotifications.value;
};

const closePopups = () => {
    showingProfileMenu.value = false;
    showingNotifications.value = false;
    showingLogoutConfirmation.value = false;
};

const openLogoutConfirmation = () => {
    showingProfileMenu.value = false;
    showingLogoutConfirmation.value = true;
};

const cancelLogout = () => {
    showingLogoutConfirmation.value = false;
};

const logout = () => {
    router.post(route("logout"));
};

const handleEscape = (event) => {
    if (event.key === "Escape") closePopups();
};

onMounted(() => {
    window.addEventListener("keydown", handleEscape);
});

onBeforeUnmount(() => {
    window.removeEventListener("keydown", handleEscape);
});
</script>

<template>
    <div class="app-layout">
        <header class="app-header">
            <div class="header-container">
                <div class="header-brand">
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
                </div>

                <div class="header-right">
                    <div class="header-date">
                        <span class="date-dot"></span>
                        {{ currentDate }}
                    </div>

                    <button
                        type="button"
                        class="header-action"
                        aria-label="Notifikasi"
                        @click="openNotifications"
                    >
                        <svg viewBox="0 0 24 24" fill="none">
                            <path
                                d="M18 9C18 5.7 15.8 3 12 3C8.2 3 6 5.7 6 9C6 15 3.5 16 3.5 17.5H20.5C20.5 16 18 15 18 9Z"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M10 21H14"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            />
                        </svg>

                        <span
                            v-if="notificationCount"
                            class="notification-badge"
                        >
                            {{ notificationCount }}
                        </span>
                    </button>

                    <button
                        type="button"
                        class="profile-button"
                        @click="openProfileMenu"
                    >
                        <div class="profile-avatar-small">
                            <svg viewBox="0 0 24 24" fill="none">
                                <circle
                                    cx="12"
                                    cy="8"
                                    r="3.5"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                />
                                <path
                                    d="M5 20C5.8 16.5 8.2 14.5 12 14.5C15.8 14.5 18.2 16.5 19 20"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    stroke-linecap="round"
                                />
                            </svg>
                        </div>

                        <div class="profile-header-text">
                            <strong>{{ adminName }}</strong>
                            <span>Administrator</span>
                        </div>

                        <svg
                            class="profile-chevron"
                            viewBox="0 0 24 24"
                            fill="none"
                        >
                            <path
                                d="M7 10L12 15L17 10"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                    </button>
                </div>
            </div>

            <Transition name="popup">
                <div
                    v-if="showingProfileMenu"
                    class="dropdown-backdrop"
                    @click.self="showingProfileMenu = false"
                >
                    <div class="profile-dropdown">
                        <div class="dropdown-user">
                            <div class="dropdown-avatar">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <circle
                                        cx="12"
                                        cy="8"
                                        r="3.5"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                    />
                                    <path
                                        d="M5 20C5.8 16.5 8.2 14.5 12 14.5C15.8 14.5 18.2 16.5 19 20"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                        stroke-linecap="round"
                                    />
                                </svg>
                            </div>

                            <div class="dropdown-user-info">
                                <strong>{{ adminName }}</strong>
                                <span>{{ adminEmail }}</span>
                            </div>
                        </div>

                        <div class="dropdown-divider"></div>

                        <button
                            type="button"
                            class="dropdown-logout"
                            @click="openLogoutConfirmation"
                        >
                            <svg viewBox="0 0 24 24" fill="none">
                                <path
                                    d="M10 17L15 12L10 7M15 12H3M12 3H19C20.1 3 21 3.9 21 5V19C21 20.1 20.1 21 19 21H12"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>
                            Keluar dari akun
                        </button>
                    </div>
                </div>
            </Transition>

            <Transition name="popup">
                <div
                    v-if="showingNotifications"
                    class="dropdown-backdrop"
                    @click.self="showingNotifications = false"
                >
                    <div class="notification-dropdown">
                        <div class="notification-heading">
                            <strong>Notifikasi</strong>
                            <span>{{ notificationCount }} informasi</span>
                        </div>

                        <div
                            v-for="(notification, index) in notifications"
                            :key="index"
                            class="notification-item"
                        >
                            <div class="notification-icon">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path
                                        d="M12 3L14.5 9.5L21 12L14.5 14.5L12 21L9.5 14.5L3 12L9.5 9.5L12 3Z"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            </div>

                            <div class="notification-content">
                                <strong>{{ notification.title }}</strong>
                                <p>{{ notification.message }}</p>
                                <span>{{ notification.time }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </Transition>
        </header>

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

                <section class="summary-section">
                    <div class="section-heading">
                        <h3>Ringkasan Penjualan</h3>
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
                                            selectedChartPeriod ===
                                            period.value,
                                    }"
                                    @click="
                                        selectedChartPeriod = period.value;
                                        activeChartPoint = null;
                                    "
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
                                            v-for="(
                                                line, index
                                            ) in chartGridLines"
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
                                            @mouseenter="
                                                showChartTooltip(point)
                                            "
                                            @mouseleave="hideChartTooltip"
                                            @click="showChartTooltip(point)"
                                        />
                                    </g>
                                </svg>

                                <div
                                    v-if="activeChartPoint"
                                    class="chart-tooltip"
                                    :style="tooltipStyle"
                                >
                                    <strong>{{
                                        activeChartPoint.label
                                    }}</strong>
                                    <span>
                                        {{
                                            formatRupiah(activeChartPoint.total)
                                        }}
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

                <section class="history-section">
                    <div class="history-card">
                        <div class="history-header">
                            <div>
                                <h3>Riwayat Transaksi</h3>
                                <p>Aktivitas transaksi terbaru.</p>
                            </div>
                        </div>

                        <div
                            v-if="transactions.length"
                            class="transaction-list"
                        >
                            <div class="transaction-table-heading">
                                <span>ID TRANSAKSI</span>
                                <span>TANGGAL</span>
                                <span>TOTAL</span>
                                <span>STATUS</span>
                            </div>

                            <article
                                v-for="transaction in transactions"
                                :key="transaction.id"
                                class="transaction-item"
                            >
                                <div class="transaction-id">
                                    <div class="transaction-icon">
                                        <svg viewBox="0 0 24 24" fill="none">
                                            <rect
                                                x="4"
                                                y="3"
                                                width="16"
                                                height="18"
                                                rx="2"
                                                stroke="currentColor"
                                                stroke-width="1.7"
                                            />
                                            <path
                                                d="M8 8H16M8 12H16M8 16H13"
                                                stroke="currentColor"
                                                stroke-width="1.7"
                                                stroke-linecap="round"
                                            />
                                        </svg>
                                    </div>

                                    <strong>#{{ transaction.id }}</strong>
                                </div>

                                <div class="transaction-date">
                                    {{
                                        formatTransactionDate(
                                            transaction.created_at ||
                                                transaction.date,
                                        )
                                    }}
                                </div>

                                <div class="transaction-amount">
                                    {{ formatRupiah(transaction.total) }}
                                </div>

                                <div class="transaction-status">
                                    <span
                                        :class="
                                            transactionStatus(
                                                transaction.status,
                                            ).class
                                        "
                                    >
                                        <span class="status-dot"></span>
                                        {{
                                            transactionStatus(
                                                transaction.status,
                                            ).label
                                        }}
                                    </span>
                                </div>
                            </article>
                        </div>

                        <div v-else class="empty-state">
                            <div class="empty-icon">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <rect
                                        x="4"
                                        y="3"
                                        width="16"
                                        height="18"
                                        rx="2"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                    />
                                    <path
                                        d="M8 8H16M8 12H16M8 16H13"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                        stroke-linecap="round"
                                    />
                                </svg>
                            </div>

                            <h4>Belum Ada Transaksi</h4>
                            <p>Transaksi terbaru akan ditampilkan di sini.</p>
                        </div>
                    </div>
                </section>

                <footer class="dashboard-footer">
                    <p>
                        © {{ new Date().getFullYear() }}
                        <strong>UMKM Ayam Jagal</strong>. All rights reserved.
                    </p>
                    <span>Point of Sale System</span>
                </footer>
            </div>
        </main>

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

        <Transition name="modal">
            <div
                v-if="showingLogoutConfirmation"
                class="modal-backdrop"
                @click.self="cancelLogout"
            >
                <div
                    class="logout-modal"
                    role="dialog"
                    aria-modal="true"
                    aria-labelledby="logout-title"
                >
                    <div class="logout-modal-icon">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path
                                d="M10 17L15 12L10 7M15 12H3M12 3H19C20.1 3 21 3.9 21 5V19C21 20.1 20.1 21 19 21H12"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                    </div>

                    <h2 id="logout-title">Konfirmasi Keluar Akun</h2>
                    <p>
                        Apakah Anda yakin ingin keluar dari akun administrator?
                    </p>

                    <div class="logout-modal-actions">
                        <button
                            type="button"
                            class="modal-cancel"
                            @click="cancelLogout"
                        >
                            Batal
                        </button>

                        <button
                            type="button"
                            class="modal-confirm"
                            @click="logout"
                        >
                            Keluar Akun
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
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
    padding-bottom: 100px;
    overflow-x: hidden;
    color: var(--text-primary);
    background: var(--background);
    font-family: Inter, "Segoe UI", Arial, sans-serif;
}

.app-header {
    position: sticky;
    top: 0;
    z-index: 100;
    width: 100%;
    min-height: 82px;
    color: white;
    background: linear-gradient(110deg, #a90000, #ca0708 55%, #e92323);
    box-shadow: 0 4px 18px rgba(128, 0, 0, 0.13);
}

.header-container {
    max-width: 1180px;
    min-height: 82px;
    margin: 0 auto;
    padding: 0 28px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
}

.header-brand {
    display: flex;
    align-items: center;
    gap: 13px;
}

.brand-logo {
    width: 54px;
    height: 54px;
    flex: 0 0 54px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    border-radius: 13px;
    background: white;
}

.brand-logo img {
    width: 90%;
    height: 90%;
    object-fit: contain;
}

.brand-text h1 {
    margin: 0;
    font-size: 18px;
    font-weight: 800;
    letter-spacing: 1px;
}

.brand-text span {
    display: block;
    margin-top: 4px;
    color: rgba(255, 255, 255, 0.85);
    font-size: 12px;
}

.header-right {
    display: flex;
    align-items: center;
    gap: 18px;
}

.header-date {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
}

.date-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #ffe08a;
}

.header-action {
    position: relative;
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(255, 255, 255, 0.25);
    border-radius: 12px;
    color: white;
    background: rgba(255, 255, 255, 0.12);
    cursor: pointer;
}

.header-action svg {
    width: 24px;
    height: 24px;
}

.notification-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    min-width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 4px;
    border: 2px solid #ca0708;
    border-radius: 20px;
    color: #ca0708;
    background: white;
    font-size: 11px;
    font-weight: 800;
}

.profile-button {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 5px 10px 5px 5px;
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.12);
    cursor: pointer;
}

.profile-avatar-small {
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    color: #ca0708;
    background: white;
}

.profile-avatar-small svg {
    width: 24px;
    height: 24px;
}

.profile-header-text {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 3px;
}

.profile-header-text strong {
    font-size: 13px;
}

.profile-header-text span {
    font-size: 11px;
    color: rgba(255, 255, 255, 0.8);
}

.profile-chevron {
    width: 17px;
    height: 17px;
}

.dropdown-backdrop {
    position: fixed;
    inset: 0;
    z-index: 200;
    display: flex;
    align-items: flex-start;
    justify-content: flex-end;
    padding: 90px max(calc((100vw - 1180px) / 2 + 28px), 18px) 20px;
    background: rgba(20, 20, 20, 0.25);
    backdrop-filter: blur(2px);
}

.profile-dropdown,
.notification-dropdown {
    width: 320px;
    padding: 20px;
    border: 1px solid #eeeeee;
    border-radius: 16px;
    background: white;
    box-shadow: 0 15px 45px rgba(0, 0, 0, 0.16);
}

.dropdown-user {
    display: flex;
    align-items: center;
    gap: 13px;
}

.dropdown-avatar {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ca0708;
    background: #fff0f0;
    border-radius: 12px;
}

.dropdown-avatar svg {
    width: 28px;
    height: 28px;
}

.dropdown-user-info {
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.dropdown-user-info strong {
    color: #252525;
    font-size: 14px;
}

.dropdown-user-info span {
    overflow-wrap: anywhere;
    color: #777777;
    font-size: 12px;
}

.dropdown-divider {
    height: 1px;
    margin: 17px 0;
    background: #eeeeee;
}

.dropdown-logout {
    width: 100%;
    min-height: 48px;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 0 13px;
    color: #ca0708;
    background: #fff3f3;
    border: 1px solid #ffe0e0;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
}

.dropdown-logout svg {
    width: 21px;
    height: 21px;
}

.notification-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
}

.notification-heading strong {
    font-size: 16px;
}

.notification-heading span {
    color: #888888;
    font-size: 12px;
}

.notification-item {
    display: flex;
    gap: 12px;
    padding: 14px 0;
    border-top: 1px solid #eeeeee;
}

.notification-icon {
    width: 40px;
    height: 40px;
    flex: 0 0 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ca0708;
    background: #fff0f0;
    border-radius: 10px;
}

.notification-icon svg {
    width: 23px;
    height: 23px;
}

.notification-content strong {
    font-size: 14px;
}

.notification-content p {
    margin: 5px 0;
    color: #666666;
    font-size: 13px;
    line-height: 1.5;
}

.notification-content span {
    color: #999999;
    font-size: 11px;
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
    animation: fadeUp 0.5s ease both;
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
}

.section-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 19px;
}

.section-heading h3,
.chart-header h3,
.history-header h3 {
    margin: 0;
    color: #202124;
    font-size: 21px;
    font-weight: 800;
}

.section-heading > span {
    color: #777e89;
    font-size: 13px;
}

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
    animation: fadeUp 0.55s ease both;
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

.summary-indicator {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #777e89;
    font-size: 11px;
    font-weight: 600;
}

.summary-indicator span {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: currentColor;
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

.chart-section,
.history-section {
    margin-top: 30px;
    animation: fadeUp 0.65s ease both;
}

.chart-card,
.history-card {
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

.chart-header p,
.history-header p {
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

.history-header {
    margin-bottom: 22px;
}

.transaction-table-heading,
.transaction-item {
    display: grid;
    grid-template-columns: 1.2fr 1.5fr 1.2fr 0.9fr;
    align-items: center;
    gap: 15px;
}

.transaction-table-heading {
    padding: 14px;
    border-radius: 9px;
    color: #777e89;
    background: #f8f8f9;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.5px;
}

.transaction-item {
    min-height: 78px;
    padding: 13px 14px;
    border-bottom: 1px solid #f0f0f0;
}

.transaction-item:last-child {
    border-bottom: none;
}

.transaction-id {
    display: flex;
    align-items: center;
    gap: 11px;
}

.transaction-icon {
    width: 42px;
    height: 42px;
    flex: 0 0 42px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ca0708;
    background: #fff0f0;
    border-radius: 10px;
}

.transaction-icon svg {
    width: 23px;
    height: 23px;
}

.transaction-id strong {
    color: #33363d;
    font-size: 13px;
}

.transaction-date {
    color: #626875;
    font-size: 12px;
    line-height: 1.5;
}

.transaction-amount {
    color: #24262b;
    font-size: 14px;
    font-weight: 800;
}

.transaction-status > span {
    width: fit-content;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 8px 11px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
}

.status-success {
    color: #168652;
    background: #e9f8f0;
}

.status-pending {
    color: #b7791f;
    background: #fff5df;
}

.status-neutral {
    color: #667085;
    background: #f0f1f4;
}

.status-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: currentColor;
}

.empty-state {
    padding: 50px 15px;
    text-align: center;
}

.empty-icon {
    width: 65px;
    height: 65px;
    margin: 0 auto 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ca0708;
    background: #fff0f0;
    border-radius: 15px;
}

.empty-icon svg {
    width: 32px;
    height: 32px;
}

.empty-state h4 {
    margin: 0;
    color: #333333;
    font-size: 17px;
}

.empty-state p {
    margin: 8px 0 0;
    color: #777e89;
    font-size: 14px;
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

.modal-backdrop {
    position: fixed;
    inset: 0;
    z-index: 300;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    background: rgba(20, 20, 20, 0.58);
    backdrop-filter: blur(4px);
}

.logout-modal {
    width: 100%;
    max-width: 420px;
    padding: 32px 28px 27px;
    border: 1px solid #eeeeee;
    border-radius: 19px;
    background: white;
    text-align: center;
    box-shadow: 0 20px 65px rgba(0, 0, 0, 0.22);
}

.logout-modal-icon {
    width: 65px;
    height: 65px;
    margin: 0 auto 19px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ca0708;
    background: #fff0f0;
    border-radius: 17px;
}

.logout-modal-icon svg {
    width: 31px;
    height: 31px;
}

.logout-modal h2 {
    margin: 0;
    color: #242424;
    font-size: 21px;
    font-weight: 800;
}

.logout-modal p {
    margin: 12px 0 25px;
    color: #626875;
    font-size: 14px;
    line-height: 1.7;
}

.logout-modal-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

.logout-modal-actions button {
    min-height: 48px;
    border-radius: 10px;
    font-family: inherit;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
}

.modal-cancel {
    color: #60656f;
    border: 1px solid #e5e6e9;
    background: white;
}

.modal-confirm {
    color: white;
    border: 1px solid #ca0708;
    background: #ca0708;
}

.popup-enter-active,
.popup-leave-active,
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.2s ease;
}

.popup-enter-from,
.popup-leave-to,
.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.popup-enter-active .profile-dropdown,
.popup-enter-active .notification-dropdown,
.modal-enter-active .logout-modal {
    animation: popupAppear 0.22s ease;
}

@keyframes popupAppear {
    from {
        opacity: 0;
        transform: translateY(-8px) scale(0.97);
    }

    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@keyframes fadeUp {
    from {
        opacity: 0;
        transform: translateY(13px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes lineAppear {
    to {
        stroke-dashoffset: 0;
    }
}

@media (max-width: 900px) {
    .dashboard-container {
        padding: 30px 22px;
    }

    .summary-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .summary-card {
        min-height: 180px;
    }
}

@media (max-width: 600px) {
    .app-layout {
        padding-bottom: 100px;
    }

    .app-header,
    .header-container {
        min-height: 76px;
    }

    .header-container {
        padding: 0 15px;
    }

    .brand-logo {
        width: 48px;
        height: 48px;
        flex-basis: 48px;
        border-radius: 12px;
    }

    .brand-text h1 {
        font-size: 16px;
    }

    .brand-text span {
        font-size: 11px;
    }

    .header-right {
        gap: 10px;
    }

    .header-date {
        display: none;
    }

    .header-action {
        width: 43px;
        height: 43px;
    }

    .profile-button {
        padding: 3px;
        border: none;
        background: transparent;
    }

    .profile-avatar-small {
        width: 43px;
        height: 43px;
    }

    .profile-header-text,
    .profile-chevron {
        display: none;
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
        font-size: 15px;
        line-height: 1.7;
    }

    .welcome-date {
        display: none;
    }

    .section-heading {
        align-items: flex-start;
        flex-direction: column;
        gap: 7px;
        margin-bottom: 17px;
    }

    .section-heading h3,
    .chart-header h3,
    .history-header h3 {
        font-size: 20px;
    }

    .section-heading > span {
        font-size: 13px;
    }

    .summary-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 13px;
    }

    .summary-card {
        min-height: 180px;
        padding: 17px;
        border-radius: 15px;
    }

    .summary-icon {
        width: 45px;
        height: 45px;
        border-radius: 11px;
    }

    .summary-icon svg {
        width: 25px;
        height: 25px;
    }

    .summary-indicator {
        gap: 5px;
        font-size: 10px;
    }

    .summary-indicator span {
        width: 7px;
        height: 7px;
    }

    .summary-card-bottom {
        margin-top: 17px;
    }

    .summary-card-bottom p {
        margin-bottom: 8px;
        font-size: 13px;
    }

    .summary-card-bottom h3 {
        font-size: 22px;
        line-height: 1.35;
    }

    .summary-card-bottom > span {
        margin-top: 7px;
        font-size: 12px;
    }

    .chart-section,
    .history-section {
        margin-top: 25px;
    }

    .chart-card,
    .history-card {
        padding: 20px 16px;
        border-radius: 15px;
    }

    .chart-header {
        flex-direction: column;
        gap: 13px;
    }

    .chart-header p,
    .history-header p {
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

    .history-header {
        margin-bottom: 16px;
    }

    .transaction-table-heading {
        display: none;
    }

    .transaction-item {
        grid-template-columns: 1fr auto;
        gap: 10px;
        padding: 16px 4px;
    }

    .transaction-id {
        grid-column: 1;
        grid-row: 1;
        gap: 10px;
    }

    .transaction-icon {
        width: 39px;
        height: 39px;
        flex-basis: 39px;
    }

    .transaction-id strong {
        font-size: 13px;
    }

    .transaction-date {
        grid-column: 1;
        grid-row: 2;
        padding-left: 49px;
        font-size: 11px;
    }

    .transaction-amount {
        grid-column: 2;
        grid-row: 1;
        text-align: right;
        font-size: 13px;
    }

    .transaction-status {
        grid-column: 2;
        grid-row: 2;
        text-align: right;
    }

    .transaction-status > span {
        padding: 7px 9px;
        font-size: 10px;
    }

    .empty-state {
        padding: 40px 12px;
    }

    .empty-state h4 {
        font-size: 17px;
    }

    .empty-state p {
        font-size: 13px;
        line-height: 1.6;
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

    .dropdown-backdrop {
        padding: 85px 12px 15px;
    }

    .profile-dropdown,
    .notification-dropdown {
        width: min(320px, 100%);
    }

    .logout-modal {
        max-width: 360px;
        padding: 28px 20px 24px;
    }

    .logout-modal h2 {
        font-size: 19px;
    }

    .logout-modal p {
        font-size: 13px;
    }
}

@media (max-width: 380px) {
    .dashboard-container {
        padding-left: 12px;
        padding-right: 12px;
    }

    .summary-grid {
        gap: 10px;
    }

    .summary-card {
        padding: 13px;
    }

    .summary-card-bottom h3 {
        font-size: 19px;
    }

    .summary-card-bottom p {
        font-size: 12px;
    }

    .summary-indicator {
        font-size: 9px;
    }

    .chart-card,
    .history-card {
        padding-left: 12px;
        padding-right: 12px;
    }

    .chart-summary-item strong {
        font-size: 11px;
    }

    .nav-item span {
        font-size: 11px;
    }
}

@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}
</style>
