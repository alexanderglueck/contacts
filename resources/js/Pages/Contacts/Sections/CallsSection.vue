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

const createForm = useForm({ called_at: '', note: '' });
const editForm = useForm({ called_at: '', note: '' });
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
        case 'create': return t('contacts.slideover.add_call');
        case 'show': return selected.value?.formatted_called_at ?? 'Call';
        case 'edit': return `Edit call`;
        case 'delete': return `Delete call?`;
        default: return t('contacts.section.calls');
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
    editForm.called_at = item.called_at ?? '';
    editForm.note = item.note ?? '';
    editForm.clearErrors();
    mode.value = 'edit';
};

const openDelete = (item) => {
    selected.value = item;
    mode.value = 'delete';
};

const refreshItems = () => router.reload({ only: ['calls'] });

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
    createForm.post(route('contact_calls.store', props.contact.ulid), {
        preserveScroll: true,
        onSuccess: () => { refreshItems(); backToList(); },
    });

const submitEdit = () =>
    editForm.put(
        route('contact_calls.update', [props.contact.ulid, selected.value.ulid]),
        {
            preserveScroll: true,
            onSuccess: () => { refreshItems(); backToList(); },
        },
    );

const submitDelete = () =>
    deleteForm.delete(
        route('contact_calls.destroy', [props.contact.ulid, selected.value.ulid]),
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
                {{ t('contacts.slideover.empty_calls') }}
            </div>
            <ul v-else class="divide-y divide-gray-200 -mx-6">
                <li v-for="item in items" :key="item.ulid">
                    <button
                        type="button"
                        class="block w-full text-left px-6 py-3 cursor-pointer hover:bg-gray-50"
                        @click="openShow(item)"
                    >
                        <div class="text-sm font-medium text-gray-900">{{ item.formatted_called_at }}</div>
                        <div v-if="item.note" class="text-sm text-gray-600 truncate">{{ item.note }}</div>
                    </button>
                </li>
            </ul>
        </template>

        <!-- Show -->
        <template v-else-if="mode === 'show'">
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="font-medium text-gray-700">When</dt>
                    <dd class="text-gray-900">{{ selected.formatted_called_at }}</dd>
                </div>
                <div v-if="selected.note">
                    <dt class="font-medium text-gray-700">Note</dt>
                    <dd class="prose-note text-gray-900" v-html="selected.note_html" />
                </div>
            </dl>
        </template>

        <!-- Create -->
        <form
            v-else-if="mode === 'create'"
            id="call-create-form"
            @submit.prevent="submitCreate"
            class="space-y-4"
        >
            <div>
                <InputLabel for="create-called_at" value="When *" />
                <TextInput
                    id="create-called_at"
                    type="datetime-local"
                    v-model="createForm.called_at"
                    autofocus
                    required
                />
                <InputError :message="createForm.errors.called_at" />
            </div>
            <div>
                <InputLabel for="create-note" value="Note" />
                <Textarea id="create-note" v-model="createForm.note" :rows="3" />
                <p class="mt-1 text-xs text-gray-500">
                    Markdown supported: **bold**, *italic*, [link](url), `code`, lists, &gt; quotes.
                </p>
                <InputError :message="createForm.errors.note" />
            </div>
        </form>

        <!-- Edit -->
        <form
            v-else-if="mode === 'edit'"
            id="call-edit-form"
            @submit.prevent="submitEdit"
            class="space-y-4"
        >
            <div>
                <InputLabel for="edit-called_at" value="When *" />
                <TextInput
                    id="edit-called_at"
                    type="datetime-local"
                    v-model="editForm.called_at"
                    autofocus
                    required
                />
                <InputError :message="editForm.errors.called_at" />
            </div>
            <div>
                <InputLabel for="edit-note" value="Note" />
                <Textarea id="edit-note" v-model="editForm.note" :rows="3" />
                <p class="mt-1 text-xs text-gray-500">
                    Markdown supported: **bold**, *italic*, [link](url), `code`, lists, &gt; quotes.
                </p>
                <InputError :message="editForm.errors.note" />
            </div>
        </form>

        <!-- Delete -->
        <template v-else-if="mode === 'delete'">
            <p class="text-sm text-gray-700">
                Permanently remove the call from <strong>{{ selected.formatted_called_at }}</strong>?
            </p>
        </template>

        <template #footer>
            <template v-if="mode === 'list'">
                <SecondaryButton type="button" @click="emit('close')">{{ t('contacts.slideover.close') }}</SecondaryButton>
                <PrimaryButton v-if="can.create" type="button" @click="openCreate">
                    {{ t('contacts.slideover.add_call') }}
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
                    form="call-create-form"
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
                    form="call-edit-form"
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
