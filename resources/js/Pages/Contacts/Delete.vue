<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const { t } = useI18n();

const props = defineProps({
    contact: { type: Object, required: true },
});

const form = useForm({});

const submit = () => form.delete(route('contacts.destroy', props.contact.ulid));
</script>

<template>
    <AppLayout :title="t('contacts.delete_confirm_title', { name: contact.fullname })">
        <Head :title="t('contacts.delete_confirm_title', { name: contact.fullname })" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">
                    {{ t('contacts.delete_confirm_title', { name: contact.fullname }) }}
                </h2>
            </div>

            <div class="px-6 py-4 text-sm text-gray-700">
                <p>{{ t('contacts.delete_confirm_body') }}</p>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
                <Link :href="route('contacts.show', contact.ulid)">
                    <SecondaryButton type="button">{{ t('common.cancel') }}</SecondaryButton>
                </Link>
                <DangerButton type="submit" :disabled="form.processing" :class="{ 'opacity-50': form.processing }">
                    {{ t('contacts.delete') }}
                </DangerButton>
            </div>
        </form>
    </AppLayout>
</template>
