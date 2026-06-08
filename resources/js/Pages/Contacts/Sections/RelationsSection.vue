<script setup>
import { ref, computed, watch } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import SlideOver from '@/Components/SlideOver.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    open: { type: Boolean, required: true },
    contact: { type: Object, required: true },
    items: { type: Array, default: () => [] },
    labels: { type: Array, default: () => [] },
    can: { type: Object, default: () => ({}) },
});

const emit = defineEmits(['close']);
const { t } = useI18n();

const mode = ref('list');
const selected = ref(null);

// Contact picker (create mode)
const searchTerm = ref('');
const searchResults = ref([]);
const selectedContact = ref(null);
let searchTimer = null;

const createForm = useForm({ related_contact: '', forward_label: '', inverse_label: '' });
const editForm = useForm({ forward_label: '', inverse_label: '' });
const deleteForm = useForm({});

// Group the flat list by the forward label so the panel reads
// "daughter: Anna, Lena / son: Max / wife: Eva".
const groupedItems = computed(() => {
    const groups = new Map();
    for (const item of props.items) {
        if (! groups.has(item.label)) {
            groups.set(item.label, []);
        }
        groups.get(item.label).push(item);
    }
    return Array.from(groups, ([label, entries]) => ({ label, entries }));
});

const resetForms = () => {
    createForm.reset();
    createForm.clearErrors();
    editForm.reset();
    editForm.clearErrors();
    searchTerm.value = '';
    searchResults.value = [];
    selectedContact.value = null;
};

watch(
    () => props.open,
    (isOpen) => {
        if (isOpen) {
            mode.value = 'list';
            selected.value = null;
            resetForms();
        }
    },
);

const title = computed(() => {
    switch (mode.value) {
        case 'create': return t('contacts.slideover.add_relation');
        case 'show': return selected.value?.contact?.fullname ?? t('contacts.section.relations');
        case 'edit': return `${t('contacts.slideover.edit')} ${selected.value?.contact?.fullname}`;
        case 'delete': return `${t('contacts.slideover.delete')} ${selected.value?.contact?.fullname}?`;
        default: return t('contacts.section.relations');
    }
});

const backToList = () => {
    mode.value = 'list';
    selected.value = null;
    resetForms();
};

const openCreate = () => {
    selected.value = null;
    resetForms();
    mode.value = 'create';
};

const openShow = (item) => {
    selected.value = item;
    mode.value = 'show';
};

const openEdit = (item) => {
    selected.value = item;
    editForm.forward_label = item.label;
    editForm.inverse_label = item.inverse;
    editForm.clearErrors();
    mode.value = 'edit';
};

const openDelete = (item) => {
    selected.value = item;
    mode.value = 'delete';
};

const refreshItems = () => router.reload({ only: ['relations', 'relationLabels'] });

// Debounced tenant-scoped contact search for the picker.
const runSearch = () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        window.axios
            .get(route('contact_relations.search', props.contact.ulid), { params: { q: searchTerm.value } })
            .then(({ data }) => { searchResults.value = data.data ?? []; })
            .catch(() => { searchResults.value = []; });
    }, 250);
};

const pickContact = (result) => {
    selectedContact.value = result;
    createForm.related_contact = result.ulid;
    searchResults.value = [];
    searchTerm.value = '';
};

const clearPickedContact = () => {
    selectedContact.value = null;
    createForm.related_contact = '';
};

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
    createForm.post(route('contact_relations.store', props.contact.ulid), {
        preserveScroll: true,
        onSuccess: () => { refreshItems(); backToList(); },
    });

const submitEdit = () =>
    editForm.put(route('contact_relations.update', [props.contact.ulid, selected.value.ulid]), {
        preserveScroll: true,
        onSuccess: () => { refreshItems(); backToList(); },
    });

const submitDelete = () =>
    deleteForm.delete(route('contact_relations.destroy', [props.contact.ulid, selected.value.ulid]), {
        preserveScroll: true,
        onSuccess: () => { refreshItems(); backToList(); },
    });
</script>

