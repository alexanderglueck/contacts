<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { usePasskeyRegister } from '@laravel/passkeys/vue';
import { PasskeyExistsError } from '@laravel/passkeys';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

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

const submitAdd = () => {
    if (!newName.value.trim()) return;
    register(newName.value.trim());
};

const formatDate = (iso) => iso ? new Date(iso).toLocaleString() : '—';

const remove = async (passkey) => {
    if (!confirm(`Remove passkey "${passkey.name}"?`)) return;
    deletingId.value = passkey.id;
    try {
        await window.axios.delete(`/user/passkeys/${passkey.id}`);
        loadList();
    } catch (e) {
        if (e?.response?.status === 423 || e?.response?.status === 401) {
            window.location.href = '/user/confirm-password';
            return;
        }
        alert(e?.response?.data?.message ?? 'Could not remove passkey.');
    } finally {
        deletingId.value = null;
    }
};
</script>

<template>
    <section class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-medium text-gray-900">Passkeys</h2>
                <p class="text-xs text-gray-600 mt-0.5">
                    Sign in without a password using Touch ID, Windows Hello, a phone, or a hardware key.
                </p>
            </div>
            <PrimaryButton
                v-if="isSupported && !adding"
                type="button"
                class="cursor-pointer"
                @click="startAdd"
            >
                Add passkey
            </PrimaryButton>
        </div>

        <div v-if="!isSupported" class="px-6 py-4 text-sm text-gray-600">
            This browser doesn't support passkeys.
        </div>

        <div v-else class="px-6 py-4 space-y-4">
            <div v-if="adding" class="border border-gray-200 rounded-md p-4 bg-gray-50">
                <InputLabel for="passkey_name" value="Passkey name" />
                <TextInput
                    id="passkey_name"
                    v-model="newName"
                    placeholder="e.g. MacBook Touch ID"
                    autofocus
                    :disabled="isLoading"
                />
                <p
                    v-if="errorInstance instanceof PasskeyExistsError"
                    class="text-sm text-red-600 mt-1"
                >
                    You've already registered a passkey on this device for this account.
                </p>
                <InputError v-else :message="error ?? ''" />
                <div class="flex gap-2 justify-end mt-3">
                    <SecondaryButton type="button" class="cursor-pointer" :disabled="isLoading" @click="cancelAdd">
                        Cancel
                    </SecondaryButton>
                    <PrimaryButton
                        type="button"
                        class="cursor-pointer"
                        :disabled="isLoading || !newName.trim()"
                        @click="submitAdd"
                    >
                        {{ isLoading ? 'Waiting for prompt…' : 'Register' }}
                    </PrimaryButton>
                </div>
            </div>

            <div v-if="!loaded" class="text-sm text-gray-500">Loading…</div>
            <div v-else-if="passkeys.length === 0" class="text-sm text-gray-600">
                No passkeys registered yet.
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
                            Added {{ formatDate(passkey.created_at) }}
                            <span v-if="passkey.last_used_at">
                                · Last used {{ formatDate(passkey.last_used_at) }}
                            </span>
                        </div>
                    </div>
                    <button
                        type="button"
                        class="text-sm text-red-600 hover:text-red-700 cursor-pointer"
                        :disabled="deletingId === passkey.id"
                        @click="remove(passkey)"
                    >
                        {{ deletingId === passkey.id ? 'Removing…' : 'Remove' }}
                    </button>
                </li>
            </ul>
        </div>
    </section>
</template>
