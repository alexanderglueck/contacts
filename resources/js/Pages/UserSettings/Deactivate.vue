<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import SettingsLayout from '@/Layouts/SettingsLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import DangerButton from '@/Components/DangerButton.vue';

const { t } = useI18n();

const props = defineProps({
    subscriptionNotCancelled: { type: Boolean, default: false },
    lastMemberTeams: { type: Array, default: () => [] },
});

const blocked = computed(() => props.lastMemberTeams.length > 0);
const teamList = computed(() =>
    props.lastMemberTeams.map((t) => t.name).join(', '),
);
const lastMemberTitleKey = computed(() =>
    props.lastMemberTeams.length === 1
        ? 'settings.deactivate.last_member_title_one'
        : 'settings.deactivate.last_member_title_many',
);
const lastMemberBodyKey = computed(() =>
    props.lastMemberTeams.length === 1
        ? 'settings.deactivate.last_member_body_one'
        : 'settings.deactivate.last_member_body_many',
);

const form = useForm({
    current_password: '',
});

const submit = () => {
    if (blocked.value) return;
    form.post(route('user_settings.deactivate.store'));
};
</script>

<template>
    <SettingsLayout :title="t('settings.deactivate.title')">
        <Head :title="t('settings.deactivate.title')" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">{{ t('settings.deactivate.heading') }}</h2>
            </div>

            <div class="px-6 py-4 space-y-4">
                <div v-if="blocked" class="rounded-md bg-amber-50 ring-1 ring-amber-200 p-4 text-sm text-amber-900">
                    <p class="font-medium">
                        {{ t(lastMemberTitleKey, { team: teamList, teams: teamList }) }}
                    </p>
                    <p class="mt-1">
                        {{ t(lastMemberBodyKey) }}
                        <Link :href="route('user_settings.delete.show')" class="underline font-medium">
                            {{ t('settings.deactivate.delete_link') }}
                        </Link>
                    </p>
                </div>

                <p v-if="!blocked && subscriptionNotCancelled" class="text-sm text-gray-600">
                    {{ t('settings.deactivate.also_cancels_subscription') }}
                </p>

                <div v-if="!blocked">
                    <InputLabel for="current_password" :value="t('settings.deactivate.current_password')" />
                    <TextInput
                        id="current_password"
                        type="password"
                        v-model="form.current_password"
                        autocomplete="current-password"
                        required
                    />
                    <InputError :message="form.errors.current_password" />
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                <DangerButton
                    :disabled="blocked || form.processing"
                    :class="{ 'opacity-50 cursor-not-allowed': blocked || form.processing }"
                >
                    {{ t('settings.deactivate.action') }}
                </DangerButton>
            </div>
        </form>
    </SettingsLayout>
</template>
