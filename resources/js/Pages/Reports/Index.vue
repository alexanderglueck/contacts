<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    reports: { type: Array, default: () => [] },
});
</script>

<template>
    <AppLayout title="Reports">
        <Head title="Reports" />

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Reports</h2>
            </div>

            <div v-if="reports.length === 0" class="px-6 py-8 text-center text-sm text-gray-500">
                No reports available.
            </div>

            <ul v-else class="divide-y divide-gray-200">
                <li v-for="report in reports" :key="report.key">
                    <Link
                        :href="route(`reports.${report.key}`)"
                        class="block px-6 py-3 hover:bg-gray-50 text-sm text-gray-900"
                    >
                        {{ report.name }}
                    </Link>
                </li>
            </ul>
        </div>
    </AppLayout>
</template>
