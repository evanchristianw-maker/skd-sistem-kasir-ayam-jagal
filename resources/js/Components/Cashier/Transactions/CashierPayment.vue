<script setup>
import { computed, ref } from "vue";

const props = defineProps({
    total: {
        type: Number,
        default: 0,
    },
    paymentMethod: {
        type: String,
        default: "tunai",
    },
    cashReceived: {
        type: [Number, String],
        default: "",
    },
    processing: {
        type: Boolean,
        default: false,
    },
    rekeningBanks: {
        type: Array,
        default: () => [],
    },
    selectedRekeningBankId: {
        type: [Number, String, null],
        default: null,
    },
});

const qrisConfirmed = ref(false);

const selectedRekening = computed(() => {
    return (
        props.rekeningBanks.find(
            (rekening) =>
                Number(rekening.id) ===
                Number(props.selectedRekeningBankId),
        ) || null
    );
});

const copyRekeningNumber = async () => {
    if (!selectedRekening.value?.nomor_rekening) return;

    try {
        await navigator.clipboard.writeText(
            selectedRekening.value.nomor_rekening,
        );
    } catch {
        window.alert(selectedRekening.value.nomor_rekening);
    }
};

const emit = defineEmits([
    "update:paymentMethod",
    "update:cashReceived",
    "update:selectedRekeningBankId",
    "submit",
    "back",
]);

const formatCurrency = (value) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(Number(value) || 0);
};

const change = computed(() => {
    return Math.max(Number(props.cashReceived || 0) - props.total, 0);
});

const quickCashAmounts = computed(() => {
    const total = Number(props.total) || 0;

    if (total <= 0) return [];

    const amounts = [
        total,
        Math.ceil(total / 10000) * 10000,
        50000,
        100000,
    ];

    return [...new Set(amounts)]
        .filter((amount) => amount >= total)
        .sort((a, b) => a - b);
});

const setCashAmount = (amount) => {
    emit("update:cashReceived", String(amount));
};

const isCashInsufficient = computed(() => {
    return (
        props.paymentMethod === "tunai" &&
        Number(props.cashReceived || 0) < props.total
    );
});

const canSubmit = computed(() => {
    if (props.processing || props.total <= 0) {
        return false;
    }

    if (props.paymentMethod === "tunai") {
        return (
            Number(props.cashReceived || 0) >= props.total &&
            Number(props.cashReceived || 0) > 0
        );
    }

    if (props.paymentMethod === "qris") return qrisConfirmed.value;

    return Boolean(props.selectedRekeningBankId);
});

const selectPayment = (method) => {
    emit("update:paymentMethod", method);

    if (method !== "tunai") {
        emit("update:cashReceived", "");
    }
};
</script>

