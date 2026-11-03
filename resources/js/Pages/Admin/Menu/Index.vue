<script setup>
import { ref, computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";

const page = usePage();
const adminName = computed(
    () => page.props.auth?.user?.name || "Administrator",
);

const searchQuery = ref("");
const selectedCategory = ref("Semua");
const showModal = ref(false);
const showDeleteModal = ref(false);
const isEditing = ref(false);
const selectedMenuId = ref(null);

const categories = ["Semua", "Makanan", "Minuman", "Paket", "Tambahan"];

// Data sementara untuk tampilan.
// Nantinya dapat diganti dengan props/API backend.
const menus = ref([
    {
        id: 1,
        name: "Ayam Geprek Original",
        category: "Makanan",
        price: 15000,
        status: true,
        description: "Ayam crispy dengan sambal geprek original.",
        image: "",
    },
    {
        id: 2,
        name: "Ayam Geprek Mozarella",
        category: "Makanan",
        price: 20000,
        status: true,
        description: "Ayam geprek dengan topping keju mozarella.",
        image: "",
    },
    {
        id: 3,
        name: "Ayam Bakar",
        category: "Makanan",
        price: 18000,
        status: true,
        description: "Ayam bakar dengan bumbu khas.",
        image: "",
    },
    {
        id: 4,
        name: "Paket Ayam Geprek",
        category: "Paket",
        price: 22000,
        status: true,
        description: "Ayam geprek, nasi, dan minuman.",
        image: "",
    },
    {
        id: 5,
        name: "Es Teh",
        category: "Minuman",
        price: 5000,
        status: true,
        description: "Es teh manis segar.",
        image: "",
    },
    {
        id: 6,
        name: "Es Jeruk",
        category: "Minuman",
        price: 7000,
        status: false,
        description: "Es jeruk segar.",
        image: "",
    },
    {
        id: 7,
        name: "Nasi Putih",
        category: "Tambahan",
        price: 5000,
        status: true,
        description: "Nasi putih satu porsi.",
        image: "",
    },
    {
        id: 8,
        name: "Sambal Tambahan",
        category: "Tambahan",
        price: 3000,
        status: true,
        description: "Sambal tambahan.",
        image: "",
    },
]);

const emptyForm = () => ({
    name: "",
    category: "Makanan",
    price: "",
    description: "",
    image: "",
});

const form = ref(emptyForm());

const filteredMenus = computed(() =>
    menus.value.filter((menu) => {
        const matchesSearch = menu.name
            .toLowerCase()
            .includes(searchQuery.value.toLowerCase().trim());

        const matchesCategory =
            selectedCategory.value === "Semua" ||
            menu.category === selectedCategory.value;

        return matchesSearch && matchesCategory;
    }),
);

const totalMenus = computed(() => menus.value.length);

const activeMenus = computed(
    () => menus.value.filter((menu) => menu.status).length,
);

const inactiveMenus = computed(
    () => menus.value.filter((menu) => !menu.status).length,
);

const currentDate = computed(() =>
    new Date().toLocaleDateString("id-ID", {
        weekday: "long",
        day: "numeric",
        month: "long",
        year: "numeric",
    }),
);

const formatPrice = (price) =>
    new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        maximumFractionDigits: 0,
    }).format(Number(price) || 0);

function openAddModal() {
    isEditing.value = false;
    selectedMenuId.value = null;
    form.value = emptyForm();
    showModal.value = true;
}

function openEditModal(menu) {
    isEditing.value = true;
    selectedMenuId.value = menu.id;

    form.value = {
        name: menu.name,
        category: menu.category,
        price: menu.price,
        description: menu.description,
        image: menu.image,
    };

    showModal.value = true;
}

function saveMenu() {
    const name = form.value.name.trim();
    const price = Number(form.value.price);

    if (!name || !Number.isFinite(price) || price <= 0) {
        window.alert("Nama dan harga menu harus diisi dengan benar.");
        return;
    }

    if (isEditing.value) {
        const index = menus.value.findIndex(
            (menu) => menu.id === selectedMenuId.value,
        );

        if (index !== -1) {
            menus.value[index] = {
                ...menus.value[index],
                ...form.value,
                name,
                price,
            };
        }
    } else {
        menus.value.unshift({
            id: Date.now(),
            ...form.value,
            name,
            price,
            status: true,
        });
    }

    showModal.value = false;
}

