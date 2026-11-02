<script setup>
import { ref, computed } from "vue";
import { router, Link } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";

// Menerima data menu dari Laravel
const props = defineProps({
    menus: {
        type: Array,
        default: () => [],
    },
});

// State halaman
const searchQuery = ref("");
const selectedCategory = ref("Semua");

const showModal = ref(false);
const showDeleteModal = ref(false);

const isEditing = ref(false);
const selectedMenuId = ref(null);

// Daftar kategori
const categories = [
    "Semua",
    "Makanan",
    "Minuman",
    "Paket",
    "Tambahan",
];

// Data menu dari database
const menus = ref(props.menus);

// Form tambah dan edit menu
const form = ref({
    name: "",
    category: "Makanan",
    price: "",
    description: "",
    image: "",
});

// Filter menu berdasarkan pencarian dan kategori
const filteredMenus = computed(() => {
    return menus.value.filter((menu) => {
        const matchesSearch = menu.name
            .toLowerCase()
            .includes(searchQuery.value.toLowerCase());

        const matchesCategory =
            selectedCategory.value === "Semua" ||
            menu.category === selectedCategory.value;

        return matchesSearch && matchesCategory;
    });
});

// Menghitung jumlah menu
const totalMenus = computed(() => menus.value.length);

const activeMenus = computed(() =>
    menus.value.filter((menu) => menu.status).length
);

const inactiveMenus = computed(() =>
    menus.value.filter((menu) => !menu.status).length
);

// Format harga ke Rupiah
const formatPrice = (price) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        maximumFractionDigits: 0,
    }).format(price);
};

// Membuka modal tambah menu
const openAddModal = () => {
    isEditing.value = false;
    selectedMenuId.value = null;

    form.value = {
        name: "",
        category: "Makanan",
        price: "",
        description: "",
        image: "",
    };

    showModal.value = true;
};

// Membuka modal edit menu
const openEditModal = (menu) => {
    isEditing.value = true;
    selectedMenuId.value = menu.id;

    form.value = {
        name: menu.name,
        category: menu.category,
        price: menu.price,
        description: menu.description || "",
        image: menu.image || "",
    };

    showModal.value = true;
};

// Menyimpan menu

const saveMenu = () => {
    if (
        !form.value.name.trim() ||
        !form.value.price ||
        Number(form.value.price) <= 0
    ) {
        alert("Nama dan harga menu harus diisi dengan benar.");
        return;
    }

    const data = {
        nama_menu: form.value.name,
        kategori: form.value.category,
        harga: form.value.price,
    };

    // Jika sedang menambahkan menu baru
    if (!isEditing.value) {
        router.post("/menu", data, {
            onSuccess: (page) => {
                menus.value = page.props.menus;
                showModal.value = false;

                form.value = {
                    name: "",
                    category: "Makanan",
                    price: "",
                    description: "",
                    image: "",
                };
            },
            onError: (errors) => {
                console.log("Terjadi kesalahan:", errors);
            },
        });

        return;
    }

    // Jika sedang mengedit menu
    router.put(`/menu/${selectedMenuId.value}`, data, {
        onSuccess: (page) => {
            menus.value = page.props.menus;
            showModal.value = false;

            form.value = {
                name: "",
                category: "Makanan",
                price: "",
                description: "",
                image: "",
            };
        },
        onError: (errors) => {
            console.log("Terjadi kesalahan:", errors);
        },
    });
};

// Membuka konfirmasi hapus
const confirmDelete = (menu) => {
    selectedMenuId.value = menu.id;
    showDeleteModal.value = true;
};

// Menghapus menu sementara dari tampilan
// Menghapus menu dari database
const deleteMenu = () => {
    if (!selectedMenuId.value) {
        return;
    }

    router.delete(`/menu/${selectedMenuId.value}`, {
        onSuccess: (page) => {
            menus.value = page.props.menus;

            showDeleteModal.value = false;
            selectedMenuId.value = null;
        },
        onError: (errors) => {
            console.log("Gagal menghapus menu:", errors);
        },
    });
};

