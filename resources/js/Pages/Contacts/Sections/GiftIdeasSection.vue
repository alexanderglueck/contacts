<script setup>
import { ref, computed, watch } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import SlideOver from '@/Components/SlideOver.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    open: { type: Boolean, required: true },
    contact: { type: Object, required: true },
    items: { type: Array, default: () => [] },
    can: { type: Object, default: () => ({}) },
});

const emit = defineEmits(['close']);

const mode = ref('list');
const selected = ref(null);

const createForm = useForm({ name: '', description: '', url: '', due_at: '' });
const editForm = useForm({ name: '', description: '', url: '', due_at: '' });
const deleteForm = useForm({});

watch(
    () => props.open,
    (isOpen) => {
        if (isOpen) {
            mode.value = 'list';
            selected.value = null;
            createForm.reset();
            createForm.clearErrors();
            editForm.reset();
            editForm.clearErrors();
        }
    },
);

const title = computed(() => {
    switch (mode.value) {
        case 'create': return 'Add gift idea';
        case 'show': return selected.value?.name ?? 'Gift idea';
        case 'edit': return `Edit ${selected.value?.name}`;
        case 'delete': return `Delete ${selected.value?.name}?`;
        default: return 'Gift ideas';
    }
});

const backToList = () => {
    mode.value = 'list';
    selected.value = null;
    createForm.reset();
    createForm.clearErrors();
    editForm.reset();
    editForm.clearErrors();
};

const openCreate = () => {
    selected.value = null;
    createForm.reset();
    createForm.clearErrors();
    mode.value = 'create';
};

const openShow = (item) => {
    selected.value = item;
    mode.value = 'show';
};

const openEdit = (item) => {
    selected.value = item;
    editForm.name = item.name;
    editForm.description = item.description ?? '';
    editForm.url = item.url ?? '';
    editForm.due_at = item.formatted_due_at ?? '';
    editForm.clearErrors();
    mode.value = 'edit';
};

const openDelete = (item) => {
    selected.value = item;
    mode.value = 'delete';
};

const refreshItems = () => router.reload({ only: ['gift_ideas'] });

const handleHeaderClose = () => {
    switch (mode.value) {
        case 'create':
        case 'show':
            backToList();
            break;
        case 'edit':
        case 'delete':
            mode.value = 'show';
            break;
        case 'list':
        default:
            emit('close');
    }
};

const submitCreate = () =>
    createForm.post(route('gift_ideas.store', props.contact.ulid), {
        preserveScroll: true,
        onSuccess: () => { refreshItems(); backToList(); },
    });

const submitEdit = () =>
    editForm.put(
        route('gift_ideas.update', [props.contact.ulid, selected.value.ulid]),
        {
            preserveScroll: true,
            onSuccess: () => { refreshItems(); backToList(); },
        },
    );

const submitDelete = () =>
    deleteForm.delete(
        route('gift_ideas.destroy', [props.contact.ulid, selected.value.ulid]),
        {
            preserveScroll: true,
            onSuccess: () => { refreshItems(); backToList(); },
        },
    );
</script>

