<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import PageJumper from '@/Components/PageJumper.vue';

defineProps({
    contactGroups: { type: Object, required: true },
    canCreate: { type: Boolean, default: false },
});
</script>

<template>
    <AppLayout title="Contact groups">
        <Head title="Contact groups" />

        <div class="bg-white shadow rounded-lg">
            <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">Contact groups</h2>
                <Link v-if="canCreate" :href="route('contact_groups.create')">
                    <PrimaryButton type="button">Create contact group</PrimaryButton>
                </Link>
            </div>

            <div v-if="contactGroups.data.length === 0" class="px-6 py-8 text-center text-sm text-gray-500">
                No contact groups yet.
            </div>

            <ul v-else class="divide-y divide-gray-200">
                <li v-for="group in contactGroups.data" :key="group.id">
                    <Link
                        :href="route('contact_groups.show', group.ulid)"
                        class="block px-6 py-3 hover:bg-gray-50 text-sm text-gray-900"
                    >
                        {{ group.name }}
                    </Link>
                </li>
            </ul>

            <div v-if="contactGroups.total > 0" class="border-t border-gray-200 px-6 py-3 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                <p class="text-sm text-gray-600">
                    Showing <span class="font-medium">{{ contactGroups.from }}</span>–<span class="font-medium">{{ contactGroups.to }}</span>
                    of <span class="font-medium">{{ contactGroups.total }}</span>
                    {{ contactGroups.total === 1 ? 'contact group' : 'contact groups' }}
                </p>
                <div v-if="contactGroups.last_page > 1" class="flex flex-wrap items-center gap-2">
                    <PageJumper :current-page="contactGroups.current_page" :last-page="contactGroups.last_page" />
                    <div class="flex gap-1">
                        <template v-for="link in contactGroups.links" :key="link.label">
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
