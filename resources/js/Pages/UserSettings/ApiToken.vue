<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    user: { type: Object, required: true },
});

const form = useForm({});

const submit = () => form.put(route('user_settings.api_token.update', props.user.slug));
</script>

<template>
    <AppLayout title="API token">
        <Head title="API token" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">API token</h2>
            </div>

            <div class="px-6 py-4 space-y-4">
                <div>
                    <InputLabel for="api_token" value="API token" />
                    <input
                        id="api_token"
                        type="text"
                        readonly
                        :value="user.api_token"
                        class="block w-full rounded-md border-gray-300 shadow-sm bg-gray-50 sm:text-sm"
                    />
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                <PrimaryButton :disabled="form.processing" :class="{ 'opacity-50': form.processing }">
                    Generate new token
                </PrimaryButton>
            </div>
        </form>
    </AppLayout>
</template>
