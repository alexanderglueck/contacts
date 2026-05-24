<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const { t } = useI18n();

const props = defineProps({
    role: { type: Object, required: true },
    deletion_blocked_reason: { type: String, default: null },
});

const form = useForm({});

const submit = () => form.delete(route('roles.destroy', props.role.ulid));
</script>

<template>
    <AppLayout :title="t('roles.delete_q', { name: role.name })">
        <Head :title="t('roles.delete_q', { name: role.name })" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">{{ t('roles.delete_q', { name: role.name }) }}</h2>
            </div>

            <div class="px-6 py-4 text-sm text-gray-700 space-y-3">
                <p>{{ t('roles.delete_body', { name: role.name }) }}</p>
                <div
                    v-if="deletion_blocked_reason"
                    class="rounded border border-amber-200 bg-amber-50 text-amber-800 px-3 py-2"
                >
                    {{ deletion_blocked_reason }}
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
                <Link :href="route('roles.show', role.ulid)">
                    <SecondaryButton type="button">{{ t('common.cancel') }}</SecondaryButton>
                </Link>
                <DangerButton
                    type="submit"
                    :disabled="form.processing || !!deletion_blocked_reason"
                    :class="{ 'opacity-50': form.processing || !!deletion_blocked_reason }"
                >
                    {{ t('roles.delete') }}
                </DangerButton>
            </div>
        </form>
    </AppLayout>
</template>