// Mengubah status menu melalui database
const toggleStatus = (menu) => {
    router.patch(`/menu/${menu.id}/status`, {}, {
        onSuccess: (page) => {
            menus.value = page.props.menus;
        },
        onError: (errors) => {
            console.log("Gagal mengubah status menu:", errors);
        },
    });
};

// Upload gambar untuk preview
const handleImageUpload = (event) => {
    const file = event.target.files[0];

    if (!file) return;

    if (!file.type.startsWith("image/")) {
        alert("File harus berupa gambar.");
        return;
    }

    form.value.image = URL.createObjectURL(file);
};
</script>


<template>
    <AdminLayout active-page="menu">
        <!-- MAIN CONTENT -->
        <main class="main-content">
            <div class="page-heading">
                <div>
                    <div class="breadcrumb">
                        <Link href="/dashboard">Dashboard</Link>
                        <span>/</span>
                        <span>Kelola Menu</span>
                    </div>

                    <h1>Kelola Menu & Harga</h1>
                    <p>
                        Kelola daftar makanan, minuman, dan harga produk UMKM
                        Ayam Jagal.
                    </p>
                </div>

                <button class="add-button" @click="openAddModal">
                    <span>＋</span>
                    Tambah Menu
                </button>
            </div>

            <!-- SUMMARY -->
            <section class="summary-grid">
                <div class="summary-card">
                    <div class="summary-icon red">▤</div>
                    <div>
                        <p>Total Menu</p>
                        <h2>{{ totalMenus }}</h2>
                        <span>Seluruh produk</span>
                    </div>
                </div>

                <div class="summary-card">
                    <div class="summary-icon green">✓</div>
                    <div>
                        <p>Menu Tersedia</p>
                        <h2>{{ activeMenus }}</h2>
                        <span>Siap dijual</span>
                    </div>
                </div>

                <div class="summary-card">
                    <div class="summary-icon orange">◷</div>
                    <div>
                        <p>Menu Tidak Tersedia</p>
                        <h2>{{ inactiveMenus }}</h2>
                        <span>Sedang tidak dijual</span>
                    </div>
                </div>
            </section>

            <!-- MENU LIST -->
            <section class="menu-section">
                <div class="section-heading">
                    <div>
                        <h2>Daftar Menu</h2>
                        <p>Daftar seluruh menu yang tersedia di toko.</p>
                    </div>

                    <span class="menu-count">
                        {{ filteredMenus.length }} Menu
                    </span>
                </div>

                <!-- SEARCH AND FILTER -->
                <div class="toolbar">
                    <div class="search-box">
                        <span>⌕</span>
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Cari nama menu..."
                        />
                    </div>

                    <div class="category-filter">
                        <button
                            v-for="category in categories"
                            :key="category"
                            :class="{
                                active: selectedCategory === category,
                            }"
                            @click="selectedCategory = category"
                        >
                            {{ category }}
                        </button>
                    </div>
                </div>

                <!-- TABLE DESKTOP -->
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

                                        <div>
                                            <strong>{{ menu.name }}</strong>
                                            <small>{{
                                                menu.description
                                            }}</small>
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
                                            class="edit-button"
                                            title="Edit menu"
                                            @click="openEditModal(menu)"
                                        >
                                            ✎
                                        </button>

                                        <button
                                            class="delete-button"
                                            title="Hapus menu"
                                            @click="confirmDelete(menu)"
                                        >
                                            ♲
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="filteredMenus.length === 0">
                                <td colspan="5">
                                    <div class="empty-state">
                                        <div>▤</div>
                                        <h3>Menu tidak ditemukan</h3>
                                        <p>
                                            Coba gunakan kata kunci atau
                                            kategori yang berbeda.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>

        <!-- ADD / EDIT MODAL -->
        <div
            v-if="showModal"
            class="modal-overlay"
            @click.self="showModal = false"
        >
            <div class="modal-card">
                <div class="modal-header">
                    <div>
                        <h2>
                            {{ isEditing ? "Edit Menu" : "Tambah Menu Baru" }}
                        </h2>
                        <p>Lengkapi informasi menu di bawah ini.</p>
                    </div>

                    <button class="close-button" @click="showModal = false">
                        ×
                    </button>
                </div>

                <form @submit.prevent="saveMenu">
                    <div class="form-group">
                        <label>Nama Menu <span>*</span></label>
                        <input
                            v-model="form.name"
                            type="text"
                            placeholder="Contoh: Ayam Geprek Original"
                            required
                        />
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Kategori <span>*</span></label>
                            <select v-model="form.category">
                                <option>Makanan</option>
                                <option>Minuman</option>
                                <option>Paket</option>
                                <option>Tambahan</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Harga <span>*</span></label>
                            <input
                                v-model="form.price"
                                type="number"
                                min="1"
                                placeholder="15000"
                                required
                            />
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea
                            v-model="form.description"
                            rows="3"
                            placeholder="Deskripsi singkat menu..."
                        ></textarea>
                    </div>

                    <div class="form-group">
                        <label>Foto Menu</label>
                        <input
                            type="file"
                            accept="image/*"
                            @change="handleImageUpload"
                        />

                        <img
                            v-if="form.image"
                            :src="form.image"
                            class="image-preview"
                            alt="Preview menu"
                        />
                    </div>

                    <div class="modal-actions">
                        <button
                            type="button"
                            class="cancel-button"
                            @click="showModal = false"
                        >
                            Batal
                        </button>

                        <button type="submit" class="save-button">
                            {{ isEditing ? "Simpan Perubahan" : "Simpan Menu" }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- DELETE CONFIRMATION -->
        <div
            v-if="showDeleteModal"
            class="modal-overlay"
            @click.self="showDeleteModal = false"
        >
            <div class="delete-modal">
                <div class="delete-icon">!</div>

                <h2>Hapus Menu?</h2>
                <p>
                    Menu yang dihapus tidak dapat dikembalikan. Apakah Anda
                    yakin ingin melanjutkan?
                </p>

                <div class="modal-actions">
                    <button
                        class="cancel-button"
                        @click="showDeleteModal = false"
                    >
                        Batal
                    </button>

                    <button class="confirm-delete-button" @click="deleteMenu">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style>
:root {
    --primary: #ca0708;
    --primary-dark: #a80000;
    --background: #f5f6f8;
    --text: #202124;
    --muted: #858b98;
    --border: #eceef1;
}

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Inter, "Segoe UI", Arial, sans-serif;
    background: var(--background);
    color: var(--text);
}

