<script setup>
import { useI18n } from 'vue-i18n';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import Select from '@/Components/Select.vue';
import InputError from '@/Components/InputError.vue';

const { t } = useI18n();

defineProps({
    form: { type: Object, required: true },
    genders: { type: Array, default: () => [] },
    contactGroups: { type: Array, default: () => [] },
    countries: { type: Array, default: () => [] },
});
</script>

<template>
    <div class="space-y-6">
        <fieldset class="space-y-4">
            <legend class="text-sm font-semibold text-gray-900 mb-2">{{ t('contacts.form_sections.basic') }}</legend>

            <div>
                <InputLabel for="salutation" :value="`${t('contacts.fields.salutation')} *`" />
                <TextInput id="salutation" v-model="form.salutation" required />
                <InputError :message="form.errors.salutation" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <InputLabel for="title" :value="t('contacts.fields.title')" />
                    <TextInput id="title" v-model="form.title" />
                    <InputError :message="form.errors.title" />
                </div>
                <div>
                    <InputLabel for="title_after" :value="t('contacts.fields.title_after')" />
                    <TextInput id="title_after" v-model="form.title_after" />
                    <InputError :message="form.errors.title_after" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <InputLabel for="firstname" :value="`${t('contacts.fields.firstname')} *`" />
                    <TextInput id="firstname" v-model="form.firstname" required />
                    <InputError :message="form.errors.firstname" />
                </div>
                <div>
                    <InputLabel for="lastname" :value="`${t('contacts.fields.lastname')} *`" />
                    <TextInput id="lastname" v-model="form.lastname" required />
                    <InputError :message="form.errors.lastname" />
                </div>
            </div>

            <div>
                <InputLabel for="nickname" :value="t('contacts.fields.nickname')" />
                <TextInput id="nickname" v-model="form.nickname" />
                <InputError :message="form.errors.nickname" />
            </div>
        </fieldset>

        <fieldset class="space-y-4">
            <legend class="text-sm font-semibold text-gray-900 mb-2">{{ t('contacts.form_sections.details') }}</legend>

            <div>
                <InputLabel for="date_of_birth" :value="t('contacts.fields.date_of_birth')" />
                <TextInput id="date_of_birth" type="date" v-model="form.date_of_birth" />
                <InputError :message="form.errors.date_of_birth" />
            </div>

            <div>
                <InputLabel for="iban" :value="t('contacts.fields.iban')" />
                <TextInput id="iban" v-model="form.iban" />
                <InputError :message="form.errors.iban" />
            </div>
        </fieldset>

        <fieldset class="space-y-4">
            <legend class="text-sm font-semibold text-gray-900 mb-2">{{ t('contacts.form_sections.work') }}</legend>

            <div>
                <InputLabel for="company" :value="t('contacts.fields.company')" />
                <TextInput id="company" v-model="form.company" />
                <InputError :message="form.errors.company" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <InputLabel for="vatin" :value="t('contacts.fields.vatin')" />
                    <TextInput id="vatin" v-model="form.vatin" />
                    <InputError :message="form.errors.vatin" />
                </div>
                <div>
                    <InputLabel for="department" :value="t('contacts.fields.department')" />
                    <TextInput id="department" v-model="form.department" />
                    <InputError :message="form.errors.department" />
                </div>
            </div>

            <div>
                <InputLabel for="job" :value="t('contacts.fields.job')" />
                <TextInput id="job" v-model="form.job" />
                <InputError :message="form.errors.job" />
            </div>
        </fieldset>

        <fieldset class="space-y-4">
            <legend class="text-sm font-semibold text-gray-900 mb-2">{{ t('contacts.form_sections.additional') }}</legend>

            <div>
                <InputLabel for="gender_id" :value="`${t('contacts.fields.gender')} *`" />
                <Select id="gender_id" v-model="form.gender_id" required>
                    <option v-for="gender in genders" :key="gender.id" :value="gender.id">
                        {{ gender.gender }}
                    </option>
                </Select>
                <InputError :message="form.errors.gender_id" />
            </div>

            <div>
                <InputLabel for="custom_id" :value="t('contacts.fields.custom_id')" />
                <TextInput id="custom_id" v-model="form.custom_id" />
                <InputError :message="form.errors.custom_id" />
            </div>

            <div>
                <InputLabel for="contact_groups" :value="t('contacts.fields.contact_groups')" />
                <Select id="contact_groups" v-model="form.contact_groups" multiple>
                    <option v-for="group in contactGroups" :key="group.id" :value="group.id">
                        {{ group.name }}
                    </option>
                </Select>
                <InputError :message="form.errors.contact_groups" />
            </div>

            <div>
                <span class="block text-sm font-medium text-gray-700 mb-1">{{ t('contacts.fields.status') }}</span>
                <label class="inline-flex items-center mr-4">
                    <input type="radio" :value="1" v-model.number="form.active" class="text-indigo-600 focus:ring-indigo-500" />
                    <span class="ms-2 text-sm text-gray-700">{{ t('contacts.fields.active') }}</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" :value="0" v-model.number="form.active" class="text-indigo-600 focus:ring-indigo-500" />
                    <span class="ms-2 text-sm text-gray-700">{{ t('contacts.fields.inactive') }}</span>
                </label>
                <InputError :message="form.errors.active" />
            </div>

            <div>
                <InputLabel for="first_met" :value="t('contacts.fields.first_met_label')" />
                <Textarea id="first_met" v-model="form.first_met" rows="2" />
                <p class="mt-1 text-xs text-gray-500">{{ t('contacts.markdown_hint') }}</p>
                <InputError :message="form.errors.first_met" />
            </div>

            <div>
                <InputLabel for="note" :value="t('contacts.fields.note_label')" />
                <Textarea id="note" v-model="form.note" rows="3" />
                <p class="mt-1 text-xs text-gray-500">{{ t('contacts.markdown_hint') }}</p>
                <InputError :message="form.errors.note" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <InputLabel for="died_at" :value="t('contacts.fields.died_at')" />
                    <TextInput id="died_at" type="date" v-model="form.died_at" />
                    <InputError :message="form.errors.died_at" />
                </div>
                <div>
                    <InputLabel for="died_from" :value="t('contacts.fields.died_from')" />
                    <TextInput id="died_from" v-model="form.died_from" />
                    <InputError :message="form.errors.died_from" />
                </div>
            </div>

            <div>
                <InputLabel for="nationality_id" :value="t('contacts.fields.nationality')" />
                <Select id="nationality_id" v-model="form.nationality_id">
                    <option :value="null"></option>
                    <option v-for="country in countries" :key="country.id" :value="country.id">
                        {{ country.country }}
                    </option>
                </Select>
                <InputError :message="form.errors.nationality_id" />
            </div>
        </fieldset>
    </div>
</template>