<template>
    <section class="cashier-payment">
        <div class="payment-heading">
            <div>
                <h2>Pembayaran</h2>
                <p>Pilih metode pembayaran transaksi.</p>
            </div>

            <span class="payment-step">Tahap 2</span>
        </div>

        <div class="payment-total-card">
            <span>Total yang harus dibayar</span>
            <strong>{{ formatCurrency(total) }}</strong>
        </div>

        <div class="payment-method-section">
            <h3>Metode Pembayaran</h3>

            <div class="payment-method-grid">
                <button
                    type="button"
                    class="payment-method-button"
                    :class="{ active: paymentMethod === 'tunai' }"
                    :aria-pressed="paymentMethod === 'tunai'"
                    @click="selectPayment('tunai')"
                >
                    <span class="payment-method-icon">
                        <svg viewBox="0 0 24 24" fill="none">
                            <rect
                                x="2.5"
                                y="5"
                                width="19"
                                height="14"
                                rx="2"
                                stroke="currentColor"
                                stroke-width="1.7"
                            />
                            <circle
                                cx="12"
                                cy="12"
                                r="3"
                                stroke="currentColor"
                                stroke-width="1.7"
                            />
                            <path
                                d="M6 9H6.01M18 15H18.01"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                            />
                        </svg>
                    </span>
                    <span>Tunai</span>
                    <span class="payment-radio"></span>
                </button>

                <button
                    type="button"
                    class="payment-method-button"
                    :class="{ active: paymentMethod === 'qris' }"
                    :aria-pressed="paymentMethod === 'qris'"
                    @click="selectPayment('qris')"
                >
                    <span class="payment-method-icon">
                        <svg viewBox="0 0 24 24" fill="none">
                            <rect
                                x="3"
                                y="3"
                                width="7"
                                height="7"
                                rx="1"
                                stroke="currentColor"
                                stroke-width="1.7"
                            />
                            <rect
                                x="14"
                                y="3"
                                width="7"
                                height="7"
                                rx="1"
                                stroke="currentColor"
                                stroke-width="1.7"
                            />
                            <rect
                                x="3"
                                y="14"
                                width="7"
                                height="7"
                                rx="1"
                                stroke="currentColor"
                                stroke-width="1.7"
                            />
                            <path
                                d="M14 14H17V17H14V14ZM18 18H21V21H18V18ZM17 21H14V18"
                                stroke="currentColor"
                                stroke-width="1.7"
                                stroke-linejoin="round"
                            />
                        </svg>
                    </span>
                    <span>QRIS</span>
                    <span class="payment-radio"></span>
                </button>

                <button
                    type="button"
                    class="payment-method-button"
                    :class="{ active: paymentMethod === 'transfer' }"
                    :aria-pressed="paymentMethod === 'transfer'"
                    @click="selectPayment('transfer')"
                >
                    <span class="payment-method-icon">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path
                                d="M3 10H21L12 4L3 10Z"
                                stroke="currentColor"
                                stroke-width="1.7"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M5 10V18M9 10V18M15 10V18M19 10V18M3 21H21M3 18H21"
                                stroke="currentColor"
                                stroke-width="1.7"
                                stroke-linecap="round"
                            />
                        </svg>
                    </span>
                    <span>Transfer</span>
                    <span class="payment-radio"></span>
                </button>
            </div>
        </div>

        <div v-if="paymentMethod === 'tunai'" class="cash-payment-section">
            <label for="cash-received">Uang yang diterima</label>

            <div class="cash-input-wrapper">
                <span>Rp</span>
                <input
                    id="cash-received"
                    :value="cashReceived"
                    type="number"
                    min="0"
                    step="1"
                    placeholder="Masukkan nominal uang"
                    @input="emit('update:cashReceived', $event.target.value)"
                />
            </div>

            <div class="quick-cash-section">
                <span class="quick-cash-label">Nominal cepat</span>

                <div class="quick-cash-buttons">
                    <button
                        v-for="amount in quickCashAmounts"
                        :key="amount"
                        type="button"
                        :class="{ selected: Number(cashReceived) === amount }"
                        @click="setCashAmount(amount)"
                    >
                        {{ formatCurrency(amount) }}
                    </button>
                </div>
            </div>

            <div class="change-card">
                <span>Uang Kembalian</span>
                <strong>{{ formatCurrency(change) }}</strong>
            </div>

            <p v-if="isCashInsufficient && cashReceived !== ''" class="payment-error">
                Nominal uang yang diterima belum mencukupi.
            </p>
        </div>

        <div v-else-if="paymentMethod === 'qris'" class="qris-payment-section">
            <div class="qris-heading">
                <h3>Pembayaran QRIS</h3>
                <p>Pindai kode QR berikut menggunakan aplikasi pembayaran.</p>
            </div>

            <div class="qris-image-wrapper">
                <img
                    src="/assets/images/payment/qris-ayam-jagal.png"
                    alt="QRIS UMKM Ayam Jagal"
                    class="qris-image"
                />
            </div>

            <div class="qris-amount">
                <span>Total pembayaran</span>
                <strong>{{ formatCurrency(total) }}</strong>
            </div>

            <div class="qris-instructions">
                <strong>Petunjuk pembayaran</strong>
                <ol>
                    <li>Buka aplikasi pembayaran pelanggan.</li>
                    <li>Pilih menu scan atau pindai QRIS.</li>
                    <li>Pindai kode QR di atas.</li>
                    <li>Pastikan nominal pembayaran sesuai.</li>
                    <li>Periksa pembayaran melalui aplikasi merchant.</li>
                </ol>
            </div>

            <label class="qris-confirmation">
                <input
                    v-model="qrisConfirmed"
                    type="checkbox"
                />
                <span>
                    Saya sudah memastikan pembayaran pelanggan berhasil diterima.
                </span>
            </label>

            <p v-if="qrisConfirmed" class="qris-confirmed-message">
                Pembayaran telah dikonfirmasi secara manual oleh kasir.
            </p>
        </div>

        <div v-else class="transfer-payment-section">
            <div class="transfer-heading">
                <strong>Transfer Bank</strong>
                <p>
                    Pilih rekening tujuan dan pastikan pembayaran benar-benar
                    diterima sebelum transaksi diselesaikan.
                </p>
            </div>

            <label for="rekening-bank">Rekening Tujuan</label>

            <select
                id="rekening-bank"
                :value="selectedRekeningBankId"
                @change="
                    emit(
                        'update:selectedRekeningBankId',
                        $event.target.value
                            ? Number($event.target.value)
                            : null,
                    )
                "
            >
                <option value="">Pilih rekening bank</option>
                <option
                    v-for="rekening in rekeningBanks"
                    :key="rekening.id"
                    :value="rekening.id"
                >
                    {{ rekening.nama_bank }} -
                    {{ rekening.nomor_rekening }} ({{ rekening.atas_nama }})
                </option>
            </select>

            <div v-if="selectedRekening" class="selected-rekening-card">
                <div>
                    <span>Nama Bank</span>
                    <strong>{{ selectedRekening.nama_bank }}</strong>
                </div>

                <div>
                    <span>Nomor Rekening</span>
                    <strong>{{ selectedRekening.nomor_rekening }}</strong>
                    <button
                        type="button"
                        class="copy-button"
                        @click="copyRekeningNumber"
                    >
                        Salin
                    </button>
                </div>

                <div>
                    <span>Atas Nama</span>
                    <strong>{{ selectedRekening.atas_nama }}</strong>
                </div>
            </div>

            <div class="transfer-amount">
                <span>Nominal yang harus ditransfer</span>
                <strong>{{ formatCurrency(total) }}</strong>
            </div>

            <p class="transfer-note">
                Transaksi akan berstatus menunggu konfirmasi sampai kasir
                memastikan uang benar-benar diterima.
            </p>
        </div>

        <div class="payment-actions">
            <button
                type="button"
                class="payment-back-button"
                :disabled="processing"
                @click="emit('back')"
            >
                Kembali
            </button>

            <button
                type="button"
                class="payment-submit-button"
                :disabled="!canSubmit"
                @click="emit('submit')"
            >
                <span v-if="processing">Memproses...</span>
                <span v-else>Selesaikan Transaksi</span>
                <svg
                    v-if="!processing"
                    viewBox="0 0 24 24"
                    fill="none"
                    aria-hidden="true"
                >
                    <path
                        d="M5 12H19M12 5L19 12L12 19"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>
            </button>
        </div>
    </section>
