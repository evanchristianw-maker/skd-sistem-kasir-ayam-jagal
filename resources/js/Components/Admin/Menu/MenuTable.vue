<script setup>
const searchQuery = defineModel("searchQuery");
const selectedCategory = defineModel("selectedCategory");

defineProps({
    filteredMenus: {
        type: Array,
        default: () => [],
    },
    categories: {
        type: Array,
        default: () => [],
    },
    formatPrice: {
        type: Function,
        required: true,
    },
});

defineEmits(["edit", "delete", "toggleStatus"]);
</script>

<template>
    <section class="menu-panel">
        <!-- PANEL HEADER -->
        <div class="panel-heading">
            <div>
                <h2>Daftar Menu</h2>
                <p>Kelola informasi dan ketersediaan setiap produk.</p>
            </div>

            <span class="result-count"> {{ filteredMenus.length }} menu </span>
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
                    aria-label="Cari nama menu"
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
                        <!-- MENU INFORMATION -->
                        <td>
                            <div class="menu-info">
                                <div class="menu-image">
                                    <img
                                        v-if="menu.image"
                                        :src="menu.image"
                                        :alt="menu.name"
                                    />

                                    <svg v-else viewBox="0 0 24 24" fill="none">
                                        <path
                                            d="M4 7.5L12 3L20 7.5V16.5L12 21L4 16.5V7.5Z"
                                            stroke="currentColor"
                                            stroke-width="1.6"
                                            stroke-linejoin="round"
                                        />
                                        <path
                                            d="M4.5 7.8L12 12L19.5 7.8M12 12V20.5"
                                            stroke="currentColor"
                                            stroke-width="1.6"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
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

                        <!-- CATEGORY -->
                        <td>
                            <span class="category-badge">
                                {{ menu.category }}
                            </span>
                        </td>

                        <!-- PRICE -->
                        <td>
                            <strong class="price">
                                {{ formatPrice(menu.price) }}
                            </strong>
                        </td>

                        <!-- STATUS -->
                        <td>
                            <button
                                type="button"
                                class="status-badge"
                                :class="
                                    menu.status ? 'available' : 'unavailable'
                                "
                                :aria-label="`Ubah status ${menu.name}`"
                                @click="$emit('toggleStatus', menu)"
                            >
                                <span></span>

                                {{
                                    menu.status ? "Tersedia" : "Tidak Tersedia"
                                }}
                            </button>
                        </td>

                        <!-- ACTIONS -->
                        <td>
                            <div class="action-buttons">
                                <button
                                    type="button"
                                    class="icon-button edit-button"
                                    title="Edit menu"
                                    aria-label="Edit menu"
                                    @click="$emit('edit', menu)"
                                >
                                    <svg viewBox="0 0 24 24" fill="none">
                                        <path
                                            d="M12 20H21"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            stroke-linecap="round"
                                        />
                                        <path
                                            d="M16.5 3.5C16.9 3.1 17.4 2.9 18 2.9C18.6 2.9 19.1 3.1 19.5 3.5C19.9 3.9 20.1 4.4 20.1 5C20.1 5.6 19.9 6.1 19.5 6.5L7 19L3 20L4 16L16.5 3.5Z"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </button>

                                <button
                                    type="button"
                                    class="icon-button delete-button"
                                    title="Hapus menu"
                                    aria-label="Hapus menu"
                                    @click="$emit('delete', menu)"
                                >
                                    <svg viewBox="0 0 24 24" fill="none">
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
                                            stroke-linejoin="round"
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
                        </td>
                    </tr>

                    <!-- EMPTY STATE -->
                    <tr v-if="filteredMenus.length === 0">
                        <td colspan="5">
                            <div class="empty-state">
                                <div class="empty-icon">
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
                                </div>

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
</template>

<style scoped>
/* MAIN PANEL */
.menu-panel {
    margin-top: 30px;
    padding: 26px;

    background: #ffffff;
    border: 1px solid #ededed;
    border-radius: 17px;

    box-shadow: 0 4px 15px rgba(20, 20, 20, 0.035);

    animation: fadeUp 0.65s ease both;
}

