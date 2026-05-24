<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    activities: { type: Array, default: () => [] },
});
</script>

<template>
    <AppLayout title="Activities">
        <Head title="Activities" />

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Activities</h2>
            </div>

            <div v-if="activities.length === 0" class="px-6 py-8 text-center text-sm text-gray-500">
                No activities yet.
            </div>

            <ul v-else class="divide-y divide-gray-200">
                <li v-for="activity in activities" :key="activity.id" class="px-6 py-3 text-sm">
                    <div class="text-gray-900">
                        <span class="font-medium">{{ activity.user ? activity.user.name : 'Unknown user' }}</span>
                        <span class="text-gray-600"> {{ activity.action }} </span>
                        <span v-if="activity.object && activity.object.fullname" class="font-medium">{{ activity.object.fullname }}</span>
                        <span class="text-gray-500"> &middot; {{ activity.created_at }}</span>
                    </div>
                    <pre v-if="activity.body" class="mt-1 text-xs bg-gray-50 p-2 rounded overflow-x-auto">{{ activity.body }}</pre>
                </li>
            </ul>
        </div>
    </AppLayout>
</template>
