<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

interface Notification {
    id: number;
    title: string;
    message: string;
    type: string;
    link: string | null;
    is_read: boolean;
    created_at: string;
}

interface NotificationResponse {
    notifications: Notification[];
    unread_count: number;
}

const isOpen = ref(false);
const notifications = ref<Notification[]>([]);
const unreadCount = ref(0);
const loading = ref(false);

const hasUnread = computed(() => unreadCount.value > 0);

const loadNotifications = async () => {
    loading.value = true;
    try {
        const response = await axios.get<NotificationResponse>('/api/notifications');
        notifications.value = response.data.notifications;
        unreadCount.value = response.data.unread_count;
    } catch (error) {
        console.error('Failed to load notifications:', error);
    } finally {
        loading.value = false;
    }
};

const markAsRead = async (notificationId: number) => {
    try {
        await axios.patch(`/api/notifications/${notificationId}/read`);
        const notification = notifications.value.find(n => n.id === notificationId);
        if (notification && !notification.is_read) {
            notification.is_read = true;
            unreadCount.value = Math.max(0, unreadCount.value - 1);
        }
    } catch (error) {
        console.error('Failed to mark notification as read:', error);
    }
};

const markAllAsRead = async () => {
    try {
        await axios.patch('/api/notifications/read-all');
        notifications.value.forEach(n => n.is_read = true);
        unreadCount.value = 0;
    } catch (error) {
        console.error('Failed to mark all notifications as read:', error);
    }
};

const deleteNotification = async (notificationId: number) => {
    try {
        await axios.delete(`/api/notifications/${notificationId}`);
        const index = notifications.value.findIndex(n => n.id === notificationId);
        if (index > -1) {
            const notification = notifications.value[index];
            if (!notification.is_read) {
                unreadCount.value = Math.max(0, unreadCount.value - 1);
            }
            notifications.value.splice(index, 1);
        }
    } catch (error) {
        console.error('Failed to delete notification:', error);
    }
};

const handleNotificationClick = (notification: Notification) => {
    if (!notification.is_read) {
        markAsRead(notification.id);
    }

    if (notification.link) {
        isOpen.value = false;
        router.visit(notification.link);
    }
};

const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
    if (isOpen.value && notifications.value.length === 0) {
        loadNotifications();
    }
};

const getNotificationIcon = (type: string) => {
    switch (type) {
        case 'danger':
            return '🚨';
        case 'warning':
            return '⚠️';
        case 'success':
            return '✅';
        case 'info':
        default:
            return 'ℹ️';
    }
};

const getNotificationColor = (type: string) => {
    switch (type) {
        case 'danger':
            return 'border-red-200 bg-red-50';
        case 'warning':
            return 'border-yellow-200 bg-yellow-50';
        case 'success':
            return 'border-green-200 bg-green-50';
        case 'info':
        default:
            return 'border-blue-200 bg-blue-50';
    }
};

const formatTime = (dateString: string) => {
    const date = new Date(dateString);
    const now = new Date();
    const diff = now.getTime() - date.getTime();
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(diff / 3600000);
    const days = Math.floor(diff / 86400000);

    if (minutes < 1) return 'Just now';
    if (minutes < 60) return `${minutes}m ago`;
    if (hours < 24) return `${hours}h ago`;
    if (days < 7) return `${days}d ago`;
    return date.toLocaleDateString();
};

onMounted(() => {
    loadNotifications();

    // Poll for new notifications every 30 seconds
    setInterval(loadNotifications, 30000);
});
</script>

<template>
    <div class="relative">
        <!-- Notification Bell Button -->
        <button
            @click="toggleDropdown"
            class="relative p-2 text-white/80 transition-all duration-200 rounded-lg hover:text-white hover:bg-white/10 hover:shadow-lg backdrop-blur-sm"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>

            <!-- Unread Badge -->
            <span
                v-if="hasUnread"
                class="absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full animate-pulse"
            >
                {{ unreadCount > 99 ? '99+' : unreadCount }}
            </span>
        </button>

        <!-- Notification Dropdown -->
        <div
            v-show="isOpen"
            class="absolute right-0 z-50 w-80 mt-2 bg-white border border-gray-200 rounded-lg shadow-xl top-full"
            @click.stop
        >
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
                <div class="flex items-center space-x-2">
                    <button
                        v-if="hasUnread"
                        @click="markAllAsRead"
                        class="text-xs text-blue-600 hover:text-blue-800 font-medium"
                    >
                        Mark all read
                    </button>
                    <button
                        @click="isOpen = false"
                        class="p-1 text-gray-400 hover:text-gray-600 rounded"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Notifications List -->
            <div class="max-h-96 overflow-y-auto">
                <div v-if="loading" class="p-4 text-center text-gray-500">
                    <svg class="w-6 h-6 mx-auto animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <p class="mt-2 text-sm">Loading notifications...</p>
                </div>

                <div v-else-if="notifications.length === 0" class="p-6 text-center text-gray-500">
                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <p class="text-sm">No notifications yet</p>
                </div>

                <div v-else class="divide-y divide-gray-100">
                    <div
                        v-for="notification in notifications"
                        :key="notification.id"
                        :class="[
                            'p-4 transition-colors duration-150 cursor-pointer hover:bg-gray-50',
                            !notification.is_read ? 'bg-blue-50' : '',
                            getNotificationColor(notification.type)
                        ]"
                        @click="handleNotificationClick(notification)"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm">{{ getNotificationIcon(notification.type) }}</span>
                                    <p
                                        :class="[
                                            'text-sm font-medium',
                                            !notification.is_read ? 'text-gray-900' : 'text-gray-700'
                                        ]"
                                    >
                                        {{ notification.title }}
                                    </p>
                                    <div
                                        v-if="!notification.is_read"
                                        class="w-2 h-2 bg-blue-500 rounded-full"
                                    ></div>
                                </div>
                                <p class="mt-1 text-xs text-gray-600 line-clamp-2">
                                    {{ notification.message }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ formatTime(notification.created_at) }}
                                </p>
                            </div>
                            <button
                                @click.stop="deleteNotification(notification.id)"
                                class="ml-2 p-1 text-gray-400 hover:text-red-600 rounded"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Backdrop -->
        <div
            v-if="isOpen"
            @click="isOpen = false"
            class="fixed inset-0 z-40"
        ></div>
    </div>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
