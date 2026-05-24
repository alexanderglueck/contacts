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
    date: props.item.date ?? '',
    skip_year: props.item.skip_year ?? 0,
});

const submit = () => form.put(route('contact_dates.update', [props.contact.slug, props.item.slug]));
</script>

<template>
    <AppLayout :title="`Edit date — ${contact.fullname}`">
        <Head :title="`Edit date — ${contact.fullname}`" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Edit important date</h2>
            </div>

            <div class="px-6 py-4 space-y-4">
                <div>
                    <InputLabel for="name" value="Name *" />
                    <TextInput id="name" v-model="form.name" required />
                    <InputError :message="form.errors.name" />
                </div>
                <div>
                    <InputLabel for="date" value="Date (DD.MM.YYYY) *" />
                    <TextInput id="date" v-model="form.date" placeholder="01.01.1990" required />
                    <InputError :message="form.errors.date" />
                </div>
                <div>
                    <span class="block text-sm font-medium text-gray-700 mb-1">Skip year</span>
                    <label class="inline-flex items-center mr-4">
                        <input
                            type="radio"
                            :value="0"
                            v-model.number="form.skip_year"
                            class="text-indigo-600 focus:ring-indigo-500"
                        />
                        <span class="ms-2 text-sm text-gray-700">No</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input
                            type="radio"
                            :value="1"
                            v-model.number="form.skip_year"
                            class="text-indigo-600 focus:ring-indigo-500"
                        />
                        <span class="ms-2 text-sm text-gray-700">Yes</span>
                    </label>
                    <InputError :message="form.errors.skip_year" />
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
                <Link :href="route('contact_dates.show', [contact.slug, item.slug])">
                    <SecondaryButton type="button">Cancel</SecondaryButton>
                </Link>
                <PrimaryButton :disabled="form.processing" :class="{ 'opacity-50': form.processing }">
                    Save
                </PrimaryButton>
            </div>
        </form>
    </AppLayout>
</template>
