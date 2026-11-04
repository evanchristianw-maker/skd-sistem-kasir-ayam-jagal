import { ref, computed } from "vue";

export default function useSalesReport() {
    // FILTER PERIODE
    const selectedPeriod = ref("7d");
    const startDate = ref("");
    const endDate = ref("");

    // DATA SIMULASI TRANSAKSI
    const transactions = ref([
        {
            id: "TRX-001",
            date: "2026-11-04",
            time: "09:15",
            cashier: "Admin",
            items: 3,
            total: 75000,
            payment: "Tunai",
            status: "Selesai",
        },
        {
            id: "TRX-002",
            date: "2026-11-04",
            time: "10:30",
            cashier: "Admin",
            items: 2,
            total: 52000,
            payment: "QRIS",
            status: "Selesai",
        },
        {
            id: "TRX-003",
            date: "2026-11-03",
            time: "12:45",
            cashier: "Kasir",
            items: 4,
            total: 98000,
            payment: "Tunai",
            status: "Selesai",
        },
        {
            id: "TRX-004",
            date: "2026-11-03",
            time: "14:20",
            cashier: "Kasir",
            items: 2,
            total: 45000,
            payment: "QRIS",
            status: "Selesai",
        },
        {
            id: "TRX-005",
            date: "2026-11-02",
            time: "11:10",
            cashier: "Admin",
            items: 5,
            total: 125000,
            payment: "Tunai",
            status: "Selesai",
        },
    ]);

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
        let result = [...transactions.value];

        if (startDate.value) {
            result = result.filter(
                (transaction) => transaction.date >= startDate.value,
            );
        }

        if (endDate.value) {
            result = result.filter(
                (transaction) => transaction.date <= endDate.value,
            );
        }

        return result.sort((a, b) => {
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
            (total, transaction) => total + transaction.total,
            0,
        );

        const totalTransactions = data.length;

        const totalProducts = data.reduce(
            (total, transaction) => total + transaction.items,
            0,
        );

        const averageTransaction =
            totalTransactions > 0 ? totalSales / totalTransactions : 0;

        return {
            total_sales: totalSales,
            transaction_count: totalTransactions,
            products_sold: totalProducts,
            average_transaction: averageTransaction,
        };
    });

    // FILTER PERIODE
    const setPeriod = (period) => {
        selectedPeriod.value = period;

        const today = new Date();
        const end = new Date(today);
        const start = new Date(today);

        if (period === "7d") {
            start.setDate(today.getDate() - 6);
        } else if (period === "30d") {
            start.setDate(today.getDate() - 29);
        } else if (period === "12m") {
            start.setMonth(today.getMonth() - 11);
            start.setDate(1);
        } else {
            return;
        }

        startDate.value = formatDate(start);
        endDate.value = formatDate(end);
    };

    // FORMAT TANGGAL YYYY-MM-DD
    const formatDate = (date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, "0");
        const day = String(date.getDate()).padStart(2, "0");

        return `${year}-${month}-${day}`;
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

    // INISIALISASI PERIODE 7 HARI
    setPeriod("7d");

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
