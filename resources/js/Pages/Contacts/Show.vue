<script setup>
import { computed, ref } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PhoneNumbersSection from './Sections/PhoneNumbersSection.vue';

const props = defineProps({
    contact: { type: Object, required: true },
    can: { type: Object, default: () => ({}) },
});

const page = usePage();
const imageUrl = computed(() => (props.contact.image ? `/storage/${props.contact.image}` : null));

// Sub-resource lists are lazy props — populated when the section slideover
// opens and asks for `only: ['numbers']` etc.
const numbers = computed(() => page.props.numbers ?? []);

const activeSection = ref(null); // null | 'numbers' (more sections to come)

// Which Inertia lazy prop to pull for each section.
const sectionDataKey = { numbers: 'numbers' };

const openSection = (key) => {
    activeSection.value = key;
    const dataKey = sectionDataKey[key];
    if (dataKey) {
        router.reload({ only: [dataKey] });
    }
};
const closeSection = () => { activeSection.value = null; };

const detailFields = computed(() => [
    { label: 'Name', value: props.contact.fullname },
    { label: 'Gender', value: props.contact.gender?.gender },
    { label: 'Company', value: props.contact.company },
    { label: 'Job', value: props.contact.job },
    { label: 'Department', value: props.contact.department },
    { label: 'Salutation', value: props.contact.salutation },
    { label: 'Date of birth', value: props.contact.formatted_date_of_birth },
    { label: 'Nickname', value: props.contact.nickname },
    { label: 'IBAN', value: props.contact.iban },
    { label: 'Nationality', value: props.contact.nationality?.country },
    { label: 'Custom ID', value: props.contact.custom_id },
]);

const tiles = computed(() => [
    {
        key: 'addresses',
        label: 'Addresses',
        count: props.contact.addresses_count ?? 0,
        canView: props.can.view_addresses,
        action: () => null, // placeholder — still routes to legacy index page
        href: route('contact_addresses.index', props.contact.ulid),
    },
    {
        key: 'numbers',
        label: 'Phone numbers',
        count: props.contact.numbers_count ?? 0,
        canView: props.can.view_numbers,
        action: () => openSection('numbers'),
        href: null,
    },
    {
        key: 'emails',
        label: 'Emails',
        count: props.contact.emails_count ?? 0,
        canView: props.can.view_emails,
        action: () => null,
        href: route('contact_emails.index', props.contact.ulid),
    },
    {
        key: 'urls',
        label: 'Websites',
        count: props.contact.urls_count ?? 0,
        canView: props.can.view_urls,
        action: () => null,
        href: route('contact_urls.index', props.contact.ulid),
    },
    {
        key: 'dates',
        label: 'Important dates',
        count: props.contact.dates_count ?? 0,
        canView: props.can.view_dates,
        action: () => null,
        href: route('contact_dates.index', props.contact.ulid),
    },
    {
        key: 'notes',
        label: 'Notes',
        count: props.contact.notes_count ?? 0,
        canView: props.can.view_notes,
        action: () => null,
        href: route('contact_notes.index', props.contact.ulid),
    },
    {
        key: 'calls',
        label: 'Calls',
        count: props.contact.calls_count ?? 0,
        canView: props.can.view_calls,
        action: () => null,
        href: route('contact_calls.index', props.contact.ulid),
    },
    {
        key: 'gift_ideas',
        label: 'Gift ideas',
        count: props.contact.gift_ideas_count ?? 0,
        canView: props.can.view_gift_ideas,
        action: () => null,
        href: route('gift_ideas.index', props.contact.ulid),
    },
]);
</script>

<template>
    <AppLayout :title="contact.fullname">
        <Head :title="contact.fullname" />

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">{{ contact.fullname }}</h2>
                <div class="flex gap-2 text-sm">
                    <Link
                        v-if="can.edit"
                        :href="route('contacts.edit', contact.ulid)"
                        class="text-indigo-600 hover:text-indigo-500"
                    >
                        Edit
                    </Link>
                    <Link
                        v-if="can.edit"
                        :href="route('contacts.image', contact.ulid)"
                        class="text-indigo-600 hover:text-indigo-500"
                    >
                        Image
                    </Link>
                    <Link
                        v-if="can.delete"
                        :href="route('contacts.delete', contact.ulid)"
                        class="text-red-600 hover:text-red-500"
                    >
                        Delete
                    </Link>
                </div>
            </div>

            <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div v-if="imageUrl" class="flex justify-center md:justify-start">
                    <img :src="imageUrl" :alt="contact.fullname" class="rounded-lg w-32 h-32 object-cover" />
                </div>

                <dl class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    <div v-for="field in detailFields" :key="field.label" v-show="field.value">
                        <dt class="font-medium text-gray-700">{{ field.label }}</dt>
                        <dd class="text-gray-900">{{ field.value }}</dd>
                    </div>
                </dl>
            </div>

            <div v-if="contact.note" class="px-6 py-4 border-t border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900 mb-1">Notes</h3>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ contact.note }}</p>
            </div>

            <div v-if="contact.first_met" class="px-6 py-4 border-t border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900 mb-1">First met</h3>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ contact.first_met }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <template v-for="tile in tiles" :key="tile.key">
                <button
                    v-if="tile.canView && tile.action !== null && !tile.href"
                    type="button"
                    class="bg-white shadow rounded-lg p-4 text-left hover:ring-2 hover:ring-indigo-500 hover:bg-indigo-50 transition"
                    @click="tile.action"
                >
                    <h3 class="text-sm font-semibold text-gray-900">{{ tile.label }}</h3>
                    <p class="mt-1 text-sm text-indigo-600">
                        Manage ({{ tile.count }})
                    </p>
                </button>
                <Link
                    v-else-if="tile.canView && tile.href"
                    :href="tile.href"
                    class="bg-white shadow rounded-lg p-4 hover:ring-2 hover:ring-indigo-500 hover:bg-indigo-50 transition"
                >
                    <h3 class="text-sm font-semibold text-gray-900">{{ tile.label }}</h3>
                    <p class="mt-1 text-sm text-indigo-600">
                        Manage ({{ tile.count }})
                    </p>
                </Link>
            </template>
        </div>

        <PhoneNumbersSection
            :open="activeSection === 'numbers'"
            :contact="contact"
            :items="numbers"
            :can="{
                create: can.create_numbers,
                edit: can.edit_numbers,
                delete: can.delete_numbers,
            }"
            @close="closeSection"
        />
    </AppLayout>
</template>
