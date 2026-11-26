<script setup>
import { onBeforeUnmount, onMounted, watch } from "vue";

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["cancel", "confirm"]);

const handleEscape = (event) => {
    if (event.key === "Escape" && props.show) {
        emit("cancel");
    }
};

watch(
    () => props.show,
    (visible) => {
        document.body.style.overflow = visible ? "hidden" : "";
    },
);

onMounted(() => {
    window.addEventListener("keydown", handleEscape);
});

onBeforeUnmount(() => {
    window.removeEventListener("keydown", handleEscape);
    document.body.style.overflow = "";
});
</script>

<template>
    <Teleport to="body">
        <Transition name="logout-modal">
            <div
                v-if="show"
                class="cashier-logout-backdrop"
                @click.self="emit('cancel')"
            >
                <section
                    class="cashier-logout-card"
                    role="dialog"
                    aria-modal="true"
                    aria-labelledby="cashier-logout-title"
                    aria-describedby="cashier-logout-description"
                >
                    <div class="logout-icon">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path
                                d="M10 17L15 12L10 7M15 12H3M12 3H19C20.1 3 21 3.9 21 5V19C21 20.1 20.1 21 19 21H12"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                    </div>

                    <h2 id="cashier-logout-title">Konfirmasi Logout</h2>

                    <p id="cashier-logout-description">
                        Apakah Anda yakin ingin keluar dari akun Kasir?
                    </p>

                    <div class="logout-actions">
                        <button
                            type="button"
                            class="logout-cancel-button"
                            @click="emit('cancel')"
                        >
                            Batal
                        </button>

                        <button
                            type="button"
                            class="logout-confirm-button"
                            @click="emit('confirm')"
                        >
                            Ya, Keluar
                        </button>
                    </div>
                </section>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.cashier-logout-backdrop {
    position: fixed;
    inset: 0;
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    background: rgba(20, 20, 20, 0.48);
    backdrop-filter: blur(3px);
}

.cashier-logout-card {
    width: 100%;
    max-width: 420px;
    padding: 32px 28px;
    border: 1px solid #eeeeee;
    border-radius: 19px;
    background: #ffffff;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.18);
    text-align: center;
}

.logout-icon {
    width: 65px;
    height: 65px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 18px;
    border-radius: 19px;
    color: #ca0708;
    background: #fff0f0;
}

.logout-icon svg {
    width: 32px;
    height: 32px;
}

.cashier-logout-card h2 {
    margin: 0 0 10px;
    color: #252525;
    font-size: 21px;
    font-weight: 800;
}

.cashier-logout-card p {
    margin: 0;
    color: #777777;
    font-size: 14px;
    line-height: 1.6;
}

.logout-actions {
    display: flex;
    gap: 12px;
    margin-top: 27px;
}

.logout-actions button {
    flex: 1;
    min-height: 48px;
    border-radius: 10px;
    font-family: inherit;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.2s ease, transform 0.2s ease;
}

.logout-cancel-button {
    border: 1px solid #e5e5e5;
    color: #555555;
    background: #ffffff;
}

.logout-cancel-button:hover {
    background: #f5f5f5;
}

.logout-confirm-button {
    border: 1px solid #ca0708;
    color: #ffffff;
    background: #ca0708;
}

.logout-confirm-button:hover {
    transform: translateY(-2px);
    border-color: #a80000;
    background: #a80000;
}

.logout-actions button:focus-visible {
    outline: 3px solid rgba(202, 7, 8, 0.25);
    outline-offset: 3px;
}

.logout-modal-enter-active,
.logout-modal-leave-active {
    transition: opacity 0.2s ease;
}

.logout-modal-enter-active .cashier-logout-card {
    animation: logoutPopup 0.22s ease;
}

.logout-modal-enter-from,
.logout-modal-leave-to {
    opacity: 0;
}

@keyframes logoutPopup {
    from {
        opacity: 0;
        transform: translateY(12px) scale(0.96);
    }

    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@media (max-width: 480px) {
    .cashier-logout-card {
        padding: 27px 20px;
        border-radius: 16px;
    }

    .cashier-logout-card h2 {
        font-size: 19px;
    }

    .cashier-logout-card p {
        font-size: 13px;
    }

    .logout-actions {
        gap: 9px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .logout-modal-enter-active,
    .logout-modal-leave-active {
        transition: none;
    }

    .logout-modal-enter-active .cashier-logout-card {
        animation: none;
    }

    .logout-actions button {
        transition: none;
    }

    .logout-confirm-button:hover {
        transform: none;
    }
}
</style>
