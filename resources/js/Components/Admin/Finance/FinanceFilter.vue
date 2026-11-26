<script setup>
defineProps({
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
    "change-period",
    "update:start-date",
    "update:end-date",
    "reset",
]);

const periods = [
    { label: "7 Hari Terakhir", value: "7d" },
    { label: "30 Hari Terakhir", value: "30d" },
    { label: "12 Bulan Terakhir", value: "12m" },
];
</script>

<template>
    <section class="finance-filter" aria-labelledby="finance-filter-title">
        <div class="finance-filter-heading">
            <div>
                <h2 id="finance-filter-title">Filter Laporan</h2>
                <p>Atur periode untuk melihat laporan keuangan.</p>
            </div>

            <button type="button" class="finance-reset-button"
                aria-label="Reset semua filter laporan keuangan" @click="emit('reset')">
                Reset Filter
            </button>
        </div>

        <div class="finance-period-options">
            <button
                v-for="period in periods"
                :key="period.value"
                type="button"
                class="finance-period-button"
                :class="{ active: selectedPeriod === period.value }"
                :aria-pressed="selectedPeriod === period.value"
                @click="emit('change-period', period.value)"
            >
                {{ period.label }}
            </button>
        </div>

        <div class="finance-date-filter">
            <div class="finance-date-field">
                <label for="finance-start-date">Tanggal Mulai</label>
                <input
                    id="finance-start-date"
                    type="date"
                    :value="startDate"
                    :max="endDate || undefined"
                    @change="emit('update:start-date', $event.target.value)"
                />
            </div>

            <span class="finance-date-separator">Sampai</span>

            <div class="finance-date-field">
                <label for="finance-end-date">Tanggal Akhir</label>
                <input
                    id="finance-end-date"
                    type="date"
                    :value="endDate"
                    :min="startDate || undefined"
                    @change="emit('update:end-date', $event.target.value)"
                />
            </div>
        </div>
    </section>
</template>

<style scoped>
.finance-filter {
    animation: financeFilterFadeUp 0.5s ease both;
}

.finance-reset-button:focus-visible,
.finance-period-button:focus-visible,
.finance-date-field input:focus-visible {
    outline: 3px solid rgba(202, 7, 8, 0.25);
    outline-offset: 3px;
}

@keyframes financeFilterFadeUp {
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
    .finance-filter {
        animation: none;
    }
}
</style>
