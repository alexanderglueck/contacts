<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import SettingsLayout from '@/Layouts/SettingsLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const { t } = useI18n();

defineProps({
    tokens: { type: Array, default: () => [] },
    newToken: { type: Object, default: null },
});

const createForm = useForm({
    name: '',
});

const submit = () => {
    createForm.post(route('user_settings.api_token.store'), {
        preserveScroll: true,
        onSuccess: () => createForm.reset(),
    });
};

const revokingId = ref(null);

const revoke = (id) => {
    if (! confirm(t('settings.api_tokens.revoke_confirm'))) return;
    revokingId.value = id;
    useForm({}).delete(route('user_settings.api_token.destroy', id), {
        preserveScroll: true,
        onFinish: () => { revokingId.value = null; },
    });
};

const copy = async (value) => {
    try {
        await navigator.clipboard.writeText(value);
    } catch (e) {
        const el = document.createElement('textarea');
        el.value = value;
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
    }
};
</script>

<template>
    <SettingsLayout :title="t('settings.api_tokens.title')">
        <Head :title="t('settings.api_tokens.title')" />

        <div v-if="newToken" class="bg-emerald-50 ring-1 ring-emerald-200 rounded-lg p-4 text-sm text-emerald-900 space-y-2">
            <p class="font-medium">
                {{ t('settings.api_tokens.new_token_intro', { name: newToken.name }) }}
            </p>
            <div class="flex items-center gap-2">
                <code class="flex-1 break-all bg-white px-3 py-2 rounded font-mono text-xs">{{ newToken.plain_text }}</code>
                <SecondaryButton type="button" @click="copy(newToken.plain_text)">{{ t('settings.api_tokens.copy') }}</SecondaryButton>
            </div>
            <p class="text-xs">
                {{ t('settings.api_tokens.bearer_help') }}
            </p>
        </div>

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">{{ t('settings.api_tokens.create') }}</h2>
            </div>
            <div class="px-6 py-4 space-y-4">
                <div>
                    <InputLabel for="name" :value="t('settings.api_tokens.name')" />
                    <TextInput
                        id="name"
                        v-model="createForm.name"
                        autocomplete="off"
                        :placeholder="t('settings.api_tokens.name_placeholder')"
                        required
                    />
                    <InputError :message="createForm.errors.name" />
                    <p class="mt-1 text-xs text-gray-500">
                        {{ t('settings.api_tokens.name_help') }}
                    </p>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                <PrimaryButton :disabled="createForm.processing" :class="{ 'opacity-50': createForm.processing }">
                    {{ t('settings.api_tokens.create') }}
                </PrimaryButton>
            </div>
        </form>

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">{{ t('settings.api_tokens.existing') }}</h2>
            </div>

            <div v-if="tokens.length === 0" class="px-6 py-6 text-center text-sm text-gray-500">
                {{ t('settings.api_tokens.none') }}
            </div>

            <table v-else class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-2 text-left text-xs font-semibold uppercase text-gray-500">{{ t('settings.api_tokens.name') }}</th>
                        <th class="px-6 py-2 text-left text-xs font-semibold uppercase text-gray-500">{{ t('settings.api_tokens.created_at') }}</th>
                        <th class="px-6 py-2 text-left text-xs font-semibold uppercase text-gray-500">{{ t('settings.api_tokens.last_used') }}</th>
                        <th class="px-6 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr v-for="token in tokens" :key="token.id">
                        <td class="px-6 py-2 text-sm text-gray-900">{{ token.name }}</td>
                        <td class="px-6 py-2 text-sm text-gray-700">{{ token.created_at }}</td>
                        <td class="px-6 py-2 text-sm text-gray-700">{{ token.last_used_at ?? t('settings.api_tokens.never') }}</td>
                        <td class="px-6 py-2 text-right">
                            <DangerButton
                                type="button"
                                @click="revoke(token.id)"
                                :disabled="revokingId === token.id"
                                :class="{ 'opacity-50': revokingId === token.id }"
                            >
                                {{ t('settings.api_tokens.revoke') }}
                            </DangerButton>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </SettingsLayout>
</template>
