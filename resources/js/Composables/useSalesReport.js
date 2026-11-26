import { ref, computed } from "vue";

export default function useSalesReport(
    initialTransactions = [],
    initialFilters = {},
) {
    // DATA TRANSAKSI DARI BACKEND
    const transactions = ref(initialTransactions);

    // FILTER PERIODE DARI BACKEND
    const selectedPeriod = ref(initialFilters.period || "7d");
    const startDate = ref(initialFilters.start_date || "");
    const endDate = ref(initialFilters.end_date || "");

    // FORMAT RUPIAH
    const formatRupiah = (value) => {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(Number(value) || 0);
    };

    // FORMAT ANGKA
    const formatNumber = (value) => {
        return new Intl.NumberFormat("id-ID").format(Number(value) || 0);
    };

    // FILTER TRANSAKSI
    const filteredTransactions = computed(() => {
        return [...transactions.value].sort((a, b) => {
            return (
                new Date(`${b.date}T${b.time}`) -
                new Date(`${a.date}T${a.time}`)
            );
        });
    });

    // RINGKASAN PENJUALAN
    const summary = computed(() => {
        const data = filteredTransactions.value;

        const totalSales = data.reduce(
            (total, transaction) => total + (Number(transaction.total) || 0),
            0,
        );

        const totalTransactions = data.length;

        const totalProducts = data.reduce(
            (total, transaction) => total + (Number(transaction.items) || 0),
            0,
        );

        return {
            total_sales: totalSales,
            transaction_count: totalTransactions,
            products_sold: totalProducts,
            average_transaction:
                totalTransactions > 0 ? totalSales / totalTransactions : 0,
        };
    });

    // FORMAT TANGGAL YYYY-MM-DD
    const formatDate = (date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, "0");
        const day = String(date.getDate()).padStart(2, "0");

        return `${year}-${month}-${day}`;
    };

    // MENGATUR PERIODE
    const setPeriod = (period) => {
        selectedPeriod.value = period;

        const today = new Date();
        const start = new Date(today);
        const end = new Date(today);

        if (period === "7d") {
            start.setDate(today.getDate() - 6);
        } else if (period === "30d") {
            start.setDate(today.getDate() - 29);
        } else if (period === "12m") {
            start.setMonth(today.getMonth() - 11);
            start.setDate(1);
        } else if (period === "all") {
            startDate.value = "";
            endDate.value = "";
            return;
        } else {
            return;
        }

        startDate.value = formatDate(start);
        endDate.value = formatDate(end);
    };

    // RESET FILTER
    const resetFilter = () => {
        selectedPeriod.value = "all";
        startDate.value = "";
        endDate.value = "";
    };

    // FORMAT TANGGAL INDONESIA
    const formatDateIndonesia = (date) => {
        if (!date) return "-";

        return new Date(`${date}T00:00:00`).toLocaleDateString("id-ID", {
            day: "2-digit",
            month: "short",
            year: "numeric",
        });
    };

    return {
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
    };
}