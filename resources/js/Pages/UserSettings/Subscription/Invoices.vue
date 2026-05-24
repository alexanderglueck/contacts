<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    invoices: { type: Array, default: () => [] },
});
</script>

<template>
    <AppLayout title="Invoices">
        <Head title="Invoices" />

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Invoices</h2>
            </div>

            <div v-if="invoices.length === 0" class="px-6 py-4 text-sm text-gray-600">
                No invoices yet.
            </div>

            <table v-else class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="invoice in invoices" :key="invoice.id">
                        <td class="px-6 py-3 text-sm text-gray-700">{{ invoice.date }}</td>
                        <td class="px-6 py-3 text-sm text-gray-700">{{ invoice.total }}</td>
                        <td class="px-6 py-3 text-sm text-right">
                            <a
                                :href="route('user_settings.subscription.invoices.show', invoice.id)"
                                class="text-indigo-600 hover:text-indigo-500 underline"
                            >
                                Download
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