</template>

<style scoped>
* {
    box-sizing: border-box;
}

.cashier-payment {
    min-width: 0;
    padding: 24px;
    border: 1px solid #e9eaed;
    border-radius: 16px;
    background: #ffffff;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.035);
}

.payment-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 20px;
}

.payment-heading h2 {
    margin: 0;
    color: #252525;
    font-size: 19px;
    font-weight: 800;
}

.payment-heading p {
    margin: 6px 0 0;
    color: #777777;
    font-size: 13px;
}

.payment-step {
    flex-shrink: 0;
    padding: 7px 12px;
    border-radius: 20px;
    color: #ca0708;
    background: #fff0f0;
    font-size: 12px;
    font-weight: 700;
}

.payment-total-card {
    display: flex;
    flex-direction: column;
    gap: 9px;
    padding: 19px;
    border: 1px solid #ffe0e0;
    border-radius: 13px;
    background: #fff7f7;
}

.payment-total-card span {
    color: #777777;
    font-size: 12px;
    font-weight: 600;
}

.payment-total-card strong {
    color: #ca0708;
    font-size: 25px;
    font-weight: 800;
}

.payment-method-section {
    margin-top: 24px;
}

.payment-method-section h3 {
    margin: 0 0 13px;
    color: #333333;
    font-size: 14px;
    font-weight: 750;
}

.payment-method-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
}

.payment-method-button {
    position: relative;
    min-height: 105px;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: center;
    gap: 9px;
    padding: 13px;
    border: 1px solid #e9eaed;
    border-radius: 12px;
    color: #444444;
    background: #ffffff;
    font-family: inherit;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: border-color 0.2s ease, background 0.2s ease, transform 0.2s ease;
}

.payment-method-button:hover {
    transform: translateY(-2px);
    border-color: #e5a0a0;
}

.payment-method-button.active {
    border: 1.5px solid #ca0708;
    color: #ca0708;
    background: #fff7f7;
}

.payment-method-icon {
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
    color: #ca0708;
    background: #fff0f0;
}

.payment-method-icon svg {
    width: 22px;
    height: 22px;
}

.payment-radio {
    position: absolute;
    top: 13px;
    right: 13px;
    width: 16px;
    height: 16px;
    border: 1.5px solid #cccccc;
    border-radius: 50%;
}

.payment-method-button.active .payment-radio {
    border: 5px solid #ca0708;
}

.payment-method-button:focus-visible,
.payment-back-button:focus-visible,
.payment-submit-button:focus-visible {
    outline: 3px solid rgba(202, 7, 8, 0.25);
    outline-offset: 3px;
}