/* PANEL HEADING */
.panel-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;

    margin-bottom: 23px;
}

.panel-heading h2 {
    margin: 0;

    color: #202124;
    font-size: 21px;
    font-weight: 800;
}

.panel-heading p {
    margin: 6px 0 0;

    color: #777e89;
    font-size: 13px;
    line-height: 1.6;
}

.result-count {
    flex-shrink: 0;

    padding: 8px 13px;

    color: #ca0708;
    background: #fff0f0;
    border-radius: 9px;

    font-size: 12px;
    font-weight: 700;
}

/* TOOLBAR */
.toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;

    margin-bottom: 22px;
}

.search-box {
    display: flex;
    align-items: center;
    gap: 10px;

    width: 100%;
    max-width: 310px;
    min-height: 43px;
    padding: 0 13px;

    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;

    transition:
        border-color 0.2s ease,
        box-shadow 0.2s ease;
}

.search-box:focus-within {
    border-color: #ca0708;
    box-shadow: 0 0 0 3px rgba(202, 7, 8, 0.07);
}

.search-box svg {
    flex-shrink: 0;

    width: 19px;
    height: 19px;

    color: #9298a2;
}

.search-box input {
    width: 100%;
    min-width: 0;

    padding: 0;

    color: #202124;
    background: transparent;
    border: none;
    outline: none;

    font-family: inherit;
    font-size: 13px;
}

.search-box input::placeholder {
    color: #a0a5ae;
}

/* CATEGORY FILTER */
.category-filter {
    display: flex;
    align-items: center;
    gap: 7px;

    flex-wrap: wrap;
}

.category-filter button {
    min-height: 36px;
    padding: 0 13px;

    color: #626875;
    background: #ffffff;
    border: 1px solid #e8e8e8;
    border-radius: 8px;

    font-family: inherit;
    font-size: 12px;
    font-weight: 600;

    cursor: pointer;

    transition:
        color 0.2s ease,
        background 0.2s ease,
        border-color 0.2s ease;
}

.category-filter button:hover {
    color: #ca0708;
    border-color: #ca0708;
}

.category-filter button.active {
    color: #ffffff;
    background: #ca0708;
    border-color: #ca0708;
}

/* TABLE */
.table-wrapper {
    width: 100%;
    overflow-x: auto;

    border: 1px solid #eeeeee;
    border-radius: 12px;
}

table {
    width: 100%;
    min-width: 720px;

    border-collapse: collapse;
    text-align: left;
}

thead {
    background: #f9fafb;
}

th {
    padding: 15px 16px;

    color: #777e89;
    border-bottom: 1px solid #eeeeee;

    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.5px;
    white-space: nowrap;
}

td {
    padding: 15px 16px;

    border-bottom: 1px solid #f0f0f0;

    color: #444b56;
    font-size: 13px;
}

tbody tr:last-child td {
    border-bottom: none;
}

tbody tr:not(:last-child) {
    transition: background 0.2s ease;
}

tbody tr:not(:last-child):hover {
    background: #fdfdfd;
}

.action-heading {
    text-align: center;
}

/* MENU INFORMATION */
.menu-info {
    display: flex;
    align-items: center;
    gap: 12px;

    min-width: 190px;
}

.menu-image {
    display: flex;
    align-items: center;
    justify-content: center;

    flex-shrink: 0;

    width: 45px;
    height: 45px;

    overflow: hidden;

    color: #ca0708;
    background: #fff0f0;
    border-radius: 11px;
}

.menu-image img {
    width: 100%;
    height: 100%;

    object-fit: cover;
}

.menu-image svg {
    width: 23px;
    height: 23px;
}

.menu-copy {
    display: flex;
    flex-direction: column;
    gap: 5px;

    min-width: 0;
}

.menu-copy strong {
    color: #202124;
    font-size: 13px;
    font-weight: 700;
}

