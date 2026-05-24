<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const { t } = useI18n();

const form = useForm({
    name: '',
});

const submit = () => form.post(route('teams.store'));
</script>

<template>
    <AppLayout :title="t('teams.create')">
        <Head :title="t('teams.create')" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">{{ t('teams.create') }}</h2>
            </div>

            <div class="px-6 py-4 space-y-4">
                <div>
                    <InputLabel for="name" :value="t('teams.name')" />
                    <TextInput id="name" v-model="form.name" required autofocus />
                    <InputError :message="form.errors.name" />
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
                <Link :href="route('teams.index')">
                    <SecondaryButton type="button">{{ t('common.cancel') }}</SecondaryButton>
                </Link>
                <PrimaryButton :disabled="form.processing" :class="{ 'opacity-50': form.processing }">
                    {{ t('common.save') }}
                </PrimaryButton>
            </div>
        </form>
    </AppLayout>
</template>
