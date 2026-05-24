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
    called_at: props.item.called_at ?? '',
    note: props.item.note ?? '',
});

const submit = () => form.put(route('contact_calls.update', [props.contact.slug, props.item.id]));
</script>

<template>
    <AppLayout :title="`Edit call — ${contact.fullname}`">
        <Head :title="`Edit call — ${contact.fullname}`" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Edit call</h2>
            </div>

            <div class="px-6 py-4 space-y-4">
                <div>
                    <InputLabel for="called_at" value="Called at (DD.MM.YYYY HH:MM) *" />
                    <TextInput id="called_at" v-model="form.called_at" placeholder="01.01.2025 14:30" required />
                    <InputError :message="form.errors.called_at" />
                </div>
                <div>
                    <InputLabel for="note" value="Note" />
                    <Textarea id="note" v-model="form.note" rows="3" />
                    <InputError :message="form.errors.note" />
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
                <Link :href="route('contact_calls.show', [contact.slug, item.id])">
                    <SecondaryButton type="button">Cancel</SecondaryButton>
                </Link>
                <PrimaryButton :disabled="form.processing" :class="{ 'opacity-50': form.processing }">
                    Save
                </PrimaryButton>
            </div>
        </form>
    </AppLayout>
</template>
