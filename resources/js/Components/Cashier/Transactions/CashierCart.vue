<script setup>
import { computed } from "vue";

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
    total: {
        type: Number,
        default: 0,
    },
});

const emit = defineEmits([
    "increase",
    "decrease",
    "remove",
    "continue-payment",
]);

const formatCurrency = (value) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(Number(value) || 0);
};

const totalItems = computed(() => {
    return props.items.reduce(
        (total, item) => total + Number(item.quantity || 0),
        0,
    );
});
</script>

<template>
    <section class="cashier-cart">
        <div class="cart-heading">
            <div>
                <h2>Keranjang Pesanan</h2>
                <p>Daftar menu yang dipilih pelanggan.</p>
            </div>

            <span class="cart-item-count">
                {{ totalItems }} Item
            </span>
        </div>

        <div v-if="items.length" class="cart-content">
            <div class="cart-item-list">
                <article
                    v-for="item in items"
                    :key="item.id"
                    class="cart-item"
                >
                                        <div class="cart-item-info">
                        <div v-if="item.image" class="cart-item-image">
                            <img
                                :src="item.image"
                                :alt="item.name"
                                loading="lazy"
                            />
                        </div>

                        <div v-else class="cart-item-image cart-item-image-empty">
                            <span>Foto belum tersedia</span>
                        </div>

                        <div class="cart-item-details">
                            <h3>{{ item.name }}</h3>
                            <span>{{ formatCurrency(item.price) }}</span>
                        </div>
                    </div>
<div class="cart-item-actions">
                        <div class="quantity-control">
                            <button
                                type="button"
                                aria-label="Kurangi jumlah"
                                @click="emit('decrease', item)"
                            >
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path
                                        d="M5 12H19"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                    />
                                </svg>
                            </button>

                            <span>{{ item.quantity }}</span>

                            <button
                                type="button"
                                aria-label="Tambah jumlah"
                                @click="emit('increase', item)"
                            >
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path
                                        d="M12 5V19M5 12H19"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                    />
                                </svg>
                            </button>
                        </div>

                        <strong class="cart-item-subtotal">
                            {{ formatCurrency(item.price * item.quantity) }}
                        </strong>

                        <button
                            type="button"
                            class="cart-remove-button"
                            :aria-label="`Hapus ${item.name} dari keranjang`"
                            @click="emit('remove', item)"
                        >
                            <svg viewBox="0 0 24 24" fill="none">
                                <path
                                    d="M4 7H20M10 11V17M14 11V17M5.5 7L6.5 20H17.5L18.5 7M9 7V4H15V7"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>
                        </button>
                    </div>
                </article>
            </div>

            <div class="cart-summary">
                <div class="cart-total-row">
                    <span>Total Pembayaran</span>
                    <strong>{{ formatCurrency(total) }}</strong>
                </div>

                <button
                    type="button"
                    class="cart-checkout-button"
                    :disabled="totalItems === 0"
                    @click="emit('continue-payment')"
                >
                    Lanjut ke Pembayaran
                    <svg viewBox="0 0 24 24" fill="none">
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
        </div>

        <div v-else class="cart-empty-state">
            <div class="cart-empty-icon">
                <svg viewBox="0 0 24 24" fill="none">
                    <path
                        d="M3 4H5L7.2 15.2C7.4 16.2 8.3 17 9.4 17H17C18 17 18.9 16.3 19.2 15.3L21 8H6"
                        stroke="currentColor"
                        stroke-width="1.7"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                    <circle cx="10" cy="20" r="1" fill="currentColor" />
                    <circle cx="17" cy="20" r="1" fill="currentColor" />
                </svg>
            </div>

            <h3>Keranjang masih kosong</h3>
            <p>Pilih menu dari daftar untuk memulai transaksi.</p>
        </div>
    </section>
</template>

<style scoped>
* {
    box-sizing: border-box;
}

.cashier-cart {
    min-width: 0;
    padding: 24px;
    border: 1px solid #e9eaed;
    border-radius: 16px;
    background: #ffffff;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.035);
}

.cart-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 20px;
}

.cart-heading h2 {
    margin: 0;
    color: #252525;
    font-size: 19px;
    font-weight: 800;
}

.cart-heading p {
    margin: 6px 0 0;
    color: #777777;
    font-size: 13px;
    line-height: 1.5;
}

.cart-item-count {
    flex-shrink: 0;
    padding: 7px 12px;
    border-radius: 20px;
    color: #ca0708;
    background: #fff0f0;
    font-size: 12px;
    font-weight: 700;
}

