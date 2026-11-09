<script setup>
import { ref, computed } from "vue";
import { router, usePage } from "@inertiajs/vue3";

// SHARED COMPONENTS
import AdminHeader from "@/Components/Admin/Shared/AdminHeader.vue";
import AdminLogoutModal from "@/Components/Admin/Shared/AdminLogoutModal.vue";

// FINANCE COMPONENTS
import FinanceFilter from "@/Components/Admin/Finance/FinanceFilter.vue";
import FinanceSummary from "@/Components/Admin/Finance/FinanceSummary.vue";
import FinanceBreakdown from "@/Components/Admin/Finance/FinanceBreakdown.vue";
import FinanceTransactions from "@/Components/Admin/Finance/FinanceTransactions.vue";

// SHARED NAVIGATION
import MenuBottomNavigation from "@/Components/Admin/Menu/MenuBottomNavigation.vue";

// COMPOSABLE
import useFinanceReport from "@/Composables/useFinanceReport.js";

// STYLES
import "@/css/finance-report.css";

// PAGE PROPS
const props = defineProps({
    summary: {
        type: Object,
        default: () => ({
            total_income: 0,
            total_expense: 0,
            net_profit: 0,
        }),
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    breakdown: {
        type: Array,
        default: () => [],
    },
    transactions: {
        type: Array,
        default: () => [],
    },
    hasData: {
        type: Boolean,
        default: false,
    },
});

// AUTHENTICATION
const page = usePage();

const adminName = computed(() => {
    return page.props.auth?.user?.name || "Admin";
});

const adminEmail = computed(() => {
    return page.props.auth?.user?.email || "-";
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
};

// FINANCE REPORT
const {
    selectedPeriod,
    startDate,
    endDate,
    summary,
    formatRupiah,
    resetFilter,
} = useFinanceReport(props.summary, props.filters);

// DATA AVAILABILITY
const hasFinanceData = computed(() => {
    return (
        props.hasData ||
        props.transactions.length > 0 ||
        props.breakdown.length > 0
    );
});

// FILTER HANDLERS
const changePeriod = (period) => {
    selectedPeriod.value = period;
    startDate.value = "";
    endDate.value = "";
};

const handleResetFilter = () => {
    resetFilter();
};
</script>

<template>
    <div class="finance-page">

        <!-- HEADER -->
        <AdminHeader
            :admin-name="adminName"
            :admin-email="adminEmail"
            :current-date="currentDate"
            @logout-request="openLogoutConfirmation"
        />

        <!-- MAIN CONTENT -->
        <main class="finance-main-content">
            <div class="finance-container">

                <!-- PAGE HEADING -->
                <section class="finance-page-heading">
                    <h1>Laporan Keuangan</h1>
                    <p>
                        Pantau pemasukan, pengeluaran, dan keuntungan
                        UMKM Ayam Jagal secara terstruktur.
                    </p>
                </section>

                <!-- FILTER -->
                <FinanceFilter
                    :selected-period="selectedPeriod"
                    :start-date="startDate"
                    :end-date="endDate"
                    @change-period="changePeriod"
                    @update:start-date="startDate = $event"
                    @update:end-date="endDate = $event"
                    @reset="handleResetFilter"
                />

                <!-- SUMMARY -->
                <FinanceSummary
                    v-if="hasFinanceData"
                    :summary="summary"
                    :format-rupiah="formatRupiah"
                />

                <!-- DATA NOT AVAILABLE -->
                <section
                    v-if="!hasFinanceData"
                    class="finance-data-notice"
                >
                    <div class="finance-notice-icon">
                        <svg viewBox="0 0 24 24" fill="none">
                            <circle
                                cx="12"
                                cy="12"
                                r="9"
                                stroke="currentColor"
                                stroke-width="1.7"
                            />
                            <path
                                d="M12 11V16M12 8H12.01"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            />
                        </svg>
                    </div>

                    <div>
                        <h3>Data Keuangan Belum Tersedia</h3>
                        <p>
                            Ringkasan laporan keuangan akan ditampilkan
                            setelah data dari sistem tersedia.
                        </p>
                    </div>
                </section>

                <!-- BREAKDOWN -->
                <FinanceBreakdown
                    :breakdown="breakdown"
                    :format-rupiah="formatRupiah"
                />

                <!-- TRANSACTIONS -->
                <FinanceTransactions
                    :transactions="transactions"
                    :format-rupiah="formatRupiah"
                />

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

<style scoped>
.finance-main-content {
    width: 100%;
    min-height: calc(100vh - 82px);
    background: #f5f6f8;
}

.finance-data-notice {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px 22px;
    margin-bottom: 24px;
    border: 1px solid #f0dfbd;
    border-radius: 13px;
    background: #fffaf0;
    animation: financeFadeUp 0.5s ease 0.2s both;
}

.finance-notice-icon {
    width: 42px;
    height: 42px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 11px;
    color: #bd8527;
    background: #fff0d2;
}

.finance-notice-icon svg {
    width: 22px;
    height: 22px;
}

.finance-data-notice h3 {
    margin: 0;
    color: #76551d;
    font-size: 13px;
    font-weight: 750;
}

.finance-data-notice p {
    margin: 5px 0 0;
    color: #94794d;
    font-size: 12px;
    line-height: 1.6;
}

@media (max-width: 600px) {
    .finance-data-notice {
        align-items: flex-start;
        padding: 16px;
    }
}
</style>
