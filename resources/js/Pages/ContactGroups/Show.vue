<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';

const { t } = useI18n();

defineProps({
    contactGroup: { type: Object, required: true },
    contacts: { type: Array, default: () => [] },
    can: { type: Object, default: () => ({}) },
});
</script>

<template>
    <AppLayout :title="contactGroup.name">
        <Head :title="contactGroup.name" />

        <div class="text-sm">
            <Link :href="route('contact_groups.index')" class="text-indigo-600 hover:text-indigo-500">
                {{ t('contact_groups.back') }}
            </Link>
        </div>

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">{{ contactGroup.name }}</h2>
                <div class="flex gap-2 text-sm">
                    <Link
                        v-if="can.edit"
                        :href="route('contact_groups.edit', contactGroup.ulid)"
                        class="text-indigo-600 hover:text-indigo-500"
                    >
                        {{ t('common.edit') }}
                    </Link>
                    <Link
                        v-if="can.delete"
                        :href="route('contact_groups.delete', contactGroup.ulid)"
                        class="text-red-600 hover:text-red-500"
                    >
                        {{ t('common.delete') }}
                    </Link>
                </div>
            </div>

            <dl class="px-6 py-4 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                <div>
                    <dt class="font-medium text-gray-700">{{ t('contact_groups.fields.name') }}</dt>
                    <dd class="text-gray-900">{{ contactGroup.name }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-700">{{ t('contact_groups.fields.contacts') }}</dt>
                    <dd class="text-gray-900">{{ contacts.length }}</dd>
                </div>
            </dl>
        </div>

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">{{ t('contact_groups.contacts_in_group') }}</h3>
            </div>

            <div v-if="contacts.length === 0" class="px-6 py-8 text-center text-sm text-gray-500">
                {{ t('contact_groups.no_contacts_in_group') }}
            </div>

            <ul v-else class="divide-y divide-gray-200">
                <li v-for="contact in contacts" :key="contact.id">
                    <Link
                        :href="route('contacts.show', contact.ulid)"
                        class="block px-6 py-3 hover:bg-gray-50 text-sm text-gray-900"
                    >
                        {{ contact.fullname }}
                    </Link>
                </li>
            </ul>
        </div>
    </AppLayout>
</template>