function confirmDelete(menu) {
    selectedMenuId.value = menu.id;
    showDeleteModal.value = true;
}

function deleteMenu() {
    menus.value = menus.value.filter(
        (menu) => menu.id !== selectedMenuId.value,
    );

    showDeleteModal.value = false;
    selectedMenuId.value = null;
}

function toggleStatus(menu) {
    menu.status = !menu.status;
}

function handleImageUpload(event) {
    const file = event.target.files?.[0];

    if (!file) return;

    if (!file.type.startsWith("image/")) {
        window.alert("File harus berupa gambar.");
        event.target.value = "";
        return;
    }

    if (form.value.image?.startsWith("blob:")) {
        URL.revokeObjectURL(form.value.image);
    }

    form.value.image = URL.createObjectURL(file);
}
</script>

<template>
    <div class="app-layout">
        <!-- HEADER -->
        <header class="app-header">
            <div class="header-container">
                <Link href="/dashboard" class="header-brand">
                    <div class="brand-logo">
                        <img
                            src="/assets/images/illustrations/ilustrasi-koki.png"
                            alt="Logo Ayam Jagal"
                        />
                    </div>

                    <div class="brand-text">
                        <h1>AYAM JAGAL</h1>
                        <span>Point of Sale System</span>
                    </div>
                </Link>

                <div class="header-right">
                    <div class="header-date">
                        <span class="date-dot"></span>
                        {{ currentDate }}
                    </div>

                    <div class="profile-button">
                        <div class="profile-avatar-small">
                            {{ adminName.charAt(0).toUpperCase() }}
                        </div>

                        <div class="profile-header-text">
                            <strong>{{ adminName }}</strong>
                            <span>Administrator</span>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- MAIN CONTENT -->
        <main class="main-content">
            <div class="breadcrumb">
                <Link href="/dashboard">Dashboard</Link>
                <span>/</span>
                <span>Kelola Menu</span>
            </div>

            <section class="page-heading">
                <div>
                    <span class="eyebrow">MANAJEMEN PRODUK</span>
                    <h1>Kelola Menu &amp; Harga</h1>
                    <p>
                        Atur daftar makanan, minuman, kategori, dan harga produk
                        UMKM Ayam Jagal.
                    </p>
                </div>

                <button
                    type="button"
                    class="primary-button"
                    @click="openAddModal"
                >
                    <span>＋</span>
                    Tambah Menu
                </button>
            </section>

            <!-- SUMMARY -->
            <section class="summary-grid">
                <article class="summary-card">
                    <div class="summary-icon red-icon">▤</div>
                    <div>
                        <span>Total Menu</span>
                        <strong>{{ totalMenus }}</strong>
                        <small>Seluruh produk</small>
                    </div>
                </article>

                <article class="summary-card">
                    <div class="summary-icon green-icon">✓</div>
                    <div>
                        <span>Menu Tersedia</span>
                        <strong>{{ activeMenus }}</strong>
                        <small>Siap dijual</small>
                    </div>
                </article>

                <article class="summary-card">
                    <div class="summary-icon orange-icon">◷</div>
                    <div>
                        <span>Menu Tidak Tersedia</span>
                        <strong>{{ inactiveMenus }}</strong>
                        <small>Sedang tidak dijual</small>
                    </div>
                </article>
            </section>

            <!-- MENU LIST -->
            <section class="menu-panel">
                <div class="panel-heading">
                    <div>
                        <h2>Daftar Menu</h2>
                        <p>Kelola informasi dan ketersediaan setiap produk.</p>
                    </div>

                    <span class="result-count">
                        {{ filteredMenus.length }} menu
                    </span>
                </div>

                <!-- SEARCH AND FILTER -->
                <div class="toolbar">
                    <label class="search-box">
                        <svg viewBox="0 0 24 24" fill="none">
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
                            placeholder="Cari nama menu..."
                        />
                    </label>

                    <div class="category-filter">
                        <button
                            v-for="category in categories"
                            :key="category"
                            type="button"
                            :class="{
                                active: selectedCategory === category,
                            }"
                            @click="selectedCategory = category"
                        >
                            {{ category }}
                        </button>
                    </div>
                </div>

                <!-- TABLE -->
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>MENU</th>
                                <th>KATEGORI</th>
                                <th>HARGA</th>
                                <th>STATUS</th>
                                <th class="action-heading">AKSI</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="menu in filteredMenus" :key="menu.id">
                                <td>
                                    <div class="menu-info">
                                        <div class="menu-image">
                                            <img
                                                v-if="menu.image"
                                                :src="menu.image"
                                                :alt="menu.name"
                                            />
                                            <span v-else>🍗</span>
                                        </div>

                                        <div class="menu-copy">
                                            <strong>{{ menu.name }}</strong>
                                            <small>
                                                {{
                                                    menu.description ||
                                                    "Tidak ada deskripsi"
                                                }}
                                            </small>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <span class="category-badge">
                                        {{ menu.category }}
                                    </span>
                                </td>

                                <td>
                                    <strong class="price">
                                        {{ formatPrice(menu.price) }}
                                    </strong>
                                </td>

                                <td>
                                    <button
                                        type="button"
                                        class="status-badge"
                                        :class="
                                            menu.status
                                                ? 'available'
                                                : 'unavailable'
                                        "
                                        @click="toggleStatus(menu)"
                                    >
                                        <span></span>
                                        {{
                                            menu.status
                                                ? "Tersedia"
                                                : "Tidak Tersedia"
                                        }}
                                    </button>
                                </td>

                                <td>
                                    <div class="action-buttons">
                                        <button
                                            type="button"
                                            class="icon-button edit-button"
                                            title="Edit menu"
                                            aria-label="Edit menu"
                                            @click="openEditModal(menu)"
                                        >
                                            ✎
                                        </button>

                                        <button
                                            type="button"
                                            class="icon-button delete-button"
                                            title="Hapus menu"
                                            aria-label="Hapus menu"
                                            @click="confirmDelete(menu)"
                                        >
                                            ⌫
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="filteredMenus.length === 0">
                                <td colspan="5">
                                    <div class="empty-state">
                                        <div>⌕</div>
                                        <strong>Menu tidak ditemukan</strong>
                                        <p>
                                            Ubah kata kunci pencarian atau pilih
                                            kategori lain.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>

        <!-- BOTTOM NAVIGATION -->
        <nav class="bottom-navigation" aria-label="Navigasi utama">
            <Link href="/dashboard" class="nav-item">
                <span class="nav-symbol">⌂</span>
                <span>Dashboard</span>
            </Link>

            <Link href="/menu" class="nav-item active" aria-current="page">
                <span class="nav-symbol">▦</span>
                <span>Menu</span>
            </Link>

            <span class="nav-item disabled">
                <span class="nav-symbol">◷</span>
                <span>Penjualan</span>
            </span>

            <span class="nav-item disabled">
                <span class="nav-symbol">▤</span>
                <span>Keuangan</span>
            </span>
        </nav>

        <!-- ADD / EDIT MODAL -->
        <div
            v-if="showModal"
            class="modal-overlay"
            @click.self="showModal = false"
        >
            <section
                class="modal-card"
                role="dialog"
                aria-modal="true"
                :aria-label="isEditing ? 'Edit menu' : 'Tambah menu baru'"
            >
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
                        @click="showModal = false"
                    >
                        ×
                    </button>
                </div>

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

                            <select id="menu-category" v-model="form.category">
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
                        <label for="menu-description">Deskripsi</label>

                        <textarea
                            id="menu-description"
                            v-model="form.description"
                            rows="3"
                            placeholder="Deskripsi singkat menu..."
                        ></textarea>
                    </div>

                    <div class="form-group">
                        <label for="menu-image">Foto Menu</label>

                        <input
                            id="menu-image"
                            type="file"
                            accept="image/*"
                            @change="handleImageUpload"
                        />

                        <img
                            v-if="form.image"
                            :src="form.image"
                            class="image-preview"
                            alt="Preview foto menu"
                        />
                    </div>

                    <div class="modal-actions">
                        <button
                            type="button"
                            class="secondary-button"
                            @click="showModal = false"
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

        <!-- DELETE CONFIRMATION -->
        <div
            v-if="showDeleteModal"
            class="modal-overlay"
            @click.self="showDeleteModal = false"
        >
            <section
                class="delete-modal"
                role="alertdialog"
                aria-modal="true"
                aria-labelledby="delete-title"
            >
                <div class="delete-icon">!</div>

                <h2 id="delete-title">Hapus Menu?</h2>

                <p>
                    Menu yang dihapus tidak dapat dikembalikan. Apakah Anda
                    yakin ingin melanjutkan?
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
                        @click="deleteMenu"
                    >
                        Ya, Hapus
                    </button>
                </div>
            </section>
        </div>
    </div>
