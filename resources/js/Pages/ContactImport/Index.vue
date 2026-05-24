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
    import_file: null,
});

const onFileChange = (event) => {
    form.import_file = event.target.files[0] ?? null;
};

const submit = () => {
    form.post(route('import.import'), {
        forceFormData: true,
        onSuccess: () => form.reset('import_file'),
    });
};
</script>

<template>
    <AppLayout :title="t('import.title')">
        <Head :title="t('import.title')" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">{{ t('import.title') }}</h2>
                <p class="text-xs text-gray-500 mt-1">
                    {{ t('import.help') }}
                </p>
            </div>

            <div class="px-6 py-4 space-y-4">
                <div>
                    <InputLabel for="contact_group_id" :value="`${t('import.contact_group')} *`" />
                    <Select id="contact_group_id" v-model.number="form.contact_group_id" required>
                        <option v-for="group in contactGroups" :key="group.id" :value="group.id">
                            {{ group.name }}
                        </option>
                    </Select>
                    <InputError :message="form.errors.contact_group_id" />
                </div>

                <div>
                    <InputLabel for="import_file" :value="`${t('import.file')} *`" />
                    <input
                        id="import_file"
                        type="file"
                        accept=".xlsx,.csv"
                        class="block w-full text-sm text-gray-700"
                        @change="onFileChange"
                        required
                    />
                    <InputError :message="form.errors.import_file" />
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                <PrimaryButton :disabled="form.processing || !form.import_file">{{ t('import.submit') }}</PrimaryButton>
            </div>
        </form>
    </AppLayout>
</template>
