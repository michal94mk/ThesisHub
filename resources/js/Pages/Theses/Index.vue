<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';

defineProps({
    theses: {
        type: Object,
        required: true,
    },
});

const getStatusColor = (status) => {
    const colors = {
        'draft': 'bg-gray-100 text-gray-800',
        'pending_approval': 'bg-yellow-100 text-yellow-800',
        'approved': 'bg-green-100 text-green-800',
        'under_review': 'bg-blue-100 text-blue-800',
        'returned_for_corrections': 'bg-orange-100 text-orange-800',
        'accepted': 'bg-emerald-100 text-emerald-800',
        'rejected': 'bg-red-100 text-red-800',
        'submitted_for_defense': 'bg-purple-100 text-purple-800',
        'defended': 'bg-indigo-100 text-indigo-800',
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

const getTypeLabel = (type) => {
    return type === 'bachelor' ? 'Bachelor' : 'Master';
};
</script>

<template>
    <Head title="My Theses" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    My Theses
                </h2>
                <Link
                    v-if="$page.props.auth.user.role === 'student'"
                    :href="route('theses.create')"
                >
                    <PrimaryButton>
                        Submit New Thesis
                    </PrimaryButton>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Empty State -->
                <div
                    v-if="theses.data.length === 0"
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-12 text-center">
                        <svg
                            class="mx-auto h-12 w-12 text-gray-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                            />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No theses</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Get started by submitting a new thesis.
                        </p>
                        <div class="mt-6" v-if="$page.props.auth.user.role === 'student'">
                            <Link :href="route('theses.create')">
                                <PrimaryButton>
                                    Submit New Thesis
                                </PrimaryButton>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Theses List -->
                <div
                    v-else
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                        >
                                            Title
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                        >
                                            Type
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                        >
                                            Student
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                        >
                                            Supervisor
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                        >
                                            Status
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                        >
                                            Submitted
                                        </th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    <tr
                                        v-for="thesis in theses.data"
                                        :key="thesis.id"
                                        class="hover:bg-gray-50"
                                    >
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ thesis.title }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ thesis.field_of_study }}
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="text-sm text-gray-900">
                                                {{ getTypeLabel(thesis.type) }}
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="text-sm text-gray-900">
                                                {{ thesis.student?.name }}
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <div class="text-sm text-gray-900">
                                                {{ thesis.supervisor?.name }}
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <span
                                                :class="getStatusColor(thesis.status)"
                                                class="inline-flex rounded-full px-2 text-xs font-semibold leading-5"
                                            >
                                                {{ getStatusLabel(thesis.status) }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                            {{ thesis.submitted_at ? new Date(thesis.submitted_at).toLocaleDateString() : '-' }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                            <Link
                                                :href="route('theses.show', thesis.id)"
                                                class="text-indigo-600 hover:text-indigo-900"
                                            >
                                                View
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div
                            v-if="theses.links.length > 3"
                            class="mt-6 flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6"
                        >
                            <div class="flex flex-1 justify-between sm:hidden">
                                <Link
                                    v-if="theses.prev_page_url"
                                    :href="theses.prev_page_url"
                                    class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                >
                                    Previous
                                </Link>
                                <Link
                                    v-if="theses.next_page_url"
                                    :href="theses.next_page_url"
                                    class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                >
                                    Next
                                </Link>
                            </div>
                            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        Showing
                                        <span class="font-medium">{{ theses.from }}</span>
                                        to
                                        <span class="font-medium">{{ theses.to }}</span>
                                        of
                                        <span class="font-medium">{{ theses.total }}</span>
                                        results
                                    </p>
                                </div>
                                <div>
                                    <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                                        <Link
                                            v-for="(link, index) in theses.links"
                                            :key="index"
                                            :href="link.url"
                                            v-html="link.label"
                                            :class="[
                                                link.active
                                                    ? 'z-10 bg-indigo-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600'
                                                    : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:outline-offset-0',
                                                'relative inline-flex items-center px-4 py-2 text-sm font-semibold',
                                            ]"
                                        />
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