</template>

<style scoped>
* {
    box-sizing: border-box;
}

.app-layout {
    --primary: #ca0708;
    --primary-dark: #a80000;
    --text: #202124;
    --muted: #858b98;
    --border: #eceef1;

    width: 100%;
    min-height: 100vh;
    min-height: 100dvh;
    padding-bottom: 100px;
    overflow-x: hidden;
    color: var(--text);
    background: #f5f6f8;
    font-family: Inter, "Segoe UI", Arial, sans-serif;
}

/* HEADER */
.app-header {
    position: sticky;
    top: 0;
    z-index: 50;
    width: 100%;
    color: #fff;
    background: linear-gradient(110deg, #a90000, #ca0708 55%, #e92323);
    box-shadow: 0 4px 18px rgba(128, 0, 0, 0.13);
}

.header-container {
    max-width: 1180px;
    min-height: 82px;
    margin: auto;
    padding: 0 28px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
}

.header-brand {
    display: flex;
    align-items: center;
    gap: 13px;
    color: #fff;
    text-decoration: none;
}

.brand-logo {
    width: 54px;
    height: 54px;
    flex: 0 0 54px;
    display: grid;
    place-items: center;
    overflow: hidden;
    border-radius: 13px;
    background: #fff;
}

.brand-logo img {
    width: 90%;
    height: 90%;
    object-fit: contain;
}

.brand-text h1 {
    margin: 0;
    font-size: 16px;
    font-weight: 850;
    letter-spacing: 0.8px;
}

.brand-text span {
    display: block;
    margin-top: 4px;
    color: rgba(255, 255, 255, 0.78);
    font-size: 10px;
}

.header-right {
    display: flex;
    align-items: center;
    gap: 22px;
}

.header-date {
    display: flex;
    align-items: center;
    gap: 9px;
    color: rgba(255, 255, 255, 0.9);
    font-size: 12px;
}

.date-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.15);
}

