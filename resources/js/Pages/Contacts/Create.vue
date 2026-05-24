<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import ContactFormFields from './Partials/ContactFormFields.vue';

const props = defineProps({
    genders: { type: Array, default: () => [] },
    contactGroups: { type: Array, default: () => [] },
    countries: { type: Array, default: () => [] },
});

const form = useForm({
    salutation: '',
    title: '',
    title_after: '',
    firstname: '',
    lastname: '',
    nickname: '',
    date_of_birth: '',
    iban: '',
    company: '',
    vatin: '',
    department: '',
    job: '',
    gender_id: props.genders[0]?.id ?? null,
    custom_id: '',
    contact_groups: [],
    active: 1,
    first_met: '',
    note: '',
    died_at: '',
    died_from: '',
    nationality_id: null,
});

const submit = () => form.post(route('contacts.store'));
</script>

<template>
    <AppLayout title="Create contact">
        <Head title="Create contact" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Create contact</h2>
            </div>

            <div class="px-6 py-4">
                <ContactFormFields
                    :form="form"
                    :genders="genders"
                    :contact-groups="contactGroups"
                    :countries="countries"
                />
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
                <Link :href="route('contacts.index')">
                    <SecondaryButton type="button">Cancel</SecondaryButton>
                </Link>
                <PrimaryButton :disabled="form.processing" :class="{ 'opacity-50': form.processing }">
                    Create
                </PrimaryButton>
            </div>
        </form>
    </AppLayout>
</template>
