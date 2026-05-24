<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AddressFormFields from './Partials/AddressFormFields.vue';

const props = defineProps({
    contact: { type: Object, required: true },
    item: { type: Object, required: true },
    countries: { type: Array, default: () => [] },
});

const form = useForm({
    name: props.item.name ?? '',
    street: props.item.street ?? '',
    zip: props.item.zip ?? '',
    city: props.item.city ?? '',
    state: props.item.state ?? '',
    country_id: props.item.country_id ?? 164,
    latitude: props.item.latitude ?? '',
    longitude: props.item.longitude ?? '',
});

const submit = () => form.put(route('contact_addresses.update', [props.contact.slug, props.item.slug]));
</script>

<template>
    <AppLayout :title="`Edit address — ${contact.fullname}`">
        <Head :title="`Edit address — ${contact.fullname}`" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Edit address</h2>
            </div>

            <div class="px-6 py-4">
                <AddressFormFields :form="form" :countries="countries" />
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
                <Link :href="route('contact_addresses.show', [contact.slug, item.slug])">
                    <SecondaryButton type="button">Cancel</SecondaryButton>
                </Link>
                <PrimaryButton :disabled="form.processing" :class="{ 'opacity-50': form.processing }">
                    Save
                </PrimaryButton>
            </div>
        </form>
    </AppLayout>
</template>