.profile-button {
    display: flex;
    align-items: center;
    gap: 10px;
}

.profile-avatar-small {
    width: 37px;
    height: 37px;
    display: grid;
    place-items: center;
    border: 1px solid rgba(255, 255, 255, 0.5);
    border-radius: 50%;
    color: var(--primary);
    background: #fff;
    font-weight: 800;
}

.profile-header-text strong,
.profile-header-text span {
    display: block;
}

.profile-header-text strong {
    max-width: 150px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-size: 12px;
}

.profile-header-text span {
    margin-top: 3px;
    color: rgba(255, 255, 255, 0.75);
    font-size: 10px;
}

/* MAIN CONTENT */
.main-content {
    width: min(1124px, 100%);
    margin: 0 auto;
    padding: 30px 28px 38px;
}

.breadcrumb {
    display: flex;
    align-items: center;
    gap: 9px;
    margin-bottom: 13px;
    color: #9a9eaa;
    font-size: 11px;
}

.breadcrumb a {
    color: var(--primary);
    text-decoration: none;
}

.page-heading {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: 20px;
    margin-bottom: 24px;
}

.eyebrow {
    display: block;
    margin-bottom: 7px;
    color: var(--primary);
    font-size: 9px;
    font-weight: 800;
    letter-spacing: 1.2px;
}

