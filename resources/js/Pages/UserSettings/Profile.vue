<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
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
    available_locales: { type: Array, default: () => ['en'] },
});

const { t } = useI18n();

const form = useForm({
    name: props.user.name ?? '',
    email: props.user.email ?? '',
    password_reset_disabled: !!props.user.password_reset_disabled,
    locale: props.user.locale ?? 'en',
});

const submit = () => form.put(route('user_settings.profile.update'));
</script>

<template>
    <SettingsLayout :title="t('profile.title')">
        <Head :title="t('profile.title')" />

        <div class="space-y-6">
            <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">{{ t('profile.title') }}</h2>
                </div>

                <div class="px-6 py-4 space-y-4">
                    <div>
                        <InputLabel for="name" :value="t('profile.name')" />
                        <TextInput id="name" v-model="form.name" required autofocus />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div>
                        <InputLabel for="email" :value="t('profile.email')" />
                        <TextInput id="email" type="email" v-model="form.email" required />
                        <InputError :message="form.errors.email" />
                    </div>

                    <div>
                        <InputLabel for="locale" :value="t('profile.language')" />
                        <select
                            id="locale"
                            v-model="form.locale"
                            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option
                                v-for="code in available_locales"
                                :key="code"
                                :value="code"
                            >
                                {{ t(`languages.${code}`) }}
                            </option>
                        </select>
                        <p class="mt-1 text-xs text-gray-600">{{ t('profile.language_help') }}</p>
                        <InputError :message="form.errors.locale" />
                    </div>

                    <div class="pt-2 border-t border-gray-200">
                        <label class="flex items-start gap-2">
                            <Checkbox v-model:checked="form.password_reset_disabled" class="mt-1" />
                            <span class="text-sm text-gray-700">
                                <span class="font-medium text-gray-900">{{ t('profile.disable_password_reset') }}</span>
                                <span class="block text-xs text-gray-600 mt-0.5">
                                    {{ t('profile.disable_password_reset_help') }}
                                </span>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                    <PrimaryButton :disabled="form.processing" :class="{ 'opacity-50': form.processing }">
                        {{ t('common.update') }}
                    </PrimaryButton>
                </div>
            </form>

            <PasskeysCard :passkeys="passkeys" />
        </div>
    </SettingsLayout>
</template>
