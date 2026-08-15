<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { usePasskeyRegister } from '@laravel/passkeys/vue';
import { PasskeyExistsError } from '@laravel/passkeys';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';
import ConfirmPasswordModal from '@/Components/ConfirmPasswordModal.vue';

const { t } = useI18n();

const props = defineProps({
    passkeys: { type: Array, default: null },
});

const loaded = computed(() => Array.isArray(props.passkeys));
const adding = ref(false);
const newName = ref('');
const deletingId = ref(null);

const loadList = () => router.reload({ only: ['passkeys'] });

const { register, isLoading, error, errorInstance, isSupported } = usePasskeyRegister({
    onSuccess: () => {
        adding.value = false;
        newName.value = '';
        loadList();
    },
});

const startAdd = () => {
    adding.value = true;
    newName.value = '';
    if (!loaded.value) loadList();
};

const cancelAdd = () => { adding.value = false; };

// Passkey management routes sit behind Laravel's `password.confirm` middleware.
// Because the passkey client sends XHRs, that middleware answers with a bare
// 423 "Password confirmation required." instead of redirecting to the confirm
// screen -- so we have to collect the password ourselves before the protected
// request goes out, otherwise the WebAuthn prompt never even opens.
//
// While a prompt is open, passwordPrompt holds the promise resolver that the
// pending action is parked on; resolving it with false means "user cancelled".
const passwordPrompt = ref(null);

const ensurePasswordConfirmed = async () => {
    try {
        const { data } = await window.axios.get('/user/confirmed-password-status');
        if (data?.confirmed) return true;
    } catch {
        // An unreadable status is treated as "not confirmed" -- asking for the
        // password once too often beats a request that fails with 423.
    }

    return new Promise((resolve) => { passwordPrompt.value = resolve; });
};

const resolvePasswordPrompt = (confirmed) => {
    const resolve = passwordPrompt.value;
    passwordPrompt.value = null;
    resolve?.(confirmed);
};

const submitAdd = async () => {
    const name = newName.value.trim();
    if (!name) return;
    if (! await ensurePasswordConfirmed()) return;
    register(name);
};

const formatDate = (iso) => iso ? new Date(iso).toLocaleString() : '—';

// Two-step delete: askRemove(passkey) populates pendingRemoval, which both
// opens the modal and identifies which passkey to act on once confirmed.
const pendingRemoval = ref(null);
const askRemove = (passkey) => { pendingRemoval.value = passkey; };
const cancelRemove = () => { pendingRemoval.value = null; };
const confirmRemove = async () => {
    const passkey = pendingRemoval.value;
    if (! passkey) return;
    pendingRemoval.value = null;
    if (! await ensurePasswordConfirmed()) return;
    deletingId.value = passkey.id;
    try {
        await window.axios.delete(`/user/passkeys/${passkey.id}`);
        loadList();
    } catch (e) {
        alert(e?.response?.data?.message ?? t('passkeys.remove_failed'));
    } finally {
        deletingId.value = null;
    }
};
</script>

<template>
    <section class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-medium text-gray-900">{{ t('passkeys.title') }}</h2>
                <p class="text-xs text-gray-600 mt-0.5">
                    {{ t('passkeys.subtitle') }}
                </p>
            </div>
            <PrimaryButton
                v-if="isSupported && !adding"
                type="button"
                class="cursor-pointer"
                @click="startAdd"
            >
                {{ t('passkeys.add') }}
            </PrimaryButton>
        </div>

        <div v-if="!isSupported" class="px-6 py-4 text-sm text-gray-600">
            {{ t('passkeys.unsupported') }}
        </div>

        <div v-else class="px-6 py-4 space-y-4">
            <div v-if="adding" class="border border-gray-200 rounded-md p-4 bg-gray-50">
                <InputLabel for="passkey_name" :value="t('passkeys.name_label')" />
                <TextInput
                    id="passkey_name"
                    v-model="newName"
                    :placeholder="t('passkeys.name_placeholder')"
                    autofocus
                    :disabled="isLoading"
                />
                <p
                    v-if="errorInstance instanceof PasskeyExistsError"
                    class="text-sm text-red-600 mt-1"
                >
                    {{ t('passkeys.already_registered') }}
                </p>
                <InputError v-else :message="error ?? ''" />
                <div class="flex gap-2 justify-end mt-3">
                    <SecondaryButton type="button" class="cursor-pointer" :disabled="isLoading" @click="cancelAdd">
                        {{ t('common.cancel') }}
                    </SecondaryButton>
                    <PrimaryButton
                        type="button"
                        class="cursor-pointer"
                        :disabled="isLoading || !newName.trim()"
                        @click="submitAdd"
                    >
                        {{ isLoading ? t('passkeys.waiting') : t('passkeys.register') }}
                    </PrimaryButton>
                </div>
            </div>

            <div v-if="!loaded" class="text-sm text-gray-500">{{ t('passkeys.loading') }}</div>
            <div v-else-if="passkeys.length === 0" class="text-sm text-gray-600">
                {{ t('passkeys.none') }}
            </div>
            <ul v-else class="divide-y divide-gray-200 border border-gray-200 rounded-md">
                <li
                    v-for="passkey in passkeys"
                    :key="passkey.id"
                    class="flex items-center justify-between px-4 py-3"
                >
                    <div class="text-sm">
                        <div class="font-medium text-gray-900">{{ passkey.name }}</div>
                        <div class="text-xs text-gray-500">
                            {{ t('passkeys.added') }} {{ formatDate(passkey.created_at) }}
                            <span v-if="passkey.last_used_at">
                                · {{ t('passkeys.last_used') }} {{ formatDate(passkey.last_used_at) }}
                            </span>
                        </div>
                    </div>
                    <button
                        type="button"
                        class="text-sm text-red-600 hover:text-red-700 cursor-pointer"
                        :disabled="deletingId === passkey.id"
                        @click="askRemove(passkey)"
                    >
                        {{ deletingId === passkey.id ? t('passkeys.removing') : t('passkeys.remove') }}
                    </button>
                </li>
            </ul>
        </div>

        <ConfirmModal
            :open="pendingRemoval !== null"
            :title="t('passkeys.remove_title')"
            :body="pendingRemoval ? t('passkeys.remove_confirm', { name: pendingRemoval.name }) : ''"
            :confirm-label="t('passkeys.remove')"
            variant="danger"
            :busy="deletingId !== null"
            @confirm="confirmRemove"
            @cancel="cancelRemove"
        />

        <ConfirmPasswordModal
            :open="passwordPrompt !== null"
            :body="t('passkeys.confirm_password_help')"
            @confirmed="resolvePasswordPrompt(true)"
            @cancel="resolvePasswordPrompt(false)"
        />
    </section>
</template>
