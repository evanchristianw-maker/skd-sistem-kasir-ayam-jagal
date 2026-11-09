<script setup>
import { computed, ref } from "vue";
import { router, usePage } from "@inertiajs/vue3";

import CashierHeader from "@/Components/Cashier/Transactions/CashierHeader.vue";
import CashierMenuList from "@/Components/Cashier/Transactions/CashierMenuList.vue";
import CashierCart from "@/Components/Cashier/Transactions/CashierCart.vue";
import CashierPayment from "@/Components/Cashier/Transactions/CashierPayment.vue";
import CashierLogoutModal from "@/Components/Cashier/Transactions/CashierLogoutModal.vue";

import useCashierTransaction from "@/Composables/useCashierTransaction";

const props = defineProps({
    menus: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();

const cashierName = computed(() => {
    return page.props.auth?.user?.name || "Kasir";
});

const cashierEmail = computed(() => {
    return page.props.auth?.user?.email || "";
});

const currentDate = computed(() => {
    return new Intl.DateTimeFormat("id-ID", {
        weekday: "long",
        day: "numeric",
        month: "long",
        year: "numeric",
    }).format(new Date());
});

const showPayment = ref(false);
const showLogoutModal = ref(false);

const {
    menus,
    cart,
    paymentMethod,
    cashReceived,
    processing,
    errors,
    errorMessage,
    transactionResult,
    totalItems,
    total,
    addToCart,
    increaseQuantity,
    decreaseQuantity,
    removeFromCart,
    submitTransaction,
    clearCart,
    clearTransactionResult,
    formatRupiah,
    restoreTransactionResult,
} = useCashierTransaction(props.menus);

restoreTransactionResult();

const logout = () => {
    router.post(route("logout"));
};

const openPayment = () => {
    if (!cart.value.length) return;

    showPayment.value = true;
    errorMessage.value = "";
    errors.value = {};
};

const backToCart = () => {
    showPayment.value = false;
};

const finishTransaction = async () => {
    const result = await submitTransaction();

    if (result) {
        showPayment.value = false;
    }
};

const startNewTransaction = () => {
    clearCart();
    clearTransactionResult();

    paymentMethod.value = "tunai";
    cashReceived.value = "";
    showPayment.value = false;
    errors.value = {};
    errorMessage.value = "";
};

const transactionNumber = computed(() => {
    return (
        transactionResult.value?.nomor_transaksi ||
        transactionResult.value?.kode_transaksi ||
        transactionResult.value?.transaction_number ||
        "-"
    );
});

const transactionDetails = computed(() => {
    const result = transactionResult.value;

    if (!result) return [];

    return (
        result.detail_transaksis ||
        result.detailTransaksis ||
        result.details ||
        []
    );
});

const printReceipt = () => {
    if (!transactionResult.value) return;
    window.print();
};

const paymentMethodLabel = computed(() => {
    const method = transactionResult.value?.metode_pembayaran || paymentMethod.value;

    const labels = {
        tunai: "Tunai",
        qris: "QRIS",
        transfer: "Transfer Bank",
    };

    return labels[method] || method;
});

const transactionDateTime = computed(() => {
    const rawDate =
        transactionResult.value?.created_at ||
        transactionResult.value?.tanggal_transaksi;

    if (!rawDate) return "-";

    const date = new Date(rawDate);

    if (Number.isNaN(date.getTime())) return "-";

    return new Intl.DateTimeFormat("id-ID", {
        day: "2-digit",
        month: "long",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    }).format(date);
});
</script>

<template>
    <div class="cashier-page">
        <CashierHeader
            :cashier-name="cashierName"
            :cashier-email="cashierEmail"
            :current-date="currentDate"
            @logout-request="showLogoutModal = true"
        />

        <main class="cashier-main">
            <template v-if="!transactionResult">
                <section class="cashier-page-heading">
                    <div>
                        <span class="cashier-eyebrow">POINT OF SALE</span>
                        <h1>Transaksi Penjualan</h1>
                        <p>
                            Kelola pesanan pelanggan dengan mudah dan cepat.
                        </p>
                    </div>

                    <div class="transaction-counter">
                        <span>Jumlah Pesanan</span>
                        <strong>{{ totalItems }} Item</strong>
                    </div>
                </section>

                <div v-if="!showPayment" class="cashier-workspace">
                    <CashierMenuList
                        :menus="menus"
                        @add-to-cart="addToCart"
                    />

                    <CashierCart
                        :items="cart"
                        :total="total"
                        @increase="increaseQuantity"
                        @decrease="decreaseQuantity"
                        @remove="removeFromCart"
                        @continue-payment="openPayment"
                    />
                </div>

                <div v-else class="payment-workspace">
                    <CashierPayment
                        :total="total"
                        :processing="processing"
                        v-model:payment-method="paymentMethod"
                        v-model:cash-received="cashReceived"
                        @back="backToCart"
                        @submit="finishTransaction"
                    />

                    <div
                        v-if="errorMessage"
                        class="transaction-error"
                        role="alert"
                    >
                        <strong>Transaksi belum berhasil</strong>
                        <p>{{ errorMessage }}</p>

                        <ul v-if="Object.keys(errors).length">
                            <li
                                v-for="(messages, field) in errors"
                                :key="field"
                            >
                                {{ messages[0] }}
                            </li>
                        </ul>
                    </div>
                </div>
            </template>

            <section v-else class="transaction-result">
                <div class="result-success-icon">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path
                            d="M5 12L10 17L20 7"
                            stroke="currentColor"
                            stroke-width="2.5"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>
                </div>

                <span class="result-eyebrow">TRANSAKSI SELESAI</span>
                <h1>Pembayaran Berhasil!</h1>
                <p class="result-description">
                    Transaksi pelanggan berhasil diproses.
                </p>

                <div class="result-card">
                    <div class="receipt-header">
                        <h2>UMKM AYAM JAGAL</h2>
                        <p>Struk Transaksi Penjualan</p>
                    </div>

                    <div class="result-number">
                        <span>Nomor Transaksi</span>
                        <strong>{{ transactionNumber }}</strong>
                    </div>

                    <div class="receipt-date">
                        <span>Tanggal</span>
                        <strong>{{ transactionDateTime }}</strong>
                    </div>

                    <div class="result-divider"></div>

                    <div
                        v-for="(detail, index) in transactionDetails"
                        :key="detail.id || index"
                        class="result-item"
                    >
                        <div class="receipt-item-details">
                            <strong>
                                {{ detail.menu?.nama_menu || detail.menu?.name || detail.nama_menu || "Menu" }}
                            </strong>

                            <span>
                                {{ formatRupiah(detail.harga_satuan || detail.price || 0) }}
                                × {{ detail.jumlah || detail.quantity || 0 }}
                            </span>
                        </div>

                        <strong>
                            {{ formatRupiah(detail.subtotal || 0) }}
                        </strong>
                    </div>

                    <div class="result-divider"></div>

                    <div class="result-payment-row">
                        <span>Metode Pembayaran</span>
                        <strong>{{ paymentMethodLabel }}</strong>
                    </div>

                    <div class="result-payment-row">
                        <span>Total Pembayaran</span>
                        <strong class="result-total">
                            {{ formatRupiah(transactionResult.total_harga) }}
                        </strong>
                    </div>

                    <template v-if="(transactionResult.metode_pembayaran || paymentMethod) === 'tunai'">
                        <div class="result-payment-row">
                            <span>Uang Diterima</span>
                            <strong>
                                {{ formatRupiah(transactionResult.uang_dibayarkan) }}
                            </strong>
                        </div>

                        <div class="result-payment-row">
                            <span>Uang Kembalian</span>
                            <strong>
                                {{ formatRupiah(transactionResult.kembalian) }}
                            </strong>
                        </div>
                    </template>

                    <div class="receipt-footer">
                        <p>Terima kasih atas kunjungan Anda.</p>
                        <strong>Selamat menikmati!</strong>
                    </div>
                </div>

                <button
                    type="button"
                    class="print-receipt-button"
                    @click="printReceipt"
                >
                    <svg viewBox="0 0 24 24" fill="none">
                        <path
                            d="M7 8V3H17V8M7 17H5C3.9 17 3 16.1 3 15V10C3 8.9 3.9 8 5 8H19C20.1 8 21 8.9 21 10V15C21 16.1 20.1 17 19 17H17M7 14H17V21H7V14Z"
                            stroke="currentColor"
                            stroke-width="1.7"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>
                    Cetak Struk
                </button>

                <button
                    type="button"
                    class="new-transaction-button"
                    @click="startNewTransaction"
                >
                    Buat Transaksi Baru
                </button>
            </section>
        </main>

        <CashierLogoutModal
            :show="showLogoutModal"
            @cancel="showLogoutModal = false"
            @confirm="logout"
        />
    </div>
</template>

<style scoped>
* {
    box-sizing: border-box;
}

.cashier-page {
    min-height: 100vh;
    color: #252525;
    background: #f5f6f8;
    font-family: Inter, "Segoe UI", Arial, sans-serif;
}

.cashier-main {
    width: 100%;
    max-width: 1180px;
    margin: 0 auto;
    padding: 32px 28px 50px;
}

.cashier-page-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 25px;
    animation: fadeUp 0.35s ease both;
}

.cashier-eyebrow,
.result-eyebrow {
    color: #ca0708;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1.2px;
}

.cashier-page-heading h1 {
    margin: 8px 0 7px;
    color: #252525;
    font-size: 28px;
    font-weight: 800;
}

.cashier-page-heading p {
    margin: 0;
    color: #777777;
    font-size: 13px;
    line-height: 1.6;
}

.transaction-counter {
    display: flex;
    flex-direction: column;
    gap: 6px;
    padding: 13px 18px;
    border: 1px solid #e9eaed;
    border-radius: 12px;
    background: #ffffff;
}

.transaction-counter span {
    color: #888888;
    font-size: 11px;
}

.transaction-counter strong {
    color: #ca0708;
    font-size: 16px;
    font-weight: 800;
}

.cashier-workspace {
    display: grid;
    grid-template-columns: minmax(0, 1.55fr) minmax(320px, 0.85fr);
    align-items: start;
    gap: 22px;
}

.cashier-workspace > * {
    min-width: 0;
}

.payment-workspace {
    width: 100%;
    max-width: 760px;
    margin: 0 auto;
    animation: fadeUp 0.3s ease both;
}

.transaction-error {
    margin-top: 15px;
    padding: 15px 18px;
    border: 1px solid #ffcaca;
    border-radius: 11px;
    color: #a80000;
    background: #fff2f2;
}

.transaction-error strong {
    font-size: 13px;
}

.transaction-error p,
.transaction-error li {
    font-size: 12px;
    line-height: 1.6;
}

.transaction-error ul {
    padding-left: 20px;
}

.transaction-result {
    width: 100%;
    max-width: 560px;
    margin: 25px auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    animation: fadeUp 0.4s ease both;
}

.result-success-icon {
    width: 72px;
    height: 72px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 18px;
    border-radius: 50%;
    color: #16834b;
    background: #e7f7ed;
}

.result-success-icon svg {
    width: 36px;
    height: 36px;
}

.transaction-result h1 {
    margin: 9px 0;
    color: #252525;
    font-size: 25px;
    font-weight: 800;
}

.result-description {
    margin: 0;
    color: #777777;
    font-size: 13px;
}

.result-card {
    width: 100%;
    margin-top: 25px;
    padding: 24px;
    border: 1px solid #e9eaed;
    border-radius: 16px;
    background: #ffffff;
    text-align: left;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.035);
}


