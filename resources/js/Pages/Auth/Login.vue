<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login.check'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Login" />

        <h1 class="text-xl font-semibold text-gray-900 mb-4">Login</h1>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="email" value="Email" />
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
                <InputLabel for="password" value="Password" />
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
                <span class="ms-2 text-sm text-gray-600">Remember me</span>
            </label>

            <div class="flex items-center justify-end gap-4 pt-2">
                <Link
                    :href="route('activation.resend')"
                    class="text-sm text-gray-600 hover:text-gray-900 underline"
                >
                    Resend activation
                </Link>
                <Link
                    :href="route('password.request')"
                    class="text-sm text-gray-600 hover:text-gray-900 underline"
                >
                    Forgot your password?
                </Link>
                <PrimaryButton :class="{ 'opacity-50': form.processing }" :disabled="form.processing">
                    Login
                </PrimaryButton>
            </div>
        </form>

        <p class="mt-6 text-sm text-center text-gray-600">
            No account?
            <Link :href="route('register')" class="text-indigo-600 hover:text-indigo-500 underline">
                Register
            </Link>
        </p>
    </GuestLayout>
</template>
