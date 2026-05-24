<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    user: { type: Object, required: true },
});

const form = useForm({
    name: props.user.name ?? '',
    email: props.user.email ?? '',
});

const submit = () => form.put(route('user_settings.profile.update'));
</script>

<template>
    <AppLayout title="Profile">
        <Head title="Profile" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Profile</h2>
            </div>

            <div class="px-6 py-4 space-y-4">
                <div>
                    <InputLabel for="name" value="Name" />
                    <TextInput id="name" v-model="form.name" required autofocus />
                    <InputError :message="form.errors.name" />
                </div>

                <div>
                    <InputLabel for="email" value="Email" />
                    <TextInput id="email" type="email" v-model="form.email" required />
                    <InputError :message="form.errors.email" />
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                <PrimaryButton :disabled="form.processing" :class="{ 'opacity-50': form.processing }">
                    Update
                </PrimaryButton>
            </div>
        </form>
    </AppLayout>
</template>