.cash-payment-section {
    margin-top: 24px;
    animation: paymentFadeUp 0.25s ease both;
}

.cash-payment-section label {
    display: block;
    margin-bottom: 9px;
    color: #333333;
    font-size: 13px;
    font-weight: 700;
}

.cash-input-wrapper {
    min-height: 48px;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 0 14px;
    border: 1px solid #e5e5e5;
    border-radius: 10px;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.cash-input-wrapper:focus-within {
    border-color: #ca0708;
    box-shadow: 0 0 0 3px rgba(202, 7, 8, 0.08);
}

.cash-input-wrapper span {
    color: #777777;
    font-size: 13px;
    font-weight: 700;
}

.cash-input-wrapper input {
    width: 100%;
    min-width: 0;
    border: none;
    outline: none;
    color: #252525;
    background: transparent;
    font-family: inherit;
    font-size: 14px;
}

.cash-input-wrapper input::-webkit-inner-spin-button,
.cash-input-wrapper input::-webkit-outer-spin-button {
    margin: 0;
    appearance: none;
}

.quick-cash-section {
    margin-top: 14px;
}

.quick-cash-label {
    display: block;
    margin-bottom: 9px;
    color: #666666;
    font-size: 12px;
    font-weight: 600;
}

.quick-cash-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.quick-cash-buttons button {
    min-height: 36px;
    padding: 0 12px;
    border: 1px solid #e9eaed;
    border-radius: 8px;
    color: #555555;
    background: #ffffff;
    font-family: inherit;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: 0.2s ease;
}

.quick-cash-buttons button:hover,
.quick-cash-buttons button.selected {
    border-color: #ca0708;
    color: #ca0708;
    background: #fff2f2;
}

.change-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-top: 14px;
    padding: 15px;
    border: 1px solid #e9eaed;
    border-radius: 10px;
    background: #f8f9fa;
}

.change-card span {
    color: #666666;
    font-size: 13px;
    font-weight: 600;
}

.change-card strong {
    color: #16834b;
    font-size: 17px;
    font-weight: 800;
}

.payment-error {
    margin: 9px 0 0;
    color: #ca0708;
    font-size: 12px;
}

.non-cash-info {
    display: flex;
    align-items: flex-start;
    gap: 13px;
    margin-top: 24px;
    padding: 16px;
    border: 1px solid #ffe0e0;
    border-radius: 11px;
    background: #fff7f7;
    animation: paymentFadeUp 0.25s ease both;
}

.non-cash-icon {
    width: 38px;
    height: 38px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    color: #ca0708;
    background: #ffeaea;
}

.non-cash-icon svg {
    width: 20px;
    height: 20px;
}

.non-cash-info strong {
    color: #333333;
    font-size: 13px;
}

.non-cash-info p {
    margin: 5px 0 0;
    color: #777777;
    font-size: 12px;
    line-height: 1.6;
}

.transfer-payment-section {
    margin-top: 24px;
    animation: paymentFadeUp 0.25s ease both;
}

.transfer-heading {
    margin-bottom: 16px;
}

.transfer-heading strong {
    color: #333333;
    font-size: 14px;
}

.transfer-heading p {
    margin: 6px 0 0;
    color: #777777;
    font-size: 12px;
    line-height: 1.6;
}

.transfer-payment-section label {
    display: block;
    margin-bottom: 9px;
    color: #333333;
    font-size: 13px;
    font-weight: 700;
}

.transfer-payment-section select {
    width: 100%;
    min-height: 46px;
    padding: 0 13px;
    border: 1px solid #e5e5e5;
    border-radius: 10px;
    background: #ffffff;
    color: #252525;
    font-family: inherit;
    font-size: 13px;
}

.selected-rekening-card {
    margin-top: 16px;
    padding: 16px;
    border: 1px solid #ffe0e0;
    border-radius: 12px;
    background: #fff7f7;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.selected-rekening-card > div {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    flex-wrap: wrap;
}

.selected-rekening-card span {
    color: #777777;
    font-size: 12px;
    font-weight: 600;
}

.selected-rekening-card strong {
    color: #252525;
    font-size: 13px;
    font-weight: 700;
}

.copy-button {
    min-height: 30px;
    padding: 0 10px;
    border: 1px solid #ca0708;
    border-radius: 8px;
    color: #ca0708;
    background: #ffffff;
    font-family: inherit;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
}

.transfer-amount {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-top: 16px;
    padding: 15px;
    border: 1px solid #e9eaed;
    border-radius: 10px;
    background: #f8f9fa;
}

.transfer-amount span {
    color: #666666;
    font-size: 13px;
    font-weight: 600;
}

