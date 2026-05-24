<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const form = useForm({
    token: '',
});

const submit = () => {
    form.post(route('login.token.check'), {
        onFinish: () => form.reset('token'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Two-factor authentication" />

        <h1 class="text-xl font-semibold text-gray-900 mb-4">Two-factor authentication</h1>
        <p class="text-sm text-gray-600 mb-4">
            Enter the 6-digit code from your authenticator app, or a backup code.
        </p>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="token" value="Authentication code" />
                <TextInput
                    id="token"
                    type="text"
                    v-model="form.token"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    autofocus
                    required
                />
                <InputError :message="form.errors.token" />
            </div>

            <div class="flex items-center justify-end">
                <PrimaryButton :class="{ 'opacity-50': form.processing }" :disabled="form.processing">
                    Verify
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