.page-heading h1 {
    margin: 0;
    font-size: 25px;
    line-height: 1.25;
    font-weight: 800;
    letter-spacing: -0.5px;
}

.page-heading p,
.panel-heading p {
    margin: 7px 0 0;
    color: var(--muted);
    font-size: 12px;
    line-height: 1.6;
}

/* BUTTON */
.primary-button {
    min-height: 40px;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    padding: 0 16px;
    border: 0;
    border-radius: 8px;
    color: #fff;
    background: var(--primary);
    box-shadow: 0 4px 10px rgba(202, 7, 8, 0.14);
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition:
        background 0.2s,
        transform 0.2s;
}

.primary-button:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
}

.primary-button span {
    font-size: 18px;
    line-height: 1;
}

/* SUMMARY */
.summary-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.summary-card {
    min-height: 105px;
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 18px;
    border: 1px solid var(--border);
    border-radius: 12px;
    background: #fff;
    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.025);
}

.summary-icon {
    width: 43px;
    height: 43px;
    flex: 0 0 43px;
    display: grid;
    place-items: center;
    border-radius: 11px;
    font-size: 20px;
    font-weight: 800;
}

.red-icon {
    color: var(--primary);
    background: #fff0f0;
}

.green-icon {
    color: #159447;
    background: #e9f8ef;
}

.orange-icon {
    color: #d98217;
    background: #fff5e8;
}

.summary-card span,
.summary-card small {
    display: block;
    color: var(--muted);
    font-size: 11px;
}

.summary-card strong {
    display: block;
    margin: 3px 0;
    font-size: 23px;
    line-height: 1.2;
}

.summary-card small {
    color: #a0a4ac;
    font-size: 10px;
}

/* MENU PANEL */
.menu-panel {
    padding: 21px;
    border: 1px solid var(--border);
    border-radius: 13px;
    background: #fff;
    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.025);
}

.panel-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
}

.panel-heading h2 {
    margin: 0;
    font-size: 16px;
    font-weight: 800;
}

.panel-heading p {
    font-size: 11px;
}

.result-count {
    padding: 7px 11px;
    border-radius: 20px;
    color: var(--primary);
    background: #fff0f0;
    font-size: 10px;
    font-weight: 750;
    white-space: nowrap;
}

/* SEARCH AND FILTER */
.toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    margin: 20px 0 17px;
}

.search-box {
    width: min(280px, 100%);
    height: 40px;
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 0 11px;
    border: 1px solid #e7e9ed;
    border-radius: 8px;
    background: #fff;
}

.search-box:focus-within {
    border-color: #e4a1a1;
    box-shadow: 0 0 0 3px rgba(202, 7, 8, 0.06);
}

.search-box svg {
    width: 17px;
    height: 17px;
    flex: 0 0 17px;
    color: #9298a3;
}

.search-box input {
    width: 100%;
    min-width: 0;
    border: 0;
    outline: 0;
    color: var(--text);
    background: transparent;
    font: inherit;
    font-size: 11px;
}

