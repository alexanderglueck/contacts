<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageJumper from '@/Components/PageJumper.vue';

const { t } = useI18n();

defineProps({
    activities: { type: Object, required: true },
});
</script>

<template>
    <AppLayout :title="t('activities.title')">
        <Head :title="t('activities.title')" />

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">{{ t('activities.title') }}</h2>
            </div>

            <div v-if="activities.data.length === 0" class="px-6 py-8 text-center text-sm text-gray-500">
                {{ t('activities.none') }}
            </div>

            <ul v-else class="divide-y divide-gray-200">
                <li v-for="activity in activities.data" :key="activity.id" class="px-6 py-3 text-sm">
                    <div class="text-gray-900">
                        <span class="font-medium">{{ activity.user ? activity.user.name : t('activities.unknown_user') }}</span>
                        <span class="text-gray-600"> {{ activity.action }} </span>
                        <span v-if="activity.object && activity.object.fullname" class="font-medium">{{ activity.object.fullname }}</span>
                        <span class="text-gray-500"> &middot; {{ activity.created_at }}</span>
                    </div>
                    <pre v-if="activity.body" class="mt-1 text-xs bg-gray-50 p-2 rounded overflow-x-auto">{{ activity.body }}</pre>
                </li>
            </ul>

            <div v-if="activities.total > 0" class="border-t border-gray-200 px-6 py-3 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                <p class="text-sm text-gray-600">
                    {{ t(activities.total === 1 ? 'activities.showing_one' : 'activities.showing_many', {
                        from: activities.from, to: activities.to, total: activities.total,
                    }) }}
                </p>
                <div v-if="activities.last_page > 1" class="flex flex-wrap items-center gap-2">
                    <PageJumper :current-page="activities.current_page" :last-page="activities.last_page" />
                    <div class="flex gap-1">
                        <template v-for="link in activities.links" :key="link.label">
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
