<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import PageJumper from '@/Components/PageJumper.vue';

const { t } = useI18n();

defineProps({
    roles: { type: Object, required: true },
    canCreate: { type: Boolean, default: false },
});
</script>

<template>
    <AppLayout :title="t('roles.title')">
        <Head :title="t('roles.title')" />

        <div class="bg-white shadow rounded-lg">
            <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">{{ t('roles.title') }}</h2>
                <Link v-if="canCreate" :href="route('roles.create')">
                    <PrimaryButton type="button">{{ t('roles.create') }}</PrimaryButton>
                </Link>
            </div>

            <div v-if="roles.data.length === 0" class="px-6 py-8 text-center text-sm text-gray-500">
                {{ t('roles.none') }}
            </div>

            <ul v-else class="divide-y divide-gray-200">
                <li v-for="role in roles.data" :key="role.id">
                    <Link
                        :href="route('roles.show', role.ulid)"
                        class="block px-6 py-3 hover:bg-gray-50 text-sm text-gray-900"
                    >
                        {{ role.name }}
                    </Link>
                </li>
            </ul>

            <div v-if="roles.total > 0" class="border-t border-gray-200 px-6 py-3 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                <p class="text-sm text-gray-600">
                    {{ t(roles.total === 1 ? 'roles.showing_one' : 'roles.showing_many', {
                        from: roles.from, to: roles.to, total: roles.total,
                    }) }}
                </p>
                <div v-if="roles.last_page > 1" class="flex flex-wrap items-center gap-2">
                    <PageJumper :current-page="roles.current_page" :last-page="roles.last_page" />
                    <div class="flex gap-1">
                        <template v-for="link in roles.links" :key="link.label">
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
