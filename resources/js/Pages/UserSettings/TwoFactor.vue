<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    state: { type: String, required: true },
    qrUrl: { type: String, default: null },
    secret: { type: String, default: null },
    backupCodes: { type: Array, default: () => [] },
});

// QR code rendered via an external image service that consumes otpauth:// URLs.
// TODO: replace with client-side renderer once a QR lib is added.
const qrImage = (url) =>
    `https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=${encodeURIComponent(url)}`;

const enableForm = useForm({});
const enable = () => enableForm.post(route('user_settings.two_factor.enable'));

const checkForm = useForm({ secret: '' });
const check = () => checkForm.post(route('user_settings.two_factor.check'));

const disableForm = useForm({});
const disable = () => disableForm.delete(route('user_settings.two_factor.destroy'));
</script>

<template>
    <AppLayout title="Two-factor authentication">
        <Head title="Two-factor authentication" />

        <div v-if="state === 'inactive'" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Two-factor authentication</h2>
            </div>
            <form @submit.prevent="enable" class="px-6 py-4 space-y-4">
                <p class="text-sm text-gray-600">
                    Click the button to enable two-factor authentication.
                </p>
                <PrimaryButton :disabled="enableForm.processing" :class="{ 'opacity-50': enableForm.processing }">
                    Enable 2FA
                </PrimaryButton>
            </form>
        </div>

        <div v-else-if="state === 'activate'" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Scan QR code</h2>
            </div>
            <form @submit.prevent="check" class="px-6 py-4 space-y-4">
                <p class="text-sm text-gray-600">
                    Scan this QR code with your authenticator app, then enter the generated code below to confirm.
                </p>

                <div class="flex justify-center">
                    <img :src="qrImage(qrUrl)" alt="2FA QR code" class="w-56 h-56" />
                </div>

                <div class="text-center">
                    <p class="text-xs text-gray-500">If you cannot scan the code, enter this secret manually:</p>
                    <code class="text-sm break-all">{{ secret }}</code>
                </div>

                <div>
                    <InputLabel for="secret" value="Authentication code" />
                    <TextInput
                        id="secret"
                        v-model="checkForm.secret"
                        autocomplete="one-time-code"
                        autofocus
                        required
                    />
                    <InputError :message="checkForm.errors.secret" />
                </div>

                <div class="flex justify-end">
                    <PrimaryButton :disabled="checkForm.processing" :class="{ 'opacity-50': checkForm.processing }">
                        Activate
                    </PrimaryButton>
                </div>
            </form>
        </div>

        <div v-else-if="state === 'active'" class="space-y-4">
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Backup codes</h2>
                </div>
                <div class="px-6 py-4">
                    <ol class="list-decimal list-inside space-y-1 text-sm font-mono text-gray-800">
                        <li v-for="code in backupCodes" :key="code.id">{{ code.value }}</li>
                    </ol>
                </div>
            </div>

            <form @submit.prevent="disable" class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 flex justify-end">
                    <DangerButton :disabled="disableForm.processing" :class="{ 'opacity-50': disableForm.processing }">
                        Deactivate 2FA
                    </DangerButton>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
