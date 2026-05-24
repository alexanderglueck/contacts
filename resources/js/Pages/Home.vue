<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const page = usePage();
const can = computed(() => page.props.auth?.can ?? {});
const isSubscribed = computed(() => page.props.auth?.is_subscribed);

const links = computed(() =>
    [
        { ability: 'view_contacts', label: 'Contacts', description: 'Manage your contacts', route: 'contacts.index' },
        { ability: 'view_contact_groups', label: 'Contact groups', description: 'Manage contact groups', route: 'contact_groups.index' },
        { ability: 'view_calendar', label: 'Calendar', description: 'View upcoming dates', route: 'calendar.index' },
        { ability: 'view_map', label: 'Map', description: 'See contacts on a map', route: 'map.index' },
    ].filter((l) => can.value[l.ability])
);

const noPermissions = computed(() => links.value.length === 0);
</script>

<template>
    <AppLayout title="Dashboard">
        <Head title="Dashboard" />

        <div class="bg-white shadow rounded-lg">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-medium text-gray-900">Dashboard</h2>
            </div>

            <div class="px-6 py-4">
                <template v-if="isSubscribed">
                    <p v-if="noPermissions" class="text-sm text-gray-600">
                        You don't have any permissions yet. Ask an administrator to grant you access.
                    </p>

                    <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <Link
                            v-for="link in links"
                            :key="link.route"
                            :href="route(link.route)"
                            class="block rounded-lg border border-gray-200 p-4 hover:border-indigo-500 hover:bg-indigo-50 transition"
                        >
                            <p class="text-sm font-semibold text-gray-900">{{ link.label }}</p>
                            <p class="mt-1 text-sm text-gray-600">{{ link.description }}</p>
                        </Link>
                    </div>
                </template>

                <div v-else>
                    <p class="text-sm text-gray-600 mb-4">
                        You do not have an active subscription.
                    </p>
                    <Link
                        :href="route('plans.index')"
                        class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500"
                    >
                        View available plans
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
