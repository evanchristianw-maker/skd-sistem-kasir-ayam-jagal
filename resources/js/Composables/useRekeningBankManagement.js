import { ref, computed, watch } from "vue";
import { router, usePage } from "@inertiajs/vue3";

export default function useRekeningBankManagement(initialRekeningBanks = []) {
    const page = usePage();

    // DATA
    const rekeningBanks = ref(initialRekeningBanks);
    const searchQuery = ref("");

    // MODAL STATE
    const showModal = ref(false);
    const showDeleteModal = ref(false);
    const isEditing = ref(false);

    const selectedRekeningId = ref(null);
    const actionType = ref("delete");
    const actionError = ref("");

    // FORM VALIDATION ERRORS
    const formErrors = ref({});

    // FORM
    const emptyForm = () => ({
        nama_bank: "",
        nomor_rekening: "",
        atas_nama: "",
    });

    const form = ref(emptyForm());

    // FILTER
    const filteredRekeningBanks = computed(() => {
        return rekeningBanks.value.filter((rekening) => {
            const query = searchQuery.value.toLowerCase().trim();
            if (!query) return true;

            return (
                String(rekening.nama_bank ?? "")
                    .toLowerCase()
                    .includes(query) ||
                String(rekening.nomor_rekening ?? "")
                    .toLowerCase()
                    .includes(query) ||
                String(rekening.atas_nama ?? "")
                    .toLowerCase()
                    .includes(query)
            );
        });
    });

    // STATISTICS
    const totalRekening = computed(() => rekeningBanks.value.length);

    const rekeningAktif = computed(
        () =>
            rekeningBanks.value.filter((r) => Boolean(r.status_aktif)).length,
    );

    const rekeningNonaktif = computed(
        () =>
            rekeningBanks.value.filter((r) => !r.status_aktif).length,
    );

    // CLEAR VALIDATION ERRORS WHEN FORM CHANGES
    watch(
        form,
        () => {
            formErrors.value = {};
        },
        { deep: true },
    );

    // CURRENT DATE
    const currentDate = computed(() =>
        new Date().toLocaleDateString("id-ID", {
            weekday: "long",
            day: "numeric",
            month: "long",
            year: "numeric",
        }),
    );

    // SYNCHRONIZE DATA
    const sync = (response) => {
        if (Array.isArray(response.props?.rekeningBanks)) {
            rekeningBanks.value = response.props.rekeningBanks;
        }
    };

    // OPEN ADD MODAL
    function openAddModal() {
        isEditing.value = false;
        selectedRekeningId.value = null;
        form.value = emptyForm();
        formErrors.value = {};
        showModal.value = true;
    }

    // OPEN EDIT MODAL
    function openEditModal(rekening) {
        isEditing.value = true;
        selectedRekeningId.value = rekening.id;

        form.value = {
            nama_bank: rekening.nama_bank ?? "",
            nomor_rekening: rekening.nomor_rekening ?? "",
            atas_nama: rekening.atas_nama ?? "",
        };

        formErrors.value = {};
        showModal.value = true;
    }

    // SAVE REKENING
    function saveRekening() {
        const namaBank = form.value.nama_bank.trim();
        const nomorRekening = form.value.nomor_rekening.trim();
        const atasNama = form.value.atas_nama.trim();

        if (!namaBank || !nomorRekening || !atasNama) {
            window.alert(
                "Nama bank, nomor rekening, dan atas nama harus diisi.",
            );
            return;
        }

        formErrors.value = {};

        const data = {
            nama_bank: namaBank,
            nomor_rekening: nomorRekening,
            atas_nama: atasNama,
        };

        const options = {
            preserveScroll: true,

            onSuccess: (response) => {
                sync(response);
                formErrors.value = {};
                showModal.value = false;
                form.value = emptyForm();
            },

            onError: (errors) => {
                console.error("Gagal menyimpan rekening bank:", errors);

                const normalized = {};

                for (const [key, value] of Object.entries(errors || {})) {
                    normalized[key] = Array.isArray(value)
                        ? value[0]
                        : value;
                }

                formErrors.value = normalized;
            },
        };

        if (isEditing.value) {
            router.put(
                `/rekening-bank/${selectedRekeningId.value}`,
                data,
                options,
            );
        } else {
            router.post("/rekening-bank", data, options);
        }
    }

    // CONFIRM DELETE
    function confirmDelete(rekening) {
        selectedRekeningId.value = rekening.id;
        actionType.value = "delete";
        actionError.value = "";
        showDeleteModal.value = true;
    }

    // CONFIRM TOGGLE STATUS
    function confirmToggleStatus(rekening) {
        selectedRekeningId.value = rekening.id;
        actionType.value = rekening.status_aktif ? "deactivate" : "activate";
        actionError.value = "";
        showDeleteModal.value = true;
    }

    // EXECUTE CONFIRMED ACTION
    function confirmAction() {
        if (!selectedRekeningId.value) return;

        const options = {
            preserveScroll: true,

            onSuccess: (response) => {
                sync(response);
                showDeleteModal.value = false;
                selectedRekeningId.value = null;
            },

            onError: (errors) => {
                console.error("Gagal memproses rekening bank:", errors);
                actionError.value =
                    errors.message ||
                    "Aksi rekening bank gagal diproses. Silakan coba lagi.";
            },
        };

        if (actionType.value === "delete") {
            router.delete(
                `/rekening-bank/${selectedRekeningId.value}`,
                options,
            );
            return;
        }

        router.patch(
            `/rekening-bank/${selectedRekeningId.value}/status`,
            {},
            options,
        );
    }

    return {
        rekeningBanks,
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
    };
}
