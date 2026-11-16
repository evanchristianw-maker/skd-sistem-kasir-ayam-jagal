<script setup>
defineProps({
    summary: {
        type: Object,
        default: () => ({
            total_income: 0,
            total_expense: 0,
            net_profit: 0,
        }),
    },
    formatRupiah: {
        type: Function,
        required: true,
    },
});
</script>

<template>
    <section class="finance-summary finance-summary-income-only">
        <article class="finance-summary-card">
            <div class="finance-summary-top">
                <span>Total Pemasukan</span>
                <div class="finance-summary-icon income">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                    </svg>
                </div>
            </div>
            <h3>{{ formatRupiah(summary.total_income) }}</h3>
            <p>Akumulasi seluruh pemasukan dari transaksi penjualan</p>
        </article>
    </section>
</template>
