<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

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

const removeMember = (user) => {
    if (!confirm(`Remove ${user.name} from the team?`)) return;
    router.delete(route('teams.members.destroy', [props.team.uuid, user.slug]));
};

const impersonate = (slug) => {
    router.post(route('user.impersonate'), { userSlug: slug });
};
</script>

<template>
    <AppLayout :title="`Members — ${team.name}`">
        <Head :title="`Members — ${team.name}`" />

        <div class="space-y-4">
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-medium text-gray-900">Members of "{{ team.name }}"</h2>
                    <Link :href="route('teams.index')">
                        <SecondaryButton type="button">Back</SecondaryButton>
                    </Link>
                </div>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3"></th>
                            <th v-if="can.impersonate" class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="user in team.users" :key="user.slug">
                            <td class="px-6 py-3 text-sm text-gray-900">{{ user.name }}</td>
                            <td class="px-6 py-3 text-right">
                                <DangerButton
                                    v-if="can.manage && !user.is_self"
                                    type="button"
                                    @click="removeMember(user)"
                                >
                                    Delete
                                </DangerButton>
                            </td>
                            <td v-if="can.impersonate" class="px-6 py-3 text-right">
                                <SecondaryButton
                                    v-if="!user.is_self"
                                    type="button"
                                    @click="impersonate(user.slug)"
                                >
                                    Impersonate
                                </SecondaryButton>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Pending invitations</h2>
                </div>

                <div v-if="team.invites.length === 0" class="px-6 py-4 text-sm text-gray-600">
                    No pending invitations.
                </div>

                <table v-else class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="invite in team.invites" :key="invite.ulid">
                            <td class="px-6 py-3 text-sm text-gray-900">{{ invite.email }}</td>
                            <td class="px-6 py-3 text-right">
                                <Link :href="route('teams.members.resend_invite', invite.ulid)">
                                    <SecondaryButton type="button">Resend invite</SecondaryButton>
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <form @submit.prevent="submitInvite" class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Invite to "{{ team.name }}"</h2>
                </div>

                <div class="px-6 py-4 space-y-4">
                    <div>
                        <InputLabel for="email" value="Email address" />
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
                        Invite to team
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
