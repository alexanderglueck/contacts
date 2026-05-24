<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    contactGroup: { type: Object, required: true },
});

const form = useForm({});

const submit = () => form.delete(route('contact_groups.destroy', props.contactGroup.ulid));
</script>

<template>
    <AppLayout :title="`Delete ${contactGroup.name}`">
        <Head :title="`Delete ${contactGroup.name}`" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Delete contact group {{ contactGroup.name }}?</h2>
            </div>

            <div class="px-6 py-4 text-sm text-gray-700">
                <p>This will permanently remove the contact group <span class="font-medium">{{ contactGroup.name }}</span>.</p>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
                <Link :href="route('contact_groups.show', contactGroup.ulid)">
                    <SecondaryButton type="button">Cancel</SecondaryButton>
                </Link>
                <DangerButton type="submit" :disabled="form.processing" :class="{ 'opacity-50': form.processing }">
                    Delete contact group
                </DangerButton>
            </div>
        </form>
    </AppLayout>
</template>
