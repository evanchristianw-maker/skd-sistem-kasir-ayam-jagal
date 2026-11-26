<script setup>
const searchQuery = defineModel("searchQuery");
const selectedCategory = defineModel("selectedCategory");

const isMenuActive = (status) =>
    status === true || status === 1 || status === "1";

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

const emit = defineEmits(["edit", "delete", "toggleStatus"]);
</script>

<template>
    <section class="menu-panel">
        <div class="panel-heading">
            <div>
                <h2>Daftar Menu</h2>
                <p>Kelola informasi dan ketersediaan setiap produk.</p>
            </div>

            <span class="result-count"> {{ filteredMenus.length }} menu </span>
        </div>

        <div class="toolbar">
            <label class="search-box">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
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
                    :class="{ active: selectedCategory === category }"
                    :aria-pressed="selectedCategory === category"
                    @click="selectedCategory = category"
                >
                    {{ category }}
                </button>
            </div>
        </div>

        <div class="table-wrapper">
            <table>
                <caption class="sr-only">
                    Daftar menu UMKM Ayam Jagal beserta kategori, harga,
                    status, dan tindakan pengelolaan.
                </caption>
                <thead>
                    <tr>
                        <th scope="col">MENU</th>
                        <th scope="col">KATEGORI</th>
                        <th scope="col">HARGA</th>
                        <th scope="col">STATUS</th>
                        <th scope="col" class="action-heading">AKSI</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="menu in filteredMenus" :key="menu.id">
                        <!-- Menu -->
                        <td data-label="Menu">
                            <div class="menu-info">
                                <div class="menu-image">
                                    <img
                                        v-if="menu.image"
                                        :src="menu.image"
                                        :alt="menu.name"
                                    />

                                    <svg
                                        v-else
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        aria-hidden="true"
                                    >
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

                        <!-- Kategori -->
                        <td data-label="Kategori">
                            <span class="category-badge">
                                {{ menu.category }}
                            </span>
                        </td>

                        <!-- Harga -->
                        <td data-label="Harga">
                            <strong class="price">
                                {{ formatPrice(menu.price) }}
                            </strong>
                        </td>

                        <!-- Status: Toggle Aktif / Tidak Aktif -->
                        <td data-label="Status">
                            <button
                                type="button"
                                class="toggle-switch"
                                :class="{ 'is-active': isMenuActive(menu.status) }"
                                :aria-pressed="isMenuActive(menu.status)"
                                :aria-label="
                                    menu.status
                                        ? `Nonaktifkan ${menu.name}`
                                        : `Aktifkan ${menu.name}`
                                "
                                @click="emit('toggleStatus', menu)"
                            >
                                <span class="toggle-track">
                                    <span class="toggle-thumb"></span>
                                </span>

                                <span class="toggle-label">
                                    {{ isMenuActive(menu.status) ? "Aktif" : "Tidak Aktif" }}
                                </span>
                            </button>
                        </td>

                        <!-- Aksi: Edit dan Hapus -->
                        <td data-label="Aksi">
                            <div class="action-buttons">
                                <button
                                    type="button"
                                    class="icon-button edit-button"
                                    title="Edit menu"
                                    :aria-label="`Edit menu ${menu.name}`"
                                    @click="emit('edit', menu)"
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
                                    title="Hapus menu"
                                    :aria-label="`Hapus menu ${menu.name}`"
                                    @click="emit('delete', menu)"
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
                        </td>
                    </tr>

                    <!-- Menu kosong -->
                    <tr v-if="filteredMenus.length === 0" class="empty-row">
                        <td colspan="5">
                            <div class="empty-state">
                                <div class="empty-icon">
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
* {
    box-sizing: border-box;
}

/* PANEL */
.menu-panel {
    width: 100%;
    margin-top: 28px;
    padding: 26px;
    background: #ffffff;
    border: 1px solid #eceef1;
    border-radius: 16px;
    box-shadow: 0 3px 12px rgba(20, 25, 35, 0.035);
    animation: menuTableFadeUp 0.5s ease both;
}

/* HEADING */
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
    letter-spacing: -0.4px;
}

