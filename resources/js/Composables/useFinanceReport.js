import { ref, computed, watch } from "vue";

export default function useFinanceReport(props = {}) {
    const selectedPeriod = ref(props.filters?.period || "7d");
    const startDate = ref(props.filters?.start_date || "");
    const endDate = ref(props.filters?.end_date || "");

    // Mengikuti perubahan props dari Laravel/Inertia.
    const summary = computed(() => ({
        total_income: Number(props.summary?.total_income) || 0,
        total_expense: Number(props.summary?.total_expense) || 0,
        net_profit: Number(props.summary?.net_profit) || 0,
    }));

    watch(
        () => props.filters,
        (filters) => {
            selectedPeriod.value = filters?.period || "7d";
            startDate.value = filters?.start_date || "";
            endDate.value = filters?.end_date || "";
        },
        { deep: true },
    );

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