.menu-copy small {
    max-width: 190px;

    overflow: hidden;
    color: #9298a2;

    font-size: 11px;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* CATEGORY BADGE */
.category-badge {
    display: inline-flex;
    align-items: center;

    padding: 6px 10px;

    color: #626875;
    background: #f3f4f6;
    border-radius: 7px;

    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
}

/* PRICE */
.price {
    color: #202124;
    font-size: 13px;
    font-weight: 700;
    white-space: nowrap;
}

/* STATUS */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 7px;

    padding: 7px 10px;

    border: none;
    border-radius: 20px;

    font-family: inherit;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;

    cursor: pointer;

    transition:
        background 0.2s ease,
        transform 0.2s ease;
}

.status-badge:hover {
    transform: translateY(-1px);
}

.status-badge > span {
    width: 7px;
    height: 7px;

    border-radius: 50%;
}

.status-badge.available {
    color: #179b61;
    background: #e9f8f0;
}

.status-badge.available > span {
    background: #179b61;
}

.status-badge.unavailable {
    color: #d97706;
    background: #fff4e7;
}

.status-badge.unavailable > span {
    background: #e98516;
}

/* ACTION BUTTONS */
.action-buttons {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.icon-button {
    display: flex;
    align-items: center;
    justify-content: center;

    width: 34px;
    height: 34px;

    border: 1px solid transparent;
    border-radius: 9px;

    cursor: pointer;

    transition:
        background 0.2s ease,
        transform 0.2s ease;
}

.icon-button:hover {
    transform: translateY(-2px);
}

.icon-button svg {
    width: 17px;
    height: 17px;
}

.edit-button {
    color: #2563eb;
    background: #eff6ff;
}

.edit-button:hover {
    background: #dbeafe;
}

.delete-button {
    color: #ca0708;
    background: #fff0f0;
}

.delete-button:hover {
    background: #ffe0e0;
}

/* EMPTY STATE */
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;

    min-height: 230px;
    padding: 30px 15px;

    text-align: center;
}

.empty-icon {
    display: flex;
    align-items: center;
    justify-content: center;

    width: 58px;
    height: 58px;
    margin-bottom: 15px;

    color: #ca0708;
    background: #fff0f0;
    border-radius: 16px;
}

.empty-icon svg {
    width: 27px;
    height: 27px;
}

.empty-state strong {
    color: #202124;
    font-size: 15px;
    font-weight: 700;
}

.empty-state p {
    max-width: 300px;
    margin: 8px 0 0;

    color: #9298a2;
    font-size: 12px;
    line-height: 1.6;
}

/* ANIMATION */
@keyframes fadeUp {
    from {
        opacity: 0;
        transform: translateY(15px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* TABLET */
@media (max-width: 900px) {
    .menu-panel {
        padding: 22px;
    }

    .toolbar {
        align-items: flex-start;
        flex-direction: column;
    }

    .search-box {
        max-width: 100%;
    }

    .category-filter {
        width: 100%;
    }
}

/* MOBILE */
@media (max-width: 600px) {
    .menu-panel {
        margin-top: 20px;
        padding: 17px;

        border-radius: 14px;
    }

    .panel-heading {
        align-items: flex-start;
        flex-direction: column;

        margin-bottom: 18px;
    }

    .panel-heading h2 {
        font-size: 18px;
    }

    .panel-heading p {
        font-size: 12px;
    }

    .result-count {
        padding: 7px 11px;
    }

    .toolbar {
        gap: 13px;
        margin-bottom: 17px;
    }

    .category-filter {
        gap: 6px;
    }

    .category-filter button {
        min-height: 34px;
        padding: 0 10px;

        font-size: 11px;
    }

    .table-wrapper {
        border-radius: 10px;
    }
}

/* SMALL MOBILE */
@media (max-width: 380px) {
    .menu-panel {
        padding: 13px;
    }

    .category-filter button {
        padding: 0 8px;
        font-size: 10px;
    }
}

/* ACCESSIBILITY */
@media (prefers-reduced-motion: reduce) {
    .menu-panel {
        animation: none;
    }

    .icon-button,
    .status-badge,
    .category-filter button,
    .search-box {
        transition: none;
    }
}
</style>
