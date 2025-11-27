<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';

const page = usePage();
const showDropdown = ref(false);
const notifications = ref([]);
const unreadCount = ref(0);
let pollingInterval = null;

const fetchNotifications = async () => {
    try {
        const response = await axios.get(route('notifications.index'));
        notifications.value = response.data.notifications;
    } catch (error) {
        console.error('Failed to fetch notifications:', error);
    }
};

const fetchUnreadCount = async () => {
    try {
        const response = await axios.get(route('notifications.unreadCount'));
        unreadCount.value = response.data.count;
    } catch (error) {
        console.error('Failed to fetch unread count:', error);
    }
};

const toggleDropdown = () => {
    showDropdown.value = !showDropdown.value;
    if (showDropdown.value) {
        fetchNotifications();
    }
};

const markAsRead = async (notificationId) => {
    try {
        await axios.post(route('notifications.markAsRead', notificationId));
        await fetchUnreadCount();
        await fetchNotifications();
    } catch (error) {
        console.error('Failed to mark as read:', error);
    }
};

const markAllAsRead = async () => {
    try {
        await axios.post(route('notifications.markAllAsRead'));
        await fetchUnreadCount();
        await fetchNotifications();
    } catch (error) {
        console.error('Failed to mark all as read:', error);
    }
};

const handleNotificationClick = (notification) => {
    markAsRead(notification.id);
    if (notification.action_url) {
        router.visit(notification.action_url);
    }
    showDropdown.value = false;
};

const formatTime = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);

    if (diffInSeconds < 60) return 'Just now';
    if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`;
    if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`;
    if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)}d ago`;
    
    return date.toLocaleDateString();
};

// Initial fetch
onMounted(() => {
    fetchUnreadCount();
    
    // Poll for new notifications every 30 seconds
    pollingInterval = setInterval(fetchUnreadCount, 30000);
});

// Cleanup
onUnmounted(() => {
    if (pollingInterval) {
        clearInterval(pollingInterval);
    }
});

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
    if (!event.target.closest('.notification-dropdown')) {
        showDropdown.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <div class="relative notification-dropdown">
        <!-- Bell Icon -->
        <button
            @click="toggleDropdown"
            class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 rounded-md transition"
        >
            <svg
                class="h-6 w-6"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                />
            </svg>

            <!-- Badge -->
            <span
                v-if="unreadCount > 0"
                class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full"
            >
                {{ unreadCount > 9 ? '9+' : unreadCount }}
            </span>
        </button>

        <!-- Dropdown -->
        <div
            v-show="showDropdown"
            class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 z-50 max-h-[600px] overflow-hidden flex flex-col"
        >
            <!-- Header -->
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                <button
                    v-if="unreadCount > 0"
                    @click="markAllAsRead"
                    class="text-xs text-indigo-600 hover:text-indigo-900 font-medium"
                >
                    Mark all as read
                </button>
            </div>

            <!-- Notifications List -->
            <div class="overflow-y-auto max-h-[500px]">
                <!-- Empty State -->
                <div
                    v-if="notifications.length === 0"
                    class="px-4 py-12 text-center"
                >
                    <svg
                        class="mx-auto h-12 w-12 text-gray-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                        />
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">No notifications</p>
                </div>

                <!-- Notifications -->
                <div v-else class="divide-y divide-gray-200">
                    <button
                        v-for="notification in notifications"
                        :key="notification.id"
                        @click="handleNotificationClick(notification)"
                        :class="[
                            'w-full text-left px-4 py-3 hover:bg-gray-50 transition',
                            notification.read_at ? 'bg-white' : 'bg-blue-50'
                        ]"
                    >
                        <div class="flex items-start space-x-3">
                            <!-- Icon -->
                            <div class="flex-shrink-0 text-2xl">
                                {{ notification.icon || '🔔' }}
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <p
                                    :class="[
                                        'text-sm font-medium',
                                        notification.read_at ? 'text-gray-900' : 'text-gray-900 font-semibold'
                                    ]"
                                >
                                    {{ notification.title }}
                                </p>
                                <p
                                    v-if="notification.message"
                                    class="mt-1 text-sm text-gray-600 truncate"
                                >
                                    {{ notification.message }}
                                </p>
                                <p class="mt-1 text-xs text-gray-400">
                                    {{ formatTime(notification.created_at) }}
                                </p>
                            </div>

                            <!-- Unread Indicator -->
                            <div
                                v-if="!notification.read_at"
                                class="flex-shrink-0"
                            >
                                <div class="h-2 w-2 bg-blue-600 rounded-full"></div>
                            </div>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

