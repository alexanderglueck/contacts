<script setup>
import { ref, computed, watch } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import SlideOver from '@/Components/SlideOver.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Select from '@/Components/Select.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    open: { type: Boolean, required: true },
    contact: { type: Object, required: true },
    items: { type: Array, default: () => [] },
    countries: { type: Array, default: () => [] },
    can: { type: Object, default: () => ({}) },
});

const emit = defineEmits(['close']);

const mode = ref('list');
const selected = ref(null);

const blankAddress = () => ({
    name: '',
    street: '',
    zip: '',
    city: '',
    state: '',
    country_id: 164,
    latitude: '',
    longitude: '',
});

const createForm = useForm(blankAddress());
const editForm = useForm(blankAddress());
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
        case 'create': return 'Add address';
        case 'show': return selected.value?.name ?? 'Address';
        case 'edit': return `Edit ${selected.value?.name}`;
        case 'delete': return `Delete ${selected.value?.name}?`;
        default: return 'Addresses';
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
    editForm.street = item.street;
    editForm.zip = item.zip;
    editForm.city = item.city;
    editForm.state = item.state ?? '';
    editForm.country_id = item.country_id ?? 164;
    editForm.latitude = item.latitude ?? '';
    editForm.longitude = item.longitude ?? '';
    editForm.clearErrors();
    mode.value = 'edit';
};

const openDelete = (item) => {
    selected.value = item;
    mode.value = 'delete';
};

const refreshItems = () => router.reload({ only: ['addresses', 'countries'] });

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
    createForm.post(route('contact_addresses.store', props.contact.ulid), {
        preserveScroll: true,
        onSuccess: () => { refreshItems(); backToList(); },
    });

const submitEdit = () =>
    editForm.put(
        route('contact_addresses.update', [props.contact.ulid, selected.value.ulid]),
        {
            preserveScroll: true,
            onSuccess: () => { refreshItems(); backToList(); },
        },
    );

