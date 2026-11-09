<script setup>
defineProps({
    summary: {
        type: Object,
        required: true,
    },
    formatRupiah: {
        type: Function,
        required: true,
    },
    formatNumber: {
        type: Function,
        required: true,
    },
});

const cards = [
    {
        key: "total_sales",
        label: "Total Penjualan",
        icon: "sales",
        type: "currency",
    },
    {
        key: "transaction_count",
        label: "Total Transaksi",
        icon: "transactions",
        type: "number",
    },
    {
        key: "products_sold",
        label: "Produk Terjual",
        icon: "products",
        type: "number",
    },
    {
        key: "average_transaction",
        label: "Rata-rata Transaksi",
        icon: "average",
        type: "currency",
    },
];
</script>

<template>
    <section class="sales-summary">
        <article
            v-for="card in cards"
            :key="card.key"
            class="sales-summary-card"
        >
            <div class="summary-card-top">
                <span class="summary-card-label">
                    {{ card.label }}
                </span>

                <div class="summary-card-icon" :class="`icon-${card.icon}`">
                    <svg
                        v-if="card.icon === 'sales' || card.icon === 'average'"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                    </svg>

                    <svg
                        v-else-if="card.icon === 'transactions'"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <rect x="4" y="3" width="16" height="18" rx="2" />
                        <path d="M8 8h8M8 12h8M8 16h5" />
                    </svg>

                    <svg
                        v-else
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M3 3v18h18" />
                        <path d="m7 14 4-4 4 3 6-7" />
                    </svg>
                </div>
            </div>

            <h3 class="summary-card-value">
                {{
                    card.type === "currency"
                        ? formatRupiah(summary[card.key])
                        : formatNumber(summary[card.key])
                }}
            </h3>

            <span class="summary-card-caption">
                Berdasarkan filter laporan
            </span>
        </article>
    </section>
</template>



