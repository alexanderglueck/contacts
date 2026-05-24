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
    <AppLayout :title="`Addresses — ${contact.fullname}`">
        <Head :title="`Addresses — ${contact.fullname}`" />

        <div class="bg-white shadow rounded-lg">
            <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                <div>
                    <Link :href="route('contacts.show', contact.ulid)" class="text-sm text-indigo-600 hover:text-indigo-500">
                        ← {{ contact.fullname }}
                    </Link>
                    <h2 class="text-lg font-medium text-gray-900">Addresses</h2>
                </div>
                <Link v-if="canCreate" :href="route('contact_addresses.create', contact.ulid)">
                    <PrimaryButton type="button">Add address</PrimaryButton>
                </Link>
            </div>

            <div v-if="items.length === 0" class="px-6 py-8 text-center text-sm text-gray-500">
                No addresses yet.
            </div>

            <ul v-else class="divide-y divide-gray-200">
                <li v-for="item in items" :key="item.id">
                    <Link
                        :href="route('contact_addresses.show', [contact.ulid, item.ulid])"
                        class="block px-6 py-3 hover:bg-gray-50"
                    >
                        <p class="text-sm font-medium text-gray-900">{{ item.name }}</p>
                        <p class="text-sm text-gray-600">
                            {{ item.street }}, {{ item.zip }} {{ item.city }}
                        </p>
                    </Link>
                </li>
            </ul>
        </div>
    </AppLayout>
</template>
