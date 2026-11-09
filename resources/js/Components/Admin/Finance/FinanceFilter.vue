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
    <section class="finance-filter">
        <div class="finance-filter-heading">
            <div>
                <h2>Filter Laporan</h2>
                <p>Atur periode untuk melihat laporan keuangan.</p>
            </div>

            <button type="button" class="finance-reset-button" @click="emit('reset')">
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
