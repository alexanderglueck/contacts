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
    <AppLayout :title="`${contact.fullname} — Calls`">
        <Head :title="`${contact.fullname} — Calls`" />

        <div class="text-sm">
            <Link :href="route('contacts.show', contact.ulid)" class="text-indigo-600 hover:text-indigo-500">
                &larr; Back to {{ contact.fullname }}
            </Link>
        </div>

        <div class="bg-white shadow rounded-lg">
            <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">Calls</h2>
                <Link v-if="canCreate" :href="route('contact_calls.create', contact.ulid)">
                    <PrimaryButton type="button">Add call</PrimaryButton>
                </Link>
            </div>

            <div v-if="items.length === 0" class="px-6 py-8 text-center text-sm text-gray-500">
                No calls yet.
            </div>

            <ul v-else class="divide-y divide-gray-200">
                <li v-for="item in items" :key="item.ulid">
                    <Link
                        :href="route('contact_calls.show', [contact.ulid, item.ulid])"
                        class="block px-6 py-3 hover:bg-gray-50"
                    >
                        <div class="text-sm font-medium text-gray-900">{{ item.formatted_called_at }}</div>
                        <div v-if="item.note" class="text-sm text-gray-600 line-clamp-2 whitespace-pre-line">{{ item.note }}</div>
                    </Link>
                </li>
            </ul>
        </div>
    </AppLayout>
</template>
