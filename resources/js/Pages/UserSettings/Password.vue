<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import SettingsLayout from '@/Layouts/SettingsLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const { t } = useI18n();

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.put(route('user-password.update'), {
        errorBag: 'updatePassword',
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <SettingsLayout :title="t('settings.password.title')">
        <Head :title="t('settings.password.title')" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">{{ t('settings.password.title') }}</h2>
            </div>

            <div class="px-6 py-4 space-y-4">
                <div>
                    <InputLabel for="current_password" :value="t('settings.password.current_password')" />
                    <TextInput
                        id="current_password"
                        type="password"
                        v-model="form.current_password"
                        autocomplete="current-password"
                        required
                    />
                    <InputError :message="form.errors.current_password" />
                </div>

                <div>
                    <InputLabel for="password" :value="t('settings.password.new_password')" />
                    <TextInput
                        id="password"
                        type="password"
                        v-model="form.password"
                        autocomplete="new-password"
                        required
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div>
                    <InputLabel for="password_confirmation" :value="t('settings.password.confirm_new_password')" />
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        v-model="form.password_confirmation"
                        autocomplete="new-password"
                        required
                    />
                    <InputError :message="form.errors.password_confirmation" />
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
