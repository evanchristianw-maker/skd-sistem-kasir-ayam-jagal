import { ref, computed } from "vue";
import { router, usePage } from "@inertiajs/vue3";

export default function useMenuManagement(initialMenus = []) {
    const page = usePage();

    // ADMIN INFORMATION
    const adminName = computed(
        () => page.props.auth?.user?.name || "Administrator",
    );

    // MENU DATA
    const menus = ref(initialMenus);

    const searchQuery = ref("");
    const selectedCategory = ref("Semua");

    // MODAL STATE
    const showModal = ref(false);
    const showDeleteModal = ref(false);
    const isEditing = ref(false);

    const selectedMenuId = ref(null);
    const actionType = ref('delete');
    const actionError = ref("");

    // CATEGORY
    const categories = ["Semua", "Makanan", "Minuman", "Paket", "Tambahan"];

    // FORM
    const emptyForm = () => ({
        name: "",
        category: "Makanan",
        price: "",
        description: "",
        image: "",
        imageFile: null,
    });

    const form = ref(emptyForm());

    // FILTER MENU
    const filteredMenus = computed(() => {
        return menus.value.filter((menu) => {
            const search = String(menu.name ?? "")
                .toLowerCase()
                .includes(searchQuery.value.toLowerCase().trim());

            const category =
                selectedCategory.value === "Semua" ||
                menu.category === selectedCategory.value;

            return search && category;
        });
    });

    // MENU STATISTICS
    const totalMenus = computed(() => menus.value.length);

    const activeMenus = computed(
        () => menus.value.filter((menu) => Boolean(menu.status)).length,
    );

    const inactiveMenus = computed(
        () => menus.value.filter((menu) => !menu.status).length,
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

    // PRICE FORMAT
    const formatPrice = (price) => {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            maximumFractionDigits: 0,
        }).format(Number(price) || 0);
    };

    // SYNCHRONIZE MENU DATA
    const sync = (response) => {
        if (Array.isArray(response.props?.menus)) {
            menus.value = response.props.menus;
        }
    };

    // IMAGE PREVIEW CLEANUP
    const clearImagePreview = () => {
        if (form.value.image?.startsWith("blob:")) {
            URL.revokeObjectURL(form.value.image);
        }
    };

    // OPEN ADD MODAL
    function openAddModal() {
        clearImagePreview();

        isEditing.value = false;
        selectedMenuId.value = null;

        form.value = emptyForm();
        showModal.value = true;
    }

    // OPEN EDIT MODAL
    function openEditModal(menu) {
        clearImagePreview();

        isEditing.value = true;
        selectedMenuId.value = menu.id;

        form.value = {
            name: menu.name ?? "",
            category: menu.category ?? "Makanan",
            price: menu.price ?? "",
            description: menu.description ?? "",
            image: menu.image ?? "",
            imageFile: null,
        };

        showModal.value = true;
    }

    // SAVE MENU
    function saveMenu() {
        const name = form.value.name.trim();
        const price = Number(form.value.price);

        if (!name || !Number.isFinite(price) || price <= 0) {
            window.alert("Nama dan harga menu harus diisi dengan benar.");
            return;
        }

        // DATA YANG DIKIRIM KE LARAVEL
        const data = {
            nama_menu: name,
            deskripsi: form.value.description,
            kategori: form.value.category,
            harga: price,
            gambar: form.value.imageFile,
        };

        const options = {
            preserveScroll: true,
            forceFormData: true,

            onSuccess: (response) => {
                sync(response);

                showModal.value = false;
                clearImagePreview();
                form.value = emptyForm();
            },

            onError: (errors) => {
                console.error("Gagal menyimpan menu:", errors);
            },
        };

        if (isEditing.value) {
            // POST + METHOD SPOOFING AGAR UPLOAD FILE PADA UPDATE BERJALAN
            router.post(
                `/menu/${selectedMenuId.value}`,
                {
                    ...data,
                    _method: "put",
                },
                options,
            );
        } else {
            router.post("/menu", data, options);
        }
    }

    // OPEN DELETE / STATUS CONFIRMATION MODAL
    function confirmDelete(menu) {
        selectedMenuId.value = menu.id;
        actionType.value = "delete";
        actionError.value = "";
        showDeleteModal.value = true;
    }

    function confirmToggleStatus(menu) {
        selectedMenuId.value = menu.id;
        actionType.value = menu.status ? "deactivate" : "activate";
        actionError.value = "";
        showDeleteModal.value = true;
    }

    // EXECUTE CONFIRMED ACTION
    function confirmAction() {
        if (!selectedMenuId.value) return;

        const options = {
            preserveScroll: true,

            onSuccess: (response) => {
                sync(response);
                showDeleteModal.value = false;
                selectedMenuId.value = null;
            },

            onError: (errors) => {
                console.error("Gagal memproses menu:", errors);

                actionError.value = errors.menu ||
                    "Aksi menu gagal diproses. Silakan coba lagi.";
            },
        };

        if (actionType.value === "delete") {
            router.delete(`/menu/${selectedMenuId.value}`, options);
            return;
        }

        router.patch(`/menu/${selectedMenuId.value}/status`, {}, options);
    }
    // IMAGE UPLOAD
    function handleImageUpload(event) {
        const file = event.target.files?.[0];

        if (!file) return;

        // VALIDASI FORMAT
        const allowedTypes = ["image/jpeg", "image/png", "image/webp"];

        if (!allowedTypes.includes(file.type)) {
            window.alert("Format gambar harus JPG, PNG, atau WEBP.");
            event.target.value = "";
            return;
        }

        // VALIDASI UKURAN MAKSIMAL 2 MB
        if (file.size > 2 * 1024 * 1024) {
            window.alert("Ukuran gambar maksimal 2 MB.");
            event.target.value = "";
            return;
        }

        clearImagePreview();

        // SIMPAN FILE ASLI UNTUK DIKIRIM KE LARAVEL
        form.value.imageFile = file;

        // BUAT PREVIEW GAMBAR
        form.value.image = URL.createObjectURL(file);
    }

    return {
        adminName,
        menus,
        searchQuery,
        selectedCategory,

        showModal,
        showDeleteModal,
        actionType,
        actionError,
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
        confirmToggleStatus,
        confirmAction,
        handleImageUpload,
    };
}
