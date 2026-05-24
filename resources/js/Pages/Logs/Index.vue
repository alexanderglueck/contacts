<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageJumper from '@/Components/PageJumper.vue';

const { t } = useI18n();

defineProps({
    logs: { type: Object, required: true },
});
</script>

<template>
    <AppLayout :title="t('logs.title')">
        <Head :title="t('logs.title')" />

        <div class="bg-white shadow rounded-lg">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-medium text-gray-900">{{ t('logs.heading') }}</h2>
            </div>

            <div v-if="logs.data.length === 0" class="px-6 py-8 text-center text-sm text-gray-500">
                {{ t('logs.none') }}
            </div>

            <table v-else class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-2 text-left text-xs font-semibold uppercase text-gray-500">{{ t('logs.event') }}</th>
                        <th class="px-6 py-2 text-left text-xs font-semibold uppercase text-gray-500">{{ t('logs.ip') }}</th>
                        <th class="px-6 py-2 text-left text-xs font-semibold uppercase text-gray-500">{{ t('logs.recorded_at') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr v-for="log in logs.data" :key="log.id">
                        <td class="px-6 py-2 text-sm text-gray-900">{{ log.event_label }}</td>
                        <td class="px-6 py-2 text-sm text-gray-700">{{ log.ip_address }}</td>
                        <td class="px-6 py-2 text-sm text-gray-700">{{ log.created_at }}</td>
                    </tr>
                </tbody>
            </table>

            <div v-if="logs.total > 0" class="border-t border-gray-200 px-6 py-3 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                <p class="text-sm text-gray-600">
                    {{ t(logs.total === 1 ? 'logs.showing_one' : 'logs.showing_many', {
                        from: logs.from, to: logs.to, total: logs.total,
                    }) }}
                </p>
                <div v-if="logs.last_page > 1" class="flex flex-wrap items-center gap-2">
                    <PageJumper :current-page="logs.current_page" :last-page="logs.last_page" />
                    <div class="flex gap-1">
                        <template v-for="link in logs.links" :key="link.label">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                class="inline-flex items-center px-3 py-1 rounded text-sm"
                                :class="link.active
                                    ? 'bg-indigo-600 text-white'
                                    : 'bg-white text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50'"
                                v-html="link.label"
                            />
                            <span
                                v-else
                                class="inline-flex items-center px-3 py-1 rounded text-sm text-gray-400"
                                v-html="link.label"
                            />
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
