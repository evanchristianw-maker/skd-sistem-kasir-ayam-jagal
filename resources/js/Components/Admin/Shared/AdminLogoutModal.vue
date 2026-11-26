<script setup>
defineProps({
    show: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["cancel", "confirm"]);
</script>

<template>
    <Transition name="logout-modal">
        <div
            v-if="show"
            class="modal-backdrop"
            @click.self="emit('cancel')"
        >
            <div
                class="logout-modal"
                role="dialog"
                aria-modal="true"
                aria-labelledby="logout-title"
            >
                <div class="logout-modal-icon">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path
                            d="M10 17L15 12L10 7M15 12H3M12 3H19C20.1 3 21 3.9 21 5V19C21 20.1 20.1 21 19 21H12"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>
                </div>

                <h2 id="logout-title">Konfirmasi Keluar Akun</h2>

                <p>
                    Apakah Anda yakin ingin keluar dari akun administrator?
                </p>

                <div class="logout-modal-actions">
                    <button
                        type="button"
                        class="modal-cancel"
                        @click="emit('cancel')"
                    >
                        Batal
                    </button>

                    <button
                        type="button"
                        class="modal-confirm"
                        @click="emit('confirm')"
                    >
                        Keluar Akun
                    </button>
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.modal-backdrop {
    position: fixed;
    inset: 0;
    z-index: 300;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    background: rgba(20, 20, 20, 0.58);
    backdrop-filter: blur(4px);
}

.logout-modal {
    width: 100%;
    max-width: 420px;
    padding: 32px 28px 27px;
    border: 1px solid #eeeeee;
    border-radius: 19px;
    background: white;
    text-align: center;
    box-shadow: 0 20px 65px rgba(0, 0, 0, 0.22);
}

.logout-modal-icon {
    width: 65px;
    height: 65px;
    margin: 0 auto 19px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ca0708;
    background: #fff0f0;
    border-radius: 17px;
}

.logout-modal-icon svg {
    width: 31px;
    height: 31px;
}

.logout-modal h2 {
    margin: 0;
    color: #242424;
    font-size: 21px;
    font-weight: 800;
}

.logout-modal p {
    margin: 12px 0 25px;
    color: #626875;
    font-size: 14px;
    line-height: 1.7;
}

.logout-modal-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

.logout-modal-actions button {
    min-height: 48px;
    border-radius: 10px;
    font-family: inherit;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
}

.modal-cancel {
    color: #60656f;
    border: 1px solid #e5e6e9;
    background: white;
}

.modal-confirm {
    color: white;
    border: 1px solid #ca0708;
    background: #ca0708;
}

.logout-modal-enter-active,
.logout-modal-leave-active {
    transition: opacity 0.2s ease;
}

.logout-modal-enter-from,
.logout-modal-leave-to {
    opacity: 0;
}

.logout-modal-enter-active .logout-modal {
    animation: popupAppear 0.22s ease;
}

@keyframes popupAppear {
    from {
        opacity: 0;
        transform: translateY(-8px) scale(0.97);
    }

    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@media (max-width: 768px) {
    .logout-modal {
        max-width: 360px;
        padding: 28px 20px 24px;
    }

    .logout-modal h2 {
        font-size: 19px;
    }

    .logout-modal p {
        font-size: 13px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .logout-modal-enter-active,
    .logout-modal-leave-active {
        transition: none;
    }

    .logout-modal-enter-active .logout-modal {
        animation: none;
    }
}
</style>
