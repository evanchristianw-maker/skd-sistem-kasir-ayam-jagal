<script setup>
import { computed, ref } from "vue";

const props = defineProps({
    menus: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(["add-to-cart"]);

const searchQuery = ref("");
const selectedCategory = ref("Semua");

const categories = computed(() => {
    const uniqueCategories = [
        ...new Set(props.menus.map((menu) => menu.category).filter(Boolean)),
    ];

    return ["Semua", ...uniqueCategories];
});

const filteredMenus = computed(() => {
    return props.menus.filter((menu) => {
        const matchesSearch = menu.name
            .toLowerCase()
            .includes(searchQuery.value.toLowerCase());

        const matchesCategory =
            selectedCategory.value === "Semua" ||
            menu.category === selectedCategory.value;

        return matchesSearch && matchesCategory;
    });
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(Number(value) || 0);
};

const addMenu = (menu) => {
    emit("add-to-cart", menu);
};
</script>

<template>
    <section class="cashier-menu-section">
        <div class="menu-section-heading">
            <div>
                <h2>Daftar Menu</h2>
                <p>Pilih menu yang ingin ditambahkan ke transaksi.</p>
            </div>

            <span class="menu-total">
                {{ filteredMenus.length }} Menu
            </span>
        </div>

        <div class="menu-toolbar">
            <div class="menu-search">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle
                        cx="10.8"
                        cy="10.8"
                        r="6.8"
                        stroke="currentColor"
                        stroke-width="1.8"
                    />
                    <path
                        d="M16 16L21 21"
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
            </div>

            <div class="menu-categories" aria-label="Filter kategori">
                <button
                    v-for="category in categories"
                    :key="category"
                    type="button"
                    class="category-button"
                    :class="{ active: selectedCategory === category }"
                    :aria-pressed="selectedCategory === category"
                    @click="selectedCategory = category"
                >
                    {{ category }}
                </button>
            </div>
        </div>

        <div v-if="filteredMenus.length" class="menu-grid">
            <article
                v-for="(menu, index) in filteredMenus"
                :key="menu.id"
                class="menu-card"
                :style="{ '--menu-index': index }"
            >
                <div class="menu-card-image">
                    <img
                        :src="menu.image || (menu.name.toLowerCase().includes('ayam goreng') ? '/assets/images/products/ayam-goreng.png' : '/assets/images/products/ayam-goreng.png')"
                        :alt="menu.name"
                        loading="lazy"
                        @error="$event.target.src = '/assets/images/products/ayam-goreng.png'"
                    />
                </div>

                <div class="menu-card-content">
                    <span class="menu-category-label">
                        {{ menu.category || "Menu" }}
                    </span>

                    <h3>{{ menu.name }}</h3>

                    <strong class="menu-price">
                        {{ formatCurrency(menu.price) }}
                    </strong>
                </div>

                <button
                    type="button"
                    class="menu-add-button"
                    :aria-label="`Tambah ${menu.name} ke keranjang`"
                    @click="addMenu(menu)"
                >
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path
                            d="M12 5V19M5 12H19"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                        />
                    </svg>
                    <span>Tambah</span>
                </button>
            </article>
        </div>

        <div v-else class="menu-empty-state">
            <div class="empty-icon">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle
                        cx="10.8"
                        cy="10.8"
                        r="6.8"
                        stroke="currentColor"
                        stroke-width="1.7"
                    />
                    <path
                        d="M16 16L21 21"
                        stroke="currentColor"
                        stroke-width="1.7"
                        stroke-linecap="round"
                    />
                </svg>
            </div>

            <h3>Menu tidak ditemukan</h3>
            <p>Coba gunakan kata kunci atau kategori yang berbeda.</p>
        </div>
    </section>
</template>

<style scoped>
* {
    box-sizing: border-box;
}

.cashier-menu-section {
    min-width: 0;
    padding: 24px;
    border: 1px solid #e9eaed;
    border-radius: 16px;
    background: #ffffff;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.035);
}

.menu-section-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    margin-bottom: 20px;
}

.menu-section-heading h2 {
    margin: 0;
    color: #252525;
    font-size: 19px;
    font-weight: 800;
}

.menu-section-heading p {
    margin: 6px 0 0;
    color: #777777;
    font-size: 13px;
    line-height: 1.5;
}

.menu-total {
    flex-shrink: 0;
    padding: 7px 12px;
    border-radius: 20px;
    color: #ca0708;
    background: #fff0f0;
    font-size: 12px;
    font-weight: 700;
}

.menu-toolbar {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-bottom: 22px;
}

.menu-search {
    min-height: 46px;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 0 14px;
    border: 1px solid #e5e5e5;
    border-radius: 11px;
    background: #ffffff;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.menu-search:focus-within {
    border-color: #ca0708;
    box-shadow: 0 0 0 3px rgba(202, 7, 8, 0.08);
}

.menu-search svg {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
    color: #999999;
}

.menu-search input {
    width: 100%;
    min-width: 0;
    border: none;
    outline: none;
    color: #252525;
    background: transparent;
    font-family: inherit;
    font-size: 13px;
}

.menu-search input::placeholder {
    color: #aaaaaa;
}

.menu-categories {
    display: flex;
    align-items: center;
    gap: 9px;
    overflow-x: auto;
    padding: 2px 2px 7px;
    scrollbar-width: thin;
}

.category-button {
    min-height: 36px;
    flex-shrink: 0;
    padding: 0 15px;
    border: 1px solid #e9eaed;
    border-radius: 20px;
    color: #666666;
    background: #ffffff;
    font-family: inherit;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: color 0.2s ease, background 0.2s ease, border-color 0.2s ease;
}

.category-button:hover {
    border-color: #ca0708;
    color: #ca0708;
}

.category-button.active {
    border-color: #ca0708;
    color: #ffffff;
    background: #ca0708;
}

.category-button:focus-visible,
.menu-add-button:focus-visible {
    outline: 3px solid rgba(202, 7, 8, 0.25);
    outline-offset: 3px;
}

.menu-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 15px;
}

.menu-card {
    min-width: 165px;
    min-height: 180px;
    display: flex;
    flex-direction: column;
    padding: 12px;
    border: 1px solid #eeeeee;
    border-radius: 14px;
    background: #ffffff;
    animation: menuFadeUp 0.35s ease both;
    animation-delay: calc(var(--menu-index) * 35ms);
    transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;

}

.menu-card:hover {
    transform: translateY(-4px);
    border-color: #f2b6b6;
    box-shadow: 0 8px 22px rgba(128, 0, 0, 0.08);
}

.menu-card-image {
    width: 100%;
    height: 145px;
    overflow: hidden;
    margin-bottom: 13px;
    border-radius: 12px;
    background: #f5f6f8;

}

.menu-card-image img {
    width: 100%;
    height: 100%;
    display: block;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.menu-card:hover .menu-card-image img {
    transform: scale(1.04);
}

.menu-card-content {
    flex: 1;
    min-width: 0;
}

.menu-category-label {
    color: #999999;
    font-size: 11px;
    font-weight: 600;
}

.menu-card-content h3 {
    overflow-wrap: anywhere;
    margin: 5px 0 9px;
    color: #252525;
    font-size: 14px;
    font-weight: 750;
    line-height: 1.4;
}

.menu-price {
    display: block;
    margin-bottom: 14px;
    color: #ca0708;
    font-size: 14px;
    font-weight: 800;
}

.menu-add-button {
    width: 100%;
    min-height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    border: 1px solid #ca0708;
    border-radius: 9px;
    color: #ca0708;
    background: #fff5f5;
    font-family: inherit;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: color 0.2s ease, background 0.2s ease;
}

.menu-add-button:hover {
    color: #ffffff;
    background: #ca0708;
}

.menu-add-button svg {
    width: 17px;
    height: 17px;
}

.menu-empty-state {
    min-height: 230px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 25px;
    text-align: center;
}

.empty-icon {
    width: 54px;
    height: 54px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 14px;
    border-radius: 15px;
    color: #ca0708;
    background: #fff0f0;
}

.empty-icon svg {
    width: 27px;
    height: 27px;
}

.menu-empty-state h3 {
    margin: 0 0 7px;
    color: #333333;
    font-size: 15px;
    font-weight: 750;
}

.menu-empty-state p {
    margin: 0;
    color: #888888;
    font-size: 13px;
    line-height: 1.5;
}

@keyframes menuFadeUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 1000px) {
    .menu-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 600px) {
    .cashier-menu-section {
        padding: 17px;
        border-radius: 14px;
    }

    .menu-section-heading h2 {
        font-size: 17px;
    }

    .menu-section-heading p {
        font-size: 12px;
    }

    .menu-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .menu-card {
        min-height: 170px;
        padding: 12px;
    
    width: 165px;
}

    .menu-card-content h3 {
        font-size: 13px;
    }

    .menu-price {
        font-size: 13px;
    }
}

@media (max-width: 360px) {
    .menu-grid {
        grid-template-columns: 1fr;
    }
}

@media (prefers-reduced-motion: reduce) {
    .menu-card {
        animation: none;
        transition: none;
    
    width: 165px;
    padding: 12px;
}

    .menu-card:hover {
        transform: none;
    }

    .menu-search,
    .category-button,
    .menu-add-button {
        transition: none;
    }
}
</style>

