<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import SettingsLayout from '@/Layouts/SettingsLayout.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const { t } = useI18n();

const props = defineProps({
    settings: { type: Object, required: true },
});

const form = useForm({
    send_daily: !!props.settings.send_daily,
    send_weekly: !!props.settings.send_weekly,
    send_daily_push: !!props.settings.send_daily_push,
    send_weekly_push: !!props.settings.send_weekly_push,
});

const submit = () => form.put(route('user_settings.notifications.update'));
</script>

<template>
    <SettingsLayout :title="t('settings.notifications.title')">
        <Head :title="t('settings.notifications.title')" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">{{ t('settings.notifications.title') }}</h2>
                <p class="text-xs text-gray-600 mt-0.5">{{ t('settings.notifications.subtitle') }}</p>
            </div>

            <div class="px-6 py-4 space-y-5">
                <div class="space-y-3">
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                        {{ t('settings.notifications.email_heading') }}
                    </h3>
                    <label class="flex items-center">
                        <Checkbox v-model:checked="form.send_daily" />
                        <span class="ms-2 text-sm text-gray-700">{{ t('settings.notifications.send_daily') }}</span>
                    </label>
                    <InputError :message="form.errors.send_daily" />

                    <label class="flex items-center">
                        <Checkbox v-model:checked="form.send_weekly" />
                        <span class="ms-2 text-sm text-gray-700">{{ t('settings.notifications.send_weekly') }}</span>
                    </label>
                    <InputError :message="form.errors.send_weekly" />
                </div>

                <div class="space-y-3 border-t border-gray-100 pt-4">
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                        {{ t('settings.notifications.push_heading') }}
                    </h3>
                    <label class="flex items-center">
                        <Checkbox v-model:checked="form.send_daily_push" />
                        <span class="ms-2 text-sm text-gray-700">{{ t('settings.notifications.send_daily_push') }}</span>
                    </label>
                    <InputError :message="form.errors.send_daily_push" />

                    <label class="flex items-center">
                        <Checkbox v-model:checked="form.send_weekly_push" />
                        <span class="ms-2 text-sm text-gray-700">{{ t('settings.notifications.send_weekly_push') }}</span>
                    </label>
                    <InputError :message="form.errors.send_weekly_push" />
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                <PrimaryButton :disabled="form.processing" :class="{ 'opacity-50': form.processing }">
                    {{ t('common.update') }}
                </PrimaryButton>
            </div>
        </form>
    </SettingsLayout>
</template>
