<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import RoleFormFields from './Partials/RoleFormFields.vue';

const { t } = useI18n();

const props = defineProps({
    role: { type: Object, required: true },
    permissions: { type: Array, default: () => [] },
    users: { type: Array, default: () => [] },
});

const form = useForm({
    name: props.role.name ?? '',
    permissions: props.role.permissions ?? [],
    users: props.role.users ?? [],
});

const submit = () => form.put(route('roles.update', props.role.ulid));
</script>

<template>
    <AppLayout :title="`${t('roles.edit')} – ${role.name}`">
        <Head :title="`${t('roles.edit')} – ${role.name}`" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">{{ t('roles.edit') }}</h2>
            </div>

            <div class="px-6 py-4">
                <RoleFormFields :form="form" :permissions="permissions" :users="users" />
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
                <Link :href="route('roles.show', role.ulid)">
                    <SecondaryButton type="button">{{ t('common.cancel') }}</SecondaryButton>
                </Link>
                <PrimaryButton :disabled="form.processing" :class="{ 'opacity-50': form.processing }">
                    {{ t('common.save') }}
                </PrimaryButton>
            </div>
        </form>
    </AppLayout>
</template>
