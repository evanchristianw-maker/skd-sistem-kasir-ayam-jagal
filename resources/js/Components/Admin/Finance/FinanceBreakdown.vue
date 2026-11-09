<script setup>
defineProps({
    breakdown: {
        type: Array,
        default: () => [],
    },
    formatRupiah: {
        type: Function,
        required: true,
    },
});
</script>

<template>
    <section class="finance-breakdown" aria-labelledby="finance-breakdown-title">
        <div class="finance-section-heading">
            <div>
                <h2 id="finance-breakdown-title">Rincian Keuangan</h2>
                <p>Ringkasan pemasukan dan pengeluaran berdasarkan kategori.</p>
            </div>
        </div>

        <div v-if="breakdown.length" class="finance-breakdown-list" aria-live="polite" aria-atomic="false">
            <article
                v-for="(item, index) in breakdown"
                :key="item.id ?? index"
                class="finance-breakdown-item"
            >
                <div class="finance-breakdown-info">
                    <span class="finance-breakdown-dot" :class="item.type" aria-hidden="true"></span>
                    <div>
                        <strong>{{ item.category }}</strong>
                        <small>{{ item.type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}</small>
                    </div>
                </div>

                <strong :class="item.type">
                    {{ formatRupiah(item.amount) }}
                </strong>
            </article>
        </div>

        <div v-else class="finance-empty-state" role="status" aria-live="polite">
            <div class="finance-empty-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none">
                    <rect x="4" y="3" width="16" height="18" rx="2"
                        stroke="currentColor" stroke-width="1.7"/>
                    <path d="M8 8H16M8 12H16M8 16H13"
                        stroke="currentColor" stroke-width="1.7"
                        stroke-linecap="round"/>
                </svg>
            </div>
            <h3>Belum Ada Rincian Keuangan</h3>
            <p>Rincian keuangan akan ditampilkan setelah data tersedia.</p>
        </div>
    </section>
</template>

<style scoped>
.finance-breakdown-item {
    animation: financeBreakdownEnter 0.45s ease both;
    animation-delay: calc(var(--item-index, 0) * 0.07s);
}

.finance-breakdown-item:focus-visible {
    outline: 3px solid rgba(202, 7, 8, 0.2);
    outline-offset: 3px;
}

@keyframes financeBreakdownEnter {
    from {
        opacity: 0;
        transform: translateY(12px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (prefers-reduced-motion: reduce) {
    .finance-breakdown-item {
        animation: none;
        transition: none;
    }
}
</style>
