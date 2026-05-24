<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import NavLink from '@/Components/NavLink.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import FlashBanner from '@/Components/FlashBanner.vue';

const { t } = useI18n();

defineProps({
    title: String,
});

const page = usePage();
const user = computed(() => page.props.auth?.user);

const isActive = (name) => {
    try {
        return route().current(name);
    } catch (e) {
        return false;
    }
};
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <nav class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="shrink-0 flex items-center">
                            <Link :href="route('home')">
                                <ApplicationLogo class="block h-9 w-auto" />
                            </Link>
                        </div>

                        <div class="hidden sm:-my-px sm:ms-10 sm:flex space-x-8">
                            <NavLink :href="route('contacts.index')" :active="isActive('contacts.*')">
                                {{ t('nav.contacts') }}
                            </NavLink>
                            <NavLink :href="route('contact_groups.index')" :active="isActive('contact_groups.*')">
                                {{ t('nav.groups') }}
                            </NavLink>
                            <NavLink :href="route('calendar.index')" :active="isActive('calendar.*')">
                                {{ t('nav.calendar') }}
                            </NavLink>
                            <NavLink :href="route('map.index')" :active="isActive('map.*')">
                                {{ t('nav.map') }}
                            </NavLink>
                            <NavLink :href="route('reports.index')" :active="isActive('reports.*')">
                                {{ t('nav.reports') }}
                            </NavLink>
                        </div>
                    </div>

                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <Dropdown v-if="user" align="right" width="48">
                            <template #trigger>
                                <button class="inline-flex items-center gap-2 px-2 py-1 text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none cursor-pointer">
                                    <img
                                        v-if="user.image"
                                        :src="`/storage/${user.image}`"
                                        :alt="user.name"
                                        class="h-8 w-8 rounded-full object-cover ring-1 ring-gray-200"
                                    />
                                    <span
                                        v-else
                                        class="h-8 w-8 rounded-full bg-indigo-600 text-white text-xs font-semibold flex items-center justify-center"
                                        aria-hidden="true"
                                    >
                                        {{ user.name?.charAt(0)?.toUpperCase() }}
                                    </span>
                                    <span>{{ user.name }}</span>
                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.06l3.71-3.83a.75.75 0 011.08 1.04l-4.25 4.39a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </template>
                            <template #content>
                                <DropdownLink :href="route('user_settings.profile.show')">{{ t('nav.profile') }}</DropdownLink>
                                <DropdownLink :href="route('user_settings.password.show')">{{ t('nav.password') }}</DropdownLink>
                                <DropdownLink :href="route('user_settings.two_factor.edit')">{{ t('nav.two_factor') }}</DropdownLink>
                                <DropdownLink :href="route('teams.index')">{{ t('nav.teams') }}</DropdownLink>
                                <DropdownLink :href="route('roles.index')">{{ t('nav.roles') }}</DropdownLink>
                                <DropdownLink :href="route('activities.index')">{{ t('nav.activities') }}</DropdownLink>
                                <DropdownLink :href="route('logs.index')">{{ t('nav.audit_log') }}</DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button">{{ t('nav.logout') }}</DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </div>
        </nav>

        <header v-if="title || $slots.header" class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <slot name="header">
                    <h1 class="text-xl font-semibold text-gray-900">{{ title }}</h1>
                </slot>
            </div>
        </header>

        <main>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-4">
                <FlashBanner />
                <slot />
            </div>
        </main>
    </div>
</template>
