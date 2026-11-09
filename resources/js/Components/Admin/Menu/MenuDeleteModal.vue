<script setup>
import { ref, watch, nextTick, onBeforeUnmount } from "vue";

const props = defineProps({
    showDeleteModal: {
        type: Boolean,
        default: false,
    },
    deleteMenu: {
        type: Function,
        required: true,
    },
});

const emit = defineEmits(["close"]);

const modalRef = ref(null);
const previousActiveElement = ref(null);

const closeModal = () => {
    emit("close");
};

const handleKeydown = (event) => {
    if (!props.showDeleteModal) return;

    if (event.key === "Escape") {
        event.preventDefault();
        closeModal();
        return;
    }

    if (event.key === "Tab") {
        const focusableElements = modalRef.value?.querySelectorAll(
            'button:not(:disabled), [href], input:not(:disabled), [tabindex]:not([tabindex="-1"])',
        );

        if (!focusableElements?.length) return;

        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];

        if (
            event.shiftKey &&
            document.activeElement === firstElement
        ) {
            event.preventDefault();
            lastElement.focus();
        } else if (
            !event.shiftKey &&
            document.activeElement === lastElement
        ) {
            event.preventDefault();
            firstElement.focus();
        }
    }
};

watch(
    () => props.showDeleteModal,
    async (isOpen) => {
        if (isOpen) {
            previousActiveElement.value = document.activeElement;

            await nextTick();

            modalRef.value?.querySelector(".secondary-button")?.focus();

            document.addEventListener("keydown", handleKeydown);
        } else {
            document.removeEventListener("keydown", handleKeydown);

            await nextTick();

            previousActiveElement.value?.focus?.();
            previousActiveElement.value = null;
        }
    },
);

onBeforeUnmount(() => {
    document.removeEventListener("keydown", handleKeydown);
});
</script>

<template>
    <Transition name="delete-modal">
        <div
            v-if="showDeleteModal"
            class="modal-overlay"
            @click.self="closeModal"
        >
            <section
                ref="modalRef"
                class="delete-modal"
                role="alertdialog"
                aria-modal="true"
                aria-labelledby="delete-title"
                aria-describedby="delete-description"
                tabindex="-1"
            >
                <div class="delete-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path
                            d="M3 6H21M8 6V4H16V6M19 6L18 20H6L5 6M10 11V16M14 11V16"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>
                </div>

                <h2 id="delete-title">Hapus Menu?</h2>

                <p id="delete-description">
                    Menu yang dihapus tidak dapat dikembalikan. Apakah Anda
                    yakin ingin melanjutkan?
                </p>

                <div class="modal-actions">
                    <button
                        type="button"
                        class="secondary-button"
                        @click="closeModal"
                    >
                        Batal
                    </button>

                    <button
                        type="button"
                        class="danger-button"
                        @click="deleteMenu"
                    >
                        Ya, Hapus
                    </button>
                </div>
            </section>
        </div>
    </Transition>
</template>

<style scoped>
.modal-overlay {
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

.delete-modal {
    width: 100%;
    max-width: 420px;
    padding: 32px 28px 27px;
    border: 1px solid #eeeeee;
    border-radius: 19px;
    background: #ffffff;
    text-align: center;
    box-shadow: 0 20px 65px rgba(0, 0, 0, 0.22);
}

.delete-icon {
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

.delete-icon svg {
    width: 31px;
    height: 31px;
}

.delete-modal h2 {
    margin: 0;
    color: #242424;
    font-size: 21px;
    font-weight: 800;
}

.delete-modal p {
    margin: 12px 0 25px;
    color: #626875;
    font-size: 14px;
    line-height: 1.7;
}

.modal-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

.modal-actions button {
    min-height: 48px;
    border-radius: 10px;
    font-family: inherit;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition:
        background 0.2s ease,
        border-color 0.2s ease,
        transform 0.2s ease,
        box-shadow 0.2s ease;
}

.secondary-button {
    color: #60656f;
    border: 1px solid #e5e6e9;
    background: #ffffff;
}

.secondary-button:hover {
    border-color: #cfd2d8;
    background: #f8f8f9;
}

.danger-button {
    color: #ffffff;
    border: 1px solid #ca0708;
    background: #ca0708;
}

.danger-button:hover {
    background: #a90607;
    border-color: #a90607;
    box-shadow: 0 5px 14px rgba(202, 7, 8, 0.18);
}

.modal-actions button:active {
    transform: scale(0.97);
}

.modal-actions button:focus-visible {
    outline: 3px solid rgba(202, 7, 8, 0.2);
    outline-offset: 3px;
}

.delete-modal-enter-active,
.delete-modal-leave-active {
    transition: opacity 0.2s ease;
}

.delete-modal-enter-from,
.delete-modal-leave-to {
    opacity: 0;
}

.delete-modal-enter-active .delete-modal {
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
    .delete-modal {
        max-width: 360px;
        padding: 28px 20px 24px;
    }

    .delete-modal h2 {
        font-size: 19px;
    }

    .delete-modal p {
        font-size: 13px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .delete-modal-enter-active,
    .delete-modal-leave-active {
        transition: none;
    }

    .delete-modal-enter-active .delete-modal {
        animation: none;
    }

    .modal-actions button {
        transition: none;
    }
}
</style>
