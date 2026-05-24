<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    team: { type: Object, required: true },
});

const form = useForm({
    name: props.team.name ?? '',
    password_reset_disabled: !!props.team.password_reset_disabled,
});

const submit = () => form.put(route('teams.update', props.team.uuid));
</script>

<template>
    <AppLayout :title="`Edit team ${team.name}`">
        <Head :title="`Edit team ${team.name}`" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Edit team {{ team.name }}</h2>
            </div>

            <div class="px-6 py-4 space-y-4">
                <div>
                    <InputLabel for="name" value="Name" />
                    <TextInput id="name" v-model="form.name" required autofocus />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="pt-2 border-t border-gray-200">
                    <label class="flex items-start gap-2">
                        <Checkbox v-model:checked="form.password_reset_disabled" class="mt-1" />
                        <span class="text-sm text-gray-700">
                            <span class="font-medium text-gray-900">Disable password reset for all team members</span>
                            <span class="block text-xs text-gray-600 mt-0.5">
                                Overrides each member's personal setting. While enabled, no member
                                of this team can receive a password reset email. Useful when the
                                team enforces passkey or SSO sign-in policy.
                            </span>
                        </span>
                    </label>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
                <Link :href="route('teams.index')">
                    <SecondaryButton type="button">Cancel</SecondaryButton>
                </Link>
                <PrimaryButton :disabled="form.processing" :class="{ 'opacity-50': form.processing }">
                    Save
                </PrimaryButton>
            </div>
        </form>
    </AppLayout>
</template>
