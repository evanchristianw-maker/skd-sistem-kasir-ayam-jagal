<script setup>
import { ref, computed } from "vue";
import { router, usePage } from "@inertiajs/vue3";

// SHARED COMPONENTS
import AdminHeader from "@/Components/Admin/Shared/AdminHeader.vue";
import AdminLogoutModal from "@/Components/Admin/Shared/AdminLogoutModal.vue";
import MenuBottomNavigation from "@/Components/Admin/Menu/MenuBottomNavigation.vue";

// COMPOSABLE
import useRekeningBankManagement from "@/Composables/useRekeningBankManagement.js";

// STYLES
import "@/css/rekening-bank-management.css";

// PAGE PROPS
const props = defineProps({
    rekeningBanks: {
        type: Array,
        default: () => [],
    },
});

// AUTHENTICATION
const page = usePage();

const adminName = computed(() => {
    return page.props.auth?.user?.name || "Admin";
});

const adminEmail = computed(() => {
    return page.props.auth?.user?.email || "-";
});

// FLASH MESSAGES
const flashSuccess = computed(() => page.props.flash?.success || "");
const flashError = computed(() => page.props.flash?.error || "");

// LOGOUT MANAGEMENT
const showingLogoutConfirmation = ref(false);

const openLogoutConfirmation = () => {
    showingLogoutConfirmation.value = true;
};

const cancelLogout = () => {
    showingLogoutConfirmation.value = false;
};

const logout = () => {
    router.post(route("logout"));
};

// REKENING BANK MANAGEMENT
const {
    searchQuery,
    showModal,
    showDeleteModal,
    actionType,
    actionError,
    isEditing,
    form,
    formErrors,
    filteredRekeningBanks,
    totalRekening,
    rekeningAktif,
    rekeningNonaktif,
    currentDate,
    openAddModal,
    openEditModal,
    saveRekening,
    confirmDelete,
    confirmToggleStatus,
    confirmAction,
} = useRekeningBankManagement(props.rekeningBanks);

// SUMMARY CARDS
const summaryCards = computed(() => [
    {
        title: "Total Rekening",
        value: totalRekening.value,
        description: "Seluruh rekening",
        icon: "total",
        color: "red",
    },
    {
        title: "Rekening Aktif",
        value: rekeningAktif.value,
        description: "Siap digunakan",
        icon: "active",
        color: "green",
    },
    {
        title: "Rekening Nonaktif",
        value: rekeningNonaktif.value,
        description: "Tidak tersedia",
        icon: "inactive",
        color: "orange",
    },
]);

// MODAL REFS
const modalRef = ref(null);
const deleteModalRef = ref(null);
</script>

