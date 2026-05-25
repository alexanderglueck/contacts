<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';

const { t } = useI18n();

defineProps({
    teams: { type: Array, default: () => [] },
});

const pendingDelete = ref(null);
const deleteBusy = ref(false);
const askDestroy = (team) => { pendingDelete.value = team; };
const cancelDestroy = () => { pendingDelete.value = null; };
const confirmDestroy = () => {
    const team = pendingDelete.value;
    if (! team) return;
    deleteBusy.value = true;
    router.delete(route('teams.destroy', team.uuid), {
        onFinish: () => {
            deleteBusy.value = false;
            pendingDelete.value = null;
        },
    });
};
</script>

<template>
    <AppLayout :title="t('teams.title')">
        <Head :title="t('teams.title')" />

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-900">{{ t('teams.title') }}</h2>
                <Link :href="route('teams.create')">
                    <PrimaryButton type="button">{{ t('teams.create') }}</PrimaryButton>
                </Link>
            </div>

            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ t('teams.name') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ t('teams.status') }}</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="team in teams" :key="team.uuid">
                        <td class="px-6 py-3 text-sm text-gray-900">{{ team.name }}</td>
                        <td class="px-6 py-3 text-sm">
                            <span
                                v-if="team.is_owner"
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800"
                            >
                                {{ t('teams.owner') }}
                            </span>
                            <span
                                v-else
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800"
                            >
                                {{ t('teams.member') }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-sm text-right space-x-2">
                            <span
                                v-if="team.is_current"
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800"
                            >
                                {{ t('teams.current_team') }}
                            </span>
                            <Link v-else-if="team.created" :href="route('teams.switch', team.uuid)">
                                <SecondaryButton type="button">{{ t('teams.switch') }}</SecondaryButton>
                            </Link>

                            <Link :href="route('teams.members.show', team.uuid)">
                                <SecondaryButton type="button">{{ t('teams.members') }}</SecondaryButton>
                            </Link>

                            <template v-if="team.is_owner">
                                <Link :href="route('teams.edit', team.uuid)">
                                    <SecondaryButton type="button">{{ t('common.edit') }}</SecondaryButton>
                                </Link>
                                <DangerButton type="button" @click="askDestroy(team)">{{ t('common.delete') }}</DangerButton>
                            </template>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <ConfirmModal
            :open="pendingDelete !== null"
            :title="t('teams.delete_title')"
            :body="pendingDelete ? t('teams.delete_confirm', { name: pendingDelete.name }) : ''"
            :confirm-label="t('common.delete')"
            variant="danger"
            :busy="deleteBusy"
            @confirm="confirmDestroy"
            @cancel="cancelDestroy"
        />
    </AppLayout>
</template>
