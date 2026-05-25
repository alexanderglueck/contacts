<script setup>
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';

const { t } = useI18n();

const props = defineProps({
    team: { type: Object, required: true },
    can: { type: Object, default: () => ({}) },
});

const inviteForm = useForm({
    email: '',
});

const submitInvite = () => {
    inviteForm.post(route('teams.members.invite', props.team.uuid), {
        onSuccess: () => inviteForm.reset('email'),
    });
};

const pendingRemove = ref(null);
const removeBusy = ref(false);
const askRemoveMember = (user) => { pendingRemove.value = user; };
const cancelRemoveMember = () => { pendingRemove.value = null; };
const confirmRemoveMember = () => {
    const user = pendingRemove.value;
    if (! user) return;
    removeBusy.value = true;
    router.delete(route('teams.members.destroy', [props.team.uuid, user.ulid]), {
        onFinish: () => {
            removeBusy.value = false;
            pendingRemove.value = null;
        },
    });
};

const impersonate = (ulid) => {
    router.post(route('user.impersonate'), { userUlid: ulid });
};
</script>

<template>
    <AppLayout :title="`${t('teams.members')} — ${team.name}`">
        <Head :title="`${t('teams.members')} — ${team.name}`" />

        <div class="space-y-4">
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-medium text-gray-900">{{ t('teams.members_of', { name: team.name }) }}</h2>
                    <Link :href="route('teams.index')">
                        <SecondaryButton type="button">{{ t('teams.back') }}</SecondaryButton>
                    </Link>
                </div>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ t('teams.name') }}</th>
                            <th class="px-6 py-3"></th>
                            <th v-if="can.impersonate" class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="user in team.users" :key="user.ulid">
                            <td class="px-6 py-3 text-sm text-gray-900">{{ user.name }}</td>
                            <td class="px-6 py-3 text-right">
                                <DangerButton
                                    v-if="can.manage && !user.is_self"
                                    type="button"
                                    @click="askRemoveMember(user)"
                                >
                                    {{ t('common.delete') }}
                                </DangerButton>
                            </td>
                            <td v-if="can.impersonate" class="px-6 py-3 text-right">
                                <SecondaryButton
                                    v-if="!user.is_self"
                                    type="button"
                                    @click="impersonate(user.ulid)"
                                >
                                    {{ t('teams.impersonate') }}
                                </SecondaryButton>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">{{ t('teams.pending_invitations') }}</h2>
                </div>

                <div v-if="team.invites.length === 0" class="px-6 py-4 text-sm text-gray-600">
                    {{ t('teams.no_pending_invitations') }}
                </div>

                <table v-else class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ t('teams.email_label') }}</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="invite in team.invites" :key="invite.ulid">
                            <td class="px-6 py-3 text-sm text-gray-900">{{ invite.email }}</td>
                            <td class="px-6 py-3 text-right">
                                <Link :href="route('teams.members.resend_invite', invite.ulid)">
                                    <SecondaryButton type="button">{{ t('teams.resend_invite') }}</SecondaryButton>
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <form @submit.prevent="submitInvite" class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">{{ t('teams.invite_to', { name: team.name }) }}</h2>
                </div>

                <div class="px-6 py-4 space-y-4">
                    <div>
                        <InputLabel for="email" :value="t('teams.email_label')" />
                        <TextInput
                            id="email"
                            type="email"
                            v-model="inviteForm.email"
                            required
                        />
                        <InputError :message="inviteForm.errors.email" />
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                    <PrimaryButton :disabled="inviteForm.processing" :class="{ 'opacity-50': inviteForm.processing }">
                        {{ t('teams.invite_action') }}
                    </PrimaryButton>
                </div>
            </form>
        </div>

        <ConfirmModal
            :open="pendingRemove !== null"
            :title="t('teams.remove_member_title')"
            :body="pendingRemove ? t('teams.remove_member_confirm', { name: pendingRemove.name }) : ''"
            :confirm-label="t('common.remove')"
            variant="danger"
            :busy="removeBusy"
            @confirm="confirmRemoveMember"
            @cancel="cancelRemoveMember"
        />
    </AppLayout>
</template>