.category-filter {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.category-filter button {
    padding: 8px 11px;
    border: 1px solid #eceef1;
    border-radius: 7px;
    color: #777d89;
    background: #fff;
    font-size: 10px;
    cursor: pointer;
    transition: 0.2s;
}

.category-filter button:hover {
    border-color: #efbaba;
    color: var(--primary);
}

.category-filter button.active {
    border-color: #ffd1d1;
    color: var(--primary);
    background: #fff0f0;
    font-weight: 750;
}

/* TABLE */
.table-wrapper {
    width: 100%;
    overflow-x: auto;
}

table {
    width: 100%;
    min-width: 690px;
    border-collapse: collapse;
    text-align: left;
}

thead {
    background: #f8f9fb;
}

th {
    padding: 13px 11px;
    color: #8b909b;
    font-size: 9px;
    font-weight: 800;
    letter-spacing: 0.6px;
}

td {
    padding: 13px 11px;
    border-bottom: 1px solid #f0f1f3;
    font-size: 11px;
}

tbody tr:last-child td {
    border-bottom: 0;
}

tbody tr:hover {
    background: #fdfdfd;
}

.action-heading {
    text-align: center;
}

.menu-info {
    display: flex;
    align-items: center;
    gap: 11px;
    min-width: 210px;
}

.menu-image {
    width: 42px;
    height: 42px;
    flex: 0 0 42px;
    display: grid;
    place-items: center;
    overflow: hidden;
    border-radius: 9px;
    background: #fff2e9;
    font-size: 21px;
}

.menu-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.menu-copy {
    min-width: 0;
}

.menu-copy strong {
    display: block;
    color: #292b31;
    font-size: 11px;
}

.menu-copy small {
    display: block;
    max-width: 230px;
    margin-top: 4px;
    overflow: hidden;
    color: var(--muted);
    font-size: 9px;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.category-badge {
    display: inline-block;
    padding: 6px 9px;
    border-radius: 5px;
    color: #686e79;
    background: #f2f3f5;
    font-size: 9px;
}

.price {
    color: #292b31;
    font-size: 11px;
    white-space: nowrap;
}

/* STATUS */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 9px;
    border: 0;
    border-radius: 20px;
    font-size: 9px;
    font-weight: 700;
    white-space: nowrap;
    cursor: pointer;
}

.status-badge span {
    width: 6px;
    height: 6px;
    border-radius: 50%;
}

.available {
    color: #198c48;
    background: #eaf8ef;
}

.available span {
    background: #20a45a;
}

.unavailable {
    color: #c26a25;
    background: #fff1e8;
}

.unavailable span {
    background: #df842f;
}

/* ACTION BUTTONS */
.action-buttons {
    display: flex;
    justify-content: center;
    gap: 6px;
}

.icon-button {
    width: 30px;
    height: 30px;
    display: grid;
    place-items: center;
    border: 1px solid var(--border);
    border-radius: 7px;
    background: #fff;
    font-size: 14px;
    cursor: pointer;
    transition: 0.2s;
}

.edit-button {
    color: #3977c9;
}

.edit-button:hover {
    background: #edf4ff;
}

.delete-button {
    color: var(--primary);
}

.delete-button:hover {
    background: #fff0f0;
}

/* EMPTY STATE */
.empty-state {
    padding: 42px 12px;
    text-align: center;
}

.empty-state div {
    margin-bottom: 8px;
    color: #c3c7ce;
    font-size: 30px;
}

.empty-state strong {
    font-size: 13px;
}

.empty-state p {
    margin: 6px 0 0;
    color: var(--muted);
    font-size: 10px;
}

/* BOTTOM NAVIGATION */
.bottom-navigation {
    position: fixed;
    z-index: 40;
    right: 0;
    bottom: 0;
    left: 0;
    min-height: 69px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 76px;
    border-top: 1px solid #eceef1;
    background: rgba(255, 255, 255, 0.97);
    box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.035);
    backdrop-filter: blur(12px);
}

.nav-item {
    min-width: 58px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    color: #969ba5;
    text-decoration: none;
    font-size: 9px;
    font-weight: 650;
}

.nav-symbol {
    font-size: 20px;
    line-height: 1.1;
}

.nav-item.active {
    color: var(--primary);
}

.nav-item.disabled {
    opacity: 0.45;
}

/* MODAL */
.modal-overlay {
    position: fixed;
    z-index: 100;
    inset: 0;
    display: grid;
    place-items: center;
    padding: 18px;
    overflow-y: auto;
    background: rgba(25, 25, 25, 0.48);
}

.modal-card,
.delete-modal {
    width: 100%;
    max-width: 490px;
    padding: 24px;
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.16);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 15px;
    margin-bottom: 20px;
}

.modal-header h2,
.delete-modal h2 {
    margin: 0;
    font-size: 18px;
}

.modal-header p {
    margin: 6px 0 0;
    color: var(--muted);
    font-size: 11px;
}

