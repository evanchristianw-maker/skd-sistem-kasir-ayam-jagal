<script setup>
defineProps({
    showModal: {
        type: Boolean,
        default: false,
    },
    isEditing: {
        type: Boolean,
        default: false,
    },
    form: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        default: () => [],
    },
    saveMenu: {
        type: Function,
        required: true,
    },
    handleImageUpload: {
        type: Function,
        required: true,
    },
});

defineEmits(["close"]);
</script>

<template>
    <Transition name="menu-modal">
        <div
            v-if="showModal"
            class="modal-overlay"
            @click.self="$emit('close')"
        >
            <section
                class="modal-card"
                role="dialog"
                aria-modal="true"
                :aria-label="isEditing ? 'Edit menu' : 'Tambah menu baru'"
            >
                <!-- Header -->
                <div class="modal-header">
                    <div>
                        <span class="eyebrow">DATA PRODUK</span>

                        <h2>
                            {{ isEditing ? "Edit Menu" : "Tambah Menu Baru" }}
                        </h2>

                        <p>Lengkapi informasi produk di bawah ini.</p>
                    </div>

                    <button
                        type="button"
                        class="close-button"
                        aria-label="Tutup"
                        @click="$emit('close')"
                    >
                        <svg viewBox="0 0 24 24" fill="none">
                            <path
                                d="M18 6L6 18M6 6L18 18"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                            />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <form @submit.prevent="saveMenu">
                    <div class="form-group">
                        <label for="menu-name"> Nama Menu <i>*</i> </label>

                        <input
                            id="menu-name"
                            v-model="form.name"
                            type="text"
                            placeholder="Contoh: Ayam Geprek Original"
                            maxlength="100"
                            required
                        />
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="menu-category">
                                Kategori <i>*</i>
                            </label>

                            <select
                                id="menu-category"
                                v-model="form.category"
                                required
                            >
                                <option value="" disabled>
                                    Pilih kategori
                                </option>

                                <option
                                    v-for="category in categories.slice(1)"
                                    :key="category"
                                    :value="category"
                                >
                                    {{ category }}
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="menu-price">
                                Harga (Rp) <i>*</i>
                            </label>

                            <input
                                id="menu-price"
                                v-model="form.price"
                                type="number"
                                min="1"
                                step="1"
                                placeholder="15000"
                                required
                            />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="menu-description"> Deskripsi </label>

                        <textarea
                            id="menu-description"
                            v-model="form.description"
                            rows="3"
                            placeholder="Deskripsi singkat menu..."
                        ></textarea>
                    </div>

                    <div class="form-group">
                        <label for="menu-image"> Foto Menu </label>

                        <label for="menu-image" class="upload-area">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path
                                    d="M12 16V4M7 9L12 4L17 9M4 16V19C4 19.55 4.45 20 5 20H19C19.55 20 20 19.55 20 19V16"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>

                            <span> Klik untuk memilih foto menu </span>

                            <small>Format gambar: JPG, PNG, atau WEBP</small>
                        </label>

                        <input
                            id="menu-image"
                            class="file-input"
                            type="file"
                            accept="image/*"
                            @change="handleImageUpload"
                        />

                        <div v-if="form.image" class="preview-container">
                            <img
                                :src="form.image"
                                class="image-preview"
                                alt="Preview foto menu"
                            />
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="modal-actions">
                        <button
                            type="button"
                            class="secondary-button"
                            @click="$emit('close')"
                        >
                            Batal
                        </button>

                        <button type="submit" class="primary-button">
                            {{ isEditing ? "Simpan Perubahan" : "Simpan Menu" }}
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </Transition>
</template>

<style scoped>
/* Overlay */
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

/* Modal */
.modal-card {
    width: 100%;
    max-width: 560px;
    max-height: 90vh;
    overflow-y: auto;
    padding: 30px;
    border: 1px solid #eeeeee;
    border-radius: 19px;
    background: #ffffff;
    box-shadow: 0 20px 65px rgba(0, 0, 0, 0.22);
}

/* Header */
.modal-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 15px;
    margin-bottom: 26px;
}

.eyebrow {
    color: #ca0708;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1px;
}

.modal-header h2 {
    margin: 8px 0 0;
    color: #242424;
    font-size: 22px;
    font-weight: 800;
}

