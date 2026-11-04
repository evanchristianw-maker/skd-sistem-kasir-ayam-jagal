<script setup>
import { ref, computed } from "vue";
import { router, usePage } from "@inertiajs/vue3";

// DASHBOARD COMPONENTS
import AdminHeader from "@/Components/Admin/Shared/AdminHeader.vue";
import AdminLogoutModal from "@/Components/Admin/Shared/AdminLogoutModal.vue";

// MENU COMPONENTS
import MenuSummary from "@/Components/Admin/Menu/MenuSummary.vue";
import MenuTable from "@/Components/Admin/Menu/MenuTable.vue";
import MenuBottomNavigation from "@/Components/Admin/Menu/MenuBottomNavigation.vue";
import MenuFormModal from "@/Components/Admin/Menu/MenuFormModal.vue";
import MenuDeleteModal from "@/Components/Admin/Menu/MenuDeleteModal.vue";

// COMPOSABLE
import useMenuManagement from "@/Composables/useMenuManagement.js";

// STYLES
import "@/css/menu-management.css";

// PAGE PROPS
const props = defineProps({
    menus: {
        type: Array,
        default: () => [],
    },
});

// AUTHENTICATION
const page = usePage();

const adminEmail = computed(() => {
    return page.props.auth?.user?.email || "adminayamjagal@gmail.com";
});

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

// MENU MANAGEMENT
const {
    adminName,
    searchQuery,
    selectedCategory,
    showModal,
    showDeleteModal,
    isEditing,
    categories,
    form,
    filteredMenus,
    totalMenus,
    activeMenus,
    inactiveMenus,
    currentDate,
    formatPrice,
    openAddModal,
    openEditModal,
    saveMenu,
    confirmDelete,
    deleteMenu,
    toggleStatus,
    handleImageUpload,
} = useMenuManagement(props.menus);
</script>

<template>
    <div class="app-layout">
        <!-- DASHBOARD HEADER -->
        <AdminHeader
            :admin-name="adminName"
            :admin-email="adminEmail"
            :current-date="currentDate"
            @logout-request="openLogoutConfirmation"
        />

        <!-- MAIN CONTENT -->
        <main class="main-content">
            <div class="menu-container">
                <!-- PAGE HEADING -->
                <section class="page-heading">
                    <div class="page-heading-content">
                        <span class="eyebrow"> MANAJEMEN PRODUK </span>

                        <h1>Kelola Menu &amp; Harga</h1>

                        <p>
                            Atur daftar makanan, minuman, kategori, dan harga
                            produk UMKM Ayam Jagal.
                        </p>
                    </div>

                    <button
                        type="button"
                        class="primary-button"
                        @click="openAddModal"
                    >
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path
                                d="M12 5V19M5 12H19"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                            />
                        </svg>

                        <span>Tambah Menu</span>
                    </button>
                </section>

                <!-- MENU SUMMARY -->
                <MenuSummary
                    :total-menus="totalMenus"
                    :active-menus="activeMenus"
                    :inactive-menus="inactiveMenus"
                />

                <!-- MENU TABLE -->
                <MenuTable
                    v-model:search-query="searchQuery"
                    v-model:selected-category="selectedCategory"
                    :filtered-menus="filteredMenus"
                    :categories="categories"
                    :format-price="formatPrice"
                    @edit="openEditModal"
                    @delete="confirmDelete"
                    @toggle-status="toggleStatus"
                />

                <!-- FOOTER -->
                <footer class="menu-footer">
                    <p>
                        Â© {{ new Date().getFullYear() }}
                        <strong>UMKM Ayam Jagal</strong>. All rights reserved.
                    </p>

                    <span>Point of Sale System</span>
                </footer>
            </div>
        </main>

        <!-- BOTTOM NAVIGATION -->
        <MenuBottomNavigation />

        <!-- ADD / EDIT MENU MODAL -->
        <MenuFormModal
            :show-modal="showModal"
            :is-editing="isEditing"
            :form="form"
            :categories="categories"
            :save-menu="saveMenu"
            :handle-image-upload="handleImageUpload"
            @close="showModal = false"
        />

        <!-- DELETE CONFIRMATION MODAL -->
        <MenuDeleteModal
            :show-delete-modal="showDeleteModal"
            :delete-menu="deleteMenu"
            @close="showDeleteModal = false"
        />

        <!-- DASHBOARD LOGOUT MODAL -->
        <AdminLogoutModal
            :show="showingLogoutConfirmation"
            @cancel="cancelLogout"
            @confirm="logout"
        />
    </div>