.panel-heading p {
    margin: 7px 0 0;
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
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.search-box:focus-within {
    border-color: #ca0708;
    box-shadow: 0 0 0 3px rgba(202, 7, 8, 0.07);
}

.search-box svg {
    width: 19px;
    height: 19px;
    flex-shrink: 0;
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

.category-filter {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 7px;
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
    transition: color 0.2s ease, background 0.2s ease, border-color 0.2s ease;
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
    border: 1px solid #eceef1;
    border-radius: 12px;
}

table {
    width: 100%;
    min-width: 720px;
    border-collapse: collapse;
    text-align: left;
}

thead {
    background: #f8f9fb;
}

th {
    padding: 15px 16px;
    color: #777e89;
    border-bottom: 1px solid #eceef1;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.5px;
    white-space: nowrap;
}

td {
    padding: 15px 16px;
    color: #444b56;
    border-bottom: 1px solid #f0f0f0;
    font-size: 13px;
    vertical-align: middle;
}

tbody tr {
    transition: background 0.2s ease;
}

tbody tr:hover:not(:last-child) {
    background: #fcfcfd;
}

tbody tr:last-child td {
    border-bottom: none;
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

/* CATEGORY */
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

/* STATUS TOGGLE */
.toggle-switch {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    min-width: 105px;
    min-height: 35px;
    padding: 5px 9px;
    color: #777e89;
    background: #f3f4f6;
    border: 1px solid #e5e7eb;
    border-radius: 20px;
    font-family: inherit;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.2s ease, border-color 0.2s ease;
}

.toggle-track {
    position: relative;
    width: 29px;
    height: 16px;
    flex-shrink: 0;
    background: #aeb4be;
    border-radius: 20px;
    transition: background 0.2s ease;
}

.toggle-thumb {
    position: absolute;
    top: 2px;
    left: 2px;
    width: 12px;
    height: 12px;
    background: #ffffff;
    border-radius: 50%;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    transition: transform 0.2s ease;
}

.toggle-switch.is-active {
    color: #168653;
    background: #e9f8f0;
    border-color: #bcebd2;
}

.toggle-switch.is-active .toggle-track {
    background: #19a66a;
}

.toggle-switch.is-active .toggle-thumb {
    transform: translateX(13px);
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
    flex-shrink: 0;
    width: 35px;
    height: 35px;
    border: none;
    border-radius: 9px;
    cursor: pointer;
    transition: transform 0.2s ease, background 0.2s ease;
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

/* FOCUS */
.toggle-switch:focus-visible,
.icon-button:focus-visible,
.category-filter button:focus-visible {
    outline: 3px solid rgba(202, 7, 8, 0.2);
    outline-offset: 2px;
}

/* EMPTY STATE */
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
    min-height: 230px;
    padding: 30px 15px;
    box-sizing: border-box;
    text-align: center;
}

.empty-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    align-self: center;
    width: 58px;
    height: 58px;
    margin: 0 auto 15px;
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
}

.empty-state p {
    max-width: 300px;
    margin: 8px 0 0;
    color: #9298a2;
    font-size: 12px;
    line-height: 1.6;
}

/* ANIMATION */
@keyframes menuTableFadeUp {
    from {
        opacity: 0;
        transform: translateY(13px);
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
        gap: 10px;
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
        overflow: visible;
        border: none;
    }

    table {
        display: block;
        width: 100%;
        min-width: 0;
    }

    thead {
        display: none;
    }

    tbody {
        display: grid;
        gap: 12px;
    }

    tbody tr {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px 10px;
        padding: 12px 10px;
        background: #ffffff;
        border: 1px solid #e9eaed;
        border-radius: 12px;
    }

    tbody tr:hover {
        background: #ffffff;
    }

    tbody td {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;
        min-width: 0;
        padding: 0;
        border: none;
        font-size: 12px;
    }

    tbody td:first-child {
        grid-column: 1 / -1;
    }

    tbody td::before {
        content: attr(data-label);
        margin-bottom: 7px;
        color: #9298a2;
        font-size: 10px;
        font-weight: 700;
    }

    tbody td:first-child::before {
        display: none;
    }

    .menu-info {
        width: 100%;
        min-width: 0;
    }

    .menu-copy {
        min-width: 0;
    }

    .menu-copy small {
        max-width: 100%;
    }

    .menu-image {
        width: 42px;
        height: 42px;
    }

    .category-badge {
        font-size: 11px;
    }

    .price {
        font-size: 13px;
    }

    .toggle-switch {
        min-width: 105px;
        min-height: 34px;
        font-size: 10px;
    }

    .action-buttons {
        justify-content: flex-start;
        gap: 7px;
    }

    .icon-button {
        width: 34px;
        height: 34px;
    }

    tbody tr.empty-row {
        display: block;
    }

    tbody tr.empty-row td {
        display: block;
    }

    tbody tr.empty-row td::before {
        display: none;
    }
}

/* SMALL MOBILE */
@media (max-width: 380px) {
    .menu-panel {
        padding: 13px;
    }

    tbody tr {
        gap: 11px 7px;
        padding: 11px 8px;
    }

    .toggle-switch {
        min-width: 95px;
        gap: 5px;
        padding: 5px 7px;
        font-size: 9px;
    }

    .action-buttons {
        gap: 5px;
    }
}

/* ACCESSIBILITY */
@media (prefers-reduced-motion: reduce) {
    .menu-panel {
        animation: none;
    }

    .toggle-switch,
    .toggle-track,
    .toggle-thumb,
    .icon-button,
    .category-filter button,
    .search-box {
        transition: none;
    }
}


/* ACCESSIBILITY */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

.category-filter button:focus-visible,
.toggle-switch:focus-visible,
.icon-button:focus-visible {
    outline: 3px solid rgba(202, 7, 8, 0.2);
    outline-offset: 3px;
}

/* EMPTY ROW */
tbody tr.empty-row td {
    text-align: center;
}

</style>