.cart-item-list {
    display: flex;
    flex-direction: column;
}

.cart-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    padding: 16px 0;
    border-bottom: 1px solid #eeeeee;
    animation: cartItemAppear 0.25s ease both;
}

.cart-item-info {
    min-width: 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.cart-item-image {
    width: 70px;
    height: 70px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    border-radius: 12px;
    background: #f5f6f8;
}

.cart-item-image img {
    width: 100%;
    height: 100%;
    display: block;
    object-fit: cover;
    transition: transform 0.25s ease;
}

.cart-item-image:hover img {
    transform: scale(1.06);
}

.cart-item-image-empty {
    padding: 5px;
    color: #999999;
    font-size: 10px;
    text-align: center;
    line-height: 1.4;
}

.cart-item-details {
    min-width: 0;
}

.cart-item-details h3 {
    overflow-wrap: anywhere;
    margin: 0 0 6px;
    color: #303030;
    font-size: 14px;
    font-weight: 700;
}

.cart-item-info span {
    color: #888888;
    font-size: 12px;
}

.cart-item-actions {
    display: flex;
    align-items: center;
    gap: 13px;
}

.quantity-control {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 3px;
    border: 1px solid #eeeeee;
    border-radius: 9px;
    background: #fafafa;
}

.quantity-control button {
    width: 29px;
    height: 29px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    border-radius: 7px;
    color: #ca0708;
    background: #fff0f0;
    cursor: pointer;
    transition: color 0.2s ease, background 0.2s ease;
}

.quantity-control button:hover {
    color: #ffffff;
    background: #ca0708;
}

.quantity-control button:focus-visible,
.cart-remove-button:focus-visible,
.cart-checkout-button:focus-visible {
    outline: 3px solid rgba(202, 7, 8, 0.25);
    outline-offset: 3px;
}

.quantity-control button svg {
    width: 16px;
    height: 16px;
}

.quantity-control span {
    min-width: 17px;
    color: #333333;
    font-size: 13px;
    font-weight: 700;
    text-align: center;
}

.cart-item-subtotal {
    min-width: 95px;
    color: #333333;
    font-size: 13px;
    text-align: right;
}

.cart-remove-button {
    width: 34px;
    height: 34px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    border-radius: 8px;
    color: #ca0708;
    background: #fff0f0;
    cursor: pointer;
    transition: background 0.2s ease;
}

.cart-remove-button:hover {
    background: #ffe0e0;
}

.cart-remove-button svg {
    width: 19px;
    height: 19px;
}

.cart-summary {
    padding-top: 20px;
}

.cart-total-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 18px;
}

.cart-total-row span {
    color: #666666;
    font-size: 13px;
    font-weight: 600;
}

.cart-total-row strong {
    color: #ca0708;
    font-size: 21px;
    font-weight: 800;
}

.cart-checkout-button {
    width: 100%;
    min-height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
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

.cart-checkout-button:hover:not(:disabled) {
    transform: translateY(-2px);
    background: #a80000;
}

.cart-checkout-button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.cart-checkout-button svg {
    width: 19px;
    height: 19px;
}

.cart-empty-state {
    min-height: 245px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 25px;
    text-align: center;
}

.cart-empty-icon {
    width: 58px;
    height: 58px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 14px;
    border-radius: 16px;
    color: #ca0708;
    background: #fff0f0;
}

.cart-empty-icon svg {
    width: 29px;
    height: 29px;
}

.cart-empty-state h3 {
    margin: 0 0 7px;
    color: #333333;
    font-size: 15px;
    font-weight: 750;
}

.cart-empty-state p {
    margin: 0;
    color: #888888;
    font-size: 13px;
    line-height: 1.5;
}

@keyframes cartItemAppear {
    from {
        opacity: 0;
        transform: translateY(7px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 600px) {
    .cashier-cart {
        padding: 17px;
        border-radius: 14px;
    }

    .cart-heading h2 {
        font-size: 17px;
    }

    .cart-heading p {
        font-size: 12px;
    }

    .cart-item {
        align-items: flex-start;
        flex-direction: column;
        gap: 12px;
    }

    .cart-item-actions {
        width: 100%;
        justify-content: space-between;
        gap: 8px;
    }

    .cart-item-subtotal {
        min-width: 0;
        margin-left: auto;
    }

    .cart-total-row strong {
        font-size: 18px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .cart-item {
        animation: none;
    }

    .quantity-control button,
    .cart-remove-button,
    .cart-checkout-button {
        transition: none;
    }

    .cart-checkout-button:hover:not(:disabled) {
        transform: none;
    }
}
</style>



