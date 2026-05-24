<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    contact: { type: Object, required: true },
    item: { type: Object, required: true },
});

const form = useForm({
    name: props.item.name ?? '',
    email: props.item.email ?? '',
});

const submit = () => form.put(route('contact_emails.update', [props.contact.slug, props.item.slug]));
</script>

<template>
    <AppLayout :title="`Edit email — ${contact.fullname}`">
        <Head :title="`Edit email — ${contact.fullname}`" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Edit email</h2>
            </div>

            <div class="px-6 py-4 space-y-4">
                <div>
                    <InputLabel for="name" value="Name *" />
                    <TextInput id="name" v-model="form.name" required />
                    <InputError :message="form.errors.name" />
                </div>
                <div>
                    <InputLabel for="email" value="Email *" />
                    <TextInput id="email" type="email" v-model="form.email" required />
                    <InputError :message="form.errors.email" />
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
                <Link :href="route('contact_emails.show', [contact.slug, item.slug])">
                    <SecondaryButton type="button">Cancel</SecondaryButton>
                </Link>
                <PrimaryButton :disabled="form.processing" :class="{ 'opacity-50': form.processing }">
                    Save
                </PrimaryButton>
            </div>
        </form>
    </AppLayout>
</template>
