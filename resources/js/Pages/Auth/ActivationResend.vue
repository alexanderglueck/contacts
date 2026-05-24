<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('activation.resend.store'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Resend activation" />

        <h1 class="text-xl font-semibold text-gray-900 mb-2">Resend activation email</h1>
        <p class="text-sm text-gray-600 mb-4">
            Enter your email and we'll send you a new activation link.
        </p>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput id="email" type="email" v-model="form.email" autocomplete="username" autofocus required />
                <InputError :message="form.errors.email" />
            </div>

            <div class="flex items-center justify-end">
                <PrimaryButton :class="{ 'opacity-50': form.processing }" :disabled="form.processing">
                    Send activation email
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
