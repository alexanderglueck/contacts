<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    settings: { type: Object, required: true },
});

const form = useForm({
    send_daily: !!props.settings.send_daily,
    send_weekly: !!props.settings.send_weekly,
});

const submit = () => form.put(route('user_settings.notifications.update'));
</script>

<template>
    <AppLayout title="Notification settings">
        <Head title="Notification settings" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Notification settings</h2>
            </div>

            <div class="px-6 py-4 space-y-3">
                <label class="flex items-center">
                    <Checkbox v-model:checked="form.send_daily" />
                    <span class="ms-2 text-sm text-gray-700">Send daily notifications</span>
                </label>
                <InputError :message="form.errors.send_daily" />

                <label class="flex items-center">
                    <Checkbox v-model:checked="form.send_weekly" />
                    <span class="ms-2 text-sm text-gray-700">Send weekly notifications</span>
                </label>
                <InputError :message="form.errors.send_weekly" />
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                <PrimaryButton :disabled="form.processing" :class="{ 'opacity-50': form.processing }">
                    Update
                </PrimaryButton>
            </div>
        </form>
    </AppLayout>
</template>
