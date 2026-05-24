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
    <AppLayout :title="`Call ${item.formatted_called_at} — ${contact.fullname}`">
        <Head :title="`Call ${item.formatted_called_at} — ${contact.fullname}`" />

        <div class="text-sm">
            <Link :href="route('contact_calls.index', contact.slug)" class="text-indigo-600 hover:text-indigo-500">
                &larr; Back to calls
            </Link>
        </div>

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">Call on {{ item.formatted_called_at }}</h2>
                <div class="flex gap-2 text-sm">
                    <Link
                        v-if="can.edit"
                        :href="route('contact_calls.edit', [contact.slug, item.id])"
                        class="text-indigo-600 hover:text-indigo-500"
                    >
                        Edit
                    </Link>
                    <Link
                        v-if="can.delete"
                        :href="route('contact_calls.delete', [contact.slug, item.id])"
                        class="text-red-600 hover:text-red-500"
                    >
                        Delete
                    </Link>
                </div>
            </div>

            <dl class="px-6 py-4 space-y-3 text-sm">
                <div>
                    <dt class="font-medium text-gray-700">Called at</dt>
                    <dd class="text-gray-900">{{ item.formatted_called_at }}</dd>
                </div>
                <div v-if="item.note">
                    <dt class="font-medium text-gray-700">Note</dt>
                    <dd class="text-gray-900 whitespace-pre-line">{{ item.note }}</dd>
                </div>
            </dl>
        </div>
    </AppLayout>
</template>
