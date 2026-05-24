<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

defineProps({
    active: { type: Object, required: true },
    inactive: { type: Object, required: true },
    displayed: { type: Object, required: true },
    hidden: { type: Object, required: true },
    read: { type: Object, required: true },
    unread: { type: Object, required: true },
    canCreate: { type: Boolean, default: false },
});

const sections = [
    { title: 'Active', key: 'active' },
    { title: 'Expired', key: 'inactive' },
    { title: 'Relevant', key: 'displayed' },
    { title: 'Irrelevant', key: 'hidden' },
    { title: 'Read', key: 'read' },
    { title: 'Unread', key: 'unread' },
];
</script>

<template>
    <AppLayout title="Announcements">
        <Head title="Announcements" />

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">Announcements</h2>
                <Link v-if="canCreate" :href="route('announcements.create')">
                    <PrimaryButton type="button">Create announcement</PrimaryButton>
                </Link>
            </div>
        </div>

        <div v-for="section in sections" :key="section.key" class="bg-white shadow rounded-lg">
            <div class="px-6 py-3 border-b border-gray-200">
                <h3 class="text-base font-medium text-gray-900">{{ section.title }}</h3>
            </div>
            <div v-if="$props[section.key].data.length === 0" class="px-6 py-6 text-center text-sm text-gray-500">
                No announcements.
            </div>
            <ul v-else class="divide-y divide-gray-200">
                <li v-for="announcement in $props[section.key].data" :key="announcement.id">
                    <Link
                        :href="route('announcements.show', announcement.slug)"
                        class="block px-6 py-3 hover:bg-gray-50 text-sm text-gray-900 flex justify-between items-center"
                    >
                        <span>{{ announcement.title }}</span>
                        <span v-if="announcement.is_pinned" class="text-xs text-indigo-600">Pinned</span>
                    </Link>
                </li>
            </ul>
        </div>
    </AppLayout>
</template>
