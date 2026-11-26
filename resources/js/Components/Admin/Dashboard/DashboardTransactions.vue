<script setup>
import { computed } from "vue";

const props = defineProps({
    transactions: {
        type: Array,
        default: () => [],
    },
    formatTransactionDate: {
        type: Function,
        required: true,
    },
    formatRupiah: {
        type: Function,
        required: true,
    },
    transactionStatus: {
        type: Function,
        required: true,
    },
});

const transactionItems = computed(() => {
    return props.transactions.map((transaction) => ({
        ...transaction,
        statusInfo: props.transactionStatus(transaction.status),
    }));
});
</script>

<template>
    <section class="history-section">
        <div class="history-card">
            <!-- HEADER -->
            <div class="history-header">
                <div class="history-heading">
                    

                    <div>
                        <h3>Riwayat Transaksi</h3>
                        <p>Aktivitas transaksi terbaru UMKM Ayam Jagal.</p>
                    </div>
                </div>

                <div class="transaction-count">
                    {{ transactions.length }} transaksi
                </div>
            </div>

            <!-- TRANSACTION TABLE -->
            <div v-if="transactionItems.length" class="transaction-list" aria-label="Daftar transaksi terbaru">
                <div class="transaction-table-heading">
                    <span>ID TRANSAKSI</span>
                    <span>TANGGAL</span>
                    <span>TOTAL PEMBAYARAN</span>
                    <span>STATUS</span>
                </div>

                <article
                    v-for="transaction in transactionItems"
                    :key="transaction.id"
                    class="transaction-item"
                >
                    <!-- ID -->
                    <div class="transaction-id">
                        <div class="transaction-icon">
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

                        <div class="transaction-id-content">
                            <strong>#{{ transaction.id }}</strong>
                            <span>Transaksi penjualan</span>
                        </div>
                    </div>

                    <!-- TANGGAL -->
                    <div class="transaction-date">
                        <svg viewBox="0 0 24 24" fill="none">
                            <rect
                                x="3"
                                y="5"
                                width="18"
                                height="16"
                                rx="2"
                                stroke="currentColor"
                                stroke-width="1.7"
                            />

                            <path
                                d="M7 3V7M17 3V7M3 10H21"
                                stroke="currentColor"
                                stroke-width="1.7"
                                stroke-linecap="round"
                            />
                        </svg>

                        <span>
                            {{
                                formatTransactionDate(
                                    transaction.created_at || transaction.date,
                                )
                            }}
                        </span>
                    </div>

                    <!-- TOTAL -->
                    <div class="transaction-amount">
                        {{ formatRupiah(transaction.total) }}
                    </div>

                    <!-- STATUS -->
                    <div class="transaction-status">
                        <span
                            :class="transaction.statusInfo.class"
                        >
                            <span class="status-dot"></span>

                            {{ transaction.statusInfo.label }}
                        </span>
                    </div>
                </article>
            </div>

            <!-- EMPTY STATE -->
            <div v-else class="empty-state">
                <div class="empty-icon">
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

                <h4>Belum Ada Transaksi</h4>

                <p>
                    Transaksi penjualan yang berhasil dilakukan akan ditampilkan
                    di bagian ini.
                </p>
            </div>

            <!-- FOOTER -->
            <div v-if="transactionItems.length" class="history-footer">
                <span>
                    Menampilkan {{ transactions.length }} transaksi terbaru
                </span>
            </div>
        </div>
    </section>
</template>

<style scoped>
* {
    box-sizing: border-box;
}

.history-section {
    width: 100%;
    margin-top: 28px;
    animation: fadeUp 0.5s ease both;
}

.history-card {
    width: 100%;
    padding: 26px;
    border: 1px solid #eceef1;
    border-radius: 16px;
    background: #ffffff;
    box-shadow: 0 3px 14px rgba(20, 25, 35, 0.035);
}

/* HEADER */

.history-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    margin-bottom: 23px;
}

.history-heading {
    display: flex;
    align-items: center;
    gap: 12px;
}



.history-header h3 {
    margin: 0;
    color: #202124;
    font-size: 20px;
    font-weight: 800;
}

.history-header p {
    margin: 6px 0 0;
    color: #858b95;
    font-size: 12px;
    line-height: 1.5;
}

.transaction-count {
    flex-shrink: 0;
    padding: 8px 12px;
    border: 1px solid #f0d0d0;
    border-radius: 20px;
    color: #ca0708;
    background: #fff5f5;
    font-size: 11px;
    font-weight: 700;
}

/* TABLE */

.transaction-table-heading,
.transaction-item {
    display: grid;
    grid-template-columns: 1.2fr 1.35fr 1.2fr 0.9fr;
    align-items: center;
    gap: 15px;
}

.transaction-table-heading {
    min-height: 44px;
    padding: 12px 14px;
    border-radius: 9px;
    color: #858b95;
    background: #f7f8fa;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.45px;
}

