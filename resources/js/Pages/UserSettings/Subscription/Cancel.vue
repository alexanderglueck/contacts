<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DangerButton from '@/Components/DangerButton.vue';

const form = useForm({});

const submit = () => form.post(route('user_settings.subscription.cancel.store'));
</script>

<template>
    <AppLayout title="Cancel subscription">
        <Head title="Cancel subscription" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Cancel subscription</h2>
            </div>

            <div class="px-6 py-4">
                <p class="text-sm text-gray-600">Confirm to cancel your subscription.</p>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                <DangerButton :disabled="form.processing" :class="{ 'opacity-50': form.processing }">
                    Cancel
                </DangerButton>
            </div>
        </form>
    </AppLayout>
</template>
