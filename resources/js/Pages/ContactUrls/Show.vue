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

        <div class="text-sm">
            <Link :href="route('contact_urls.index', contact.ulid)" class="text-indigo-600 hover:text-indigo-500">
                &larr; Back to websites
            </Link>
        </div>

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">{{ item.name }}</h2>
                <div class="flex gap-2 text-sm">
                    <Link
                        v-if="can.edit"
                        :href="route('contact_urls.edit', [contact.ulid, item.ulid])"
                        class="text-indigo-600 hover:text-indigo-500"
                    >
                        Edit
                    </Link>
                    <Link
                        v-if="can.delete"
                        :href="route('contact_urls.delete', [contact.ulid, item.ulid])"
                        class="text-red-600 hover:text-red-500"
                    >
                        Delete
                    </Link>
                </div>
            </div>

            <dl class="px-6 py-4 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                <div>
                    <dt class="font-medium text-gray-700">Name</dt>
                    <dd class="text-gray-900">{{ item.name }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-700">URL</dt>
                    <dd class="text-gray-900 break-all">
                        <a :href="item.url" target="_blank" rel="noopener" class="text-indigo-600 hover:text-indigo-500">
                            {{ item.url }}
                        </a>
                    </dd>
                </div>
            </dl>
        </div>
    </AppLayout>
</template>
