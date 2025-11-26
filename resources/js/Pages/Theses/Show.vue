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
import ChatBox from '@/Components/ChatBox.vue';

const props = defineProps({
    thesis: {
        type: Object,
        required: true,
    },
});

const showRejectModal = ref(false);
const showReturnModal = ref(false);
const showUploadModal = ref(false);

const rejectForm = useForm({
    notes: '',
});

const returnForm = useForm({
    notes: '',
});

const uploadForm = useForm({
    file: null,
    description: '',
});

const fileInput = ref(null);

const handleFileSelect = (event) => {
    uploadForm.file = event.target.files[0];
};

const uploadDocument = () => {
    const formData = new FormData();
    formData.append('file', uploadForm.file);
    if (uploadForm.description) {
        formData.append('description', uploadForm.description);
    }

    router.post(route('documents.store', props.thesis.id), formData, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            showUploadModal.value = false;
            uploadForm.reset();
            if (fileInput.value) {
                fileInput.value.value = '';
            }
        },
    });
};

const deleteDocument = (documentId) => {
    if (confirm('Are you sure you want to delete this document?')) {
        router.delete(route('documents.destroy', documentId), {
            preserveScroll: true,
        });
    }
};

const formatFileSize = (bytes) => {
    const units = ['B', 'KB', 'MB', 'GB'];
    let size = bytes;
    let unitIndex = 0;
    
    while (size > 1024 && unitIndex < units.length - 1) {
        size /= 1024;
        unitIndex++;
    }
    
    return size.toFixed(2) + ' ' + units[unitIndex];
};

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

                <!-- Documents -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-medium text-gray-900">Documents</h4>
                            <PrimaryButton
                                v-if="canEdit($page.props.auth.user)"
                                @click="showUploadModal = true"
                                class="text-sm"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                Upload Document
                            </PrimaryButton>
                        </div>

                        <!-- Documents List -->
                        <div v-if="thesis.documents && thesis.documents.length > 0" class="space-y-3">
                            <div
                                v-for="document in thesis.documents"
                                :key="document.id"
                                class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition"
                            >
                                <div class="flex items-center space-x-3 flex-1">
                                    <!-- File Icon -->
                                    <div class="flex-shrink-0">
                                        <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    
                                    <!-- File Info -->
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ document.original_name }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ formatFileSize(document.size) }} • 
                                            Uploaded by {{ document.uploader?.name }} • 
                                            {{ new Date(document.created_at).toLocaleString() }}
                                        </p>
                                        <p v-if="document.description" class="text-xs text-gray-600 mt-1">
                                            {{ document.description }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center space-x-2">
                                    <a
                                        :href="route('documents.download', document.id)"
                                        class="inline-flex items-center px-3 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                    >
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        Download
                                    </a>
                                    <button
                                        v-if="canEdit($page.props.auth.user)"
                                        @click="deleteDocument(document.id)"
                                        class="inline-flex items-center px-3 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-else class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No documents</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Upload your first document to get started.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Chat -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Communication</h4>
                        <ChatBox :thesis-id="thesis.id" />
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

        <!-- Upload Document Modal -->
        <Modal :show="showUploadModal" @close="showUploadModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Upload Document
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    Upload a document for this thesis. Accepted formats: PDF, DOC, DOCX, TXT, ZIP, RAR (max 50MB).
                </p>

                <form @submit.prevent="uploadDocument" class="mt-6 space-y-4">
                    <div>
                        <InputLabel for="file" value="Select File *" />
                        <input
                            ref="fileInput"
                            id="file"
                            type="file"
                            @change="handleFileSelect"
                            accept=".pdf,.doc,.docx,.txt,.zip,.rar"
                            required
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                        />
                        <p v-if="uploadForm.file" class="mt-2 text-sm text-gray-600">
                            Selected: {{ uploadForm.file.name }} ({{ formatFileSize(uploadForm.file.size) }})
                        </p>
                        <InputError :message="uploadForm.errors.file" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="description" value="Description (optional)" />
                        <textarea
                            id="description"
                            v-model="uploadForm.description"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Add a brief description of this document..."
                        ></textarea>
                        <InputError :message="uploadForm.errors.description" class="mt-2" />
                    </div>

                    <div class="flex justify-end gap-3">
                        <SecondaryButton type="button" @click="showUploadModal = false">
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton
                            type="submit"
                            :disabled="uploadForm.processing || !uploadForm.file"
                        >
                            <span v-if="uploadForm.processing">Uploading...</span>
                            <span v-else>Upload Document</span>
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

