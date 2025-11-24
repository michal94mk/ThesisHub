<script setup>
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';

defineProps({
    stats: {
        type: Object,
        default: () => ({}),
    },
    recentTheses: {
        type: Array,
        default: () => [],
    },
});

const user = computed(() => window.Laravel.page.props.auth.user);

const getStatusColor = (status) => {
    const colors = {
        'draft': 'bg-gray-100 text-gray-800',
        'pending_approval': 'bg-yellow-100 text-yellow-800',
        'approved': 'bg-green-100 text-green-800',
        'under_review': 'bg-blue-100 text-blue-800',
        'returned_for_corrections': 'bg-orange-100 text-orange-800',
        'accepted': 'bg-emerald-100 text-emerald-800',
        'rejected': 'bg-red-100 text-red-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const getStatusLabel = (status) => {
    const labels = {
        'draft': 'Draft',
        'pending_approval': 'Pending Approval',
        'approved': 'Approved',
        'under_review': 'Under Review',
        'returned_for_corrections': 'Returned for Corrections',
        'accepted': 'Accepted',
        'rejected': 'Rejected',
        'submitted_for_defense': 'Submitted for Defense',
        'defended': 'Defended',
    };
    return labels[status] || status;
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                <!-- Welcome Message -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-2xl font-bold text-gray-900">
                            Welcome back, {{ $page.props.auth.user.name }}!
                        </h3>
                        <p class="mt-1 text-sm text-gray-600">
                            <span v-if="$page.props.auth.user.role === 'student'">
                                Manage your thesis submissions and communicate with your supervisor.
                            </span>
                            <span v-else-if="$page.props.auth.user.role === 'supervisor'">
                                Review student theses and track their progress.
                            </span>
                            <span v-else-if="$page.props.auth.user.role === 'admin'">
                                Manage the entire thesis system and oversee all submissions.
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h4>
                        <div class="flex flex-wrap gap-3">
                            <Link :href="route('theses.index')">
                                <PrimaryButton>
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    View All Theses
                                </PrimaryButton>
                            </Link>
                            
                            <Link v-if="$page.props.auth.user.role === 'student'" :href="route('theses.create')">
                                <PrimaryButton class="bg-green-600 hover:bg-green-700">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Submit New Thesis
                                </PrimaryButton>
                            </Link>

                            <Link :href="route('profile.edit')">
                                <button class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Edit Profile
                                </button>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards (if stats are provided) -->
                <div v-if="stats && Object.keys(stats).length > 0" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div v-for="(value, key) in stats" :key="key" class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-500 truncate">
                                        {{ key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) }}
                                    </p>
                                    <p class="mt-1 text-3xl font-semibold text-gray-900">
                                        {{ value }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Theses -->
                <div v-if="recentTheses && recentTheses.length > 0" class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-medium text-gray-900">Recent Theses</h4>
                            <Link :href="route('theses.index')" class="text-sm text-indigo-600 hover:text-indigo-900">
                                View All →
                            </Link>
                        </div>
                        <div class="space-y-3">
                            <div
                                v-for="thesis in recentTheses"
                                :key="thesis.id"
                                class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition"
                            >
                                <div class="flex-1">
                                    <Link :href="route('theses.show', thesis.id)" class="text-sm font-medium text-gray-900 hover:text-indigo-600">
                                        {{ thesis.title }}
                                    </Link>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ thesis.student?.name }} • {{ thesis.type === 'bachelor' ? 'Bachelor' : 'Master' }}
                                    </p>
                                </div>
                                <span
                                    :class="getStatusColor(thesis.status)"
                                    class="inline-flex rounded-full px-2 py-1 text-xs font-semibold"
                                >
                                    {{ getStatusLabel(thesis.status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="(!recentTheses || recentTheses.length === 0) && $page.props.auth.user.role === 'student'" class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No theses yet</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Get started by submitting your first thesis.
                        </p>
                        <div class="mt-6">
                            <Link :href="route('theses.create')">
                                <PrimaryButton>
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Submit New Thesis
                                </PrimaryButton>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