.receipt-header {
    text-align: center;
    padding-bottom: 18px;
}

.receipt-header h2 {
    margin: 0;
    color: #ca0708;
    font-size: 20px;
    font-weight: 800;
    letter-spacing: 1px;
}

.receipt-header p {
    margin: 5px 0 0;
    color: #777;
    font-size: 12px;
}

.receipt-date {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    margin-top: 12px;
    font-size: 12px;
}

.receipt-date strong {
    text-align: right;
}

.receipt-item-details {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.receipt-item-details span {
    color: #777;
    font-size: 12px;
}

.receipt-footer {
    margin-top: 22px;
    padding-top: 16px;
    border-top: 1px dashed #ddd;
    text-align: center;
}

.receipt-footer p {
    margin: 0 0 5px;
    color: #777;
    font-size: 12px;
}

.receipt-footer strong {
    font-size: 13px;
}
.result-number {
    display: flex;
    flex-direction: column;
    gap: 7px;
}

.result-number span,
.result-payment-row span {
    color: #888888;
    font-size: 12px;
}

.result-number strong {
    color: #ca0708;
    font-size: 16px;
}

.result-divider {
    height: 1px;
    margin: 18px 0;
    background: #eeeeee;
}

.result-item,
.result-payment-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 13px;
}

.result-item:last-child,
.result-payment-row:last-child {
    margin-bottom: 0;
}

