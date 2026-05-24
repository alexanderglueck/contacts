<script setup>
import { computed, ref } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import PhoneNumbersSection from './Sections/PhoneNumbersSection.vue';
import EmailsSection from './Sections/EmailsSection.vue';
import UrlsSection from './Sections/UrlsSection.vue';
import NotesSection from './Sections/NotesSection.vue';
import CallsSection from './Sections/CallsSection.vue';
import DatesSection from './Sections/DatesSection.vue';
import GiftIdeasSection from './Sections/GiftIdeasSection.vue';
import AddressesSection from './Sections/AddressesSection.vue';
import ActivitySection from './Sections/ActivitySection.vue';
import CommentsSection from './Sections/CommentsSection.vue';

const props = defineProps({
    contact: { type: Object, required: true },
    can: { type: Object, default: () => ({}) },
});

const { t } = useI18n();
const page = usePage();
const imageUrl = computed(() => (props.contact.image ? `/storage/${props.contact.image}` : null));

const numbers = computed(() => page.props.numbers ?? []);
const emails = computed(() => page.props.emails ?? []);
const urls = computed(() => page.props.urls ?? []);
const notes = computed(() => page.props.notes ?? []);
const calls = computed(() => page.props.calls ?? []);
const dates = computed(() => page.props.dates ?? []);
const giftIdeas = computed(() => page.props.gift_ideas ?? []);
const addresses = computed(() => page.props.addresses ?? []);
const countries = computed(() => page.props.countries ?? []);
const activities = computed(() => page.props.activities ?? []);
const comments = computed(() => page.props.comments ?? []);

const activeSection = ref(null);

const sectionDataKey = {
    numbers: ['numbers'],
    emails: ['emails'],
    urls: ['urls'],
    notes: ['notes'],
    calls: ['calls'],
    dates: ['dates'],
    gift_ideas: ['gift_ideas'],
    addresses: ['addresses', 'countries'],
    activities: ['activities'],
    comments: ['comments'],
};

const openSection = (key) => {
    activeSection.value = key;
    const dataKeys = sectionDataKey[key];
    if (dataKeys && dataKeys.length) {
        router.reload({ only: dataKeys });
    }
};
const closeSection = () => { activeSection.value = null; };

const detailFields = computed(() => [
    { label: t('contacts.fields.name'), value: props.contact.fullname },
    { label: t('contacts.fields.gender'), value: props.contact.gender?.gender },
    { label: t('contacts.fields.company'), value: props.contact.company },
    { label: t('contacts.fields.job'), value: props.contact.job },
    { label: t('contacts.fields.department'), value: props.contact.department },
    { label: t('contacts.fields.salutation'), value: props.contact.salutation },
    { label: t('contacts.fields.date_of_birth'), value: props.contact.formatted_date_of_birth },
    { label: t('contacts.fields.nickname'), value: props.contact.nickname },
    { label: t('contacts.fields.iban'), value: props.contact.iban },
    { label: t('contacts.fields.nationality'), value: props.contact.nationality?.country },
    { label: t('contacts.fields.custom_id'), value: props.contact.custom_id },
]);

const tiles = computed(() => [
    { key: 'addresses', label: t('contacts.section.addresses'), count: props.contact.addresses_count ?? 0, canView: props.can.view_addresses, action: () => openSection('addresses') },
    { key: 'numbers', label: t('contacts.section.numbers'), count: props.contact.numbers_count ?? 0, canView: props.can.view_numbers, action: () => openSection('numbers') },
    { key: 'emails', label: t('contacts.section.emails'), count: props.contact.emails_count ?? 0, canView: props.can.view_emails, action: () => openSection('emails') },
    { key: 'urls', label: t('contacts.section.urls'), count: props.contact.urls_count ?? 0, canView: props.can.view_urls, action: () => openSection('urls') },
    { key: 'dates', label: t('contacts.section.dates'), count: props.contact.dates_count ?? 0, canView: props.can.view_dates, action: () => openSection('dates') },
    { key: 'notes', label: t('contacts.section.notes'), count: props.contact.notes_count ?? 0, canView: props.can.view_notes, action: () => openSection('notes') },
    { key: 'calls', label: t('contacts.section.calls'), count: props.contact.calls_count ?? 0, canView: props.can.view_calls, action: () => openSection('calls') },
    { key: 'gift_ideas', label: t('contacts.section.gift_ideas'), count: props.contact.gift_ideas_count ?? 0, canView: props.can.view_gift_ideas, action: () => openSection('gift_ideas') },
    { key: 'comments', label: t('contacts.section.comments'), count: props.contact.comments_count ?? 0, canView: props.can.view_comments, action: () => openSection('comments') },
    { key: 'activities', label: t('contacts.section.activities'), count: null, canView: props.can.view_activities, action: () => openSection('activities') },
]);
</script>