.modal-header p {
    margin: 8px 0 0;
    color: #777e89;
    font-size: 13px;
    line-height: 1.6;
}

.close-button {
    width: 38px;
    height: 38px;
    flex: 0 0 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #eeeeee;
    border-radius: 10px;
    color: #626875;
    background: #ffffff;
    cursor: pointer;
    transition: 0.2s ease;
}

.close-button:hover {
    color: #ca0708;
    border-color: #f5caca;
    background: #fff0f0;
}

.close-button svg {
    width: 19px;
    height: 19px;
}

/* Form */
.form-group {
    display: flex;
    flex-direction: column;
    gap: 9px;
    margin-bottom: 19px;
}

.form-group label {
    color: #33363d;
    font-size: 13px;
    font-weight: 700;
}

.form-group label i {
    color: #ca0708;
    font-style: normal;
}

.form-group input:not([type="file"]),
.form-group select,
.form-group textarea {
    width: 100%;
    min-height: 46px;
    padding: 11px 13px;
    border: 1px solid #e5e6e9;
    border-radius: 10px;
    outline: none;
    color: #33363d;
    background: #ffffff;
    font-family: inherit;
    font-size: 13px;
    transition:
        border-color 0.2s ease,
        box-shadow 0.2s ease;
}

.form-group textarea {
    min-height: 95px;
    resize: vertical;
    line-height: 1.6;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: #ca0708;
    box-shadow: 0 0 0 3px rgba(202, 7, 8, 0.08);
}

.form-group input::placeholder,
.form-group textarea::placeholder {
    color: #a0a5af;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

/* Image upload */
.file-input {
    display: none;
}

.upload-area {
    min-height: 115px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 18px;
    border: 1px dashed #d9dce2;
    border-radius: 12px;
    color: #626875;
    background: #fafafa;
    cursor: pointer;
    transition: 0.2s ease;
}

.upload-area:hover {
    border-color: #ca0708;
    color: #ca0708;
    background: #fff8f8;
}

.upload-area svg {
    width: 27px;
    height: 27px;
}

.upload-area span {
    font-size: 13px;
    font-weight: 700;
}

.upload-area small {
    color: #9298a3;
    font-size: 11px;
}

.preview-container {
    margin-top: 12px;
}

.image-preview {
    width: 100%;
    max-height: 180px;
    object-fit: contain;
    border: 1px solid #eeeeee;
    border-radius: 10px;
    background: #fafafa;
}

/* Actions */
.modal-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-top: 27px;
    padding-top: 20px;
    border-top: 1px solid #f0f0f0;
}

.modal-actions button {
    min-height: 48px;
    border-radius: 10px;
    font-family: inherit;
    font-size: 13px;
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

.primary-button {
    color: #ffffff;
    border: 1px solid #ca0708;
    background: #ca0708;
}

.primary-button:hover {
    border-color: #a90607;
    background: #a90607;
    box-shadow: 0 5px 14px rgba(202, 7, 8, 0.18);
}

.modal-actions button:active {
    transform: scale(0.97);
}

.modal-actions button:focus-visible {
    outline: 3px solid rgba(202, 7, 8, 0.2);
    outline-offset: 3px;
}

/* Animation */
.menu-modal-enter-active,
.menu-modal-leave-active {
    transition: opacity 0.2s ease;
}

.menu-modal-enter-from,
.menu-modal-leave-to {
    opacity: 0;
}

.menu-modal-enter-active .modal-card {
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

/* Responsive */
@media (max-width: 768px) {
    .modal-card {
        max-width: 460px;
        padding: 25px 20px;
    }

    .modal-header h2 {
        font-size: 20px;
    }

    .form-row {
        grid-template-columns: 1fr;
        gap: 0;
    }
}

@media (max-width: 480px) {
    .modal-overlay {
        padding: 12px;
    }

    .modal-card {
        max-height: 92vh;
        padding: 22px 17px;
        border-radius: 15px;
    }

    .modal-header h2 {
        font-size: 18px;
    }

    .modal-actions {
        gap: 9px;
    }

    .modal-actions button {
        font-size: 12px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .menu-modal-enter-active,
    .menu-modal-leave-active {
        transition: none;
    }

    .menu-modal-enter-active .modal-card {
        animation: none;
    }

    .modal-actions button,
    .close-button {
        transition: none;
    }
}
</style>