<template>
    <SlideOver :open="open" :title="title" @close="handleHeaderClose">
        <!-- List -->
        <template v-if="mode === 'list'">
            <div v-if="items.length === 0" class="text-sm text-gray-500 text-center py-6">
                {{ t('contacts.slideover.empty_relations') }}
            </div>
            <div v-else class="space-y-5">
                <div v-for="group in groupedItems" :key="group.label">
                    <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">{{ group.label }}</h4>
                    <ul class="divide-y divide-gray-200 -mx-6">
                        <li v-for="item in group.entries" :key="item.ulid">
                            <button
                                type="button"
                                class="block w-full text-left px-6 py-3 cursor-pointer hover:bg-gray-50"
                                @click="openShow(item)"
                            >
                                <div class="text-sm font-medium text-gray-900">{{ item.contact.fullname }}</div>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </template>

        <!-- Show -->
        <template v-else-if="mode === 'show'">
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="font-medium text-gray-700">{{ t('contacts.slideover.relation_fields.related_contact') }}</dt>
                    <dd>
                        <Link
                            :href="route('contacts.show', selected.contact.ulid)"
                            class="text-indigo-600 hover:text-indigo-500"
                        >
                            {{ selected.contact.fullname }}
                        </Link>
                    </dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-700">{{ t('contacts.slideover.relation_fields.forward_label') }}</dt>
                    <dd class="text-gray-900">{{ selected.label }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-700">{{ t('contacts.slideover.relation_fields.inverse_label') }}</dt>
                    <dd class="text-gray-900">{{ selected.inverse }}</dd>
                </div>
            </dl>
        </template>

        <!-- Create -->
        <form v-else-if="mode === 'create'" id="relation-create-form" @submit.prevent="submitCreate" class="space-y-4">
            <div>
                <InputLabel for="relation-search" :value="`${t('contacts.slideover.relation_fields.related_contact')} *`" />
                <div v-if="selectedContact" class="mt-1 flex items-center justify-between rounded-md border border-gray-300 px-3 py-2">
                    <span class="text-sm text-gray-900">{{ selectedContact.fullname }}</span>
                    <button type="button" class="text-sm text-indigo-600 hover:text-indigo-500" @click="clearPickedContact">
                        {{ t('contacts.slideover.change') }}
                    </button>
                </div>
                <div v-else class="relative">
                    <TextInput
                        id="relation-search"
                        v-model="searchTerm"
                        autocomplete="off"
                        :placeholder="t('contacts.slideover.relation_search_placeholder')"
                        @input="runSearch"
                    />
                    <ul
                        v-if="searchResults.length"
                        class="absolute z-10 mt-1 w-full max-h-48 overflow-auto rounded-md border border-gray-200 bg-white shadow-lg"
                    >
                        <li v-for="result in searchResults" :key="result.ulid">
                            <button
                                type="button"
                                class="block w-full text-left px-3 py-2 text-sm hover:bg-indigo-50 cursor-pointer"
                                @click="pickContact(result)"
                            >
                                {{ result.fullname }}
                            </button>
                        </li>
                    </ul>
                    <p v-else-if="searchTerm" class="mt-1 text-sm text-gray-500">
                        {{ t('contacts.slideover.relation_search_empty') }}
                    </p>
                </div>
                <InputError :message="createForm.errors.related_contact" />
            </div>

            <div>
                <InputLabel for="relation-forward" :value="`${t('contacts.slideover.relation_fields.forward_label')} *`" />
                <TextInput id="relation-forward" v-model="createForm.forward_label" list="relation-label-options" required />
                <p class="mt-1 text-xs text-gray-500">{{ t('contacts.slideover.relation_fields.forward_hint') }}</p>
                <InputError :message="createForm.errors.forward_label" />
            </div>

            <div>
                <InputLabel for="relation-inverse" :value="t('contacts.slideover.relation_fields.inverse_label')" />
                <TextInput id="relation-inverse" v-model="createForm.inverse_label" list="relation-label-options" />
                <p class="mt-1 text-xs text-gray-500">{{ t('contacts.slideover.relation_fields.inverse_hint') }}</p>
                <InputError :message="createForm.errors.inverse_label" />
            </div>
        </form>

        <!-- Edit -->
        <form v-else-if="mode === 'edit'" id="relation-edit-form" @submit.prevent="submitEdit" class="space-y-4">
            <div>
                <span class="block text-sm font-medium text-gray-700">{{ t('contacts.slideover.relation_fields.related_contact') }}</span>
                <p class="mt-1 text-sm text-gray-900">{{ selected.contact.fullname }}</p>
            </div>
            <div>
                <InputLabel for="relation-edit-forward" :value="`${t('contacts.slideover.relation_fields.forward_label')} *`" />
                <TextInput id="relation-edit-forward" v-model="editForm.forward_label" list="relation-label-options" autofocus required />
                <p class="mt-1 text-xs text-gray-500">{{ t('contacts.slideover.relation_fields.forward_hint') }}</p>
                <InputError :message="editForm.errors.forward_label" />
            </div>
            <div>
                <InputLabel for="relation-edit-inverse" :value="t('contacts.slideover.relation_fields.inverse_label')" />
                <TextInput id="relation-edit-inverse" v-model="editForm.inverse_label" list="relation-label-options" />
                <p class="mt-1 text-xs text-gray-500">{{ t('contacts.slideover.relation_fields.inverse_hint') }}</p>
                <InputError :message="editForm.errors.inverse_label" />
            </div>
        </form>

        <!-- Delete -->
        <template v-else-if="mode === 'delete'">
            <p class="text-sm text-gray-700">
                {{ t('contacts.slideover.remove_q', { item: `${selected.contact.fullname} (${selected.label})` }) }}
            </p>
        </template>

        <!-- Shared autocomplete options for label fields -->
        <datalist id="relation-label-options">
            <option v-for="label in labels" :key="label" :value="label" />
        </datalist>

        <template #footer>
            <template v-if="mode === 'list'">
                <SecondaryButton type="button" @click="emit('close')">{{ t('contacts.slideover.close') }}</SecondaryButton>
                <PrimaryButton v-if="can.create" type="button" @click="openCreate">
                    {{ t('contacts.slideover.add_relation') }}
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
                    form="relation-create-form"
                    :disabled="createForm.processing || ! selectedContact"
                    :class="{ 'opacity-50': createForm.processing || ! selectedContact }"
                >
                    {{ t('contacts.slideover.create') }}
                </PrimaryButton>
            </template>

            <template v-else-if="mode === 'edit'">
                <SecondaryButton type="button" @click="openShow(selected)">{{ t('contacts.slideover.cancel') }}</SecondaryButton>
                <PrimaryButton
                    type="submit"
                    form="relation-edit-form"
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