button,
input,
select,
textarea {
    font: inherit;
}

button {
    cursor: pointer;
}

/* MAIN */
.main-content {
    max-width: 1400px;
    margin: auto;
    padding: 35px 6%;
}

.page-heading {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: 20px;
    margin-bottom: 28px;
}

.breadcrumb {
    display: flex;
    gap: 9px;
    font-size: 12px;
    color: var(--muted);
    margin-bottom: 12px;
}

.breadcrumb a {
    color: var(--primary);
    text-decoration: none;
}

.page-heading h1 {
    margin: 0;
    font-size: 26px;
    font-weight: 800;
}

.page-heading p {
    color: var(--muted);
    font-size: 13px;
    margin: 8px 0 0;
}

.add-button {
    background: var(--primary);
    border: none;
    color: white;
    border-radius: 9px;
    padding: 12px 17px;
    font-weight: 650;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 10px rgba(202, 7, 8, 0.15);
}

.add-button:hover,
.save-button:hover {
    background: var(--primary-dark);
}

.add-button span {
    font-size: 20px;
    line-height: 15px;
}

/* SUMMARY */
.summary-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 18px;
    margin-bottom: 25px;
}

.summary-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: 13px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.025);
}

.summary-icon {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    display: grid;
    place-items: center;
    font-size: 21px;
    font-weight: 700;
}

