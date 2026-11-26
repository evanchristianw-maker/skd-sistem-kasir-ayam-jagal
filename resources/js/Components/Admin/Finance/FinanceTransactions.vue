<script setup>
defineProps({
    transactions: {
        type: Array,
        default: () => [],
    },
    formatRupiah: {
        type: Function,
        required: true,
    },
});

const formatMethodName = (category) => {
    if (!category) return "Penjualan";
    const lower = String(category).toLowerCase();
    if (lower === "qris") return "QRIS";
    if (lower === "tunai") return "Tunai";
    if (lower === "transfer") return "Transfer";
    return category;
};
</script>

<template>
    <section class="finance-transactions" aria-labelledby="finance-transactions-title">
        <div class="finance-section-heading">
            <div>
                <h2 id="finance-transactions-title">Riwayat Transaksi Pemasukan</h2>
                <p>Daftar transaksi penjualan berdasarkan periode yang dipilih.</p>
            </div>

            <span class="finance-transaction-count" aria-live="polite" aria-atomic="true">
                {{ transactions.length }} transaksi
            </span>
        </div>

        <div v-if="transactions.length" class="finance-table-wrapper">
            <table class="finance-table" aria-label="Daftar riwayat transaksi pemasukan">
                <thead>
                    <tr>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Kategori</th>
                        <th scope="col">Jenis</th>
                        <th scope="col">Jumlah</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="transaction in transactions" :key="transaction.id">
                        <td>{{ transaction.date || '-' }}</td>
                        <td>{{ formatMethodName(transaction.category) }}</td>
                        <td>
                            <span class="finance-type-badge income">
                                Pemasukan
                            </span>
                        </td>
                        <td class="finance-transaction-amount">
                            {{ formatRupiah(transaction.amount) }}
                        </td>
                    </tr>
                </tbody>
            </table>
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
            <h3>Belum Ada Transaksi Pemasukan</h3>
            <p>Aktivitas transaksi penjualan akan ditampilkan setelah data tersedia.</p>
        </div>
    </section>
</template>

