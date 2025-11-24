<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    thesis: {
        type: Object,
        required: true,
    },
    supervisors: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    title: props.thesis.title,
    type: props.thesis.type,
    supervisor_id: props.thesis.supervisor_id,
    field_of_study: props.thesis.field_of_study || '',
    specialization: props.thesis.specialization || '',
    abstract: props.thesis.abstract || '',
    outline: props.thesis.outline || '',
    keywords: props.thesis.keywords || [],
    academic_year: props.thesis.academic_year || '',
});

const keywordInput = ref('');

const addKeyword = () => {
    if (keywordInput.value.trim() && !form.keywords.includes(keywordInput.value.trim())) {
        form.keywords.push(keywordInput.value.trim());
        keywordInput.value = '';
    }
};

const removeKeyword = (index) => {
    form.keywords.splice(index, 1);
};

const submit = () => {
    form.put(route('theses.update', props.thesis.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Edit Thesis" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Edit Thesis
                </h2>
                <Link
                    :href="route('theses.show', thesis.id)"
                    class="text-sm text-gray-600 hover:text-gray-900"
                >
                    ← Back to Thesis
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Title -->
                            <div>
                                <InputLabel for="title" value="Thesis Title *" />
                                <TextInput
                                    id="title"
                                    v-model="form.title"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                    autofocus
                                />
                                <InputError :message="form.errors.title" class="mt-2" />
                            </div>

                            <!-- Type -->
                            <div>
                                <InputLabel for="type" value="Thesis Type *" />
                                <select
                                    id="type"
                                    v-model="form.type"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                >
                                    <option value="bachelor">Bachelor</option>
                                    <option value="master">Master</option>
                                </select>
                                <InputError :message="form.errors.type" class="mt-2" />
                            </div>

                            <!-- Supervisor -->
                            <div>
                                <InputLabel for="supervisor_id" value="Supervisor *" />
                                <select
                                    id="supervisor_id"
                                    v-model="form.supervisor_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                >
                                    <option value="">Select a supervisor</option>
                                    <option
                                        v-for="supervisor in supervisors"
                                        :key="supervisor.id"
                                        :value="supervisor.id"
                                    >
                                        {{ supervisor.name }} ({{ supervisor.email }})
                                    </option>
                                </select>
                                <InputError :message="form.errors.supervisor_id" class="mt-2" />
                            </div>

                            <!-- Field of Study -->
                            <div>
                                <InputLabel for="field_of_study" value="Field of Study" />
                                <TextInput
                                    id="field_of_study"
                                    v-model="form.field_of_study"
                                    type="text"
                                    class="mt-1 block w-full"
                                />
                                <InputError :message="form.errors.field_of_study" class="mt-2" />
                            </div>

                            <!-- Specialization -->
                            <div>
                                <InputLabel for="specialization" value="Specialization" />
                                <TextInput
                                    id="specialization"
                                    v-model="form.specialization"
                                    type="text"
                                    class="mt-1 block w-full"
                                />
                                <InputError :message="form.errors.specialization" class="mt-2" />
                            </div>

                            <!-- Academic Year -->
                            <div>
                                <InputLabel for="academic_year" value="Academic Year" />
                                <TextInput
                                    id="academic_year"
                                    v-model="form.academic_year"
                                    type="text"
                                    class="mt-1 block w-full"
                                />
                                <InputError :message="form.errors.academic_year" class="mt-2" />
                            </div>

                            <!-- Abstract -->
                            <div>
                                <InputLabel for="abstract" value="Abstract" />
                                <textarea
                                    id="abstract"
                                    v-model="form.abstract"
                                    rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                ></textarea>
                                <InputError :message="form.errors.abstract" class="mt-2" />
                            </div>

                            <!-- Outline -->
                            <div>
                                <InputLabel for="outline" value="Thesis Outline" />
                                <textarea
                                    id="outline"
                                    v-model="form.outline"
                                    rows="6"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                ></textarea>
                                <InputError :message="form.errors.outline" class="mt-2" />
                            </div>

                            <!-- Keywords -->
                            <div>
                                <InputLabel for="keywords" value="Keywords" />
                                <div class="mt-1 space-y-2">
                                    <div class="flex gap-2">
                                        <TextInput
                                            id="keywords"
                                            v-model="keywordInput"
                                            type="text"
                                            class="block w-full"
                                            placeholder="Add a keyword and press Enter"
                                            @keydown.enter.prevent="addKeyword"
                                        />
                                        <button
                                            type="button"
                                            @click="addKeyword"
                                            class="rounded-md bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-300"
                                        >
                                            Add
                                        </button>
                                    </div>
                                    <div v-if="form.keywords.length > 0" class="flex flex-wrap gap-2">
                                        <span
                                            v-for="(keyword, index) in form.keywords"
                                            :key="index"
                                            class="inline-flex items-center gap-1 rounded-full bg-indigo-100 px-3 py-1 text-sm font-medium text-indigo-800"
                                        >
                                            {{ keyword }}
                                            <button
                                                type="button"
                                                @click="removeKeyword(index)"
                                                class="hover:text-indigo-600"
                                            >
                                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                                <InputError :message="form.errors.keywords" class="mt-2" />
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center justify-end gap-4">
                                <Link
                                    :href="route('theses.show', thesis.id)"
                                    class="text-sm text-gray-600 hover:text-gray-900"
                                >
                                    Cancel
                                </Link>
                                <PrimaryButton :disabled="form.processing">
                                    Update Thesis
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

