<script setup>
import { computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import SettingsLayout from '@/Layouts/SettingsLayout.vue';
import DangerButton from '@/Components/DangerButton.vue';

const { t } = useI18n();

const props = defineProps({
    lastMemberTeams: { type: Array, default: () => [] },
    coOwnedTeams: { type: Array, default: () => [] },
});

const blocked = computed(() => props.coOwnedTeams.length > 0);
const teamCountLabelKey = (count) =>
    count === 1 ? 'settings.delete.team_count_label_one' : 'settings.delete.team_count_label_many';
const stillOwnerBodyKey = computed(() =>
    props.coOwnedTeams.length === 1
        ? 'settings.delete.still_owner_body_one'
        : 'settings.delete.still_owner_body_many',
);
const alsoDeletedTitleKey = computed(() =>
    props.lastMemberTeams.length === 1
        ? 'settings.delete.also_deleted_title_one'
        : 'settings.delete.also_deleted_title_many',
);
const alsoDeletedBodyKey = computed(() =>
    props.lastMemberTeams.length === 1
        ? 'settings.delete.also_deleted_body_one'
        : 'settings.delete.also_deleted_body_many',
);

const form = useForm({});

const submit = () => {
    if (blocked.value) return;
    form.delete(route('user_settings.delete.destroy'));
};
</script>

<template>
    <SettingsLayout :title="t('settings.delete.title')">
        <Head :title="t('settings.delete.title')" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">{{ t('settings.delete.heading') }}</h2>
            </div>

            <div class="px-6 py-4 space-y-4">
                <p class="text-sm text-gray-600">
                    {{ t('settings.delete.body') }}
                </p>

                <div v-if="blocked" class="rounded-md bg-red-50 ring-1 ring-red-200 p-4 text-sm text-red-900">
                    <p class="font-medium">{{ t('settings.delete.still_owner_title') }}</p>
                    <ul class="mt-1 list-disc list-inside">
                        <li v-for="team in coOwnedTeams" :key="team.name">
                            <span class="font-medium">{{ team.name }}</span>
                            {{ t(teamCountLabelKey(team.members), { count: team.members }) }}
                        </li>
                    </ul>
                    <p class="mt-2">
                        {{ t(stillOwnerBodyKey) }}
                    </p>
                </div>

                <div v-if="lastMemberTeams.length && !blocked" class="rounded-md bg-amber-50 ring-1 ring-amber-200 p-4 text-sm text-amber-900">
                    <p class="font-medium">
                        {{ t(alsoDeletedTitleKey) }}
                    </p>
                    <ul class="mt-1 list-disc list-inside">
                        <li v-for="team in lastMemberTeams" :key="team.name">{{ team.name }}</li>
                    </ul>
                    <p class="mt-2">
                        {{ t(alsoDeletedBodyKey) }}
                    </p>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                <DangerButton
                    :disabled="blocked || form.processing"
                    :class="{ 'opacity-50 cursor-not-allowed': blocked || form.processing }"
                >
                    {{ t('settings.delete.action') }}
                </DangerButton>
            </div>
        </form>
    </SettingsLayout>
</template>
