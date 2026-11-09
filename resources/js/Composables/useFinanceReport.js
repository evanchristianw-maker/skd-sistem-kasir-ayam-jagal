import { ref, computed } from "vue";

export default function useFinanceReport(initialSummary = {}, initialFilters = {}) {
    const selectedPeriod = ref(initialFilters.period || "7d");
    const startDate = ref(initialFilters.start_date || "");
    const endDate = ref(initialFilters.end_date || "");

    const summary = computed(() => ({
        total_income: Number(initialSummary.total_income) || 0,
        total_expense: Number(initialSummary.total_expense) || 0,
        net_profit: Number(initialSummary.net_profit) || 0,
    }));

    const formatRupiah = (value) => {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(Number(value) || 0);
    };

    const resetFilter = () => {
        selectedPeriod.value = "7d";
        startDate.value = "";
        endDate.value = "";
    };

    return {
        selectedPeriod,
        startDate,
        endDate,
        summary,
        formatRupiah,
        resetFilter,
    };
}
