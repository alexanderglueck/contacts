<script setup>
import { computed } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const { t } = useI18n();

const page = usePage();
const status = computed(() => page.props.status);
const verificationLinkSent = computed(() => status.value === 'verification-link-sent');

const resendForm = useForm({});
const resend = () => resendForm.post(route('verification.send'));

const logoutForm = useForm({});
const logout = () => logoutForm.post(route('logout'));
</script>

<template>
    <GuestLayout>
        <Head :title="t('auth.verify_email_title')" />

        <h1 class="text-xl font-semibold text-gray-900 mb-2">{{ t('auth.verify_email_title') }}</h1>
        <p class="text-sm text-gray-600 mb-4">
            {{ t('auth.verify_email_body') }}
        </p>

        <div v-if="verificationLinkSent" class="mb-4 rounded-md bg-green-50 p-3 text-sm text-green-800">
            {{ t('auth.verify_email_sent') }}
        </div>

        <div class="flex items-center justify-between">
            <form @submit.prevent="resend">
                <PrimaryButton :class="{ 'opacity-50': resendForm.processing }" :disabled="resendForm.processing">
                    {{ t('auth.resend_verification_email') }}
                </PrimaryButton>
            </form>

            <form @submit.prevent="logout">
                <button type="submit" class="text-sm text-gray-600 hover:text-gray-900 underline">
                    {{ t('nav.logout') }}
                </button>
            </form>
        </div>
    </GuestLayout>
</template>
