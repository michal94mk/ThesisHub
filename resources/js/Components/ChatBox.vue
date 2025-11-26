<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';

const page = usePage();

const props = defineProps({
    thesisId: {
        type: Number,
        required: true,
    },
});

const messages = ref([]);
const newMessage = ref('');
const loading = ref(false);
const unreadCount = ref(0);
const messagesContainer = ref(null);
let pollingInterval = null;

const fetchMessages = async () => {
    try {
        const response = await axios.get(route('messages.index', props.thesisId));
        messages.value = response.data.messages;
        unreadCount.value = response.data.unread_count;
        
        // Scroll to bottom after messages load
        await nextTick();
        scrollToBottom();
    } catch (error) {
        console.error('Failed to fetch messages:', error);
    }
};

const sendMessage = async () => {
    if (!newMessage.value.trim() || loading.value) return;

    loading.value = true;

    try {
        await axios.post(route('messages.store', props.thesisId), {
            message: newMessage.value,
        });

        newMessage.value = '';
        await fetchMessages();
    } catch (error) {
        console.error('Failed to send message:', error);
        alert('Failed to send message. Please try again.');
    } finally {
        loading.value = false;
    }
};

const scrollToBottom = () => {
    if (messagesContainer.value) {
        messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
    }
};

const formatTime = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);

    if (diffInSeconds < 60) return 'Just now';
    if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`;
    if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`;
    
    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};

const isOwnMessage = (message) => {
    return message.user.id === page.props.auth.user.id;
};

const getRoleBadgeColor = (role) => {
    const colors = {
        'student': 'bg-blue-100 text-blue-800',
        'supervisor': 'bg-green-100 text-green-800',
        'admin': 'bg-purple-100 text-purple-800',
    };
    return colors[role] || 'bg-gray-100 text-gray-800';
};

const getRoleLabel = (role) => {
    const labels = {
        'student': 'Student',
        'supervisor': 'Supervisor',
        'admin': 'Admin',
    };
    return labels[role] || role;
};

// Initial fetch
onMounted(() => {
    fetchMessages();
    
    // Poll for new messages every 5 seconds
    pollingInterval = setInterval(fetchMessages, 5000);
});

// Cleanup
onUnmounted(() => {
    if (pollingInterval) {
        clearInterval(pollingInterval);
    }
});
</script>

<template>
    <div class="flex flex-col h-[600px] bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Chat Header -->
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <h3 class="text-sm font-medium text-gray-900">Chat</h3>
            </div>
            <span class="text-xs text-gray-500">
                {{ messages.length }} message{{ messages.length !== 1 ? 's' : '' }}
            </span>
        </div>

        <!-- Messages Container -->
        <div ref="messagesContainer" class="flex-1 overflow-y-auto p-4 space-y-4">
            <!-- Empty State -->
            <div v-if="messages.length === 0" class="flex flex-col items-center justify-center h-full text-center">
                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <p class="text-gray-500 text-sm">No messages yet</p>
                <p class="text-gray-400 text-xs mt-1">Start the conversation!</p>
            </div>

            <!-- Messages -->
            <div
                v-for="message in messages"
                :key="message.id"
                :class="[
                    'flex',
                    isOwnMessage(message) ? 'justify-end' : 'justify-start'
                ]"
            >
                <div :class="[
                    'max-w-[70%] rounded-lg px-4 py-2',
                    isOwnMessage(message)
                        ? 'bg-indigo-600 text-white'
                        : 'bg-gray-100 text-gray-900'
                ]">
                    <!-- Sender Info (only for other's messages) -->
                    <div v-if="!isOwnMessage(message)" class="flex items-center space-x-2 mb-1">
                        <span class="text-xs font-medium">{{ message.user.name }}</span>
                        <span :class="[
                            'text-xs px-2 py-0.5 rounded-full',
                            getRoleBadgeColor(message.user.role)
                        ]">
                            {{ getRoleLabel(message.user.role) }}
                        </span>
                    </div>

                    <!-- Message Content -->
                    <p class="text-sm whitespace-pre-wrap break-words">{{ message.message }}</p>

                    <!-- Timestamp -->
                    <p :class="[
                        'text-xs mt-1',
                        isOwnMessage(message) ? 'text-indigo-100' : 'text-gray-500'
                    ]">
                        {{ formatTime(message.created_at) }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Message Input -->
        <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
            <form @submit.prevent="sendMessage" class="flex space-x-2">
                <div class="flex-1">
                    <textarea
                        v-model="newMessage"
                        rows="2"
                        placeholder="Type your message..."
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm resize-none"
                        :disabled="loading"
                        @keydown.enter.exact.prevent="sendMessage"
                        @keydown.enter.shift.exact="newMessage += '\n'"
                    ></textarea>
                    <p class="text-xs text-gray-500 mt-1">
                        Press Enter to send, Shift+Enter for new line
                    </p>
                </div>
                <div class="flex items-start">
                    <PrimaryButton
                        type="submit"
                        :disabled="!newMessage.trim() || loading"
                        class="h-full"
                    >
                        <span v-if="loading">Sending...</span>
                        <span v-else>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </span>
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </div>
</template>

