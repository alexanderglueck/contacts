<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Select from '@/Components/Select.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const { t } = useI18n();

const props = defineProps({
    contactGroups: { type: Array, default: () => [] },
});

const form = useForm({
    contact_group_id: props.contactGroups[0]?.id ?? null,
});

const submit = () => {
    const formEl = document.createElement('form');
    formEl.method = 'POST';
    formEl.action = route('export.export');

    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    formEl.innerHTML = `
        <input type="hidden" name="_token" value="${csrf}">
        <input type="hidden" name="contact_group_id" value="${form.contact_group_id}">
    `;
    document.body.appendChild(formEl);
    formEl.submit();
    formEl.remove();
};
</script>

<template>
    <AppLayout :title="t('export.title')">
        <Head :title="t('export.title')" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">{{ t('export.title') }}</h2>
                <p class="text-xs text-gray-500 mt-1">
                    {{ t('export.help') }}
                </p>
            </div>

            <div class="px-6 py-4 space-y-4">
                <div>
                    <InputLabel for="contact_group_id" :value="`${t('export.contact_group')} *`" />
                    <Select id="contact_group_id" v-model.number="form.contact_group_id" required>
                        <option v-for="group in contactGroups" :key="group.id" :value="group.id">
                            {{ group.name }}
                        </option>
                    </Select>
                    <InputError :message="form.errors.contact_group_id" />
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                <PrimaryButton :disabled="!form.contact_group_id">{{ t('export.submit') }}</PrimaryButton>
            </div>
        </form>
    </AppLayout>
</template>
