<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageJumper from '@/Components/PageJumper.vue';

defineProps({
    title: { type: String, required: true },
    contacts: { type: Object, required: true },
});
</script>

<template>
    <AppLayout :title="title">
        <Head :title="title" />

        <div class="text-sm">
            <Link :href="route('reports.index')" class="text-indigo-600 hover:text-indigo-500">
                &larr; Back to reports
            </Link>
        </div>

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">{{ title }}</h2>
            </div>

            <div v-if="contacts.data.length === 0" class="px-6 py-8 text-center text-sm text-gray-500">
                No contacts found.
            </div>

            <ul v-else class="divide-y divide-gray-200">
                <li v-for="contact in contacts.data" :key="contact.id">
                    <Link
                        :href="route('contacts.show', contact.ulid)"
                        class="block px-6 py-3 hover:bg-gray-50 text-sm text-gray-900"
                    >
                        {{ contact.fullname }}
                    </Link>
                </li>
            </ul>

            <div v-if="contacts.links && contacts.last_page > 1" class="border-t border-gray-200 px-6 py-3 flex flex-wrap justify-between items-center gap-2">
                <p class="text-sm text-gray-600">
                    Page {{ contacts.current_page }} of {{ contacts.last_page }}
                </p>
                <div class="flex flex-wrap items-center gap-2">
                <PageJumper :current-page="contacts.current_page" :last-page="contacts.last_page" />
                <div class="flex gap-1">
                    <template v-for="link in contacts.links" :key="link.label">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            class="inline-flex items-center px-3 py-1 rounded text-sm"
                            :class="link.active
                                ? 'bg-indigo-600 text-white'
                                : 'bg-white text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50'"
                            v-html="link.label"
                        />
                        <span
                            v-else
                            class="inline-flex items-center px-3 py-1 rounded text-sm text-gray-400"
                            v-html="link.label"
                        />
                    </template>
                </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
