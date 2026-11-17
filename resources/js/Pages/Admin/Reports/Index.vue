<script setup>
import { ref, computed, watch } from "vue";
import { router, usePage } from "@inertiajs/vue3";

// SHARED COMPONENTS
import AdminHeader from "@/Components/Admin/Shared/AdminHeader.vue";
import AdminLogoutModal from "@/Components/Admin/Shared/AdminLogoutModal.vue";
import MenuBottomNavigation from "@/Components/Admin/Menu/MenuBottomNavigation.vue";

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
        <main class="main-content" aria-labelledby="sales-page-title">
            <div class="sales-container">

                <!-- PAGE HEADING -->
                <section class="sales-page-heading">
                    <div>

                        <h1 id="sales-page-title">Laporan Penjualan</h1>

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
                <section class="sales-content-card" aria-labelledby="sales-history-title">
                    <div class="sales-section-heading">
                        <div>
                            <h2 id="sales-history-title">Riwayat Transaksi Penjualan</h2>
                            <p>
                                Daftar transaksi berdasarkan periode yang
                                dipilih.
                            </p>
                        </div>

                        <span class="sales-transaction-count" aria-live="polite" aria-atomic="true">
                            {{ filteredTransactions.length }} transaksi
                        </span>
                    </div>

                    <!-- TRANSACTION TABLE -->
                    <div
                        v-if="filteredTransactions.length > 0"
                        class="sales-table-wrapper"
                    >
                        <table class="sales-table">
                            <caption class="sr-only">Daftar riwayat transaksi penjualan</caption>
                            <thead>
                                <tr>
                                    <th scope="col">ID Transaksi</th>
                                    <th scope="col">Tanggal</th>
                                    <th scope="col">Kasir</th>
                                    <th scope="col">Jumlah Item</th>
                                    <th scope="col">Metode Pembayaran</th>
                                    <th scope="col">Total</th>
                                    <th scope="col">Status</th>
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

                                    <td class="transaction-cashier">Kasir</td>

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
                            </tbody>
                        </table>
                    </div>

                    <!-- EMPTY STATE -->
                    <div v-else class="sales-empty-state" role="status" aria-live="polite">
                        <div class="sales-empty-icon">
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

                        <h3>Belum Ada Transaksi</h3>

                        <p>
                            Transaksi penjualan yang berhasil dilakukan akan
                            ditampilkan di bagian ini.
                        </p>
                    </div>
                </section>

                <!-- FOOTER -->
                <footer class="dashboard-footer">
                    <p>
                        &copy; {{ new Date().getFullYear() }}
                        <strong>UMKM Ayam Jagal</strong>. All rights reserved.
                    </p>

                    <span>Point of Sale System</span>
                </footer>

            </div>
        </main>

        <!-- BOTTOM NAVIGATION -->
        <MenuBottomNavigation />

        <!-- LOGOUT MODAL -->
        <AdminLogoutModal
            :show="showingLogoutConfirmation"
            @cancel="cancelLogout"
            @confirm="logout"
        />

    </div>
</template>







