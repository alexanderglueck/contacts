<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import ContactFormFields from './Partials/ContactFormFields.vue';

const { t } = useI18n();

const props = defineProps({
    contact: { type: Object, required: true },
    genders: { type: Array, default: () => [] },
    contactGroups: { type: Array, default: () => [] },
    countries: { type: Array, default: () => [] },
});

const form = useForm({
    salutation: props.contact.salutation ?? '',
    title: props.contact.title ?? '',
    title_after: props.contact.title_after ?? '',
    firstname: props.contact.firstname ?? '',
    lastname: props.contact.lastname ?? '',
    nickname: props.contact.nickname ?? '',
    date_of_birth: props.contact.date_of_birth ?? '',
    iban: props.contact.iban ?? '',
    company: props.contact.company ?? '',
    vatin: props.contact.vatin ?? '',
    department: props.contact.department ?? '',
    job: props.contact.job ?? '',
    gender_id: props.contact.gender_id ?? null,
    custom_id: props.contact.custom_id ?? '',
    contact_groups: props.contact.contact_groups ?? [],
    active: props.contact.active ? 1 : 0,
    first_met: props.contact.first_met ?? '',
    note: props.contact.note ?? '',
    died_at: props.contact.died_at ?? '',
    died_from: props.contact.died_from ?? '',
    nationality_id: props.contact.nationality_id ?? null,
});

const submit = () => form.put(route('contacts.update', props.contact.ulid));
</script>

<template>
    <AppLayout :title="`${t('contacts.edit')} – ${contact.fullname}`">
        <Head :title="`${t('contacts.edit')} – ${contact.fullname}`" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">{{ t('contacts.edit') }}</h2>
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
                <Link :href="route('contacts.show', contact.ulid)">
                    <SecondaryButton type="button">{{ t('common.cancel') }}</SecondaryButton>
                </Link>
                <PrimaryButton :disabled="form.processing" :class="{ 'opacity-50': form.processing }">
                    {{ t('common.save') }}
                </PrimaryButton>
            </div>
        </form>
    </AppLayout>
</template>
