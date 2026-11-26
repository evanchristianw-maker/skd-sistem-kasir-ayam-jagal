import { ref, onMounted, onBeforeUnmount } from "vue";
import axios from "axios";

export default function useNotifications() {
    const notifications = ref([]);
    const unreadCount = ref(0);
    const isLoading = ref(false);
    const processingId = ref(null);
    const errorMessage = ref("");

    let refreshInterval = null;

    const fetchNotifications = async (showLoading = false) => {
        if (showLoading) isLoading.value = true;

        try {
            const response = await axios.get("/notifications");

            notifications.value = response.data.notifications ?? [];
            unreadCount.value = response.data.unread_count ?? 0;
            errorMessage.value = "";
        } catch (error) {
            errorMessage.value =
                error.response?.data?.message ??
                "Gagal memuat notifikasi.";
        } finally {
            isLoading.value = false;
        }
    };

    const markAsRead = async (id) => {
        if (processingId.value) return;

        processingId.value = id;

        try {
            await axios.patch(`/notifications/${id}/read`);
            await fetchNotifications();
        } catch (error) {
            errorMessage.value =
                error.response?.data?.message ??
                "Gagal memperbarui notifikasi.";
        } finally {
            processingId.value = null;
        }
    };

    const deleteNotification = async (id) => {
        if (processingId.value) return;

        processingId.value = id;

        try {
            await axios.delete(`/notifications/${id}`);
            await fetchNotifications();
        } catch (error) {
            errorMessage.value =
                error.response?.data?.message ??
                "Gagal menghapus notifikasi.";
        } finally {
            processingId.value = null;
        }
    };

    onMounted(() => {
        fetchNotifications(true);
        refreshInterval = setInterval(fetchNotifications, 5000);
    });

    onBeforeUnmount(() => {
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
    });

    return {
        notifications,
        unreadCount,
        isLoading,
        processingId,
        errorMessage,
        fetchNotifications,
        markAsRead,
        deleteNotification,
    };
}