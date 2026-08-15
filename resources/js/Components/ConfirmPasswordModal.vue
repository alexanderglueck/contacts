<script setup>
import { computed, nextTick, ref, watch } from 'vue';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';
import { usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const { t } = useI18n();

const props = defineProps({
    open: { type: Boolean, required: true },
    title: { type: String, default: '' },
    body: { type: String, default: '' },
});

const emit = defineEmits(['confirmed', 'cancel']);

const page = usePage();

// Names the account whose password is being asked for -- see the field itself
// for why a password manager needs it.
const username = computed(() => page.props.auth?.user?.email ?? '');

const password = ref('');
const error = ref('');
const busy = ref(false);
const input = ref(null);

watch(() => props.open, (open) => {
    password.value = '';
    error.value = '';
    busy.value = false;
    if (open) nextTick(() => input.value?.focus());
});

// Fortify's password confirmation endpoint answers a JSON request with 201 on
// success and 422 on a wrong password. A success puts `auth.password_confirmed_at`
// in the session, which is what the `password.confirm` middleware looks for — so
// the caller can run its protected request as soon as `confirmed` fires.
const submit = async () => {
    if (busy.value || ! password.value) return;

    busy.value = true;
    error.value = '';

    try {
        await window.axios.post('/user/confirm-password', { password: password.value });
        password.value = '';
        emit('confirmed');
    } catch (e) {
        error.value = e?.response?.data?.errors?.password?.[0]
            ?? e?.response?.data?.message
            ?? t('auth.confirm_password_failed');
    } finally {
        busy.value = false;
    }
};

const cancel = () => {
    if (! busy.value) emit('cancel');
};
</script>

<template>
    <TransitionRoot as="template" :show="open">
        <Dialog as="div" class="relative z-50" @close="cancel">
            <TransitionChild
                as="template"
                enter="ease-out duration-200"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="ease-in duration-150"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div class="fixed inset-0 bg-gray-500/60 transition-opacity" />
            </TransitionChild>

            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <TransitionChild
                        as="template"
                        enter="ease-out duration-200"
                        enter-from="opacity-0 translate-y-2 sm:scale-95"
                        enter-to="opacity-100 translate-y-0 sm:scale-100"
                        leave="ease-in duration-150"
                        leave-from="opacity-100 translate-y-0 sm:scale-100"
                        leave-to="opacity-0 translate-y-2 sm:scale-95"
                    >
                        <DialogPanel class="w-full max-w-md bg-white rounded-lg shadow-xl">
                            <form @submit.prevent="submit">
                                <div class="px-6 py-4 border-b border-gray-200">
                                    <DialogTitle class="text-base font-semibold text-gray-900">
                                        {{ title || t('auth.confirm_password_title') }}
                                    </DialogTitle>
                                </div>

                                <div class="px-6 py-4 space-y-3">
                                    <p class="text-sm text-gray-700">
                                        {{ body || t('auth.confirm_password_help') }}
                                    </p>

                                    <!--
                                        A password manager matches a saved login by pairing a
                                        username field with the password field. Given a lone
                                        password box it reads the form as a place to invent a
                                        password, so 1Password was offering to suggest one
                                        instead of filling the one on file. This field is what
                                        tells it which account to look up; it stays in the
                                        layout rather than `hidden`, because managers skip
                                        fields that aren't rendered at all.
                                    -->
                                    <input
                                        type="text"
                                        :value="username"
                                        autocomplete="username"
                                        class="sr-only"
                                        tabindex="-1"
                                        aria-hidden="true"
                                        readonly
                                    />

                                    <div>
                                        <InputLabel for="current_password" :value="t('auth.password')" />
                                        <TextInput
                                            id="current_password"
                                            ref="input"
                                            v-model="password"
                                            name="current_password"
                                            type="password"
                                            autocomplete="current-password"
                                            :disabled="busy"
                                        />
                                        <InputError :message="error" />
                                    </div>
                                </div>

                                <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2 bg-gray-50 rounded-b-lg">
                                    <SecondaryButton
                                        type="button"
                                        class="cursor-pointer"
                                        :disabled="busy"
                                        @click="cancel"
                                    >
                                        {{ t('common.cancel') }}
                                    </SecondaryButton>
                                    <PrimaryButton
                                        type="submit"
                                        class="cursor-pointer"
                                        :disabled="busy || ! password"
                                    >
                                        {{ t('common.confirm') }}
                                    </PrimaryButton>
                                </div>
                            </form>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>
