<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';

const { t, locale } = useI18n();

const props = defineProps({
    todaysEvents: { type: Array, default: () => [] },
    upcomingEvents: { type: Array, default: () => [] },
});

const page = usePage();
const can = computed(() => page.props.auth?.can ?? {});
const isSubscribed = computed(() => page.props.auth?.is_subscribed);

const links = computed(() =>
    [
        { ability: 'view_contacts', labelKey: 'home.tiles.contacts', descKey: 'home.tiles.contacts_desc', route: 'contacts.index' },
        { ability: 'view_contact_groups', labelKey: 'home.tiles.groups', descKey: 'home.tiles.groups_desc', route: 'contact_groups.index' },
        { ability: 'view_calendar', labelKey: 'home.tiles.calendar', descKey: 'home.tiles.calendar_desc', route: 'calendar.index' },
        { ability: 'view_map', labelKey: 'home.tiles.map', descKey: 'home.tiles.map_desc', route: 'map.index' },
    ].filter((l) => can.value[l.ability])
);

const noPermissions = computed(() => links.value.length === 0);

const formatDate = (iso) =>
    new Date(iso + 'T00:00:00').toLocaleDateString(locale.value, {
        weekday: 'short',
        month: 'short',
        day: 'numeric',
    });

const relative = (days) => {
    if (days === 0) return t('home.today');
    if (days === 1) return t('home.tomorrow');
    return t('home.in_n_days', { days });
};

// Both birthdays and important dates show their label (e.g. "32. Geburtstag",
// "16. Hochzeitstag") — same wording the ContactDate reminders have always used.
const detail = (event) => event.label;
</script>

<template>
    <AppLayout :title="t('home.title')">
        <Head :title="t('home.title')" />

        <div class="space-y-4">
            <div
                v-if="isSubscribed && todaysEvents.length"
                class="rounded-lg bg-amber-50 ring-1 ring-amber-200 px-6 py-4"
            >
                <p class="text-sm font-semibold text-amber-900">
                    {{ todaysEvents.length === 1 ? t('home.events_today_one') : t('home.events_today_many') }}
                </p>
                <ul class="mt-2 space-y-1">
                    <li v-for="(event, i) in todaysEvents" :key="event.ulid + '-' + i" class="text-sm text-amber-900">
                        <Link :href="route('contacts.show', event.ulid)" class="font-medium underline hover:no-underline">
                            {{ event.fullname }}
                        </Link>
                        — <span class="font-medium">{{ event.label }}</span>
                    </li>
                </ul>
            </div>

            <div class="bg-white shadow rounded-lg">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-medium text-gray-900">{{ t('home.title') }}</h2>
                </div>

                <div class="px-6 py-4">
                    <template v-if="isSubscribed">
                        <p v-if="noPermissions" class="text-sm text-gray-600">
                            {{ t('home.no_permissions') }}
                        </p>

                        <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <Link
                                v-for="link in links"
                                :key="link.route"
                                :href="route(link.route)"
                                class="block rounded-lg border border-gray-200 p-4 hover:border-indigo-500 hover:bg-indigo-50 transition"
                            >
                                <p class="text-sm font-semibold text-gray-900">{{ t(link.labelKey) }}</p>
                                <p class="mt-1 text-sm text-gray-600">{{ t(link.descKey) }}</p>
                            </Link>
                        </div>
                    </template>

                    <div v-else>
                        <p class="text-sm text-gray-600 mb-4">
                            {{ t('home.no_subscription') }}
                        </p>
                        <Link
                            :href="route('plans.index')"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500"
                        >
                            {{ t('home.view_plans') }}
                        </Link>
                    </div>
                </div>
            </div>

            <div v-if="isSubscribed" class="bg-white shadow rounded-lg">
                <div class="border-b border-gray-200 px-6 py-4 flex items-baseline justify-between">
                    <h2 class="text-sm font-semibold text-gray-900">{{ t('home.upcoming_events') }}</h2>
                    <p class="text-xs text-gray-500">{{ t('home.next_7_days') }}</p>
                </div>

                <ol v-if="upcomingEvents.length" class="divide-y divide-gray-100">
                    <li v-for="(event, i) in upcomingEvents" :key="event.ulid + '-' + i">
                        <Link
                            :href="route('contacts.show', event.ulid)"
                            class="block px-6 py-3 hover:bg-gray-50 flex items-baseline justify-between gap-4"
                        >
                            <div>
                                <p class="text-sm font-medium text-gray-900 flex items-center gap-2">
                                    {{ event.fullname }}
                                    <span
                                        class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-medium ring-1 ring-inset"
                                        :class="event.type === 'birthday'
                                            ? 'bg-amber-50 text-amber-700 ring-amber-200'
                                            : 'bg-sky-50 text-sky-700 ring-sky-200'"
                                    >
                                        {{ event.type === 'birthday' ? t('home.type_birthday') : t('home.type_date') }}
                                    </span>
                                </p>
                                <p class="text-xs text-gray-500">{{ detail(event) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-medium text-indigo-600">{{ formatDate(event.date) }}</p>
                                <p class="text-xs text-gray-500">{{ relative(event.days_until) }}</p>
                            </div>
                        </Link>
                    </li>
                </ol>

                <p v-else class="px-6 py-6 text-sm text-gray-500 text-center">
                    {{ t('home.no_upcoming_events') }}
                </p>
            </div>
        </div>
    </AppLayout>
</template>
