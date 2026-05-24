<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DangerButton from '@/Components/DangerButton.vue';

const form = useForm({});

const submit = () => form.delete(route('user_settings.delete.destroy'));
</script>

<template>
    <AppLayout title="Delete account">
        <Head title="Delete account" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Delete account</h2>
            </div>

            <div class="px-6 py-4 space-y-4">
                <p class="text-sm text-gray-600">
                    Deleting your account is permanent. All your data, including any active subscription, will be removed.
                </p>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                <DangerButton :disabled="form.processing" :class="{ 'opacity-50': form.processing }">
                    Delete account
                </DangerButton>
            </div>
        </form>
    </AppLayout>
</template>
