<script setup>
import { computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import SettingsLayout from '@/Layouts/SettingsLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const { t } = useI18n();

const props = defineProps({
    twoFactorEnabled: { type: Boolean, required: true },
    twoFactorConfirmed: { type: Boolean, required: true },
    qrSvg: { type: String, default: null },
    secretKey: { type: String, default: null },
    recoveryCodes: { type: Array, default: () => [] },
});

const stage = computed(() => {
    if (! props.twoFactorEnabled) return 'inactive';
    if (! props.twoFactorConfirmed) return 'setup';
    return 'active';
});

const enableForm = useForm({});
const enable = () => enableForm.post(route('two-factor.enable'), { preserveScroll: true });

const confirmForm = useForm({ code: '' });
const confirm = () => confirmForm.post(route('two-factor.confirm'), {
    preserveScroll: true,
    onSuccess: () => confirmForm.reset(),
});

const disableForm = useForm({});
const disable = () => disableForm.delete(route('two-factor.disable'), { preserveScroll: true });

const regenerateForm = useForm({});
const regenerate = () => regenerateForm.post(route('two-factor.regenerate-recovery-codes'), { preserveScroll: true });
</script>

<template>
    <SettingsLayout :title="t('settings.two_factor.title')">
        <Head :title="t('settings.two_factor.title')" />

        <div v-if="stage === 'inactive'" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">{{ t('settings.two_factor.title') }}</h2>
            </div>
            <form @submit.prevent="enable" class="px-6 py-4 space-y-4">
                <p class="text-sm text-gray-600">
                    {{ t('settings.two_factor.intro') }}
                </p>
                <PrimaryButton :disabled="enableForm.processing" :class="{ 'opacity-50': enableForm.processing }">
                    {{ t('settings.two_factor.enable_2fa') }}
                </PrimaryButton>
            </form>
        </div>

        <div v-else-if="stage === 'setup'" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">{{ t('settings.two_factor.finish_setup_title') }}</h2>
            </div>
            <form @submit.prevent="confirm" class="px-6 py-4 space-y-4">
                <p class="text-sm text-gray-600">
                    {{ t('settings.two_factor.scan_help') }}
                </p>

                <div class="flex justify-center" v-html="qrSvg" />

                <div class="text-center">
                    <p class="text-xs text-gray-500">{{ t('settings.two_factor.secret_help') }}</p>
                    <code class="text-sm break-all">{{ secretKey }}</code>
                </div>

                <div>
                    <InputLabel for="code" :value="t('settings.two_factor.code_label')" />
                    <TextInput
                        id="code"
                        v-model="confirmForm.code"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        autofocus
                        required
                    />
                    <InputError :message="confirmForm.errors.code" />
                </div>

                <div class="flex justify-between">
                    <DangerButton type="button" @click="disable" :disabled="disableForm.processing">
                        {{ t('settings.two_factor.cancel') }}
                    </DangerButton>
                    <PrimaryButton :disabled="confirmForm.processing" :class="{ 'opacity-50': confirmForm.processing }">
                        {{ t('settings.two_factor.activate') }}
                    </PrimaryButton>
                </div>
            </form>
        </div>

        <div v-else class="space-y-4">
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-lg font-medium text-gray-900">{{ t('settings.two_factor.recovery_codes_title') }}</h2>
                    <form @submit.prevent="regenerate">
                        <SecondaryButton :disabled="regenerateForm.processing">
                            {{ t('settings.two_factor.regenerate') }}
                        </SecondaryButton>
                    </form>
                </div>
                <div class="px-6 py-4">
                    <p class="text-sm text-gray-600 mb-3">
                        {{ t('settings.two_factor.recovery_codes_body') }}
                    </p>
                    <ol class="list-decimal list-inside space-y-1 text-sm font-mono text-gray-800">
                        <li v-for="code in recoveryCodes" :key="code">{{ code }}</li>
                    </ol>
                </div>
            </div>

            <form @submit.prevent="disable" class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 flex justify-end">
                    <DangerButton :disabled="disableForm.processing" :class="{ 'opacity-50': disableForm.processing }">
                        {{ t('settings.two_factor.disable_2fa') }}
                    </DangerButton>
                </div>
            </form>
        </div>
    </SettingsLayout>
</template>
