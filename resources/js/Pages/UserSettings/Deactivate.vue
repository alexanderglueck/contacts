<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import DangerButton from '@/Components/DangerButton.vue';

defineProps({
    subscriptionNotCancelled: { type: Boolean, default: false },
});

const form = useForm({
    current_password: '',
});

const submit = () => form.post(route('user_settings.deactivate.store'));
</script>

<template>
    <AppLayout title="Deactivate account">
        <Head title="Deactivate account" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Deactivate</h2>
            </div>

            <div class="px-6 py-4 space-y-4">
                <p v-if="subscriptionNotCancelled" class="text-sm text-gray-600">
                    This will also cancel your subscription.
                </p>

                <div>
                    <InputLabel for="current_password" value="Current password" />
                    <TextInput
                        id="current_password"
                        type="password"
                        v-model="form.current_password"
                        autocomplete="current-password"
                        required
                    />
                    <InputError :message="form.errors.current_password" />
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                <DangerButton :disabled="form.processing" :class="{ 'opacity-50': form.processing }">
                    Deactivate account
                </DangerButton>
            </div>
        </form>
    </AppLayout>
</template>
