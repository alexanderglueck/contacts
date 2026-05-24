<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    announcement: { type: Object, required: true },
    can: { type: Object, default: () => ({}) },
});
</script>

<template>
    <AppLayout :title="announcement.title">
        <Head :title="announcement.title" />

        <div class="text-sm">
            <Link :href="route('announcements.index')" class="text-indigo-600 hover:text-indigo-500">
                &larr; Back to announcements
            </Link>
        </div>

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">{{ announcement.title }}</h2>
                <div class="flex gap-3 text-sm">
                    <Link
                        :href="route('announcements.mark_as_read', announcement.ulid)"
                        class="text-indigo-600 hover:text-indigo-500"
                    >
                        Mark as read
                    </Link>
                    <Link
                        v-if="can.edit"
                        :href="route('announcements.edit', announcement.ulid)"
                        class="text-indigo-600 hover:text-indigo-500"
                    >
                        Edit
                    </Link>
                    <Link
                        v-if="can.delete"
                        :href="route('announcements.delete', announcement.ulid)"
                        class="text-red-600 hover:text-red-500"
                    >
                        Delete
                    </Link>
                </div>
            </div>

            <div class="px-6 py-4 text-sm text-gray-900">
                <div class="prose max-w-none" v-html="announcement.parsed_body" />
            </div>
        </div>
    </AppLayout>
</template>
