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
    description: props.item.description ?? '',
    url: props.item.url ?? '',
    due_at: props.item.due_at ?? '',
});

const submit = () => form.put(route('gift_ideas.update', [props.contact.ulid, props.item.ulid]));
</script>

<template>
    <AppLayout :title="`${contact.fullname} — Edit gift idea`">
        <Head :title="`${contact.fullname} — Edit gift idea`" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Edit gift idea</h2>
            </div>

            <div class="px-6 py-4 space-y-4">
                <div>
                    <InputLabel for="name" value="Name *" />
                    <TextInput id="name" v-model="form.name" required />
                    <InputError :message="form.errors.name" />
                </div>
                <div>
                    <InputLabel for="description" value="Description" />
                    <Textarea id="description" v-model="form.description" rows="3" />
                    <InputError :message="form.errors.description" />
                </div>
                <div>
                    <InputLabel for="url" value="URL" />
                    <TextInput id="url" type="url" v-model="form.url" />
                    <InputError :message="form.errors.url" />
                </div>
                <div>
                    <InputLabel for="due_at" value="Due at (DD.MM.YYYY)" />
                    <TextInput id="due_at" v-model="form.due_at" placeholder="01.01.2025" />
                    <InputError :message="form.errors.due_at" />
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
                <Link :href="route('gift_ideas.show', [contact.ulid, item.ulid])">
                    <SecondaryButton type="button">Cancel</SecondaryButton>
                </Link>
                <PrimaryButton :disabled="form.processing" :class="{ 'opacity-50': form.processing }">
                    Save
                </PrimaryButton>
            </div>
        </form>
    </AppLayout>
</template>
