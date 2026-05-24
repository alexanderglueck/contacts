<script setup>
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import SettingsLayout from '@/Layouts/SettingsLayout.vue';

const { t } = useI18n();

const props = defineProps({
    intent: { type: Object, required: true },
    stripeKey: { type: String, default: '' },
});

const status = computed(() =>
    props.intent.client_secret ? t('settings.subscription.yes') : t('settings.subscription.no'),
);
</script>

<template>
    <SettingsLayout :title="t('settings.subscription.card_title')">
        <Head :title="t('settings.subscription.card_title')" />

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">{{ t('settings.subscription.card_title') }}</h2>
            </div>

            <div class="px-6 py-4 space-y-3">
                <p class="text-sm text-gray-600">
                    {{ t('settings.subscription.card_todo') }}
                </p>
                <p class="text-xs text-gray-500">
                    {{ t('settings.subscription.card_intent_status', { status }) }}
                </p>
            </div>
        </div>
    </SettingsLayout>
</template>
