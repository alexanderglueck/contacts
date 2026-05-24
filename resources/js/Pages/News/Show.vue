<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    news: { type: Object, required: true },
    can: { type: Object, default: () => ({}) },
    isAuthenticated: { type: Boolean, default: false },
});
</script>

<template>
    <AppLayout :title="news.title">
        <Head :title="news.title" />

        <div class="text-sm">
            <Link :href="route('news.index')" class="text-indigo-600 hover:text-indigo-500">
                &larr; Back to news
            </Link>
        </div>

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">{{ news.title }}</h2>
                <div class="flex gap-3 text-sm">
                    <Link
                        v-if="isAuthenticated"
                        :href="route('news.mark_as_read', news.slug)"
                        class="text-indigo-600 hover:text-indigo-500"
                    >
                        Mark as read
                    </Link>
                    <Link
                        v-if="can.edit"
                        :href="route('news.edit', news.slug)"
                        class="text-indigo-600 hover:text-indigo-500"
                    >
                        Edit
                    </Link>
                    <Link
                        v-if="can.delete"
                        :href="route('news.delete', news.slug)"
                        class="text-red-600 hover:text-red-500"
                    >
                        Delete
                    </Link>
                </div>
            </div>

            <div class="px-6 py-4 text-sm text-gray-900">
                <div class="prose max-w-none" v-html="news.parsed_body" />
            </div>
        </div>
    </AppLayout>
</template>
