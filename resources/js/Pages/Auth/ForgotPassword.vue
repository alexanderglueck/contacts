<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Forgot password" />

        <h1 class="text-xl font-semibold text-gray-900 mb-2">Reset password</h1>
        <p class="text-sm text-gray-600 mb-4">
            We'll email you a link to choose a new password.
        </p>

        <div v-if="status" class="mb-4 rounded-md bg-green-50 px-4 py-3 text-sm text-green-800 ring-1 ring-inset ring-green-200">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput id="email" type="email" v-model="form.email" autocomplete="username" autofocus required />
                <InputError :message="form.errors.email" />
            </div>

            <div class="flex items-center justify-end">
                <PrimaryButton :class="{ 'opacity-50': form.processing }" :disabled="form.processing">
                    Send reset link
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
