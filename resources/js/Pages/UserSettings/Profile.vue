<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import SettingsLayout from '@/Layouts/SettingsLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import PasskeysCard from '@/Components/Passkeys/PasskeysCard.vue';

const props = defineProps({
    user: { type: Object, required: true },
    passkeys: { type: Array, default: null },
});

const form = useForm({
    name: props.user.name ?? '',
    email: props.user.email ?? '',
    password_reset_disabled: !!props.user.password_reset_disabled,
});

const submit = () => form.put(route('user_settings.profile.update'));
</script>

<template>
    <SettingsLayout title="Profile">
        <Head title="Profile" />

        <div class="space-y-6">
            <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Profile</h2>
                </div>

                <div class="px-6 py-4 space-y-4">
                    <div>
                        <InputLabel for="name" value="Name" />
                        <TextInput id="name" v-model="form.name" required autofocus />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div>
                        <InputLabel for="email" value="Email" />
                        <TextInput id="email" type="email" v-model="form.email" required />
                        <InputError :message="form.errors.email" />
                    </div>

                    <div class="pt-2 border-t border-gray-200">
                        <label class="flex items-start gap-2">
                            <Checkbox v-model:checked="form.password_reset_disabled" class="mt-1" />
                            <span class="text-sm text-gray-700">
                                <span class="font-medium text-gray-900">Disable password reset emails</span>
                                <span class="block text-xs text-gray-600 mt-0.5">
                                    When checked, the "forgot password" flow won't send a reset link
                                    to this address. Useful if you sign in with a passkey or SSO and
                                    want to make sure the password is the only path in.
                                </span>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                    <PrimaryButton :disabled="form.processing" :class="{ 'opacity-50': form.processing }">
                        Update
                    </PrimaryButton>
                </div>
            </form>

            <PasskeysCard :passkeys="passkeys" />
        </div>
    </SettingsLayout>
</template>
