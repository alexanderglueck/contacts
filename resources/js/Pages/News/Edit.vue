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
    news: { type: Object, required: true },
});

const form = useForm({
    title: props.news.title ?? '',
    body: props.news.body ?? '',
    expired_at: props.news.expired_at ?? '',
    pinned_at: props.news.pinned_at ?? '',
});

const togglePinned = (event) => {
    form.pinned_at = event.target.checked ? new Date().toISOString().slice(0, 19).replace('T', ' ') : '';
};

const submit = () => form.put(route('news.update', props.news.slug));
</script>

<template>
    <AppLayout title="Edit news">
        <Head title="Edit news" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Edit news</h2>
            </div>

            <div class="px-6 py-4 space-y-4">
                <div>
                    <InputLabel for="title" value="Title *" />
                    <TextInput id="title" v-model="form.title" required autofocus />
                    <InputError :message="form.errors.title" />
                </div>
                <div>
                    <InputLabel for="body" value="Body *" />
                    <Textarea id="body" v-model="form.body" rows="6" required />
                    <InputError :message="form.errors.body" />
                </div>
                <div>
                    <InputLabel for="expired_at" value="Expires at" />
                    <TextInput id="expired_at" v-model="form.expired_at" placeholder="YYYY-MM-DD HH:MM:SS" />
                    <InputError :message="form.errors.expired_at" />
                </div>
                <div>
                    <label class="inline-flex items-center">
                        <input
                            type="checkbox"
                            :checked="!!form.pinned_at"
                            @change="togglePinned"
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        />
                        <span class="ms-2 text-sm text-gray-700">Pinned</span>
                    </label>
                    <InputError :message="form.errors.pinned_at" />
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
                <Link :href="route('news.show', news.slug)">
                    <SecondaryButton type="button">Cancel</SecondaryButton>
                </Link>
                <PrimaryButton :disabled="form.processing" :class="{ 'opacity-50': form.processing }">
                    Save
                </PrimaryButton>
            </div>
        </form>
    </AppLayout>
</template>
