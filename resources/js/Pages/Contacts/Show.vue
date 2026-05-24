<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    contact: { type: Object, required: true },
    can: { type: Object, default: () => ({}) },
});

const page = usePage();
const imageUrl = computed(() => (props.contact.image ? `/storage/${props.contact.image}` : null));

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
                        :href="route('contacts.edit', contact.slug)"
                        class="text-indigo-600 hover:text-indigo-500"
                    >
                        Edit
                    </Link>
                    <Link
                        v-if="can.edit"
                        :href="route('contacts.image', contact.slug)"
                        class="text-indigo-600 hover:text-indigo-500"
                    >
                        Image
                    </Link>
                    <Link
                        v-if="can.delete"
                        :href="route('contacts.delete', contact.slug)"
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
            <div v-if="can.view_addresses" class="bg-white shadow rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-2">Addresses</h3>
                <Link :href="route('contact_addresses.index', contact.slug)" class="text-sm text-indigo-600 hover:text-indigo-500">
                    Manage addresses ({{ contact.addresses_count ?? 0 }})
                </Link>
            </div>
            <div v-if="can.view_numbers" class="bg-white shadow rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-2">Phone numbers</h3>
                <Link :href="route('contact_numbers.index', contact.slug)" class="text-sm text-indigo-600 hover:text-indigo-500">
                    Manage phone numbers ({{ contact.numbers_count ?? 0 }})
                </Link>
            </div>
            <div v-if="can.view_emails" class="bg-white shadow rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-2">Emails</h3>
                <Link :href="route('contact_emails.index', contact.slug)" class="text-sm text-indigo-600 hover:text-indigo-500">
                    Manage emails ({{ contact.emails_count ?? 0 }})
                </Link>
            </div>
            <div v-if="can.view_urls" class="bg-white shadow rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-2">Websites</h3>
                <Link :href="route('contact_urls.index', contact.slug)" class="text-sm text-indigo-600 hover:text-indigo-500">
                    Manage websites ({{ contact.urls_count ?? 0 }})
                </Link>
            </div>
            <div v-if="can.view_dates" class="bg-white shadow rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-2">Important dates</h3>
                <Link :href="route('contact_dates.index', contact.slug)" class="text-sm text-indigo-600 hover:text-indigo-500">
                    Manage dates ({{ contact.dates_count ?? 0 }})
                </Link>
            </div>
            <div v-if="can.view_notes" class="bg-white shadow rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-2">Notes</h3>
                <Link :href="route('contact_notes.index', contact.slug)" class="text-sm text-indigo-600 hover:text-indigo-500">
                    Manage notes ({{ contact.notes_count ?? 0 }})
                </Link>
            </div>
            <div v-if="can.view_calls" class="bg-white shadow rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-2">Calls</h3>
                <Link :href="route('contact_calls.index', contact.slug)" class="text-sm text-indigo-600 hover:text-indigo-500">
                    Manage calls ({{ contact.calls_count ?? 0 }})
                </Link>
            </div>
            <div v-if="can.view_gift_ideas" class="bg-white shadow rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-2">Gift ideas</h3>
                <Link :href="route('gift_ideas.index', contact.slug)" class="text-sm text-indigo-600 hover:text-indigo-500">
                    Manage gift ideas ({{ contact.gift_ideas_count ?? 0 }})
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
