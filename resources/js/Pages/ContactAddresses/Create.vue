<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AddressFormFields from './Partials/AddressFormFields.vue';

const props = defineProps({
    contact: { type: Object, required: true },
    countries: { type: Array, default: () => [] },
});

const form = useForm({
    name: '',
    street: '',
    zip: '',
    city: '',
    state: '',
    country_id: 164,
    latitude: '',
    longitude: '',
});

const submit = () => form.post(route('contact_addresses.store', props.contact.slug));
</script>

<template>
    <AppLayout :title="`Add address — ${contact.fullname}`">
        <Head :title="`Add address — ${contact.fullname}`" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Add address for {{ contact.fullname }}</h2>
            </div>

            <div class="px-6 py-4">
                <AddressFormFields :form="form" :countries="countries" />
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
                <Link :href="route('contact_addresses.index', contact.slug)">
                    <SecondaryButton type="button">Cancel</SecondaryButton>
                </Link>
                <PrimaryButton :disabled="form.processing" :class="{ 'opacity-50': form.processing }">
                    Create
                </PrimaryButton>
            </div>
        </form>
    </AppLayout>
</template>
