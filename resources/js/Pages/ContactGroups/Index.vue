<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

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
                        :href="route('contact_groups.show', group.slug)"
                        class="block px-6 py-3 hover:bg-gray-50 text-sm text-gray-900"
                    >
                        {{ group.name }}
                    </Link>
                </li>
            </ul>

            <div v-if="contactGroups.links && contactGroups.last_page > 1" class="border-t border-gray-200 px-6 py-3 flex justify-between items-center">
                <p class="text-sm text-gray-600">
                    Page {{ contactGroups.current_page }} of {{ contactGroups.last_page }}
                </p>
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
    </AppLayout>
</template>