const submitDelete = () =>
    deleteForm.delete(
        route('contact_addresses.destroy', [props.contact.ulid, selected.value.ulid]),
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
                No addresses yet.
            </div>
            <ul v-else class="divide-y divide-gray-200 -mx-6">
                <li v-for="item in items" :key="item.id">
                    <button
                        type="button"
                        class="block w-full text-left px-6 py-3 cursor-pointer hover:bg-gray-50"
                        @click="openShow(item)"
                    >
                        <div class="text-sm font-medium text-gray-900">{{ item.name }}</div>
                        <div class="text-sm text-gray-600">{{ item.street }}, {{ item.zip }} {{ item.city }}</div>
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
                    <dt class="font-medium text-gray-700">Street</dt>
                    <dd class="text-gray-900">{{ selected.street }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-700">City</dt>
                    <dd class="text-gray-900">{{ selected.zip }} {{ selected.city }}</dd>
                </div>
                <div v-if="selected.state">
                    <dt class="font-medium text-gray-700">State / region</dt>
                    <dd class="text-gray-900">{{ selected.state }}</dd>
                </div>
                <div v-if="selected.country">
                    <dt class="font-medium text-gray-700">Country</dt>
                    <dd class="text-gray-900">{{ selected.country }}</dd>
                </div>
                <div v-if="selected.latitude || selected.longitude">
                    <dt class="font-medium text-gray-700">Coordinates</dt>
                    <dd class="text-gray-900">{{ selected.latitude }}, {{ selected.longitude }}</dd>
                </div>
            </dl>
        </template>

        <!-- Create -->
        <form
            v-else-if="mode === 'create'"
            id="address-create-form"
            @submit.prevent="submitCreate"
            class="space-y-4"
        >
            <div>
                <InputLabel for="create-name" value="Name *" />
                <TextInput id="create-name" v-model="createForm.name" autofocus required />
                <InputError :message="createForm.errors.name" />
            </div>
            <div>
                <InputLabel for="create-street" value="Street *" />
                <TextInput id="create-street" v-model="createForm.street" required />
                <InputError :message="createForm.errors.street" />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <InputLabel for="create-zip" value="ZIP *" />
                    <TextInput id="create-zip" v-model="createForm.zip" required />
                    <InputError :message="createForm.errors.zip" />
                </div>
                <div class="sm:col-span-2">
                    <InputLabel for="create-city" value="City *" />
                    <TextInput id="create-city" v-model="createForm.city" required />
                    <InputError :message="createForm.errors.city" />
                </div>
            </div>
            <div>
                <InputLabel for="create-state" value="State / region" />
                <TextInput id="create-state" v-model="createForm.state" />
                <InputError :message="createForm.errors.state" />
            </div>
            <div>
                <InputLabel for="create-country_id" value="Country *" />
                <Select id="create-country_id" v-model="createForm.country_id" required>
                    <option v-for="country in countries" :key="country.id" :value="country.id">
                        {{ country.country }}
                    </option>
                </Select>
                <InputError :message="createForm.errors.country_id" />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <InputLabel for="create-latitude" value="Latitude" />
                    <TextInput id="create-latitude" v-model="createForm.latitude" />
                    <InputError :message="createForm.errors.latitude" />
                </div>
                <div>
                    <InputLabel for="create-longitude" value="Longitude" />
                    <TextInput id="create-longitude" v-model="createForm.longitude" />
                    <InputError :message="createForm.errors.longitude" />
                </div>
            </div>
            <p class="text-xs text-gray-500">
                Geocoding from address is not wired up yet — enter coordinates manually if you need map markers.
            </p>
        </form>

        <!-- Edit -->
        <form
            v-else-if="mode === 'edit'"
            id="address-edit-form"
            @submit.prevent="submitEdit"
            class="space-y-4"
        >
            <div>
                <InputLabel for="edit-name" value="Name *" />
                <TextInput id="edit-name" v-model="editForm.name" autofocus required />
                <InputError :message="editForm.errors.name" />
            </div>
            <div>
                <InputLabel for="edit-street" value="Street *" />
                <TextInput id="edit-street" v-model="editForm.street" required />
                <InputError :message="editForm.errors.street" />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <InputLabel for="edit-zip" value="ZIP *" />
                    <TextInput id="edit-zip" v-model="editForm.zip" required />
                    <InputError :message="editForm.errors.zip" />
                </div>
                <div class="sm:col-span-2">
                    <InputLabel for="edit-city" value="City *" />
                    <TextInput id="edit-city" v-model="editForm.city" required />
                    <InputError :message="editForm.errors.city" />
                </div>
            </div>
            <div>
                <InputLabel for="edit-state" value="State / region" />
                <TextInput id="edit-state" v-model="editForm.state" />
                <InputError :message="editForm.errors.state" />
            </div>
            <div>
                <InputLabel for="edit-country_id" value="Country *" />
                <Select id="edit-country_id" v-model="editForm.country_id" required>
                    <option v-for="country in countries" :key="country.id" :value="country.id">
                        {{ country.country }}
                    </option>
                </Select>
                <InputError :message="editForm.errors.country_id" />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <InputLabel for="edit-latitude" value="Latitude" />
                    <TextInput id="edit-latitude" v-model="editForm.latitude" />
                    <InputError :message="editForm.errors.latitude" />
                </div>
                <div>
                    <InputLabel for="edit-longitude" value="Longitude" />
                    <TextInput id="edit-longitude" v-model="editForm.longitude" />
                    <InputError :message="editForm.errors.longitude" />
                </div>
            </div>
            <p class="text-xs text-gray-500">
                Geocoding from address is not wired up yet — enter coordinates manually if you need map markers.
            </p>
        </form>

        <!-- Delete -->
        <template v-else-if="mode === 'delete'">
            <p class="text-sm text-gray-700">
                Permanently remove <strong>{{ selected.name }}</strong> ({{ selected.street }}, {{ selected.zip }} {{ selected.city }})?
            </p>
        </template>

        <template #footer>
            <template v-if="mode === 'list'">
                <SecondaryButton type="button" @click="emit('close')">Close</SecondaryButton>
                <PrimaryButton v-if="can.create" type="button" @click="openCreate">
                    Add address
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
                    form="address-create-form"
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
                    form="address-edit-form"
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
