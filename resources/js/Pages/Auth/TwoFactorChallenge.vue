<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const { t } = useI18n();
const useRecovery = ref(false);

const form = useForm({
    code: '',
    recovery_code: '',
});

const submit = () => {
    form.post(route('two-factor.login.store'), {
        onFinish: () => form.reset('code', 'recovery_code'),
    });
};

const toggleRecovery = () => {
    useRecovery.value = !useRecovery.value;
    form.reset('code', 'recovery_code');
};
</script>

<template>
    <GuestLayout>
        <Head :title="t('auth.two_factor_title')" />

        <h1 class="text-xl font-semibold text-gray-900 mb-2">{{ t('auth.two_factor_title') }}</h1>
        <p class="text-sm text-gray-600 mb-4">
            <template v-if="useRecovery">
                {{ t('auth.two_factor_recovery_help') }}
            </template>
            <template v-else>
                {{ t('auth.two_factor_code_help') }}
            </template>
        </p>

        <form @submit.prevent="submit" class="space-y-4">
            <div v-if="!useRecovery">
                <InputLabel for="code" :value="t('auth.code')" />
                <TextInput
                    id="code"
                    v-model="form.code"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    autofocus
                    required
                />
                <InputError :message="form.errors.code" />
            </div>

            <div v-else>
                <InputLabel for="recovery_code" :value="t('auth.recovery_code')" />
                <TextInput
                    id="recovery_code"
                    v-model="form.recovery_code"
                    autocomplete="one-time-code"
                    autofocus
                    required
                />
                <InputError :message="form.errors.recovery_code" />
            </div>

            <div class="flex items-center justify-between">
                <button type="button" @click="toggleRecovery" class="text-sm text-gray-600 hover:text-gray-900 underline">
                    {{ useRecovery ? t('auth.use_auth_code') : t('auth.use_recovery_code') }}
                </button>
                <PrimaryButton :class="{ 'opacity-50': form.processing }" :disabled="form.processing">
                    {{ t('common.confirm') }}
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
