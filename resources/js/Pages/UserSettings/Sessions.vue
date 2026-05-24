<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import SettingsLayout from '@/Layouts/SettingsLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    sessions: { type: Array, required: true },
    driver: { type: String, required: true },
});

const confirmingLogoutOthers = ref(false);
const passwordInput = ref(null);

const logoutForm = useForm({
    password: '',
});

const confirmLogoutOthers = () => {
    confirmingLogoutOthers.value = true;
    setTimeout(() => passwordInput.value?.focus(), 50);
};

const submitLogoutOthers = () => {
    logoutForm.delete(route('user_settings.sessions.destroy_others'), {
        preserveScroll: true,
        onSuccess: () => {
            confirmingLogoutOthers.value = false;
            logoutForm.reset();
        },
        onError: () => {
            passwordInput.value?.focus();
        },
    });
};

const cancelLogoutOthers = () => {
    confirmingLogoutOthers.value = false;
    logoutForm.reset();
    logoutForm.clearErrors();
};

const revoke = (session) => {
    if (!confirm('Sign this browser out?')) return;
    router.delete(route('user_settings.sessions.destroy', session.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <SettingsLayout title="Active sessions">
        <Head title="Active sessions" />

        <section class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Active sessions</h2>
                <p class="text-xs text-gray-600 mt-0.5">
                    Browsers and devices currently signed in to your account. Sign out anything
                    you don't recognise.
                </p>
            </div>

            <div v-if="driver !== 'database'" class="px-6 py-4 text-sm text-amber-700 bg-amber-50 border-b border-amber-200">
                Session listing requires <code>SESSION_DRIVER=database</code>. Update your
                <code>.env</code> and run <code>php artisan session:table &amp;&amp; php artisan migrate</code>.
            </div>

            <div v-else-if="sessions.length === 0" class="px-6 py-4 text-sm text-gray-600">
                No active sessions found.
            </div>

            <ul v-else class="divide-y divide-gray-200">
                <li
                    v-for="session in sessions"
                    :key="session.id"
                    class="px-6 py-4 flex items-center justify-between gap-4"
                >
                    <div class="flex items-start gap-3">
                        <div
                            class="mt-1 w-9 h-9 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center"
                            :title="session.is_desktop ? 'Desktop' : 'Mobile'"
                        >
                            <span class="text-xs font-semibold">
                                {{ session.is_desktop ? 'PC' : 'M' }}
                            </span>
                        </div>
                        <div class="text-sm">
                            <div class="font-medium text-gray-900">
                                {{ session.browser }} on {{ session.platform }}
                                <span
                                    v-if="session.is_current"
                                    class="ml-2 text-xs text-green-700 bg-green-50 border border-green-200 rounded px-1.5 py-0.5 align-middle"
                                >
                                    This device
                                </span>
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ session.ip_address ?? 'unknown IP' }}
                                · Last active {{ session.last_active }}
                            </div>
                        </div>
                    </div>

                    <button
                        v-if="!session.is_current"
                        type="button"
                        class="text-sm text-red-600 hover:text-red-700 cursor-pointer"
                        @click="revoke(session)"
                    >
                        Sign out
                    </button>
                </li>
            </ul>

            <div
                v-if="driver === 'database' && sessions.length > 1"
                class="px-6 py-4 border-t border-gray-200"
            >
                <div v-if="!confirmingLogoutOthers">
                    <SecondaryButton type="button" class="cursor-pointer" @click="confirmLogoutOthers">
                        Sign out all other browsers
                    </SecondaryButton>
                </div>

                <form v-else @submit.prevent="submitLogoutOthers" class="space-y-3">
                    <p class="text-sm text-gray-700">
                        Re-enter your password to sign out every browser other than this one.
                    </p>
                    <div>
                        <InputLabel for="password" value="Password" />
                        <TextInput
                            id="password"
                            ref="passwordInput"
                            type="password"
                            v-model="logoutForm.password"
                            autocomplete="current-password"
                            required
                        />
                        <InputError :message="logoutForm.errors.password" />
                    </div>
                    <div class="flex gap-2 justify-end">
                        <SecondaryButton type="button" class="cursor-pointer" @click="cancelLogoutOthers">
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton
                            type="submit"
                            :disabled="logoutForm.processing"
                            :class="{ 'opacity-50': logoutForm.processing }"
                        >
                            Sign out other browsers
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </section>
    </SettingsLayout>
</template>
