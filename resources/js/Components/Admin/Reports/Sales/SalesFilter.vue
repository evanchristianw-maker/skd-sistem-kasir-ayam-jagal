<script setup>
const props = defineProps({
    selectedPeriod: {
        type: String,
        default: "7d",
    },
    startDate: {
        type: String,
        default: "",
    },
    endDate: {
        type: String,
        default: "",
    },
});

const emit = defineEmits([
    "update:start-date",
    "update:end-date",
    "change-period",
    "reset",
]);

const periods = [
    { label: "7 Hari Terakhir", value: "7d" },
    { label: "30 Hari Terakhir", value: "30d" },
    { label: "12 Bulan Terakhir", value: "12m" },
];

const updateStartDate = (event) => {
    emit("update:start-date", event.target.value);
};

const updateEndDate = (event) => {
    emit("update:end-date", event.target.value);
};
</script>

<template>
    <section class="sales-filter" aria-labelledby="sales-filter-title">
        <div class="filter-heading">
            <div>
                <h2 id="sales-filter-title">Filter Laporan</h2>
                <p>Atur periode untuk melihat data penjualan.</p>
            </div>

            <button
                type="button"
                class="reset-button"
                aria-label="Reset semua filter laporan"
                @click="emit('reset')"
            >
                Reset Filter
            </button>
        </div>

        <div class="period-options">
            <button
                v-for="period in periods"
                :key="period.value"
                type="button"
                class="period-button"
                :class="{ active: selectedPeriod === period.value }"
                :aria-pressed="selectedPeriod === period.value"
                @click="emit('change-period', period.value)"
            >
                {{ period.label }}
            </button>
        </div>

        <div class="date-filter">
            <div class="date-field">
                <label for="sales-start-date">Tanggal Mulai</label>
                <input
                    id="sales-start-date"
                    type="date"
                    :value="startDate"
                    :max="endDate || undefined"
                    @change="updateStartDate"
                />
            </div>

            <div class="date-separator">Sampai</div>

            <div class="date-field">
                <label for="sales-end-date">Tanggal Akhir</label>
                <input
                    id="sales-end-date"
                    type="date"
                    :value="endDate"
                    :min="startDate || undefined"
                    @change="updateEndDate"
                />
            </div>
        </div>
    </section>
</template>

<style scoped>
.sales-filter {
    animation: salesFilterFadeUp 0.5s ease both;
}

.reset-button:focus-visible,
.period-button:focus-visible,
.date-field input:focus-visible {
    outline: 3px solid rgba(202, 7, 8, 0.2);
    outline-offset: 3px;
}

@keyframes salesFilterFadeUp {
    from {
        opacity: 0;
        transform: translateY(14px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (prefers-reduced-motion: reduce) {
    .sales-filter {
        animation: none;
    }
}
</style>
