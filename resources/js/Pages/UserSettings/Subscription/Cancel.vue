<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import SettingsLayout from '@/Layouts/SettingsLayout.vue';
import DangerButton from '@/Components/DangerButton.vue';

const { t } = useI18n();

const form = useForm({});

const submit = () => form.post(route('user_settings.subscription.cancel.store'));
</script>

<template>
    <SettingsLayout :title="t('settings.subscription.cancel_title')">
        <Head :title="t('settings.subscription.cancel_title')" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">{{ t('settings.subscription.cancel_title') }}</h2>
            </div>

            <div class="px-6 py-4">
                <p class="text-sm text-gray-600">{{ t('settings.subscription.cancel_body') }}</p>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                <DangerButton :disabled="form.processing" :class="{ 'opacity-50': form.processing }">
                    {{ t('settings.subscription.cancel_action') }}
                </DangerButton>
            </div>
        </form>
    </SettingsLayout>
</template>
