<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { usePasskeyVerify } from '@laravel/passkeys/vue';

const { t } = useI18n();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login.store'), {
        onFinish: () => form.reset('password'),
    });
};

const {
    verify,
    isLoading: passkeyBusy,
    error: passkeyError,
    isSupported: passkeySupported,
} = usePasskeyVerify({
    onSuccess: (response) => {
        window.location.href = response.redirect ?? '/';
    },
});

// The @laravel/passkeys JS reads CSRF from <meta name="csrf-token"> first and
// only falls back to the XSRF-TOKEN cookie if the tag is absent. The meta tag
// is baked in at initial page render and never refreshes, so it goes stale once
// the session expires (SESSION_LIFETIME), is GC'd, or is regenerated — and the
// POST to /passkeys/login then 419s. Refresh the session via Sanctum's CSRF
// endpoint and clear the stale meta tag so the client uses the fresh cookie.
const signInWithPasskey = async () => {
    try {
        await fetch('/sanctum/csrf-cookie', { credentials: 'same-origin' });
        document.querySelector('meta[name="csrf-token"]')?.setAttribute('content', '');
    } catch {
        // Network hiccup — fall through to verify() and let the user retry.
    }
    await verify();
};
</script>

<template>
    <GuestLayout>
        <Head :title="t('auth.login')" />

        <h1 class="text-xl font-semibold text-gray-900 mb-4">{{ t('auth.login') }}</h1>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="email" :value="t('auth.email')" />
                <TextInput
                    id="email"
                    type="email"
                    v-model="form.email"
                    autocomplete="username"
                    autofocus
                    required
                />
                <InputError :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" :value="t('auth.password')" />
                <TextInput
                    id="password"
                    type="password"
                    v-model="form.password"
                    autocomplete="current-password"
                    required
                />
                <InputError :message="form.errors.password" />
            </div>

            <label class="flex items-center">
                <Checkbox v-model:checked="form.remember" />
                <span class="ms-2 text-sm text-gray-600">{{ t('auth.remember') }}</span>
            </label>

            <div class="flex items-center justify-end gap-4 pt-2">
                <Link
                    :href="route('password.request')"
                    class="text-sm text-gray-600 hover:text-gray-900 underline"
                >
                    {{ t('auth.forgot_password') }}
                </Link>
                <PrimaryButton :class="{ 'opacity-50': form.processing }" :disabled="form.processing">
                    {{ t('auth.login') }}
                </PrimaryButton>
            </div>
        </form>

        <div v-if="passkeySupported" class="mt-6">
            <div class="relative flex items-center">
                <div class="flex-grow border-t border-gray-200"></div>
                <span class="px-3 text-xs uppercase tracking-wide text-gray-500">{{ t('auth.or') }}</span>
                <div class="flex-grow border-t border-gray-200"></div>
            </div>
            <SecondaryButton
                type="button"
                class="w-full mt-4 justify-center cursor-pointer"
                :disabled="passkeyBusy"
                @click="signInWithPasskey"
            >
                {{ passkeyBusy ? t('auth.waiting_for_passkey') : t('auth.sign_in_with_passkey') }}
            </SecondaryButton>
            <InputError :message="passkeyError ?? ''" />
        </div>

        <p class="mt-6 text-sm text-center text-gray-600">
            {{ t('auth.no_account') }}
            <Link :href="route('register')" class="text-indigo-600 hover:text-indigo-500 underline">
                {{ t('auth.register') }}
            </Link>
        </p>
    </GuestLayout>
</template>
