<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import SlideOver from '@/Components/SlideOver.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    contact: { type: Object, required: true },
    items: { type: Array, default: () => [] },
    can: { type: Object, default: () => ({}) },
});

// mode: null | 'create' | 'show' | 'edit' | 'delete'
const mode = ref(null);
const selected = ref(null);
const isOpen = computed(() => mode.value !== null);

const slideTitle = computed(() => {
    switch (mode.value) {
        case 'create': return 'Add phone number';
        case 'show': return selected.value?.name;
        case 'edit': return `Edit ${selected.value?.name}`;
        case 'delete': return `Delete ${selected.value?.name}?`;
        default: return '';
    }
});

const close = () => {
    mode.value = null;
    selected.value = null;
    createForm.reset();
    createForm.clearErrors();
    editForm.reset();
    editForm.clearErrors();
};

const refreshItems = () => router.reload({ only: ['items'] });

const openCreate = () => { mode.value = 'create'; };
const openShow = (item) => { selected.value = item; mode.value = 'show'; };
const openEdit = (item) => {
    selected.value = item;
    editForm.name = item.name;
    editForm.number = item.number;
    mode.value = 'edit';
};
const openDelete = (item) => { selected.value = item; mode.value = 'delete'; };

const createForm = useForm({ name: '', number: '' });
const submitCreate = () =>
    createForm.post(route('contact_numbers.store', props.contact.ulid), {
        preserveScroll: true,
        onSuccess: () => { close(); refreshItems(); },
    });

const editForm = useForm({ name: '', number: '' });
const submitEdit = () =>
    editForm.put(route('contact_numbers.update', [props.contact.ulid, selected.value.ulid]), {
        preserveScroll: true,
        onSuccess: () => { close(); refreshItems(); },
    });

const deleteForm = useForm({});
const submitDelete = () =>
    deleteForm.delete(route('contact_numbers.destroy', [props.contact.ulid, selected.value.ulid]), {
        preserveScroll: true,
        onSuccess: () => { close(); refreshItems(); },
    });
</script>

<template>
    <AppLayout :title="`${contact.fullname} — Phone numbers`">
        <Head :title="`${contact.fullname} — Phone numbers`" />

        <div class="text-sm">
            <Link :href="route('contacts.show', contact.ulid)" class="text-indigo-600 hover:text-indigo-500">
                &larr; Back to {{ contact.fullname }}
            </Link>
        </div>

        <div class="bg-white shadow rounded-lg">
            <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">Phone numbers</h2>
                <PrimaryButton v-if="can.create" type="button" @click="openCreate">
                    Add phone number
                </PrimaryButton>
            </div>

            <div v-if="items.length === 0" class="px-6 py-8 text-center text-sm text-gray-500">
                No phone numbers yet.
            </div>

            <ul v-else class="divide-y divide-gray-200">
                <li v-for="item in items" :key="item.id">
                    <button
                        type="button"
                        class="block w-full text-left px-6 py-3 hover:bg-gray-50"
                        @click="openShow(item)"
                    >
                        <div class="text-sm font-medium text-gray-900">{{ item.name }}</div>
                        <div class="text-sm text-gray-600">{{ item.number }}</div>
                    </button>
                </li>
            </ul>
        </div>

        <SlideOver :open="isOpen" :title="slideTitle" @close="close">
            <!-- Show -->
            <template v-if="mode === 'show'">
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="font-medium text-gray-700">Name</dt>
                        <dd class="text-gray-900">{{ selected.name }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-700">Number</dt>
                        <dd class="text-gray-900">
                            <a :href="`tel:${selected.number}`" class="text-indigo-600 hover:text-indigo-500">
                                {{ selected.number }}
                            </a>
                        </dd>
                    </div>
                </dl>
            </template>

            <!-- Create -->
            <form v-else-if="mode === 'create'" id="number-create-form" @submit.prevent="submitCreate" class="space-y-4">
                <div>
                    <InputLabel for="create-name" value="Name *" />
                    <TextInput id="create-name" v-model="createForm.name" autofocus required />
                    <InputError :message="createForm.errors.name" />
                </div>
                <div>
                    <InputLabel for="create-number" value="Phone number *" />
                    <TextInput id="create-number" type="tel" v-model="createForm.number" required />
                    <InputError :message="createForm.errors.number" />
                </div>
            </form>

            <!-- Edit -->
            <form v-else-if="mode === 'edit'" id="number-edit-form" @submit.prevent="submitEdit" class="space-y-4">
                <div>
                    <InputLabel for="edit-name" value="Name *" />
                    <TextInput id="edit-name" v-model="editForm.name" autofocus required />
                    <InputError :message="editForm.errors.name" />
                </div>
                <div>
                    <InputLabel for="edit-number" value="Phone number *" />
                    <TextInput id="edit-number" type="tel" v-model="editForm.number" required />
                    <InputError :message="editForm.errors.number" />
                </div>
            </form>

            <!-- Delete -->
            <template v-else-if="mode === 'delete'">
                <p class="text-sm text-gray-700">
                    Permanently remove <strong>{{ selected.name }}</strong> ({{ selected.number }})?
                </p>
            </template>

            <template #footer>
                <template v-if="mode === 'show'">
                    <SecondaryButton type="button" @click="close">Close</SecondaryButton>
                    <DangerButton v-if="can.delete" type="button" @click="openDelete(selected)">
                        Delete
                    </DangerButton>
                    <PrimaryButton v-if="can.edit" type="button" @click="openEdit(selected)">
                        Edit
                    </PrimaryButton>
                </template>
                <template v-else-if="mode === 'create'">
                    <SecondaryButton type="button" @click="close">Cancel</SecondaryButton>
                    <PrimaryButton
                        type="submit"
                        form="number-create-form"
                        :disabled="createForm.processing"
                        :class="{ 'opacity-50': createForm.processing }"
                    >
                        Create
                    </PrimaryButton>
                </template>
                <template v-else-if="mode === 'edit'">
                    <SecondaryButton type="button" @click="close">Cancel</SecondaryButton>
                    <PrimaryButton
                        type="submit"
                        form="number-edit-form"
                        :disabled="editForm.processing"
                        :class="{ 'opacity-50': editForm.processing }"
                    >
                        Save
                    </PrimaryButton>
                </template>
                <template v-else-if="mode === 'delete'">
                    <SecondaryButton type="button" @click="openShow(selected)">Cancel</SecondaryButton>
                    <DangerButton
                        type="button"
                        :disabled="deleteForm.processing"
                        :class="{ 'opacity-50': deleteForm.processing }"
                        @click="submitDelete"
                    >
                        Delete
                    </DangerButton>
                </template>
            </template>
        </SlideOver>
    </AppLayout>
</template>
