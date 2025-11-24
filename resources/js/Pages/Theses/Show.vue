<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    thesis: {
        type: Object,
        required: true,
    },
});

const showRejectModal = ref(false);
const showReturnModal = ref(false);

const rejectForm = useForm({
    notes: '',
});

const returnForm = useForm({
    notes: '',
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

const submitForApproval = () => {
    router.post(route('theses.submit', props.thesis.id));
};

const approveThesis = () => {
    router.post(route('theses.approve', props.thesis.id));
};

const rejectThesis = () => {
    rejectForm.post(route('theses.reject', props.thesis.id), {
        preserveScroll: true,
        onSuccess: () => {
            showRejectModal.value = false;
            rejectForm.reset();
        },
    });
};

const returnForCorrections = () => {
    returnForm.post(route('theses.returnForCorrections', props.thesis.id), {
        preserveScroll: true,
        onSuccess: () => {
            showReturnModal.value = false;
            returnForm.reset();
        },
    });
};

const deleteThesis = () => {
    if (confirm('Are you sure you want to delete this thesis? This action cannot be undone.')) {
        router.delete(route('theses.destroy', props.thesis.id));
    }
};

const canEdit = (user) => {
    if (user.role === 'admin') return true;
    if (user.role === 'student' && props.thesis.student_id === user.id) {
        return ['draft', 'returned_for_corrections'].includes(props.thesis.status);
    }
    return false;
};

const canSubmit = (user) => {
    return user.role === 'student' && 
           props.thesis.student_id === user.id && 
           props.thesis.status === 'draft';
};

const canApprove = (user) => {
    return user.role === 'supervisor' && 
           props.thesis.supervisor_id === user.id && 
           props.thesis.status === 'pending_approval';
};

const canDelete = (user) => {
    if (user.role === 'admin') return true;
    return user.role === 'student' && 
           props.thesis.student_id === user.id && 
           props.thesis.status === 'draft';
};
</script>

<template>
    <Head :title="thesis.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Thesis Details
                </h2>
                <Link
                    :href="route('theses.index')"
                    class="text-sm text-gray-600 hover:text-gray-900"
                >
                    ← Back to List
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                <!-- Main Info Card -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900">{{ thesis.title }}</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ thesis.type === 'bachelor' ? 'Bachelor' : 'Master' }} Thesis
                                </p>
                            </div>
                            <span
                                :class="getStatusColor(thesis.status)"
                                class="inline-flex rounded-full px-3 py-1 text-sm font-semibold"
                            >
                                {{ getStatusLabel(thesis.status) }}
                            </span>
                        </div>

                        <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Student</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ thesis.student?.name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Supervisor</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ thesis.supervisor?.name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Field of Study</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ thesis.field_of_study || '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Specialization</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ thesis.specialization || '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Academic Year</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ thesis.academic_year || '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Submitted At</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ thesis.submitted_at ? new Date(thesis.submitted_at).toLocaleString() : '-' }}
                                </dd>
                            </div>
                            <div v-if="thesis.approved_at">
                                <dt class="text-sm font-medium text-gray-500">Approved At</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ new Date(thesis.approved_at).toLocaleString() }}
                                </dd>
                            </div>
                            <div v-if="thesis.defense_date">
                                <dt class="text-sm font-medium text-gray-500">Defense Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ new Date(thesis.defense_date).toLocaleDateString() }}
                                </dd>
                            </div>
                        </dl>

                        <!-- Keywords -->
                        <div v-if="thesis.keywords && thesis.keywords.length > 0" class="mt-6">
                            <dt class="text-sm font-medium text-gray-500 mb-2">Keywords</dt>
                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-for="(keyword, index) in thesis.keywords"
                                    :key="index"
                                    class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-1 text-sm font-medium text-indigo-800"
                                >
                                    {{ keyword }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Abstract -->
                <div v-if="thesis.abstract" class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-3">Abstract</h4>
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ thesis.abstract }}</p>
                    </div>
                </div>

                <!-- Outline -->
                <div v-if="thesis.outline" class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-3">Thesis Outline</h4>
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ thesis.outline }}</p>
                    </div>
                </div>

                <!-- Supervisor Notes -->
                <div v-if="thesis.supervisor_notes" class="overflow-hidden bg-orange-50 shadow-sm sm:rounded-lg border border-orange-200">
                    <div class="p-6">
                        <h4 class="text-lg font-medium text-orange-900 mb-3">Supervisor Notes</h4>
                        <p class="text-sm text-orange-800 whitespace-pre-wrap">{{ thesis.supervisor_notes }}</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex flex-wrap gap-3">
                            <!-- Student Actions -->
                            <PrimaryButton
                                v-if="canSubmit($page.props.auth.user)"
                                @click="submitForApproval"
                            >
                                Submit for Approval
                            </PrimaryButton>

                            <Link
                                v-if="canEdit($page.props.auth.user)"
                                :href="route('theses.edit', thesis.id)"
                            >
                                <SecondaryButton>
                                    Edit Thesis
                                </SecondaryButton>
                            </Link>

                            <!-- Supervisor Actions -->
                            <PrimaryButton
                                v-if="canApprove($page.props.auth.user)"
                                @click="approveThesis"
                                class="bg-green-600 hover:bg-green-700"
                            >
                                Approve Thesis
                            </PrimaryButton>

                            <SecondaryButton
                                v-if="canApprove($page.props.auth.user)"
                                @click="showReturnModal = true"
                            >
                                Return for Corrections
                            </SecondaryButton>

                            <DangerButton
                                v-if="canApprove($page.props.auth.user)"
                                @click="showRejectModal = true"
                            >
                                Reject Thesis
                            </DangerButton>

                            <!-- Delete -->
                            <DangerButton
                                v-if="canDelete($page.props.auth.user)"
                                @click="deleteThesis"
                                class="ml-auto"
                            >
                                Delete Thesis
                            </DangerButton>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <Modal :show="showRejectModal" @close="showRejectModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Reject Thesis
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    Please provide a reason for rejecting this thesis.
                </p>

                <div class="mt-6">
                    <InputLabel for="reject_notes" value="Rejection Reason" />
                    <textarea
                        id="reject_notes"
                        v-model="rejectForm.notes"
                        rows="4"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Explain why this thesis is being rejected..."
                        required
                    ></textarea>
                    <InputError :message="rejectForm.errors.notes" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton @click="showRejectModal = false">
                        Cancel
                    </SecondaryButton>
                    <DangerButton
                        @click="rejectThesis"
                        :disabled="rejectForm.processing || !rejectForm.notes"
                    >
                        Reject Thesis
                    </DangerButton>
                </div>
            </div>
        </Modal>

        <!-- Return for Corrections Modal -->
        <Modal :show="showReturnModal" @close="showReturnModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Return for Corrections
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    Please provide feedback for the student on what needs to be corrected.
                </p>

                <div class="mt-6">
                    <InputLabel for="return_notes" value="Correction Notes" />
                    <textarea
                        id="return_notes"
                        v-model="returnForm.notes"
                        rows="4"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Explain what needs to be corrected..."
                        required
                    ></textarea>
                    <InputError :message="returnForm.errors.notes" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton @click="showReturnModal = false">
                        Cancel
                    </SecondaryButton>
                    <PrimaryButton
                        @click="returnForCorrections"
                        :disabled="returnForm.processing || !returnForm.notes"
                    >
                        Return for Corrections
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

