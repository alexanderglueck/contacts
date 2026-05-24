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

const submit = () => form.delete(route('contact_numbers.destroy', [props.contact.ulid, props.item.ulid]));
</script>

<template>
    <AppLayout :title="`Delete ${item.name}`">
        <Head :title="`Delete ${item.name}`" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Delete phone number {{ item.name }}?</h2>
            </div>

            <div class="px-6 py-4 text-sm text-gray-700">
                <p>This will permanently remove the phone number <span class="font-medium">{{ item.number }}</span> from {{ contact.fullname }}.</p>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
                <Link :href="route('contact_numbers.show', [contact.ulid, item.ulid])">
                    <SecondaryButton type="button">Cancel</SecondaryButton>
                </Link>
                <DangerButton type="submit" :disabled="form.processing" :class="{ 'opacity-50': form.processing }">
                    Delete phone number
                </DangerButton>
            </div>
        </form>
    </AppLayout>
</template>
