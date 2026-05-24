<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    title: { type: String, default: 'Settings' },
});

const page = usePage();
const subscriptionCancelled = computed(() => page.props.auth?.subscription_cancelled);

const isActive = (name) => {
    try {
        return route().current(name);
    } catch (e) {
        return false;
    }
};

const sections = computed(() => [
    {
        heading: 'Account',
        links: [
            { name: 'user_settings.profile.show', label: 'Profile' },
            { name: 'user_settings.image.show', label: 'Profile picture' },
            { name: 'user_settings.password.show', label: 'Password' },
            { name: 'user_settings.two_factor.edit', label: 'Two-factor auth' },
            { name: 'user_settings.sessions.index', label: 'Active sessions' },
        ],
    },
    {
        heading: 'Communication',
        links: [
            { name: 'user_settings.notifications.show', label: 'Notifications' },
        ],
    },
    {
        heading: 'Developer',
        links: [
            { name: 'user_settings.api_token.index', label: 'API tokens' },
        ],
    },
    {
        heading: 'Billing',
        links: [
            { name: 'user_settings.subscription.swap.index', label: 'Change plan' },
            { name: 'user_settings.subscription.invoices.index', label: 'Invoices' },
            { name: 'user_settings.subscription.card.index', label: 'Payment method' },
            subscriptionCancelled.value
                ? { name: 'user_settings.subscription.resume.index', label: 'Resume subscription' }
                : { name: 'user_settings.subscription.cancel.index', label: 'Cancel subscription' },
        ],
    },
    {
        heading: 'Danger zone',
        links: [
            { name: 'user_settings.deactivate.index', label: 'Deactivate account' },
            { name: 'user_settings.delete.show', label: 'Delete account' },
        ],
    },
]);
</script>

<template>
    <AppLayout :title="title">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
            <aside class="lg:col-span-3">
                <nav class="bg-white shadow rounded-lg overflow-hidden divide-y divide-gray-100">
                    <div v-for="section in sections" :key="section.heading" class="py-2">
                        <p class="px-4 pt-1 pb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">
                            {{ section.heading }}
                        </p>
                        <ul>
                            <li v-for="link in section.links" :key="link.name">
                                <Link
                                    :href="route(link.name)"
                                    class="block px-4 py-2 text-sm"
                                    :class="isActive(link.name)
                                        ? 'bg-indigo-50 text-indigo-700 font-medium border-l-2 border-indigo-600'
                                        : 'text-gray-700 hover:bg-gray-50'"
                                >
                                    {{ link.label }}
                                </Link>
                            </li>
                        </ul>
                    </div>
                </nav>
            </aside>

            <div class="lg:col-span-9 space-y-4">
                <slot />
            </div>
        </div>
    </AppLayout>
</template>
