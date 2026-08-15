<script setup>
import { computed } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const { t } = useI18n();

const page = usePage();

// See ConfirmPasswordModal: without a username field alongside the password,
// a password manager reads this as a form for inventing a new password.
const username = computed(() => page.props.auth?.user?.email ?? '');

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
            <input
                type="text"
                :value="username"
                autocomplete="username"
                class="sr-only"
                tabindex="-1"
                aria-hidden="true"
                readonly
            />

            <div>
                <InputLabel for="current_password" :value="t('auth.password')" />
                <TextInput
                    id="current_password"
                    name="current_password"
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