.summary-icon.red {
    background: #fff0f0;
    color: var(--primary);
}

.summary-icon.green {
    background: #e9f8ef;
    color: #159447;
}

.summary-icon.orange {
    background: #fff5e8;
    color: #d98217;
}

.summary-card p {
    margin: 0;
    color: var(--muted);
    font-size: 12px;
}

.summary-card h2 {
    margin: 5px 0;
    font-size: 24px;
}

.summary-card span {
    color: #a0a4ac;
    font-size: 11px;
}

/* MENU SECTION */
.menu-section {
    background: white;
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 23px;
    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.025);
}

.section-heading {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.section-heading h2 {
    font-size: 17px;
    margin: 0;
}

.section-heading p {
    font-size: 12px;
    color: var(--muted);
    margin: 6px 0 0;
}

.menu-count {
    font-size: 11px;
    background: #fff0f0;
    color: var(--primary);
    padding: 7px 11px;
    border-radius: 20px;
    font-weight: 700;
}

.toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    margin: 23px 0;
}

.search-box {
    height: 42px;
    width: 260px;
    border: 1px solid var(--border);
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 0 12px;
}

.search-box span {
    color: var(--muted);
    font-size: 22px;
}

.search-box input {
    width: 100%;
    border: none;
    outline: none;
    font-size: 12px;
    background: transparent;
}

.category-filter {
    display: flex;
    gap: 7px;
    flex-wrap: wrap;
}

.category-filter button {
    border: 1px solid var(--border);
    background: white;
    color: #777d89;
    padding: 9px 12px;
    border-radius: 7px;
    font-size: 11px;
    transition: 0.2s;
}

.category-filter button.active {
    background: #fff0f0;
    color: var(--primary);
    border-color: #ffd1d1;
    font-weight: 700;
}

/* TABLE */
.table-wrapper {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 700px;
}

thead {
    background: #f8f9fb;
}

th {
    text-align: left;
    font-size: 10px;
    letter-spacing: 0.5px;
    color: #8b909b;
    padding: 14px 12px;
    font-weight: 700;
}

td {
    padding: 14px 12px;
    border-bottom: 1px solid #f0f1f3;
    font-size: 12px;
}

tbody tr:last-child td {
    border-bottom: none;
}

.menu-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.menu-image {
    width: 43px;
    height: 43px;
    flex-shrink: 0;
    border-radius: 9px;
    background: #fff2e9;
    display: grid;
    place-items: center;
    overflow: hidden;
    font-size: 23px;
}

.menu-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.menu-info strong {
    display: block;
    font-size: 12px;
}

.menu-info small {
    display: block;
    margin-top: 5px;
    color: var(--muted);
    font-size: 10px;
    max-width: 250px;
}

.category-badge {
    background: #f2f3f5;
    color: #686e79;
    padding: 6px 10px;
    border-radius: 5px;
    font-size: 10px;
}

.price {
    font-size: 12px;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    border: none;
    padding: 7px 9px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 650;
}

.status-badge span {
    width: 6px;
    height: 6px;
    border-radius: 50%;
}

.status-badge.available {
    background: #eaf8ef;
    color: #198c48;
}

.status-badge.available span {
    background: #20a45a;
}

.status-badge.unavailable {
    background: #fff1e8;
    color: #c26a25;
}

.status-badge.unavailable span {
    background: #df842f;
}

.action-heading {
    text-align: center;
}

.action-buttons {
    display: flex;
    justify-content: center;
    gap: 7px;
}

.edit-button,
.delete-button {
    width: 31px;
    height: 31px;
    border-radius: 7px;
    border: 1px solid var(--border);
    background: white;
    font-size: 15px;
}

