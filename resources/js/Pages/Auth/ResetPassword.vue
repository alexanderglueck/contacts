<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const { t } = useI18n();

const props = defineProps({
    token: { type: String, required: true },
    email: { type: String, default: '' },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post('/password/reset', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head :title="t('auth.reset_password')" />

        <h1 class="text-xl font-semibold text-gray-900 mb-4">{{ t('auth.reset_password') }}</h1>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="email" :value="t('auth.email')" />
                <TextInput id="email" type="email" v-model="form.email" autocomplete="username" required />
                <InputError :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" :value="t('settings.password.new_password')" />
                <TextInput
                    id="password"
                    type="password"
                    v-model="form.password"
                    autocomplete="new-password"
                    autofocus
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
            </div>

            <div class="flex items-center justify-end">
                <PrimaryButton :class="{ 'opacity-50': form.processing }" :disabled="form.processing">
                    {{ t('auth.reset_password') }}
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
