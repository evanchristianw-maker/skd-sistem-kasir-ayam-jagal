import { ref, computed } from "vue";
import axios from "axios";

export default function useCashierTransaction(initialMenus = []) {
    const menus = ref(initialMenus);
    const cart = ref([]);

    const paymentMethod = ref("tunai");
    const cashReceived = ref("");

    const processing = ref(false);
    const errors = ref({});
    const errorMessage = ref("");
    const transactionResult = ref(null);

    const totalItems = computed(() => {
        return cart.value.reduce(
            (total, item) => total + item.quantity,
            0,
        );
    });

    const total = computed(() => {
        return cart.value.reduce(
            (sum, item) => sum + item.price * item.quantity,
            0,
        );
    });

    const change = computed(() => {
        return Math.max(Number(cashReceived.value || 0) - total.value, 0);
    });

    const formatRupiah = (value) => {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(Number(value) || 0);
    };

    const getMenuImage = (menu) => {
        if (menu.image) {
            return menu.image;
        }

        const menuName = String(menu.name || "")
            .toLowerCase()
            .trim();

        const imageMappings = [
            {
                keywords: ["ayam goreng", "nasi ayam goreng"],
                image: "/assets/images/products/ayam-goreng.png",
            },
            {
                keywords: ["ayam geprek"],
                image: "/assets/images/products/ayam-goreng.png",
            },
        ];

        const matchedImage = imageMappings.find((item) =>
            item.keywords.some((keyword) => menuName.includes(keyword))
        );

        return matchedImage?.image || "";
    };

    const addToCart = (menu) => {
        const existingItem = cart.value.find(
            (item) => item.id === menu.id,
        );

        if (existingItem) {
            existingItem.quantity += 1;
            return;
        }

        cart.value.push({
            id: menu.id,
            name: menu.name,
            category: menu.category,
            price: Number(menu.price) || 0,
            image: getMenuImage(menu),
            quantity: 1,
        });
    };

    const increaseQuantity = (item) => {
        const existingItem = cart.value.find(
            (cartItem) => cartItem.id === item.id,
        );

        if (existingItem) {
            existingItem.quantity += 1;
        }
    };

    const decreaseQuantity = (item) => {
        const existingItem = cart.value.find(
            (cartItem) => cartItem.id === item.id,
        );

        if (!existingItem) return;

        if (existingItem.quantity > 1) {
            existingItem.quantity -= 1;
            return;
        }

        removeFromCart(item);
    };

    const removeFromCart = (item) => {
        cart.value = cart.value.filter(
            (cartItem) => cartItem.id !== item.id,
        );
    };

    const clearCart = () => {
        cart.value = [];
    };

    const selectPaymentMethod = (method) => {
        if (!["tunai", "qris", "transfer"].includes(method)) {
            return;
        }

        paymentMethod.value = method;

        if (method !== "tunai") {
            cashReceived.value = "";
        }
    };

    const resetErrors = () => {
        errors.value = {};
        errorMessage.value = "";
    };

    const submitTransaction = async () => {
        resetErrors();

        if (processing.value) return null;

        if (!cart.value.length) {
            errorMessage.value = "Keranjang transaksi masih kosong.";
            return null;
        }

        if (paymentMethod.value === "tunai") {
            if (
                cashReceived.value === "" ||
                Number(cashReceived.value) < total.value
            ) {
                errors.value = {
                    uang_dibayarkan: [
                        "Nominal uang yang diterima belum mencukupi.",
                    ],
                };

                return null;
            }
        }

        processing.value = true;

        const payload = {
            items: cart.value.map((item) => ({
                menu_id: item.id,
                jumlah: item.quantity,
            })),
            metode_pembayaran: paymentMethod.value,
            uang_dibayarkan:
                paymentMethod.value === "tunai"
                    ? Number(cashReceived.value)
                    : null,
        };

        try {
            const response = await axios.post(
                route("transaksi.store"),
                payload,
                {
                    headers: {
                        Accept: "application/json",
                        "Content-Type": "application/json",
                    },
                },
            );

            const result = response.data?.data;

            if (!result) {
                throw new Error(
                    "Respons transaksi tidak memiliki data.",
                );
            }

            transactionResult.value = result;

            sessionStorage.setItem(
                "cashier_last_transaction",
                JSON.stringify(result),
            );

            return result;
        } catch (error) {
            const response = error.response;

            if (response?.status === 422) {
                errors.value = response.data?.errors || {};
                errorMessage.value =
                    response.data?.message ||
                    "Periksa kembali data transaksi.";
            } else {
                errorMessage.value =
                    response?.data?.message ||
                    error.message ||
                    "Terjadi kesalahan saat memproses transaksi.";
            }

            return null;
        } finally {
            processing.value = false;
        }
    };

    const restoreTransactionResult = () => {
        try {
            const storedResult = sessionStorage.getItem(
                "cashier_last_transaction",
            );

            if (!storedResult) return null;

            transactionResult.value = JSON.parse(storedResult);

            return transactionResult.value;
        } catch {
            sessionStorage.removeItem("cashier_last_transaction");
            transactionResult.value = null;

            return null;
        }
    };

    const clearTransactionResult = () => {
        transactionResult.value = null;
        sessionStorage.removeItem("cashier_last_transaction");
    };

    return {
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
        change,
        formatRupiah,
        addToCart,
        increaseQuantity,
        decreaseQuantity,
        removeFromCart,
        clearCart,
        selectPaymentMethod,
        submitTransaction,
        restoreTransactionResult,
        clearTransactionResult,
    };
}

