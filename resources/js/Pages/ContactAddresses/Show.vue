<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    contact: { type: Object, required: true },
    item: { type: Object, required: true },
    can: { type: Object, default: () => ({}) },
});
</script>

<template>
    <AppLayout :title="`${item.name} — ${contact.fullname}`">
        <Head :title="`${item.name} — ${contact.fullname}`" />

        <div class="bg-white shadow rounded-lg">
            <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                <div>
                    <Link :href="route('contact_addresses.index', contact.slug)" class="text-sm text-indigo-600 hover:text-indigo-500">
                        ← Addresses
                    </Link>
                    <h2 class="text-lg font-medium text-gray-900">{{ item.name }}</h2>
                </div>
                <div class="flex gap-2 text-sm">
                    <Link
                        v-if="can.edit"
                        :href="route('contact_addresses.edit', [contact.slug, item.slug])"
                        class="text-indigo-600 hover:text-indigo-500"
                    >
                        Edit
                    </Link>
                    <Link
                        v-if="can.delete"
                        :href="route('contact_addresses.delete', [contact.slug, item.slug])"
                        class="text-red-600 hover:text-red-500"
                    >
                        Delete
                    </Link>
                </div>
            </div>

            <dl class="px-6 py-4 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                <div>
                    <dt class="font-medium text-gray-700">Street</dt>
                    <dd class="text-gray-900">{{ item.street }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-700">ZIP / city</dt>
                    <dd class="text-gray-900">{{ item.zip }} {{ item.city }}</dd>
                </div>
                <div v-if="item.state">
                    <dt class="font-medium text-gray-700">State</dt>
                    <dd class="text-gray-900">{{ item.state }}</dd>
                </div>
                <div v-if="item.country">
                    <dt class="font-medium text-gray-700">Country</dt>
                    <dd class="text-gray-900">{{ item.country }}</dd>
                </div>
                <div v-if="item.latitude || item.longitude">
                    <dt class="font-medium text-gray-700">Coordinates</dt>
                    <dd class="text-gray-900">{{ item.latitude }}, {{ item.longitude }}</dd>
                </div>
            </dl>
        </div>
    </AppLayout>
</template>
