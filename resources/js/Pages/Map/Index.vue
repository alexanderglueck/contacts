<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    googleMapsKey: { type: String, default: '' },
});
</script>

<template>
    <AppLayout title="Map">
        <Head title="Map" />

        <div class="bg-white shadow rounded-lg">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-medium text-gray-900">Contacts on the map</h2>
            </div>

            <div class="px-6 py-8 text-center text-sm text-gray-600">
                <p v-if="!googleMapsKey" class="mb-2">
                    Set <code class="font-mono text-xs bg-gray-100 px-1 rounded">CONTACTS_GOOGLE_MAPS_KEY</code>
                    in your .env to enable the map view.
                </p>
                <p>
                    The map widget will be wired up in a follow-up. The
                    <code class="font-mono text-xs bg-gray-100 px-1 rounded">POST /map/contacts</code>
                    JSON endpoint is already updated for the bounded-box query.
                </p>
                <p class="mt-4">
                    <Link :href="route('contacts.index')" class="text-indigo-600 hover:text-indigo-500 underline">
                        View contacts as a list instead
                    </Link>
                </p>
            </div>
        </div>
    </AppLayout>
</template>