</template>

<style scoped>
* {
    box-sizing: border-box;
}

.app-layout {
    --primary: #ca0708;
    --primary-dark: #a80000;
    --text-primary: #202124;
    --text-secondary: #626875;
    --background: #f5f6f8;

    width: 100%;
    min-height: 100vh;
    min-height: 100dvh;
    margin: 0 auto;
    padding-bottom: 100px;
    overflow-x: hidden;

    color: var(--text-primary);
    background: var(--background);

    font-family: Inter, "Segoe UI", Arial, sans-serif;
}

.main-content {
    width: 100%;
    min-height: calc(100vh - 82px);
    background: var(--background);
}

.menu-container {
    width: 100%;
    max-width: 1180px;
    margin: 0 auto;
    padding: 35px 28px 30px;
}

.page-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;

    margin-bottom: 34px;

    animation: welcomeFadeUp 0.5s ease both;
}

.page-heading-content {
    min-width: 0;
}

.eyebrow {
    display: inline-block;
    margin-bottom: 9px;

    color: #ca0708;

    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1.2px;
}

.page-heading h1 {
    margin: 0;

    color: #202124;

    font-size: 28px;
    font-weight: 800;
    letter-spacing: -0.7px;
}

.page-heading p {
    margin: 10px 0 0;

    color: #626875;

    font-size: 15px;
    line-height: 1.6;
}

.primary-button {
    min-height: 48px;
    flex-shrink: 0;

    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 9px;

    padding: 0 19px;

    border: 1px solid #ca0708;
    border-radius: 10px;

    color: white;
    background: #ca0708;

    font-family: inherit;
    font-size: 13px;
    font-weight: 700;

    cursor: pointer;

    transition:
        background 0.2s ease,
        transform 0.2s ease,
        box-shadow 0.2s ease;
}

.primary-button:hover {
    background: #a80000;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(202, 7, 8, 0.18);
}

.primary-button svg {
    width: 19px;
    height: 19px;
}

.menu-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;

    margin-top: 28px;
    padding: 18px 0 5px;

    border-top: 1px solid #e9eaed;
}

.menu-footer p,
.menu-footer span {
    margin: 0;
    color: #777e89;
    font-size: 12px;
}

.menu-footer strong {
    color: #ca0708;
}

@keyframes welcomeFadeUp {
    from {
        opacity: 0;
        transform: translateY(13px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 900px) {
    .menu-container {
        padding: 30px 22px;
    }
}

@media (max-width: 600px) {
    .app-layout {
        padding-bottom: 100px;
    }

    .menu-container {
        padding: 28px 16px 25px;
    }

    .page-heading {
        align-items: flex-start;
        flex-direction: column;
        margin-bottom: 30px;
    }

    .page-heading h1 {
        font-size: 26px;
    }

    .page-heading p {
        max-width: 340px;
        font-size: 15px;
        line-height: 1.7;
    }

    .primary-button {
        width: 100%;
    }

    .menu-footer {
        align-items: flex-start;
        flex-direction: column;
        gap: 9px;
    }

    .menu-footer p,
    .menu-footer span {
        font-size: 11px;
    }
}

@media (max-width: 380px) {
    .menu-container {
        padding-left: 12px;
        padding-right: 12px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .page-heading {
        animation: none;
    }

    .primary-button {
        transition: none;
    }
}
</style>
