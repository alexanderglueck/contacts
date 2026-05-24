<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const { t } = useI18n();

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head :title="t('auth.register')" />

        <h1 class="text-xl font-semibold text-gray-900 mb-4">{{ t('auth.register') }}</h1>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="name" :value="t('auth.name')" />
                <TextInput id="name" v-model="form.name" autocomplete="name" autofocus required />
                <InputError :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" :value="t('auth.email')" />
                <TextInput id="email" type="email" v-model="form.email" autocomplete="username" required />
                <InputError :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" :value="t('auth.password')" />
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
                <InputLabel for="password_confirmation" :value="t('auth.confirm_password')" />
                <TextInput
                    id="password_confirmation"
                    type="password"
                    v-model="form.password_confirmation"
                    autocomplete="new-password"
                    required
                />
            </div>

            <label class="flex items-start">
                <Checkbox v-model:checked="form.terms" />
                <span class="ms-2 text-sm text-gray-600">
                    {{ t('auth.register_terms') }}
                </span>
            </label>
            <InputError :message="form.errors.terms" />

            <div class="flex items-center justify-between pt-2">
                <Link :href="route('login')" class="text-sm text-gray-600 hover:text-gray-900 underline">
                    {{ t('auth.have_account') }}
                </Link>
                <PrimaryButton :class="{ 'opacity-50': form.processing }" :disabled="form.processing">
                    {{ t('auth.register') }}
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
