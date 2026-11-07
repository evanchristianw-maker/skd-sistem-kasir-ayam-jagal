<script setup>
import { ref, computed, watch } from "vue";
import { router, usePage } from "@inertiajs/vue3";

// SHARED COMPONENTS
import AdminHeader from "@/Components/Admin/Shared/AdminHeader.vue";
import AdminLogoutModal from "@/Components/Admin/Shared/AdminLogoutModal.vue";

// SALES COMPONENTS
import SalesFilter from "@/Components/Admin/Reports/Sales/SalesFilter.vue";
import SalesSummary from "@/Components/Admin/Reports/Sales/SalesSummary.vue";

// COMPOSABLE
import useSalesReport from "@/Composables/useSalesReport.js";

// STYLES
import "@/css/sales-report.css";

// AUTHENTICATION
const page = usePage();

const adminName = computed(() => {
    return page.props.auth?.user?.name || "Admin";
});

const adminEmail = computed(() => {
    return page.props.auth?.user?.email || "adminayamjagal@gmail.com";
});

const currentDate = computed(() => {
    return new Date().toLocaleDateString("id-ID", {
        weekday: "long",
        day: "numeric",
        month: "long",
        year: "numeric",
    });
});

// LOGOUT MANAGEMENT
const showingLogoutConfirmation = ref(false);

const openLogoutConfirmation = () => {
    showingLogoutConfirmation.value = true;
};

const cancelLogout = () => {
    showingLogoutConfirmation.value = false;
};

const logout = () => {
    router.post(route("logout"));
    showingLogoutConfirmation.value = false;
};

// SALES REPORT MANAGEMENT
const {
    transactions,
    selectedPeriod,
    startDate,
    endDate,
    filteredTransactions,
    summary,
    formatRupiah,
    formatNumber,
    formatDateIndonesia,
    setPeriod,
    resetFilter,
} = useSalesReport(
    page.props.transactions ?? [],
    page.props.filters ?? {}
);

// Memperbarui data ketika Laravel mengirim respons baru
watch(
    () => page.props.transactions,
    (newTransactions) => {
        transactions.value = newTransactions ?? [];
    }
);

// Memperbarui filter ketika respons Laravel diterima
watch(
    () => page.props.filters,
    (newFilters) => {
        if (!newFilters) return;

        selectedPeriod.value = newFilters.period || "all";
        startDate.value = newFilters.start_date || "";
        endDate.value = newFilters.end_date || "";
    }
);

// MENGIRIM FILTER KE LARAVEL
const applyFilter = () => {
    router.get(
        "/sales",
        {
            period: selectedPeriod.value,
            start_date: startDate.value || null,
            end_date: endDate.value || null,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
};

// PILIH PERIODE
const handlePeriodChange = (period) => {
    setPeriod(period);
    applyFilter();
};

// UBAH TANGGAL MULAI
const handleStartDateChange = (date) => {
    startDate.value = date;
    selectedPeriod.value = "custom";
    applyFilter();
};

// UBAH TANGGAL AKHIR
const handleEndDateChange = (date) => {
    endDate.value = date;
    selectedPeriod.value = "custom";
    applyFilter();
};

// RESET FILTER
const handleResetFilter = () => {
    resetFilter();
    applyFilter();
};
</script>

<template>
    <div class="app-layout sales-report-page">

        <!-- HEADER -->
        <AdminHeader
            :admin-name="adminName"
            :admin-email="adminEmail"
            :current-date="currentDate"
            @logout-request="openLogoutConfirmation"
        />

        <!-- MAIN CONTENT -->
        <main class="main-content">
            <div class="sales-container">

                <!-- PAGE HEADING -->
                <section class="sales-page-heading">
                    <div>
                        <span class="sales-eyebrow">
                            ANALISIS BISNIS
                        </span>

                        <h1>Laporan Penjualan</h1>

                        <p>
                            Pantau performa penjualan dan aktivitas transaksi
                            UMKM Ayam Jagal.
                        </p>
                    </div>
                </section>

                <!-- FILTER -->
                <SalesFilter
                    :selected-period="selectedPeriod"
                    :start-date="startDate"
                    :end-date="endDate"
                    @change-period="handlePeriodChange"
                    @update:start-date="handleStartDateChange"
                    @update:end-date="handleEndDateChange"
                    @reset="handleResetFilter"
                />

                <!-- SUMMARY -->
                <SalesSummary
                    :summary="summary"
                    :format-rupiah="formatRupiah"
                    :format-number="formatNumber"
                />

                <!-- TRANSACTION HISTORY -->
                <section class="sales-content-card">
                    <div class="sales-section-heading">
                        <div>
                            <h2>Riwayat Transaksi Penjualan</h2>
                            <p>
                                Daftar transaksi berdasarkan periode yang
                                dipilih.
                            </p>
                        </div>

                        <span class="sales-transaction-count">
                            {{ filteredTransactions.length }} Transaksi
                        </span>
                    </div>

                    <div class="sales-table-wrapper">
                        <table class="sales-table">
                            <thead>
                                <tr>
                                    <th>ID Transaksi</th>
                                    <th>Tanggal</th>
                                    <th>Kasir</th>
                                    <th>Jumlah Item</th>
                                    <th>Metode Pembayaran</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr
                                    v-for="transaction in filteredTransactions"
                                    :key="transaction.id"
                                >
                                    <td class="transaction-id">
                                        {{ transaction.id }}
                                    </td>

                                    <td>
                                        <div class="transaction-date">
                                            {{ formatDateIndonesia(transaction.date) }}
                                            <small>{{ transaction.time }}</small>
                                        </div>
                                    </td>

                                    <td>{{ transaction.cashier }}</td>

                                    <td>{{ transaction.items }} Item</td>

                                    <td>
                                        <span class="payment-badge">
                                            {{ transaction.payment }}
                                        </span>
                                    </td>

                                    <td class="transaction-total">
                                        {{ formatRupiah(transaction.total) }}
                                    </td>

                                    <td>
                                        <span class="status-badge">
                                            {{ transaction.status }}
                                        </span>
                                    </td>
                                </tr>

                                <tr v-if="filteredTransactions.length === 0">
                                    <td colspan="7" class="empty-sales">
                                        Tidak ada transaksi pada periode ini.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- DATA INFORMATION -->
                <p class="sales-data-note">
                    Data laporan diambil langsung dari database sistem kasir.
                </p>

            </div>
        </main>

        <!-- LOGOUT MODAL -->
        <AdminLogoutModal
            :show="showingLogoutConfirmation"
            @cancel="cancelLogout"
            @confirm="logout"
        />

    </div>
</template>