<script setup>
defineProps({
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
</script>

<template>
    <section class="history-section">
        <div class="history-card">
            <div class="history-header">
                <div>
                    <h3>Riwayat Transaksi</h3>
                    <p>Aktivitas transaksi terbaru.</p>
                </div>
            </div>

            <div v-if="transactions.length" class="transaction-list">
                <div class="transaction-table-heading">
                    <span>ID TRANSAKSI</span>
                    <span>TANGGAL</span>
                    <span>TOTAL</span>
                    <span>STATUS</span>
                </div>

                <article
                    v-for="transaction in transactions"
                    :key="transaction.id"
                    class="transaction-item"
                >
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

                        <strong>#{{ transaction.id }}</strong>
                    </div>

                    <div class="transaction-date">
                        {{
                            formatTransactionDate(
                                transaction.created_at || transaction.date,
                            )
                        }}
                    </div>

                    <div class="transaction-amount">
                        {{ formatRupiah(transaction.total) }}
                    </div>

                    <div class="transaction-status">
                        <span :class="transactionStatus(transaction.status).class">
                            <span class="status-dot"></span>
                            {{ transactionStatus(transaction.status).label }}
                        </span>
                    </div>
                </article>
            </div>

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
                <p>Transaksi terbaru akan ditampilkan di sini.</p>
            </div>
        </div>
    </section>
</template>

<style scoped>
.history-section {
    margin-top: 30px;
    animation: fadeUp 0.65s ease both;
}

.history-card {
    padding: 26px;
    border: 1px solid #ededed;
    border-radius: 17px;
    background: white;
    box-shadow: 0 4px 15px rgba(20, 20, 20, 0.035);
}

.history-header {
    margin-bottom: 22px;
}

.history-header h3 {
    margin: 0;
    color: #202124;
    font-size: 21px;
    font-weight: 800;
}

.history-header p {
    margin: 8px 0 0;
    color: #777e89;
    font-size: 14px;
}

.transaction-table-heading,
.transaction-item {
    display: grid;
    grid-template-columns: 1.2fr 1.5fr 1.2fr 0.9fr;
    align-items: center;
    gap: 15px;
}

.transaction-table-heading {
    padding: 14px;
    border-radius: 9px;
    color: #777e89;
    background: #f8f8f9;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.5px;
}

.transaction-item {
    min-height: 78px;
    padding: 13px 14px;
    border-bottom: 1px solid #f0f0f0;
}

.transaction-item:last-child {
    border-bottom: none;
}

.transaction-id {
    display: flex;
    align-items: center;
    gap: 11px;
}

.transaction-icon {
    width: 42px;
    height: 42px;
    flex: 0 0 42px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ca0708;
    background: #fff0f0;
    border-radius: 10px;
}

.transaction-icon svg {
    width: 23px;
    height: 23px;
}

.transaction-id strong {
    color: #33363d;
    font-size: 13px;
}

.transaction-date {
    color: #626875;
    font-size: 12px;
    line-height: 1.5;
}

.transaction-amount {
    color: #24262b;
    font-size: 14px;
    font-weight: 800;
}

.transaction-status > span {
    width: fit-content;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 8px 11px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
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
    border-radius: 50%;
    background: currentColor;
}

.empty-state {
    padding: 50px 15px;
    text-align: center;
}

.empty-icon {
    width: 65px;
    height: 65px;
    margin: 0 auto 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ca0708;
    background: #fff0f0;
    border-radius: 15px;
}

.empty-icon svg {
    width: 32px;
    height: 32px;
}

.empty-state h4 {
    margin: 0;
    color: #333333;
    font-size: 17px;
}

.empty-state p {
    margin: 8px 0 0;
    color: #777e89;
    font-size: 14px;
}

@media (max-width: 768px) {
    .history-section {
        margin-top: 25px;
    }

    .history-card {
        padding: 20px 16px;
        border-radius: 15px;
    }

    .history-header {
        margin-bottom: 16px;
    }

    .history-header h3 {
        font-size: 20px;
    }

    .history-header p {
        font-size: 13px;
        line-height: 1.6;
    }

    .transaction-table-heading {
        display: none;
    }

    .transaction-item {
        grid-template-columns: 1fr auto;
        gap: 10px;
        padding: 16px 4px;
    }

    .transaction-id {
        grid-column: 1;
        grid-row: 1;
        gap: 10px;
    }

    .transaction-icon {
        width: 39px;
        height: 39px;
        flex-basis: 39px;
    }

    .transaction-id strong {
        font-size: 13px;
    }

    .transaction-date {
        grid-column: 1;
        grid-row: 2;
        padding-left: 49px;
        font-size: 11px;
    }

    .transaction-amount {
        grid-column: 2;
        grid-row: 1;
        text-align: right;
        font-size: 13px;
    }

    .transaction-status {
        grid-column: 2;
        grid-row: 2;
        text-align: right;
    }

    .transaction-status > span {
        padding: 7px 9px;
        font-size: 10px;
    }

    .empty-state {
        padding: 40px 12px;
    }

    .empty-state h4 {
        font-size: 17px;
    }

    .empty-state p {
        font-size: 13px;
        line-height: 1.6;
    }
}

@media (prefers-reduced-motion: reduce) {
    .history-section {
        animation: none;
    }
}
</style>
