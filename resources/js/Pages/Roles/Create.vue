<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import RoleFormFields from './Partials/RoleFormFields.vue';

defineProps({
    permissions: { type: Array, default: () => [] },
    users: { type: Array, default: () => [] },
});

const form = useForm({
    name: '',
    permissions: [],
    users: [],
});

const submit = () => form.post(route('roles.store'));
</script>

<template>
    <AppLayout title="Create role">
        <Head title="Create role" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Create role</h2>
            </div>

            <div class="px-6 py-4">
                <RoleFormFields :form="form" :permissions="permissions" :users="users" />
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
                <Link :href="route('roles.index')">
                    <SecondaryButton type="button">Cancel</SecondaryButton>
                </Link>
                <PrimaryButton :disabled="form.processing" :class="{ 'opacity-50': form.processing }">
                    Create
                </PrimaryButton>
            </div>
        </form>
    </AppLayout>
</template>