.result-item > div {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.result-item strong,
.result-payment-row strong {
    color: #333333;
    font-size: 13px;
}

.result-item span {
    color: #888888;
    font-size: 11px;
}

.result-payment-row .result-total {
    color: #ca0708;
    font-size: 19px;
    font-weight: 800;
}

.print-receipt-button {
    width: 100%;
    min-height: 48px;
    margin-top: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    border: 1px solid #ca0708;
    border-radius: 10px;
    color: #ca0708;
    background: #ffffff;
    font-family: inherit;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.2s ease;
}

.print-receipt-button:hover {
    background: #fff2f2;
}

.print-receipt-button svg {
    width: 19px;
    height: 19px;
}

.new-transaction-button {
    width: 100%;
    min-height: 48px;
    margin-top: 20px;
    border: none;
    border-radius: 10px;
    color: #ffffff;
    background: #ca0708;
    font-family: inherit;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.2s ease, transform 0.2s ease;
}

.new-transaction-button:hover {
    transform: translateY(-2px);
    background: #a80000;
}

.new-transaction-button:focus-visible {
    outline: 3px solid rgba(202, 7, 8, 0.25);
    outline-offset: 3px;
}

@keyframes fadeUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 900px) {
    .cashier-workspace {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 600px) {
    .cashier-main {
        padding: 23px 15px 35px;
    }

    .cashier-page-heading {
        align-items: flex-start;
        flex-direction: column;
        gap: 15px;
    }

    .cashier-page-heading h1 {
        font-size: 23px;
    }

    .transaction-counter {
        width: 100%;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }

    .result-card {
        padding: 18px;
    }

    .transaction-result h1 {
        font-size: 22px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .cashier-page-heading,
    .payment-workspace,
    .transaction-result {
        animation: none;
    }

    .new-transaction-button {
        transition: none;
    }

    .new-transaction-button:hover {
        transform: none;
    }
}

@media print {
    @page {
        size: 80mm auto;
        margin: 4mm;
    }

    :global(body *) {
        visibility: hidden !important;
    }

    .transaction-result,
    .transaction-result * {
        visibility: visible !important;
    }

    .transaction-result {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        max-width: none;
        margin: 0;
        padding: 0;
        color: #000000;
        background: #ffffff;
        animation: none;
    }

    .result-success-icon,
    .result-eyebrow,
    .result-description,
    .print-receipt-button,
    .new-transaction-button {
        display: none !important;
    }

    .result-card {
        width: 100%;
        margin: 0;
        padding: 0;
        border: none;
        border-radius: 0;
        box-shadow: none;
    }

    .result-item,
    .result-payment-row {
        break-inside: avoid;
    }
}
</style>