<template>
    <SlideOver :open="open" :title="title" @close="handleHeaderClose">
        <!-- List -->
        <template v-if="mode === 'list'">
            <div v-if="items.length === 0" class="text-sm text-gray-500 text-center py-6">
                No gift ideas yet.
            </div>
            <ul v-else class="divide-y divide-gray-200 -mx-6">
                <li v-for="item in items" :key="item.ulid">
                    <button
                        type="button"
                        class="block w-full text-left px-6 py-3 cursor-pointer hover:bg-gray-50"
                        @click="openShow(item)"
                    >
                        <div class="text-sm font-medium text-gray-900">{{ item.name }}</div>
                        <div v-if="item.formatted_due_at" class="text-sm text-gray-600">
                            Due {{ item.formatted_due_at }}
                        </div>
                    </button>
                </li>
            </ul>
        </template>

        <!-- Show -->
        <template v-else-if="mode === 'show'">
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="font-medium text-gray-700">Name</dt>
                    <dd class="text-gray-900">{{ selected.name }}</dd>
                </div>
                <div v-if="selected.description">
                    <dt class="font-medium text-gray-700">Description</dt>
                    <dd class="prose-note text-gray-900" v-html="selected.description_html" />
                </div>
                <div v-if="selected.url">
                    <dt class="font-medium text-gray-700">URL</dt>
                    <dd class="text-gray-900 break-all">
                        <a
                            :href="selected.url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-indigo-600 hover:text-indigo-500"
                        >
                            {{ selected.url }}
                        </a>
                    </dd>
                </div>
                <div v-if="selected.formatted_due_at">
                    <dt class="font-medium text-gray-700">Due</dt>
                    <dd class="text-gray-900">{{ selected.formatted_due_at }}</dd>
                </div>
            </dl>
        </template>

        <!-- Create -->
        <form
            v-else-if="mode === 'create'"
            id="gift-create-form"
            @submit.prevent="submitCreate"
            class="space-y-4"
        >
            <div>
                <InputLabel for="create-name" value="Name *" />
                <TextInput id="create-name" v-model="createForm.name" autofocus required />
                <InputError :message="createForm.errors.name" />
            </div>
            <div>
                <InputLabel for="create-description" value="Description" />
                <Textarea id="create-description" v-model="createForm.description" :rows="3" />
                <p class="mt-1 text-xs text-gray-500">
                    Markdown supported: **bold**, *italic*, [link](url), `code`, lists, &gt; quotes.
                </p>
                <InputError :message="createForm.errors.description" />
            </div>
            <div>
                <InputLabel for="create-url" value="URL" />
                <TextInput id="create-url" type="url" v-model="createForm.url" />
                <InputError :message="createForm.errors.url" />
            </div>
            <div>
                <InputLabel for="create-due_at" value="Due (DD.MM.YYYY)" />
                <TextInput
                    id="create-due_at"
                    v-model="createForm.due_at"
                    placeholder="01.01.2025"
                />
                <InputError :message="createForm.errors.due_at" />
            </div>
        </form>

        <!-- Edit -->
        <form
            v-else-if="mode === 'edit'"
            id="gift-edit-form"
            @submit.prevent="submitEdit"
            class="space-y-4"
        >
            <div>
                <InputLabel for="edit-name" value="Name *" />
                <TextInput id="edit-name" v-model="editForm.name" autofocus required />
                <InputError :message="editForm.errors.name" />
            </div>
            <div>
                <InputLabel for="edit-description" value="Description" />
                <Textarea id="edit-description" v-model="editForm.description" :rows="3" />
                <p class="mt-1 text-xs text-gray-500">
                    Markdown supported: **bold**, *italic*, [link](url), `code`, lists, &gt; quotes.
                </p>
                <InputError :message="editForm.errors.description" />
            </div>
            <div>
                <InputLabel for="edit-url" value="URL" />
                <TextInput id="edit-url" type="url" v-model="editForm.url" />
                <InputError :message="editForm.errors.url" />
            </div>
            <div>
                <InputLabel for="edit-due_at" value="Due (DD.MM.YYYY)" />
                <TextInput
                    id="edit-due_at"
                    v-model="editForm.due_at"
                    placeholder="01.01.2025"
                />
                <InputError :message="editForm.errors.due_at" />
            </div>
        </form>

        <!-- Delete -->
        <template v-else-if="mode === 'delete'">
            <p class="text-sm text-gray-700">
                Permanently remove <strong>{{ selected.name }}</strong>?
            </p>
        </template>

        <template #footer>
            <template v-if="mode === 'list'">
                <SecondaryButton type="button" @click="emit('close')">Close</SecondaryButton>
                <PrimaryButton v-if="can.create" type="button" @click="openCreate">
                    Add gift idea
                </PrimaryButton>
            </template>

            <template v-else-if="mode === 'show'">
                <SecondaryButton type="button" @click="backToList">Back</SecondaryButton>
                <DangerButton v-if="can.delete" type="button" @click="openDelete(selected)">
                    Delete
                </DangerButton>
                <PrimaryButton v-if="can.edit" type="button" @click="openEdit(selected)">
                    Edit
                </PrimaryButton>
            </template>

            <template v-else-if="mode === 'create'">
                <SecondaryButton type="button" @click="backToList">Cancel</SecondaryButton>
                <PrimaryButton
                    type="submit"
                    form="gift-create-form"
                    :disabled="createForm.processing"
                    :class="{ 'opacity-50': createForm.processing }"
                >
                    Create
                </PrimaryButton>
            </template>

            <template v-else-if="mode === 'edit'">
                <SecondaryButton type="button" @click="openShow(selected)">Cancel</SecondaryButton>
                <PrimaryButton
                    type="submit"
                    form="gift-edit-form"
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
</template>
