<script setup>
import { ref, computed, watch } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
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
    can: { type: Object, default: () => ({}) },
});

const emit = defineEmits(['close']);
const { t } = useI18n();

const mode = ref('list');
const selected = ref(null);

const createForm = useForm({ name: '', date: '', skip_year: 0 });
const editForm = useForm({ name: '', date: '', skip_year: 0 });
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
        case 'create': return t('contacts.slideover.add_date');
        case 'show': return selected.value?.name ?? t('contacts.section.dates');
        case 'edit': return `${t('contacts.slideover.edit')} ${selected.value?.name}`;
        case 'delete': return `${t('contacts.slideover.delete')} ${selected.value?.name}?`;
        default: return t('contacts.section.dates');
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
    editForm.date = item.date ?? '';
    editForm.skip_year = item.skip_year ? 1 : 0;
    editForm.clearErrors();
    mode.value = 'edit';
};

const openDelete = (item) => {
    selected.value = item;
    mode.value = 'delete';
};

const refreshItems = () => router.reload({ only: ['dates'] });

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
    createForm.post(route('contact_dates.store', props.contact.ulid), {
        preserveScroll: true,
        onSuccess: () => { refreshItems(); backToList(); },
    });

const submitEdit = () =>
    editForm.put(
        route('contact_dates.update', [props.contact.ulid, selected.value.ulid]),
        {
            preserveScroll: true,
            onSuccess: () => { refreshItems(); backToList(); },
        },
    );

const submitDelete = () =>
    deleteForm.delete(
        route('contact_dates.destroy', [props.contact.ulid, selected.value.ulid]),
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
                {{ t('contacts.slideover.empty_dates') }}
            </div>
            <ul v-else class="divide-y divide-gray-200 -mx-6">
                <li v-for="item in items" :key="item.id">
                    <button
                        type="button"
                        class="block w-full text-left px-6 py-3 cursor-pointer hover:bg-gray-50"
                        @click="openShow(item)"
                    >
                        <div class="text-sm font-medium text-gray-900">{{ item.name }}</div>
                        <div class="text-sm text-gray-600">{{ item.formatted_date }}</div>
                    </button>
                </li>
            </ul>
        </template>

        <!-- Show -->
        <template v-else-if="mode === 'show'">
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="font-medium text-gray-700">{{ t('contacts.slideover.date_fields.name') }}</dt>
                    <dd class="text-gray-900">{{ selected.name }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-700">{{ t('contacts.slideover.date_fields.date') }}</dt>
                    <dd class="text-gray-900">{{ selected.formatted_date }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-700">{{ t('contacts.slideover.date_fields.skip_year') }}</dt>
                    <dd class="text-gray-900">{{ selected.skip_year ? 'Yes' : 'No' }}</dd>
                </div>
            </dl>
        </template>

        <!-- Create -->
        <form
            v-else-if="mode === 'create'"
            id="date-create-form"
            @submit.prevent="submitCreate"
            class="space-y-4"
        >
            <div>
                <InputLabel for="create-name" :value="`${t('contacts.slideover.date_fields.name')} *`" />
                <TextInput id="create-name" v-model="createForm.name" autofocus required />
                <InputError :message="createForm.errors.name" />
            </div>
            <div>
                <InputLabel for="create-date" :value="`${t('contacts.slideover.date_fields.date')} *`" />
                <TextInput id="create-date" type="date" v-model="createForm.date" required />
                <InputError :message="createForm.errors.date" />
            </div>
            <div>
                <span class="block text-sm font-medium text-gray-700 mb-1">{{ t('contacts.slideover.date_fields.skip_year') }}</span>
                <label class="inline-flex items-center mr-4">
                    <input
                        type="radio"
                        :value="0"
                        v-model.number="createForm.skip_year"
                        class="text-indigo-600 focus:ring-indigo-500"
                    />
                    <span class="ms-2 text-sm text-gray-700">{{ t('common.no') }}</span>
                </label>
                <label class="inline-flex items-center">
                    <input
                        type="radio"
                        :value="1"
                        v-model.number="createForm.skip_year"
                        class="text-indigo-600 focus:ring-indigo-500"
                    />
                    <span class="ms-2 text-sm text-gray-700">{{ t('common.yes') }}</span>
                </label>
                <InputError :message="createForm.errors.skip_year" />
            </div>
        </form>

        <!-- Edit -->
        <form
            v-else-if="mode === 'edit'"
            id="date-edit-form"
            @submit.prevent="submitEdit"
            class="space-y-4"
        >
            <div>
                <InputLabel for="edit-name" :value="`${t('contacts.slideover.date_fields.name')} *`" />
                <TextInput id="edit-name" v-model="editForm.name" autofocus required />
                <InputError :message="editForm.errors.name" />
            </div>
            <div>
                <InputLabel for="edit-date" :value="`${t('contacts.slideover.date_fields.date')} *`" />
                <TextInput id="edit-date" type="date" v-model="editForm.date" required />
                <InputError :message="editForm.errors.date" />
            </div>
            <div>
                <span class="block text-sm font-medium text-gray-700 mb-1">{{ t('contacts.slideover.date_fields.skip_year') }}</span>
                <label class="inline-flex items-center mr-4">
                    <input
                        type="radio"
                        :value="0"
                        v-model.number="editForm.skip_year"
                        class="text-indigo-600 focus:ring-indigo-500"
                    />
                    <span class="ms-2 text-sm text-gray-700">{{ t('common.no') }}</span>
                </label>
                <label class="inline-flex items-center">
                    <input
                        type="radio"
                        :value="1"
                        v-model.number="editForm.skip_year"
                        class="text-indigo-600 focus:ring-indigo-500"
                    />
                    <span class="ms-2 text-sm text-gray-700">{{ t('common.yes') }}</span>
                </label>
                <InputError :message="editForm.errors.skip_year" />
            </div>
        </form>

        <!-- Delete -->
        <template v-else-if="mode === 'delete'">
            <p class="text-sm text-gray-700">
                {{ t('contacts.slideover.remove_q', { item: `${selected.name} (${selected.formatted_date})` }) }}
            </p>
        </template>

        <template #footer>
            <template v-if="mode === 'list'">
                <SecondaryButton type="button" @click="emit('close')">{{ t('contacts.slideover.close') }}</SecondaryButton>
                <PrimaryButton v-if="can.create" type="button" @click="openCreate">
                    Add date
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
                    form="date-create-form"
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
                    form="date-edit-form"
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
