<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

defineProps({
    teams: { type: Array, default: () => [] },
});

const destroy = (team) => {
    if (!confirm(`Delete team "${team.name}"?`)) return;
    router.delete(route('teams.destroy', team.id));
};
</script>

<template>
    <AppLayout title="Teams">
        <Head title="Teams" />

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-900">Teams</h2>
                <Link :href="route('teams.create')">
                    <PrimaryButton type="button">Create team</PrimaryButton>
                </Link>
            </div>

            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="team in teams" :key="team.id">
                        <td class="px-6 py-3 text-sm text-gray-900">{{ team.name }}</td>
                        <td class="px-6 py-3 text-sm">
                            <span
                                v-if="team.is_owner"
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800"
                            >
                                Owner
                            </span>
                            <span
                                v-else
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800"
                            >
                                Member
                            </span>
                        </td>
                        <td class="px-6 py-3 text-sm text-right space-x-2">
                            <span
                                v-if="team.is_current"
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800"
                            >
                                Current team
                            </span>
                            <Link v-else-if="team.created" :href="route('teams.switch', team.id)">
                                <SecondaryButton type="button">Switch</SecondaryButton>
                            </Link>

                            <Link :href="route('teams.members.show', team.id)">
                                <SecondaryButton type="button">Members</SecondaryButton>
                            </Link>

                            <template v-if="team.is_owner">
                                <Link :href="route('teams.edit', team.id)">
                                    <SecondaryButton type="button">Edit</SecondaryButton>
                                </Link>
                                <DangerButton type="button" @click="destroy(team)">Delete</DangerButton>
                            </template>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