.close-button {
    width: 30px;
    height: 30px;
    border: 0;
    border-radius: 7px;
    color: #858b98;
    background: #f5f6f8;
    font-size: 22px;
    cursor: pointer;
}

/* FORM */
.form-group {
    display: flex;
    flex-direction: column;
    gap: 7px;
    margin-bottom: 15px;
}

.form-group label {
    color: #383b43;
    font-size: 11px;
    font-weight: 700;
}

.form-group label i {
    color: var(--primary);
    font-style: normal;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    min-height: 39px;
    padding: 10px 11px;
    border: 1px solid #e3e5e9;
    border-radius: 7px;
    outline: 0;
    color: #30333a;
    background: #fff;
    font: inherit;
    font-size: 11px;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(202, 7, 8, 0.07);
}

.form-group textarea {
    resize: vertical;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

.image-preview {
    width: 86px;
    height: 86px;
    object-fit: cover;
    border-radius: 8px;
}

/* MODAL ACTIONS */
.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 9px;
    margin-top: 21px;
}

.secondary-button,
.danger-button {
    min-height: 38px;
    padding: 0 14px;
    border: 0;
    border-radius: 7px;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
}

.secondary-button {
    color: #666d78;
    background: #f0f1f3;
}

.danger-button {
    color: #fff;
    background: var(--primary);
}

/* DELETE MODAL */
.delete-modal {
    max-width: 370px;
    text-align: center;
}

.delete-icon {
    width: 46px;
    height: 46px;
    display: grid;
    place-items: center;
    margin: 0 auto 13px;
    border-radius: 50%;
    color: var(--primary);
    background: #fff0f0;
    font-size: 24px;
    font-weight: 800;
}

.delete-modal p {
    margin: 10px 0 19px;
    color: var(--muted);
    font-size: 11px;
    line-height: 1.7;
}

.delete-modal .modal-actions {
    justify-content: center;
}

/* RESPONSIVE */
@media (max-width: 760px) {
    .header-container {
        min-height: 72px;
        padding: 0 17px;
    }

    .brand-logo {
        width: 45px;
        height: 45px;
        flex-basis: 45px;
    }

    .brand-text h1 {
        font-size: 14px;
    }

    .header-date {
        display: none;
    }

    .main-content {
        padding: 23px 17px 30px;
    }

    .page-heading {
        align-items: flex-start;
        flex-direction: column;
        gap: 13px;
    }

    .page-heading h1 {
        font-size: 22px;
    }

    .page-heading p {
        max-width: 520px;
        font-size: 11px;
    }

    .page-heading .primary-button {
        width: 100%;
    }

    .summary-grid {
        gap: 9px;
    }

    .summary-card {
        min-height: 92px;
        gap: 10px;
        padding: 12px;
    }

    .summary-icon {
        width: 36px;
        height: 36px;
        flex-basis: 36px;
        font-size: 17px;
    }

    .summary-card strong {
        font-size: 20px;
    }

    .summary-card span {
        font-size: 10px;
    }

    .summary-card small {
        font-size: 9px;
    }

    .menu-panel {
        padding: 16px 12px;
    }

    .toolbar {
        align-items: stretch;
        flex-direction: column;
    }

    .search-box {
        width: 100%;
    }

    .category-filter {
        flex-wrap: nowrap;
        overflow-x: auto;
        padding-bottom: 4px;
    }

    .category-filter button {
        flex: 0 0 auto;
    }

    .bottom-navigation {
        justify-content: space-around;
        gap: 0;
        min-height: 65px;
    }

    .form-row {
        grid-template-columns: 1fr;
        gap: 0;
    }
}

@media (max-width: 430px) {
    .main-content {
        padding-right: 13px;
        padding-left: 13px;
    }

    .summary-grid {
        grid-template-columns: 1fr;
    }

    .summary-card {
        min-height: 76px;
    }

    .profile-header-text {
        display: none;
    }

    .panel-heading h2 {
        font-size: 15px;
    }

    .panel-heading p {
        font-size: 10px;
    }

    .modal-card,
    .delete-modal {
        padding: 19px;
    }
}
</style>
