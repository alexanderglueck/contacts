<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    contact: { type: Object, required: true },
    item: { type: Object, required: true },
});

const form = useForm({});

const submit = () => form.delete(route('contact_emails.destroy', [props.contact.ulid, props.item.ulid]));
</script>

<template>
    <AppLayout :title="`${contact.fullname} — Delete ${item.name}`">
        <Head :title="`${contact.fullname} — Delete ${item.name}`" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Delete email {{ item.name }}?</h2>
            </div>

            <div class="px-6 py-4 text-sm text-gray-700">
                <p>This will permanently remove the email <span class="font-medium">{{ item.email }}</span> from {{ contact.fullname }}.</p>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
                <Link :href="route('contact_emails.show', [contact.ulid, item.ulid])">
                    <SecondaryButton type="button">Cancel</SecondaryButton>
                </Link>
                <DangerButton type="submit" :disabled="form.processing" :class="{ 'opacity-50': form.processing }">
                    Delete email
                </DangerButton>
            </div>
        </form>
    </AppLayout>
</template>
