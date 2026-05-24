<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

defineProps({
    contact: { type: Object, required: true },
    items: { type: Array, default: () => [] },
    canCreate: { type: Boolean, default: false },
});
</script>

<template>
    <AppLayout :title="`${contact.fullname} — Emails`">
        <Head :title="`${contact.fullname} — Emails`" />

        <div class="text-sm">
            <Link :href="route('contacts.show', contact.ulid)" class="text-indigo-600 hover:text-indigo-500">
                &larr; Back to {{ contact.fullname }}
            </Link>
        </div>

        <div class="bg-white shadow rounded-lg">
            <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">Emails</h2>
                <Link v-if="canCreate" :href="route('contact_emails.create', contact.ulid)">
                    <PrimaryButton type="button">Add email</PrimaryButton>
                </Link>
            </div>

            <div v-if="items.length === 0" class="px-6 py-8 text-center text-sm text-gray-500">
                No emails yet.
            </div>

            <ul v-else class="divide-y divide-gray-200">
                <li v-for="item in items" :key="item.id">
                    <Link
                        :href="route('contact_emails.show', [contact.ulid, item.ulid])"
                        class="block px-6 py-3 hover:bg-gray-50"
                    >
                        <div class="text-sm font-medium text-gray-900">{{ item.name }}</div>
                        <div class="text-sm text-gray-600">{{ item.email }}</div>
                    </Link>
                </li>
            </ul>
        </div>
    </AppLayout>
</template>