.transaction-item {
    min-height: 82px;
    padding: 13px 14px;
    border-bottom: 1px solid #f0f1f3;
}

.transaction-item:last-child {
    border-bottom: none;
}

/* ID TRANSAKSI */

.transaction-id {
    display: flex;
    align-items: center;
    gap: 11px;
    min-width: 0;
}

.transaction-icon {
    width: 41px;
    height: 41px;
    flex: 0 0 41px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    color: #ca0708;
    background: #fff0f0;
}

.transaction-icon svg {
    width: 22px;
    height: 22px;
}

.transaction-id-content {
    display: flex;
    flex-direction: column;
    gap: 5px;
    min-width: 0;
}

.transaction-id-content strong {
    color: #33363d;
    font-size: 13px;
    font-weight: 800;
    overflow-wrap: anywhere;
}

.transaction-id-content span {
    color: #999fa8;
    font-size: 10px;
}

/* TANGGAL */

.transaction-date {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
    color: #626875;
    font-size: 11px;
    line-height: 1.5;
}

.transaction-date svg {
    width: 16px;
    height: 16px;
    flex-shrink: 0;
    color: #9298a2;
}

/* NOMINAL */

.transaction-amount {
    color: #24262b;
    font-size: 13px;
    font-weight: 800;
    overflow-wrap: anywhere;
}

/* STATUS */

.transaction-status > span {
    width: fit-content;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 8px 10px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 700;
    white-space: nowrap;
}

.status-success {
    color: #168652;
    background: #e9f8f0;
}

.status-pending {
    color: #b7791f;
    background: #fff5df;
}

.status-neutral {
    color: #667085;
    background: #f0f1f4;
}

.status-dot {
    width: 7px;
    height: 7px;
    flex-shrink: 0;
    border-radius: 50%;
    background: currentColor;
}

/* EMPTY STATE */
.empty-state {
    width: 100%;
    min-height: 240px;

    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;

    padding: 30px 20px;
    text-align: center;
}

.empty-icon {
    width: 56px;
    height: 56px;

    display: flex;
    align-items: center;
    justify-content: center;

    align-self: center;
    flex-shrink: 0;

    margin: 0 auto 16px;

    border-radius: 15px;
    color: #ca0708;
    background: #fff0f0;
}

.empty-icon svg {
    width: 27px;
    height: 27px;
}

.empty-state h4 {
    margin: 0;

    color: #33363d;
    font-size: 16px;
    font-weight: 800;
    line-height: 1.5;
    text-align: center;
}

.empty-state p {
    width: 100%;
    max-width: 340px;

    margin: 9px auto 0;

    color: #858b95;
    font-size: 12px;
    line-height: 1.7;
    text-align: center;
}

/* FOOTER */

.history-footer {
    margin-top: 8px;
    padding-top: 15px;
    border-top: 1px solid #f0f1f3;
    color: #9298a2;
    font-size: 11px;
}

/* RESPONSIVE */

@media (max-width: 900px) {
    .transaction-table-heading,
    .transaction-item {
        grid-template-columns: 1.1fr 1.1fr 1fr 0.85fr;
        gap: 10px;
    }

    .transaction-table-heading {
        font-size: 9px;
    }

    .transaction-amount {
        font-size: 12px;
    }
}

@media (max-width: 700px) {
    .history-card {
        padding: 20px 16px;
        border-radius: 14px;
    }

    .history-header {
        margin-bottom: 18px;
    }

    .history-header h3 {
        font-size: 18px;
    }

    .history-header p {
        font-size: 11px;
    }

    .transaction-table-heading {
        display: none;
    }

    .transaction-item {
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 10px;
        padding: 16px 4px;
    }

    .transaction-id {
        grid-column: 1;
        grid-row: 1;
    }

    .transaction-date {
        grid-column: 1;
        grid-row: 2;
        padding-left: 51px;
        font-size: 10px;
    }

    .transaction-amount {
        grid-column: 2;
        grid-row: 1;
        text-align: right;
        font-size: 12px;
    }

    .transaction-status {
        grid-column: 2;
        grid-row: 2;
        text-align: right;
    }

    .transaction-status > span {
        padding: 7px 9px;
        font-size: 9px;
    }

    .transaction-count {
        padding: 7px 9px;
        font-size: 10px;
    }
}

@media (max-width: 380px) {
    .history-card {
        padding-right: 12px;
        padding-left: 12px;
    }

    .history-header h3 {
        font-size: 16px;
    }

    .history-header p {
        font-size: 10px;
    }

    .transaction-icon {
        width: 35px;
        height: 35px;
        flex-basis: 35px;
    }

    .transaction-date {
        padding-left: 45px;
    }

    .transaction-amount {
        font-size: 11px;
    }
}

@keyframes fadeUp {
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
    .history-section {
        animation: none;
    }
}
</style>



