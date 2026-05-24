<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    active: { type: Object, required: true },
    inactive: { type: Object, required: true },
    displayed: { type: Object, default: null },
    hidden: { type: Object, default: null },
    read: { type: Object, default: null },
    unread: { type: Object, default: null },
    canCreate: { type: Boolean, default: false },
});

const sections = computed(() => {
    const list = [
        { title: 'Active', data: props.active },
        { title: 'Expired', data: props.inactive },
    ];
    if (props.displayed) list.push({ title: 'Relevant', data: props.displayed });
    if (props.hidden) list.push({ title: 'Irrelevant', data: props.hidden });
    if (props.read) list.push({ title: 'Read', data: props.read });
    if (props.unread) list.push({ title: 'Unread', data: props.unread });
    return list;
});
</script>

<template>
    <AppLayout title="News">
        <Head title="News" />

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">News</h2>
                <Link v-if="canCreate" :href="route('news.create')">
                    <PrimaryButton type="button">Create news</PrimaryButton>
                </Link>
            </div>
        </div>

        <div v-for="section in sections" :key="section.title" class="bg-white shadow rounded-lg">
            <div class="px-6 py-3 border-b border-gray-200">
                <h3 class="text-base font-medium text-gray-900">{{ section.title }}</h3>
            </div>
            <div v-if="section.data.data.length === 0" class="px-6 py-6 text-center text-sm text-gray-500">
                No news.
            </div>
            <ul v-else class="divide-y divide-gray-200">
                <li v-for="item in section.data.data" :key="item.id">
                    <Link
                        :href="route('news.show', item.ulid)"
                        class="block px-6 py-3 hover:bg-gray-50 text-sm text-gray-900 flex justify-between items-center"
                    >
                        <span>{{ item.title }}</span>
                        <span v-if="item.is_pinned" class="text-xs text-indigo-600">Pinned</span>
                    </Link>
                </li>
            </ul>
        </div>
    </AppLayout>
</template>
