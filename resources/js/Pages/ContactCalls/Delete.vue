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

const submit = () => form.delete(route('contact_calls.destroy', [props.contact.slug, props.item.id]));
</script>

<template>
    <AppLayout :title="`Delete call ${item.formatted_called_at}`">
        <Head :title="`Delete call ${item.formatted_called_at}`" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Delete call on {{ item.formatted_called_at }}?</h2>
            </div>

            <div class="px-6 py-4 text-sm text-gray-700">
                <p>This will permanently remove this call entry from {{ contact.fullname }}.</p>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
                <Link :href="route('contact_calls.show', [contact.slug, item.id])">
                    <SecondaryButton type="button">Cancel</SecondaryButton>
                </Link>
                <DangerButton type="submit" :disabled="form.processing" :class="{ 'opacity-50': form.processing }">
                    Delete call
                </DangerButton>
            </div>
        </form>
    </AppLayout>
</template>
