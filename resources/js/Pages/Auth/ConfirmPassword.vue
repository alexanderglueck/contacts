<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const { t } = useI18n();

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm.store'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout>
        <Head :title="t('auth.confirm_password_title')" />

        <h1 class="text-xl font-semibold text-gray-900 mb-2">{{ t('auth.confirm_password_title') }}</h1>
        <p class="text-sm text-gray-600 mb-4">
            {{ t('auth.confirm_password_help') }}
        </p>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="password" :value="t('auth.password')" />
                <TextInput
                    id="password"
                    type="password"
                    v-model="form.password"
                    autocomplete="current-password"
                    autofocus
                    required
                />
                <InputError :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-end">
                <PrimaryButton :class="{ 'opacity-50': form.processing }" :disabled="form.processing">
                    {{ t('common.confirm') }}
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