.edit-button {
    color: #3977c9;
}

.delete-button {
    color: var(--primary);
}

.edit-button:hover {
    background: #edf4ff;
}

.delete-button:hover {
    background: #fff0f0;
}

.empty-state {
    text-align: center;
    padding: 45px 15px;
}

.empty-state div {
    font-size: 35px;
    color: #c3c7ce;
}

.empty-state h3 {
    margin: 12px 0 5px;
    font-size: 14px;
}

.empty-state p {
    margin: 0;
    font-size: 11px;
    color: var(--muted);
}

/* MODAL */
.modal-overlay {
    position: fixed;
    inset: 0;
    z-index: 100;
    background: rgba(25, 25, 25, 0.48);
    display: grid;
    place-items: center;
    padding: 18px;
    overflow-y: auto;
}

.modal-card,
.delete-modal {
    background: white;
    width: 100%;
    max-width: 500px;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 22px;
}

.modal-header h2,
.delete-modal h2 {
    margin: 0;
    font-size: 18px;
}

.modal-header p {
    font-size: 11px;
    color: var(--muted);
    margin: 6px 0 0;
}

.close-button {
    background: none;
    border: none;
    font-size: 25px;
    color: #8c919a;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 17px;
}

.form-group label {
    font-size: 12px;
    font-weight: 650;
}

.form-group label span {
    color: var(--primary);
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    border: 1px solid #e3e5e9;
    border-radius: 8px;
    padding: 11px 12px;
    font-size: 12px;
    outline: none;
    background: white;
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
    width: 90px;
    height: 90px;
    object-fit: cover;
    border-radius: 8px;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 9px;
    margin-top: 22px;
}

.cancel-button,
.save-button,
.confirm-delete-button {
    border: none;
    border-radius: 8px;
    padding: 11px 15px;
    font-size: 12px;
    font-weight: 650;
}

.cancel-button {
    background: #f0f1f3;
    color: #666d78;
}

.save-button {
    background: var(--primary);
    color: white;
}

.delete-modal {
    max-width: 380px;
    text-align: center;
}

.delete-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    margin: 0 auto 15px;
    display: grid;
    place-items: center;
    background: #fff0f0;
    color: var(--primary);
    font-size: 25px;
    font-weight: 800;
}

.delete-modal p {
    color: var(--muted);
    font-size: 12px;
    line-height: 1.7;
    margin: 10px 0 20px;
}

.delete-modal .modal-actions {
    justify-content: center;
}

.confirm-delete-button {
    background: var(--primary);
    color: white;
}

/* RESPONSIVE */
@media (max-width: 900px) {
    .main-content {
        padding: 28px 4%;
    }

    .toolbar {
        align-items: flex-start;
        flex-direction: column;
    }

    .search-box {
        width: 100%;
    }
}

@media (max-width: 600px) {
    .main-content {
        padding: 23px 15px;
    }

    .page-heading {
        align-items: flex-start;
        flex-direction: column;
        gap: 15px;
    }

    .page-heading h1 {
        font-size: 21px;
    }

    .page-heading p {
        font-size: 11px;
        line-height: 1.6;
    }

    .add-button {
        width: 100%;
        justify-content: center;
    }

    .summary-grid {
        grid-template-columns: 1fr;
        gap: 10px;
    }

    .summary-card {
        padding: 15px;
    }

    .menu-section {
        padding: 16px 12px;
    }

    .section-heading h2 {
        font-size: 15px;
    }

    .category-filter {
        flex-wrap: nowrap;
        overflow-x: auto;
        width: 100%;
        padding-bottom: 5px;
    }

    .category-filter button {
        flex-shrink: 0;
    }

    .form-row {
        grid-template-columns: 1fr;
        gap: 0;
    }

    .modal-card {
        padding: 19px;
    }
}
</style>