.transfer-amount strong {
    color: #ca0708;
    font-size: 17px;
    font-weight: 800;
}

.transfer-note {
    margin: 12px 0 0;
    color: #777777;
    font-size: 12px;
    line-height: 1.6;
}

.payment-actions {
    display: flex;
    gap: 12px;
    margin-top: 25px;
}

.payment-back-button,
.payment-submit-button {
    min-height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    padding: 0 18px;
    border-radius: 10px;
    font-family: inherit;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.2s ease, transform 0.2s ease;
}

.payment-back-button {
    flex: 0 0 auto;
    border: 1px solid #e5e5e5;
    color: #555555;
    background: #ffffff;
}

.payment-back-button:hover:not(:disabled) {
    background: #f5f5f5;
}

.payment-submit-button {
    flex: 1;
    border: none;
    color: #ffffff;
    background: #ca0708;
}

.payment-submit-button:hover:not(:disabled) {
    transform: translateY(-2px);
    background: #a80000;
}

.payment-submit-button:disabled,
.payment-back-button:disabled {
    opacity: 0.55;
    cursor: not-allowed;
}

.payment-submit-button svg {
    width: 19px;
    height: 19px;
}

@keyframes paymentFadeUp {
    from {
        opacity: 0;
        transform: translateY(8px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 600px) {
    .cashier-payment {
        padding: 17px;
        border-radius: 14px;
    }

    .payment-heading h2 {
        font-size: 17px;
    }

    .payment-heading p {
        font-size: 12px;
    }

    .payment-total-card strong {
        font-size: 22px;
    }

    .payment-method-grid {
        gap: 8px;
    }

    .payment-method-button {
        min-height: 95px;
        padding: 10px;
        font-size: 12px;
    }

    .payment-actions {
        gap: 8px;
    }

    .payment-back-button,
    .payment-submit-button {
        min-height: 46px;
        padding: 0 12px;
        font-size: 12px;
    }
}

@media (max-width: 360px) {
    .payment-method-grid {
        grid-template-columns: 1fr;
    }

    .payment-method-button {
        min-height: 65px;
        flex-direction: row;
        justify-content: flex-start;
    }
}

@media (prefers-reduced-motion: reduce) {
    .cash-payment-section,
    .non-cash-info {
        animation: none;
    }

    .payment-method-button,
    .cash-input-wrapper,
    .payment-back-button,
    .payment-submit-button {
        transition: none;
    }

    .payment-method-button:hover,
    .payment-submit-button:hover:not(:disabled) {
        transform: none;
    }
}

.qris-payment-section {
    margin-top: 24px;
    padding: 20px;
    border: 1px solid #eeeeee;
    border-radius: 14px;
    background: #ffffff;
}

.qris-heading {
    text-align: center;
}

.qris-heading h3 {
    margin: 0;
    color: #252525;
    font-size: 17px;
    font-weight: 800;
}

.qris-heading p {
    margin: 7px 0 18px;
    color: #777777;
    font-size: 12px;
    line-height: 1.6;
}

.qris-image-wrapper {
    width: 100%;
    max-width: 290px;
    margin: 0 auto;
    padding: 12px;
    border: 1px solid #eeeeee;
    border-radius: 14px;
    background: #ffffff;
}

.qris-image {
    display: block;
    width: 100%;
    height: auto;
    object-fit: contain;
}

.qris-amount {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    margin-top: 18px;
    padding: 15px;
    border-radius: 11px;
    background: #fff5f5;
}

.qris-amount span {
    color: #777777;
    font-size: 12px;
}

.qris-amount strong {
    color: #ca0708;
    font-size: 23px;
    font-weight: 800;
}

.qris-instructions {
    margin-top: 20px;
}

.qris-instructions > strong {
    font-size: 13px;
    color: #333333;
}

.qris-instructions ol {
    margin: 10px 0 0;
    padding-left: 20px;
    color: #666666;
    font-size: 12px;
    line-height: 1.9;
}

.qris-confirmation {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-top: 20px;
    padding: 13px;
    border: 1px solid #f0cccc;
    border-radius: 10px;
    background: #fff8f8;
    cursor: pointer;
}

.qris-confirmation input {
    width: 17px;
    height: 17px;
    flex-shrink: 0;
    margin-top: 2px;
    accent-color: #ca0708;
    cursor: pointer;
}

.qris-confirmation span {
    color: #444444;
    font-size: 12px;
    line-height: 1.6;
    font-weight: 600;
}

.qris-confirmed-message {
    margin: 10px 0 0;
    color: #16834a;
    font-size: 12px;
    font-weight: 700;
}
</style>






