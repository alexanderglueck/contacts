<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    contact: { type: Object, required: true },
    item: { type: Object, required: true },
});

const form = useForm({
    name: props.item.name ?? '',
    note: props.item.note ?? '',
});

const submit = () => form.put(route('contact_notes.update', [props.contact.ulid, props.item.ulid]));
</script>

<template>
    <AppLayout :title="`${contact.fullname} — Edit note`">
        <Head :title="`${contact.fullname} — Edit note`" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Edit note</h2>
            </div>

            <div class="px-6 py-4 space-y-4">
                <div>
                    <InputLabel for="name" value="Name *" />
                    <TextInput id="name" v-model="form.name" required />
                    <InputError :message="form.errors.name" />
                </div>
                <div>
                    <InputLabel for="note" value="Note *" />
                    <Textarea id="note" v-model="form.note" rows="3" />
                    <InputError :message="form.errors.note" />
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
                <Link :href="route('contact_notes.show', [contact.ulid, item.ulid])">
                    <SecondaryButton type="button">Cancel</SecondaryButton>
                </Link>
                <PrimaryButton :disabled="form.processing" :class="{ 'opacity-50': form.processing }">
                    Save
                </PrimaryButton>
            </div>
        </form>
    </AppLayout>
</template>
