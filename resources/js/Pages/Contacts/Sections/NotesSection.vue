<script setup>
import { ref, computed, watch } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
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
const { t } = useI18n();

const mode = ref('list');
const selected = ref(null);

const createForm = useForm({ name: '', note: '' });
const editForm = useForm({ name: '', note: '' });
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
        case 'create': return t('contacts.slideover.add_note');
        case 'show': return selected.value?.name ?? t('contacts.section.notes');
        case 'edit': return `${t('contacts.slideover.edit')} ${selected.value?.name}`;
        case 'delete': return `${t('contacts.slideover.delete')} ${selected.value?.name}?`;
        default: return t('contacts.section.notes');
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
    editForm.note = item.note;
    editForm.clearErrors();
    mode.value = 'edit';
};

const openDelete = (item) => {
    selected.value = item;
    mode.value = 'delete';
};

const refreshItems = () => router.reload({ only: ['notes'] });

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
    createForm.post(route('contact_notes.store', props.contact.ulid), {
        preserveScroll: true,
        onSuccess: () => { refreshItems(); backToList(); },
    });

const submitEdit = () =>
    editForm.put(
        route('contact_notes.update', [props.contact.ulid, selected.value.ulid]),
        {
            preserveScroll: true,
            onSuccess: () => { refreshItems(); backToList(); },
        },
    );

const submitDelete = () =>
    deleteForm.delete(
        route('contact_notes.destroy', [props.contact.ulid, selected.value.ulid]),
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
                {{ t('contacts.slideover.empty_notes') }}
            </div>
            <ul v-else class="divide-y divide-gray-200 -mx-6">
                <li v-for="item in items" :key="item.id">
                    <button
                        type="button"
                        class="block w-full text-left px-6 py-3 cursor-pointer hover:bg-gray-50"
                        @click="openShow(item)"
                    >
                        <div class="text-sm font-medium text-gray-900">{{ item.name }}</div>
                        <div class="text-sm text-gray-600 truncate">{{ item.note }}</div>
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
                <div>
                    <dt class="font-medium text-gray-700">Note</dt>
                    <dd class="prose-note text-gray-900" v-html="selected.note_html" />
                </div>
            </dl>
        </template>

        <!-- Create -->
        <form
            v-else-if="mode === 'create'"
            id="note-create-form"
            @submit.prevent="submitCreate"
            class="space-y-4"
        >
            <div>
                <InputLabel for="create-name" value="Name *" />
                <TextInput id="create-name" v-model="createForm.name" autofocus required />
                <InputError :message="createForm.errors.name" />
            </div>
            <div>
                <InputLabel for="create-note" value="Note *" />
                <Textarea id="create-note" v-model="createForm.note" :rows="4" />
                <p class="mt-1 text-xs text-gray-500">
                    Markdown supported: **bold**, *italic*, [link](url), `code`, lists, &gt; quotes.
                </p>
                <InputError :message="createForm.errors.note" />
            </div>
        </form>

        <!-- Edit -->
        <form
            v-else-if="mode === 'edit'"
            id="note-edit-form"
            @submit.prevent="submitEdit"
            class="space-y-4"
        >
            <div>
                <InputLabel for="edit-name" value="Name *" />
                <TextInput id="edit-name" v-model="editForm.name" autofocus required />
                <InputError :message="editForm.errors.name" />
            </div>
            <div>
                <InputLabel for="edit-note" value="Note *" />
                <Textarea id="edit-note" v-model="editForm.note" :rows="4" />
                <p class="mt-1 text-xs text-gray-500">
                    Markdown supported: **bold**, *italic*, [link](url), `code`, lists, &gt; quotes.
                </p>
                <InputError :message="editForm.errors.note" />
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
                <SecondaryButton type="button" @click="emit('close')">{{ t('contacts.slideover.close') }}</SecondaryButton>
                <PrimaryButton v-if="can.create" type="button" @click="openCreate">
                    {{ t('contacts.slideover.add_note') }}
                </PrimaryButton>
            </template>

            <template v-else-if="mode === 'show'">
                <SecondaryButton type="button" @click="backToList">{{ t('contacts.slideover.back') }}</SecondaryButton>
                <DangerButton v-if="can.delete" type="button" @click="openDelete(selected)">
                    {{ t('contacts.slideover.delete') }}
                </DangerButton>
                <PrimaryButton v-if="can.edit" type="button" @click="openEdit(selected)">
                    {{ t('contacts.slideover.edit') }}
                </PrimaryButton>
            </template>

            <template v-else-if="mode === 'create'">
                <SecondaryButton type="button" @click="backToList">{{ t('contacts.slideover.cancel') }}</SecondaryButton>
                <PrimaryButton
                    type="submit"
                    form="note-create-form"
                    :disabled="createForm.processing"
                    :class="{ 'opacity-50': createForm.processing }"
                >
                    {{ t('contacts.slideover.create') }}
                </PrimaryButton>
            </template>

            <template v-else-if="mode === 'edit'">
                <SecondaryButton type="button" @click="openShow(selected)">{{ t('contacts.slideover.cancel') }}</SecondaryButton>
                <PrimaryButton
                    type="submit"
                    form="note-edit-form"
                    :disabled="editForm.processing"
                    :class="{ 'opacity-50': editForm.processing }"
                >
                    {{ t('contacts.slideover.save') }}
                </PrimaryButton>
            </template>

            <template v-else-if="mode === 'delete'">
                <SecondaryButton type="button" @click="openShow(selected)">{{ t('contacts.slideover.cancel') }}</SecondaryButton>
                <DangerButton
                    type="button"
                    :disabled="deleteForm.processing"
                    :class="{ 'opacity-50': deleteForm.processing }"
                    @click="submitDelete"
                >
                    {{ t('contacts.slideover.delete') }}
                </DangerButton>
            </template>
        </template>
    </SlideOver>
</template>