<template>
    <AppLayout :title="contact.fullname">
        <Head :title="contact.fullname" />

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">{{ contact.fullname }}</h2>
                <div class="flex gap-2 text-sm">
                    <Link v-if="can.edit" :href="route('contacts.edit', contact.ulid)" class="text-indigo-600 hover:text-indigo-500">
                        {{ t('common.edit') }}
                    </Link>
                    <Link v-if="can.edit" :href="route('contacts.image', contact.ulid)" class="text-indigo-600 hover:text-indigo-500">
                        {{ t('contacts.image') }}
                    </Link>
                    <Link v-if="can.delete" :href="route('contacts.delete', contact.ulid)" class="text-red-600 hover:text-red-500">
                        {{ t('common.delete') }}
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
                <h3 class="text-sm font-semibold text-gray-900 mb-1">{{ t('contacts.section.general_notes') }}</h3>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ contact.note }}</p>
            </div>

            <div v-if="contact.first_met" class="px-6 py-4 border-t border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900 mb-1">{{ t('contacts.section.first_met') }}</h3>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ contact.first_met }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <button
                v-for="tile in tiles"
                v-show="tile.canView"
                :key="tile.key"
                type="button"
                class="bg-white shadow rounded-lg p-4 text-left cursor-pointer hover:ring-2 hover:ring-indigo-500 hover:bg-indigo-50 transition"
                @click="tile.action"
            >
                <h3 class="text-sm font-semibold text-gray-900">{{ tile.label }}</h3>
                <p class="mt-1 text-sm text-indigo-600">
                    <template v-if="tile.count !== null">{{ t('contacts.tile_manage', { count: tile.count }) }}</template>
                    <template v-else>{{ t('contacts.tile_view') }}</template>
                </p>
            </button>
        </div>

        <PhoneNumbersSection :open="activeSection === 'numbers'" :contact="contact" :items="numbers" :can="{ create: can.create_numbers, edit: can.edit_numbers, delete: can.delete_numbers }" @close="closeSection" />
        <EmailsSection :open="activeSection === 'emails'" :contact="contact" :items="emails" :can="{ create: can.create_emails, edit: can.edit_emails, delete: can.delete_emails }" @close="closeSection" />
        <UrlsSection :open="activeSection === 'urls'" :contact="contact" :items="urls" :can="{ create: can.create_urls, edit: can.edit_urls, delete: can.delete_urls }" @close="closeSection" />
        <NotesSection :open="activeSection === 'notes'" :contact="contact" :items="notes" :can="{ create: can.create_notes, edit: can.edit_notes, delete: can.delete_notes }" @close="closeSection" />
        <CallsSection :open="activeSection === 'calls'" :contact="contact" :items="calls" :can="{ create: can.create_calls, edit: can.edit_calls, delete: can.delete_calls }" @close="closeSection" />
        <DatesSection :open="activeSection === 'dates'" :contact="contact" :items="dates" :can="{ create: can.create_dates, edit: can.edit_dates, delete: can.delete_dates }" @close="closeSection" />
        <GiftIdeasSection :open="activeSection === 'gift_ideas'" :contact="contact" :items="giftIdeas" :can="{ create: can.create_gift_ideas, edit: can.edit_gift_ideas, delete: can.delete_gift_ideas }" @close="closeSection" />
        <AddressesSection :open="activeSection === 'addresses'" :contact="contact" :items="addresses" :countries="countries" :can="{ create: can.create_addresses, edit: can.edit_addresses, delete: can.delete_addresses }" @close="closeSection" />
        <ActivitySection :open="activeSection === 'activities'" :contact="contact" :items="activities" @close="closeSection" />
        <CommentsSection :open="activeSection === 'comments'" :contact="contact" :items="comments" :can="{ create: can.create_comments, edit: can.edit_comments, delete: can.delete_comments }" @close="closeSection" />
    </AppLayout>
</template>