<template>
    <div class="app-layout">
        <!-- HEADER -->
        <AdminHeader
            :admin-name="adminName"
            :admin-email="adminEmail"
            :current-date="currentDate"
            @logout-request="openLogoutConfirmation"
        />

        <!-- MAIN CONTENT -->
        <main class="main-content" aria-labelledby="rekening-page-title">
            <div class="rekening-container">
                <!-- FLASH MESSAGES -->
                <div
                    v-if="flashSuccess"
                    class="flash-message flash-success"
                    role="status"
                >
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle
                            cx="12"
                            cy="12"
                            r="9"
                            stroke="currentColor"
                            stroke-width="1.8"
                        />
                        <path
                            d="M8 12L10.5 14.5L16 9"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>
                    {{ flashSuccess }}
                </div>

                <div
                    v-if="flashError"
                    class="flash-message flash-error"
                    role="alert"
                >
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle
                            cx="12"
                            cy="12"
                            r="9"
                            stroke="currentColor"
                            stroke-width="1.8"
                        />
                        <path
                            d="M15 9L9 15M9 9L15 15"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                        />
                    </svg>
                    {{ flashError }}
                </div>

                <!-- PAGE HEADING -->
                <section class="page-heading">
                    <div class="page-heading-content">
                        <h1 id="rekening-page-title">
                            Manajemen Rekening Bank
                        </h1>

                        <p>
                            Kelola rekening bank tujuan pembayaran transfer
                            pelanggan.
                        </p>
                    </div>

                    <button
                        type="button"
                        class="primary-button"
                        aria-label="Tambah rekening bank baru"
                        @click="openAddModal"
                    >
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            aria-hidden="true"
                        >
                            <path
                                d="M12 5V19M5 12H19"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                            />
                        </svg>

                        <span>Tambah Rekening</span>
                    </button>
                </section>

                <!-- SUMMARY CARDS -->
                <section
                    class="summary-section"
                    aria-labelledby="rekening-summary-title"
                >
                    <div class="section-heading">
                        <h3 id="rekening-summary-title">
                            Ringkasan Rekening Bank
                        </h3>
                    </div>

                    <div class="summary-grid" aria-live="polite">
                        <article
                            v-for="(card, index) in summaryCards"
                            :key="card.title"
                            class="summary-card"
                            :class="`card-${card.color}`"
                            :style="{ '--card-index': index }"
                        >
                            <div class="summary-card-top">
                                <div class="summary-icon">
                                    <!-- Total -->
                                    <svg
                                        v-if="card.icon === 'total'"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        aria-hidden="true"
                                    >
                                        <rect
                                            x="3"
                                            y="5"
                                            width="18"
                                            height="14"
                                            rx="2"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                        />
                                        <path
                                            d="M7 15H11M7 11H17"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            stroke-linecap="round"
                                        />
                                    </svg>

                                    <!-- Aktif -->
                                    <svg
                                        v-else-if="card.icon === 'active'"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        aria-hidden="true"
                                    >
                                        <circle
                                            cx="12"
                                            cy="12"
                                            r="9"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                        />
                                        <path
                                            d="M8 12L10.5 14.5L16 9"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>

                                    <!-- Nonaktif -->
                                    <svg
                                        v-else
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        aria-hidden="true"
                                    >
                                        <circle
                                            cx="12"
                                            cy="12"
                                            r="9"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                        />
                                        <path
                                            d="M9 9L15 15M15 9L9 15"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            stroke-linecap="round"
                                        />
                                    </svg>
                                </div>
                            </div>

                            <div class="summary-card-bottom">
                                <p>{{ card.title }}</p>
                                <h3>{{ card.value }}</h3>
                                <span>{{ card.description }}</span>
                            </div>
                        </article>
                    </div>
                </section>

                <!-- REKENING BANK TABLE -->
                <section class="rekening-panel">
                    <div class="panel-heading">
                        <div>
                            <h2>Daftar Rekening Bank</h2>
                            <p>
                                Kelola informasi rekening bank tujuan
                                pembayaran.
                            </p>
                        </div>

                        <span class="result-count">
                            {{ filteredRekeningBanks.length }} rekening
                        </span>
                    </div>

                    <div class="toolbar">
                        <label class="search-box">
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                aria-hidden="true"
                            >
                                <circle
                                    cx="10.8"
                                    cy="10.8"
                                    r="6.8"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                />
                                <path
                                    d="m16 16 4.2 4.2"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                />
                            </svg>

                            <input
                                v-model="searchQuery"
                                type="search"
                                placeholder="Cari nama bank, nomor rekening, atau atas nama..."
                                aria-label="Cari rekening bank"
                            />
                        </label>
                    </div>

                    <div class="table-wrapper">
                        <table>
                            <caption class="sr-only">
                                Daftar rekening bank tujuan pembayaran transfer
                                beserta status dan tindakan pengelolaan.
                            </caption>
                            <thead>
                                <tr>
                                    <th scope="col">BANK</th>
                                    <th scope="col">NOMOR REKENING</th>
                                    <th scope="col">ATAS NAMA</th>
                                    <th scope="col">STATUS</th>
                                    <th scope="col" class="action-heading">
                                        AKSI
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr
                                    v-for="rekening in filteredRekeningBanks"
                                    :key="rekening.id"
                                >
                                    <!-- Bank -->
                                    <td data-label="Bank">
                                        <div class="bank-info">
                                            <div class="bank-icon">
                                                <svg
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    aria-hidden="true"
                                                >
                                                    <path
                                                        d="M3 21H21M4 18H20M6 18V13M10 18V13M14 18V13M18 18V13M3 10L12 4L21 10H3Z"
                                                        stroke="currentColor"
                                                        stroke-width="1.7"
                                                        stroke-linejoin="round"
                                                    />
                                                </svg>
                                            </div>

                                            <div class="bank-copy">
                                                <strong>{{
                                                    rekening.nama_bank
                                                }}</strong>
                                                <small>
                                                    Ditambahkan
                                                    {{
                                                        rekening.created_at
                                                    }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Nomor Rekening -->
                                    <td data-label="No. Rekening">
                                        <span class="rekening-number">
                                            {{ rekening.nomor_rekening }}
                                        </span>
                                    </td>

                                    <!-- Atas Nama -->
                                    <td data-label="Atas Nama">
                                        <strong>{{
                                            rekening.atas_nama
                                        }}</strong>
                                    </td>

                                    <!-- Status -->
                                    <td data-label="Status">
                                        <button
                                            type="button"
                                            class="toggle-switch"
                                            :class="{
                                                'is-active':
                                                    rekening.status_aktif,
                                            }"
                                            :aria-pressed="
                                                rekening.status_aktif
                                            "
                                            :aria-label="
                                                rekening.status_aktif
                                                    ? `Nonaktifkan ${rekening.nama_bank}`
                                                    : `Aktifkan ${rekening.nama_bank}`
                                            "
                                            @click="
                                                confirmToggleStatus(rekening)
                                            "
                                        >
                                            <span class="toggle-track">
                                                <span
                                                    class="toggle-thumb"
                                                ></span>
                                            </span>

                                            <span class="toggle-label">
                                                {{
                                                    rekening.status_aktif
                                                        ? "Aktif"
                                                        : "Tidak Aktif"
                                                }}
                                            </span>
                                        </button>
                                    </td>

                                    <!-- Aksi -->
                                    <td data-label="Aksi">
                                        <div class="action-buttons">
                                            <button
                                                type="button"
                                                class="icon-button edit-button"
                                                title="Edit rekening bank"
                                                :aria-label="`Edit rekening ${rekening.nama_bank}`"
                                                @click="
                                                    openEditModal(rekening)
                                                "
                                            >
                                                <svg
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    aria-hidden="true"
                                                >
                                                    <path
                                                        d="M12 20H21"
                                                        stroke="currentColor"
                                                        stroke-width="1.8"
                                                        stroke-linecap="round"
                                                    />
                                                    <path
                                                        d="M16.5 3.5L20.5 7.5L7 21L3 21L3 17L16.5 3.5Z"
                                                        stroke="currentColor"
                                                        stroke-width="1.8"
                                                        stroke-linejoin="round"
                                                    />
                                                </svg>
                                            </button>

                                            <button
                                                type="button"
                                                class="icon-button delete-button"
                                                :title="
                                                    rekening.transaksis_count >
                                                    0
                                                        ? 'Rekening tidak dapat dihapus karena sudah digunakan dalam transaksi'
                                                        : 'Hapus rekening bank'
                                                "
                                                :aria-label="`Hapus rekening ${rekening.nama_bank}`"
                                                :disabled="
                                                    rekening.transaksis_count >
                                                    0
                                                "
                                                @click="
                                                    confirmDelete(rekening)
                                                "
                                            >
                                                <svg
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    aria-hidden="true"
                                                >
                                                    <path
                                                        d="M3 6H21"
                                                        stroke="currentColor"
                                                        stroke-width="1.8"
                                                        stroke-linecap="round"
                                                    />
                                                    <path
                                                        d="M8 6V4H16V6"
                                                        stroke="currentColor"
                                                        stroke-width="1.8"
                                                        stroke-linecap="round"
                                                    />
                                                    <path
                                                        d="M19 6L18 20H6L5 6"
                                                        stroke="currentColor"
                                                        stroke-width="1.8"
                                                        stroke-linejoin="round"
                                                    />
                                                    <path
                                                        d="M10 10V16M14 10V16"
                                                        stroke="currentColor"
                                                        stroke-width="1.8"
                                                        stroke-linecap="round"
                                                    />
                                                </svg>
                                            </button>
                                        </div>

                                        <span
                                            v-if="
                                                rekening.transaksis_count > 0
                                            "
                                            class="used-badge"
                                        >
                                            Digunakan
                                            {{
                                                rekening.transaksis_count
                                            }}
                                            transaksi
                                        </span>
                                    </td>
                                </tr>

                                <!-- Empty State -->
                                <tr
                                    v-if="
                                        filteredRekeningBanks.length === 0
                                    "
                                    class="empty-row"
                                >
                                    <td colspan="5">
                                        <div class="empty-state">
                                            <div class="empty-icon">
                                                <svg
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    aria-hidden="true"
                                                >
                                                    <path
                                                        d="M3 21H21M4 18H20M6 18V13M10 18V13M14 18V13M18 18V13M3 10L12 4L21 10H3Z"
                                                        stroke="currentColor"
                                                        stroke-width="1.7"
                                                        stroke-linejoin="round"
                                                    />
                                                </svg>
                                            </div>

                                            <strong
                                                >Belum ada rekening
                                                bank</strong
                                            >
                                            <p>
                                                Tambahkan rekening bank
                                                tujuan pembayaran untuk
                                                memulai menerima
                                                pembayaran transfer.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- FOOTER -->
                <footer class="rekening-footer">
                    <p>
                        &copy; {{ new Date().getFullYear() }}
                        <strong>UMKM Ayam Jagal</strong>. All rights
                        reserved.
                    </p>

                    <span>Point of Sale System</span>
                </footer>
            </div>
        </main>

        <!-- BOTTOM NAVIGATION -->
        <MenuBottomNavigation />

        <!-- ADD / EDIT MODAL -->
        <Transition name="modal">
            <div
                v-if="showModal"
                class="modal-overlay"
                @click.self="showModal = false"
            >
                <section
                    ref="modalRef"
                    class="modal-card"
                    role="dialog"
                    aria-modal="true"
                    aria-labelledby="rekening-form-title"
                >
                    <!-- Header -->
                    <div class="modal-header">
                        <div>
                            <span class="eyebrow">REKENING BANK</span>

                            <h2 id="rekening-form-title">
                                {{
                                    isEditing
                                        ? "Edit Rekening Bank"
                                        : "Tambah Rekening Baru"
                                }}
                            </h2>

                            <p>
                                Lengkapi informasi rekening bank di bawah
                                ini.
                            </p>
                        </div>

                        <button
                            type="button"
                            class="close-button"
                            aria-label="Tutup"
                            @click="showModal = false"
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
                    <form @submit.prevent="saveRekening">
                        <div class="form-group">
                            <label for="nama-bank">
                                Nama Bank <i>*</i>
                            </label>

                            <input
                                id="nama-bank"
                                v-model="form.nama_bank"
                                type="text"
                                placeholder="Contoh: BCA, BRI, Mandiri"
                                maxlength="50"
                                required
                            />

                            <p
                                v-if="formErrors.nama_bank"
                                class="field-error"
                            >
                                {{ formErrors.nama_bank }}
                            </p>
                        </div>

                        <div class="form-group">
                            <label for="nomor-rekening">
                                Nomor Rekening <i>*</i>
                            </label>

                            <input
                                id="nomor-rekening"
                                v-model="form.nomor_rekening"
                                type="text"
                                placeholder="Contoh: 1234567890"
                                maxlength="50"
                                required
                            />

                            <p
                                v-if="formErrors.nomor_rekening"
                                class="field-error"
                            >
                                {{ formErrors.nomor_rekening }}
                            </p>
                        </div>

                        <div class="form-group">
                            <label for="atas-nama">
                                Atas Nama <i>*</i>
                            </label>

                            <input
                                id="atas-nama"
                                v-model="form.atas_nama"
                                type="text"
                                placeholder="Contoh: PT Ayam Jagal"
                                maxlength="100"
                                required
                            />

                            <p
                                v-if="formErrors.atas_nama"
                                class="field-error"
                            >
                                {{ formErrors.atas_nama }}
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="modal-actions">
                            <button
                                type="button"
                                class="secondary-button"
                                @click="showModal = false"
                            >
                                Batal
                            </button>

                            <button type="submit" class="primary-button">
                                {{
                                    isEditing
                                        ? "Simpan Perubahan"
                                        : "Simpan Rekening"
                                }}
                            </button>
                        </div>
                    </form>
                </section>
            </div>
        </Transition>

        <!-- DELETE / CONFIRM MODAL -->
        <Transition name="modal">
            <div
                v-if="showDeleteModal"
                class="modal-overlay"
                @click.self="showDeleteModal = false"
            >
                <section
                    ref="deleteModalRef"
                    class="delete-modal"
                    role="alertdialog"
                    aria-modal="true"
                    aria-labelledby="confirm-title"
                    aria-describedby="confirm-description"
                    tabindex="-1"
                >
                    <div class="delete-icon" aria-hidden="true">
                        <svg
                            v-if="actionType === 'delete'"
                            viewBox="0 0 24 24"
                            fill="none"
                        >
                            <path
                                d="M3 6H21M8 6V4H16V6M19 6L18 20H6L5 6M10 11V16M14 11V16"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>

                        <svg v-else viewBox="0 0 24 24" fill="none">
                            <rect
                                x="3"
                                y="6"
                                width="18"
                                height="12"
                                rx="6"
                                stroke="currentColor"
                                stroke-width="1.8"
                            />
                            <circle
                                :cx="
                                    actionType === 'deactivate' ? 15 : 9
                                "
                                cy="12"
                                r="3"
                                fill="currentColor"
                            />
                        </svg>
                    </div>

                    <h2 id="confirm-title">
                        {{
                            actionType === "delete"
                                ? "Hapus Rekening Bank?"
                                : actionType === "deactivate"
                                  ? "Nonaktifkan Rekening?"
                                  : "Aktifkan Rekening?"
                        }}
                    </h2>

                    <p id="confirm-description">
                        {{
                            actionType === "delete"
                                ? "Rekening yang dihapus tidak dapat dikembalikan. Apakah Anda yakin ingin melanjutkan?"
                                : actionType === "deactivate"
                                  ? "Apakah Anda yakin ingin menonaktifkan rekening ini? Kasir tidak akan dapat memilih rekening ini untuk pembayaran transfer."
                                  : "Apakah Anda yakin ingin mengaktifkan kembali rekening ini? Rekening akan tersedia kembali untuk pembayaran transfer."
                        }}
                    </p>

                    <p
                        v-if="actionError"
                        class="action-error"
                        role="alert"
                    >
                        {{ actionError }}
                    </p>

                    <div class="modal-actions">
                        <button
                            type="button"
                            class="secondary-button"
                            @click="showDeleteModal = false"
                        >
                            Batal
                        </button>

                        <button
                            type="button"
                            class="danger-button"
                            @click="confirmAction"
                        >
                            {{
                                actionType === "delete"
                                    ? "Ya, Hapus"
                                    : actionType === "deactivate"
                                      ? "Ya, Nonaktifkan"
                                      : "Ya, Aktifkan"
                            }}
                        </button>
                    </div>
                </section>
            </div>
        </Transition>

        <!-- LOGOUT MODAL -->
        <AdminLogoutModal
            :show="showingLogoutConfirmation"
            @cancel="cancelLogout"
            @confirm="logout"
        />
    </div>
</template>
