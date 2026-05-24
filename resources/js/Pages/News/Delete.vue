<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    news: { type: Object, required: true },
});

const form = useForm({});

const submit = () => form.delete(route('news.destroy', props.news.slug));
</script>

<template>
    <AppLayout :title="`Delete ${news.title}`">
        <Head :title="`Delete ${news.title}`" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Delete news {{ news.title }}?</h2>
            </div>

            <div class="px-6 py-4 text-sm text-gray-700">
                <p>This will permanently remove the news <span class="font-medium">{{ news.title }}</span>.</p>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
                <Link :href="route('news.show', news.slug)">
                    <SecondaryButton type="button">Cancel</SecondaryButton>
                </Link>
                <DangerButton type="submit" :disabled="form.processing" :class="{ 'opacity-50': form.processing }">
                    Delete news
                </DangerButton>
            </div>
        </form>
    </AppLayout>
</template>
