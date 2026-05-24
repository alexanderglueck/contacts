<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    title: { type: String, default: null },
});

const { t } = useI18n();

const page = usePage();
const subscriptionCancelled = computed(() => page.props.auth?.subscription_cancelled);
const resolvedTitle = computed(() => props.title ?? t('nav.profile'));

const isActive = (name) => {
    try {
        return route().current(name);
    } catch (e) {
        return false;
    }
};

const sections = computed(() => [
    {
        heading: t('settings_nav.section_account'),
        links: [
            { name: 'user_settings.profile.show', label: t('settings_nav.profile') },
            { name: 'user_settings.image.show', label: t('settings_nav.profile_picture') },
            { name: 'user_settings.password.show', label: t('settings_nav.password') },
            { name: 'user_settings.two_factor.edit', label: t('settings_nav.two_factor') },
            { name: 'user_settings.sessions.index', label: t('settings_nav.sessions') },
        ],
    },
    {
        heading: t('settings_nav.section_communication'),
        links: [
            { name: 'user_settings.notifications.show', label: t('settings_nav.notifications') },
        ],
    },
    {
        heading: t('settings_nav.section_developer'),
        links: [
            { name: 'user_settings.api_token.index', label: t('settings_nav.api_tokens') },
        ],
    },
    {
        heading: t('settings_nav.section_billing'),
        links: [
            { name: 'user_settings.subscription.swap.index', label: t('settings_nav.change_plan') },
            { name: 'user_settings.subscription.invoices.index', label: t('settings_nav.invoices') },
            { name: 'user_settings.subscription.card.index', label: t('settings_nav.payment_method') },
            subscriptionCancelled.value
                ? { name: 'user_settings.subscription.resume.index', label: t('settings_nav.resume_subscription') }
                : { name: 'user_settings.subscription.cancel.index', label: t('settings_nav.cancel_subscription') },
        ],
    },
    {
        heading: t('settings_nav.section_danger'),
        links: [
            { name: 'user_settings.deactivate.index', label: t('settings_nav.deactivate_account') },
            { name: 'user_settings.delete.show', label: t('settings_nav.delete_account') },
        ],
    },
]);
</script>

<template>
    <AppLayout :title="resolvedTitle">
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
