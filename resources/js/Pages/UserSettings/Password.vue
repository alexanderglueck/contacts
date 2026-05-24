<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const form = useForm({
    password_current: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.put(route('user_settings.password.update'), {
        onFinish: () => form.reset('password_current', 'password', 'password_confirmation'),
    });
};
</script>

<template>
    <AppLayout title="Change password">
        <Head title="Change password" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Change password</h2>
            </div>

            <div class="px-6 py-4 space-y-4">
                <div>
                    <InputLabel for="password_current" value="Current password" />
                    <TextInput
                        id="password_current"
                        type="password"
                        v-model="form.password_current"
                        autocomplete="current-password"
                        required
                    />
                    <InputError :message="form.errors.password_current" />
                </div>

                <div>
                    <InputLabel for="password" value="New password" />
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
                    <InputLabel for="password_confirmation" value="Confirm password" />
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
                    Change password
                </PrimaryButton>
            </div>
        </form>
    </AppLayout>
</template>
