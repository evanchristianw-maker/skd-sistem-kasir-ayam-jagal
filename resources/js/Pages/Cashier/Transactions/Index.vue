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
    rekeningBanks: {
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
const receiptWidth = ref("58");

const {
    menus,
    cart,
    paymentMethod,
    cashReceived,
    selectedRekeningBankId,
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
    confirmTransaction,
    cancelTransaction,
    clearCart,
    clearTransactionResult,
    formatRupiah,
    restoreTransactionResult,
} = useCashierTransaction(props.menus, props.rekeningBanks);

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

const confirmPendingTransaction = async () => {
    const result = await confirmTransaction();

    if (result) {
        transactionResult.value = result;
    }
};

const cancelPendingTransaction = async () => {
    const result = await cancelTransaction();

    if (result) {
        transactionResult.value = result;
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

const transactionStatus = computed(() => {
    return transactionResult.value?.status || "";
});

const isPendingTransfer = computed(() => {
    return transactionStatus.value === "menunggu_konfirmasi";
});

const isCancelledTransaction = computed(() => {
    return transactionStatus.value === "dibatalkan";
});

const canPrintReceipt = computed(() => {
    return transactionResult.value?.status === "selesai";
});

const paymentMethodLabel = computed(() => {
    const method =
        transactionResult.value?.metode_pembayaran || paymentMethod.value;

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

const printReceipt = () => {
    if (!transactionResult.value) return;

    if (transactionResult.value.status !== "selesai") return;

    window.print();
};
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
                    :rekening-banks="props.rekeningBanks"
                    v-model:payment-method="paymentMethod"
                    v-model:cash-received="cashReceived"
                    v-model:selected-rekening-bank-id="selectedRekeningBankId"
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
                <div
                    class="result-status-icon"
                    :class="{
                        'status-success': !isPendingTransfer && !isCancelledTransaction,
                        'status-pending': isPendingTransfer,
                        'status-cancelled': isCancelledTransaction,
                    }"
                >
                    <svg
                        v-if="isPendingTransfer"
                        viewBox="0 0 24 24"
                        fill="none"
                    >
                        <circle
                            cx="12"
                            cy="12"
                            r="9"
                            stroke="currentColor"
                            stroke-width="2"
                        />
                        <path
                            d="M12 7V12L15 15"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                        />
                    </svg>

                    <svg
                        v-else-if="isCancelledTransaction"
                        viewBox="0 0 24 24"
                        fill="none"
                    >
                        <circle
                            cx="12"
                            cy="12"
                            r="9"
                            stroke="currentColor"
                            stroke-width="2"
                        />
                        <path
                            d="M9 9L15 15M15 9L9 15"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                        />
                    </svg>

                    <svg v-else viewBox="0 0 24 24" fill="none">
                        <path
                            d="M5 12L10 17L20 7"
                            stroke="currentColor"
                            stroke-width="2.5"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>
                </div>

                <span class="result-eyebrow">
                    {{
                        isPendingTransfer
                            ? "MENUNGGU KONFIRMASI"
                            : isCancelledTransaction
                              ? "TRANSAKSI DIBATALKAN"
                              : "TRANSAKSI SELESAI"
                    }}
                </span>
                <h1>
                    {{
                        isPendingTransfer
                            ? "Menunggu Konfirmasi Pembayaran"
                            : isCancelledTransaction
                              ? "Transaksi Dibatalkan"
                              : "Pembayaran Berhasil!"
                    }}
                </h1>
                <p class="result-description">
                    {{
                        isPendingTransfer
                            ? "Transaksi transfer telah dibuat. Konfirmasi setelah uang benar-benar diterima."
                            : isCancelledTransaction
                              ? "Transaksi transfer telah dibatalkan."
                              : "Transaksi pelanggan berhasil diproses."
                    }}
                </p>

                <div
                    class="result-card"
                    :class="`receipt-paper-${receiptWidth}`"
                >
                    <!-- Header toko -->
                    <div class="receipt-header">
                        <img
                            src="/assets/images/illustrations/ilustrasi-koki.png"
                            alt="Logo Ayam Jagal"
                            class="receipt-logo"
                        />

                        <h2>AYAM JAGAL</h2>
                        <p class="receipt-tagline">
                            SAK WAREGE, SAK PEDESE, SAK KEMBUNGE
                        </p>

                        <div class="receipt-store-info">
                            <p>
                                Jl. Gotong Royong No.14, Jagalan,
                                Kec. Jebres, Kota Surakarta,
                                Jawa Tengah 57162
                            </p>
                            <p>Telp. 085859565758</p>
                        </div>
                    </div>

                    <div class="receipt-section-title">
                        STRUK PEMBAYARAN
                    </div>

                    <!-- Informasi transaksi -->
                    <div class="receipt-meta">
                        <div class="receipt-meta-row">
                            <span>No. Transaksi</span>
                            <strong>{{ transactionNumber }}</strong>
                        </div>

                        <div class="receipt-meta-row">
                            <span>Tanggal</span>
                            <strong>{{ transactionDateTime }}</strong>
                        </div>

                        <div class="receipt-meta-row">
                            <span>Kasir</span>
                            <strong>{{ cashierName }}</strong>
                        </div>
                    </div>

                    <div class="receipt-divider"></div>

                    <!-- Daftar produk -->
                    <div class="receipt-items">
                        <div
                            v-for="(detail, index) in transactionDetails"
                            :key="detail.id || index"
                            class="receipt-item"
                        >
                            <strong class="receipt-product-name">
                                {{
                                    detail.menu?.nama_menu ||
                                    detail.menu?.name ||
                                    detail.nama_menu ||
                                    "Menu"
                                }}
                            </strong>

                            <div class="receipt-item-calculation">
                                <span>
                                    {{ detail.jumlah || detail.quantity || 0 }}
                                    x
                                    {{
                                        formatRupiah(
                                            detail.harga_satuan ||
                                            detail.price ||
                                            0
                                        )
                                    }}
                                </span>

                                <strong>
                                    {{ formatRupiah(detail.subtotal || 0) }}
                                </strong>
                            </div>
                        </div>
                    </div>

                    <div class="receipt-divider"></div>

                    <!-- Ringkasan pembayaran -->
                    <div class="receipt-payment">
                        <div class="receipt-payment-row">
                            <span>Metode Pembayaran</span>
                            <strong>{{ paymentMethodLabel }}</strong>
                        </div>

                        <template
                            v-if="
                                (transactionResult.metode_pembayaran ||
                                    paymentMethod) === 'transfer'
                            "
                        >
                            <div class="receipt-payment-row">
                                <span>Bank Tujuan</span>
                                <strong>
                                    {{
                                        transactionResult.nama_bank ||
                                        "-"
                                    }}
                                </strong>
                            </div>

                            <div class="receipt-payment-row">
                                <span>No. Rekening</span>
                                <strong>
                                    {{
                                        transactionResult.nomor_rekening ||
                                        "-"
                                    }}
                                </strong>
                            </div>

                            <div class="receipt-payment-row">
                                <span>Atas Nama</span>
                                <strong>
                                    {{
                                        transactionResult.atas_nama ||
                                        "-"
                                    }}
                                </strong>
                            </div>
                        </template>

                        <div class="receipt-payment-row receipt-grand-total">
                            <span>TOTAL</span>
                            <strong>
                                {{
                                    formatRupiah(
                                        transactionResult.total_harga || 0
                                    )
                                }}
                            </strong>
                        </div>

                        <template
                            v-if="
                                (transactionResult.metode_pembayaran ||
                                    paymentMethod) === 'tunai'
                            "
                        >
                            <div class="receipt-payment-row">
                                <span>Uang Diterima</span>
                                <strong>
                                    {{
                                        formatRupiah(
                                            transactionResult.uang_dibayarkan ||
                                            0
                                        )
                                    }}
                                </strong>
                            </div>

                            <div class="receipt-payment-row">
                                <span>Kembalian</span>
                                <strong>
                                    {{
                                        formatRupiah(
                                            transactionResult.kembalian || 0
                                        )
                                    }}
                                </strong>
                            </div>
                        </template>
                    </div>

                    <!-- Footer struk -->
                    <div class="receipt-footer">
                        <p>Terima kasih telah berbelanja</p>
                        <strong>Selamat menikmati!</strong>
                        <span>AYAM JAGAL - Surakarta</span>
                    </div>
                </div>

                <!-- Pengaturan dan tombol cetak -->
                <div class="receipt-actions">
                    <label for="receipt-width">Ukuran kertas struk</label>

                    <select id="receipt-width" v-model="receiptWidth">
                        <option value="58">Thermal 58 mm</option>
                        <option value="80">Thermal 80 mm</option>
                    </select>

                    <button
                        v-if="canPrintReceipt"
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
                        v-if="isPendingTransfer"
                        type="button"
                        class="confirm-payment-button"
                        :disabled="processing"
                        @click="confirmPendingTransaction"
                    >
                        <span v-if="processing">Memproses...</span>
                        <span v-else>Konfirmasi Pembayaran Diterima</span>
                    </button>

                    <button
                        v-if="isPendingTransfer"
                        type="button"
                        class="cancel-transaction-button"
                        :disabled="processing"
                        @click="cancelPendingTransaction"
                    >
                        Batalkan Transaksi
                    </button>

                    <button
                        type="button"
                        class="new-transaction-button"
                        @click="startNewTransaction"
                    >
                        Buat Transaksi Baru
                    </button>
                </div>
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

/* Halaman transaksi berhasil */
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

.result-status-icon {
    width: 72px;
    height: 72px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 18px;
    border-radius: 50%;
}

.result-status-icon svg {
    width: 36px;
    height: 36px;
}

.result-status-icon.status-success {
    color: #16834b;
    background: #e7f7ed;
}

.result-status-icon.status-pending {
    color: #b45309;
    background: #fef3c7;
}

.result-status-icon.status-cancelled {
    color: #b91c1c;
    background: #fee2e2;
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

/* Desain struk */
.result-card {
    width: 100%;
    max-width: 390px;
    margin-top: 25px;
    padding: 25px 22px;
    border: 1px solid #e9eaed;
    border-radius: 12px;
    background: #ffffff;
    color: #222222;
    text-align: left;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.045);
}

.receipt-header {
    padding-bottom: 16px;
    text-align: center;
}

.receipt-logo {
    display: block;
    width: 65px;
    height: 65px;
    margin: 0 auto 8px;
    object-fit: contain;
}

.receipt-header h2 {
    margin: 0;
    color: #ca0708;
    font-size: 23px;
    font-weight: 900;
    letter-spacing: 1.5px;
}

.receipt-tagline {
    margin: 4px 0 12px;
    color: #555555;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 1.4px;
}

.receipt-store-info {
    max-width: 310px;
    margin: 0 auto;
    color: #555555;
    font-size: 10px;
    line-height: 1.55;
}

.receipt-store-info p {
    margin: 2px 0;
}

.receipt-section-title {
    padding: 9px 0;
    border-top: 1px dashed #999999;
    border-bottom: 1px dashed #999999;
    text-align: center;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1.3px;
}

.receipt-meta {
    display: flex;
    flex-direction: column;
    gap: 7px;
    padding: 13px 0;
}

.receipt-meta-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    font-size: 10px;
    line-height: 1.5;
}

.receipt-meta-row span {
    flex-shrink: 0;
    color: #666666;
}

.receipt-meta-row strong {
    max-width: 65%;
    text-align: right;
    overflow-wrap: anywhere;
}

.receipt-divider {
    width: 100%;
    border-top: 1px dashed #999999;
    margin: 5px 0 13px;
}

.receipt-items {
    display: flex;
    flex-direction: column;
    gap: 13px;
    padding: 2px 0 12px;
}

.receipt-item {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.receipt-product-name {
    color: #222222;
    font-size: 11px;
    font-weight: 700;
    overflow-wrap: anywhere;
}

.receipt-item-calculation {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 10px;
    font-size: 10px;
}

.receipt-item-calculation span {
    color: #555555;
}

.receipt-item-calculation strong {
    text-align: right;
    white-space: nowrap;
}

.receipt-payment {
    display: flex;
    flex-direction: column;
    gap: 9px;
    padding: 2px 0;
}

.receipt-payment-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 10px;
    font-size: 10px;
    line-height: 1.5;
}

.receipt-payment-row span {
    color: #555555;
}

.receipt-payment-row strong {
    text-align: right;
}

.receipt-grand-total {
    align-items: center;
    margin: 3px 0;
    padding: 11px 0;
    border-top: 1px dashed #999999;
    border-bottom: 1px dashed #999999;
    font-size: 12px;
    font-weight: 800;
}

.receipt-grand-total span {
    color: #222222;
    font-weight: 800;
}

.receipt-grand-total strong {
    color: #ca0708;
    font-size: 17px;
    font-weight: 900;
}

.receipt-footer {
    display: flex;
    flex-direction: column;
    gap: 5px;
    margin-top: 18px;
    padding-top: 14px;
    border-top: 1px dashed #999999;
    text-align: center;
}

.receipt-footer p {
    margin: 0;
    color: #333333;
    font-size: 11px;
}

.receipt-footer strong {
    font-size: 12px;
}

.receipt-footer span {
    margin-top: 5px;
    color: #777777;
    font-size: 9px;
    letter-spacing: 0.5px;
}

/* Pengaturan cetak */
.receipt-actions {
    width: 100%;
    max-width: 390px;
    margin-top: 20px;
    display: flex;
    flex-direction: column;
    gap: 9px;
    text-align: left;
}

.receipt-actions label {
    color: #555555;
    font-size: 12px;
    font-weight: 700;
}

.receipt-actions select {
    width: 100%;
    min-height: 43px;
    padding: 0 12px;
    border: 1px solid #dedede;
    border-radius: 9px;
    background: #ffffff;
    color: #333333;
    font-family: inherit;
    font-size: 13px;
}

.print-receipt-button {
    width: 100%;
    min-height: 48px;
    margin-top: 5px;
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
    margin-top: 5px;
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

.confirm-payment-button,
.cancel-transaction-button {
    width: 100%;
    min-height: 48px;
    margin-top: 5px;
    border: none;
    border-radius: 10px;
    font-family: inherit;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.2s ease, transform 0.2s ease;
}

.confirm-payment-button {
    color: #ffffff;
    background: #ca0708;
}

.confirm-payment-button:hover:not(:disabled) {
    transform: translateY(-2px);
    background: #a80000;
}

.cancel-transaction-button {
    color: #b91c1c;
    background: #fee2e2;
}

.cancel-transaction-button:hover:not(:disabled) {
    background: #fecaca;
}

.confirm-payment-button:disabled,
.cancel-transaction-button:disabled {
    opacity: 0.55;
    cursor: not-allowed;
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
        padding: 20px 17px;
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

/*
 * Named page untuk ukuran printer thermal.
 * Pilihan ukuran di atas struk menentukan halaman yang digunakan.
 */
@page receipt58 {
    size: 58mm auto;
    margin: 2mm;
}

@page receipt80 {
    size: 80mm auto;
    margin: 3mm;
}

@media print {
    :global(html),
    :global(body) {
        margin: 0 !important;
        padding: 0 !important;
        background: #ffffff !important;
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
    .receipt-actions,
    .print-receipt-button,
    .new-transaction-button {
        display: none !important;
    }

    .result-card {
        width: 100%;
        max-width: none;
        margin: 0;
        padding: 0;
        border: none;
        border-radius: 0;
        box-shadow: none;
        color: #000000;
        background: #ffffff;
    }

    .receipt-paper-58 {
        page: receipt58;
        width: 54mm !important;
    }

    .receipt-paper-80 {
        page: receipt80;
        width: 74mm !important;
    }

    .receipt-header,
    .receipt-meta,
    .receipt-items,
    .receipt-payment,
    .receipt-footer {
        break-inside: avoid;
    }

    .receipt-header h2,
    .receipt-grand-total strong {
        color: #000000 !important;
    }

    .receipt-logo {
        width: 48px;
        height: 48px;
        filter: grayscale(1);
    }

    .receipt-header h2 {
        font-size: 17px;
    }

    .receipt-tagline {
        font-size: 8px;
    }

    .receipt-store-info {
        font-size: 8px;
    }

    .receipt-section-title {
        font-size: 9px;
    }

    .receipt-meta-row,
    .receipt-item-calculation,
    .receipt-payment-row {
        font-size: 9px;
    }

    .receipt-product-name {
        font-size: 10px;
    }

    .receipt-grand-total strong {
        font-size: 14px;
    }

    .receipt-footer p {
        font-size: 9px;
    }

    .receipt-footer strong {
        font-size: 10px;
    }

    .receipt-footer span {
        font-size: 8px;
    }
}
</style>