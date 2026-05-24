<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import Select from '@/Components/Select.vue';
import InputError from '@/Components/InputError.vue';

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
            <legend class="text-sm font-semibold text-gray-900 mb-2">Basic information</legend>

            <div>
                <InputLabel for="salutation" value="Salutation *" />
                <TextInput id="salutation" v-model="form.salutation" required />
                <InputError :message="form.errors.salutation" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <InputLabel for="title" value="Title" />
                    <TextInput id="title" v-model="form.title" />
                    <InputError :message="form.errors.title" />
                </div>
                <div>
                    <InputLabel for="title_after" value="Title after" />
                    <TextInput id="title_after" v-model="form.title_after" />
                    <InputError :message="form.errors.title_after" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <InputLabel for="firstname" value="First name *" />
                    <TextInput id="firstname" v-model="form.firstname" required />
                    <InputError :message="form.errors.firstname" />
                </div>
                <div>
                    <InputLabel for="lastname" value="Last name *" />
                    <TextInput id="lastname" v-model="form.lastname" required />
                    <InputError :message="form.errors.lastname" />
                </div>
            </div>

            <div>
                <InputLabel for="nickname" value="Nickname" />
                <TextInput id="nickname" v-model="form.nickname" />
                <InputError :message="form.errors.nickname" />
            </div>
        </fieldset>

        <fieldset class="space-y-4">
            <legend class="text-sm font-semibold text-gray-900 mb-2">Details</legend>

            <div>
                <InputLabel for="date_of_birth" value="Date of birth (DD.MM.YYYY)" />
                <TextInput id="date_of_birth" v-model="form.date_of_birth" placeholder="01.01.1990" />
                <InputError :message="form.errors.date_of_birth" />
            </div>

            <div>
                <InputLabel for="iban" value="IBAN" />
                <TextInput id="iban" v-model="form.iban" />
                <InputError :message="form.errors.iban" />
            </div>
        </fieldset>

        <fieldset class="space-y-4">
            <legend class="text-sm font-semibold text-gray-900 mb-2">Work</legend>

            <div>
                <InputLabel for="company" value="Company" />
                <TextInput id="company" v-model="form.company" />
                <InputError :message="form.errors.company" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <InputLabel for="vatin" value="VAT ID" />
                    <TextInput id="vatin" v-model="form.vatin" />
                    <InputError :message="form.errors.vatin" />
                </div>
                <div>
                    <InputLabel for="department" value="Department" />
                    <TextInput id="department" v-model="form.department" />
                    <InputError :message="form.errors.department" />
                </div>
            </div>

            <div>
                <InputLabel for="job" value="Job" />
                <TextInput id="job" v-model="form.job" />
                <InputError :message="form.errors.job" />
            </div>
        </fieldset>

        <fieldset class="space-y-4">
            <legend class="text-sm font-semibold text-gray-900 mb-2">Additional</legend>

            <div>
                <InputLabel for="gender_id" value="Gender *" />
                <Select id="gender_id" v-model="form.gender_id" required>
                    <option v-for="gender in genders" :key="gender.id" :value="gender.id">
                        {{ gender.gender }}
                    </option>
                </Select>
                <InputError :message="form.errors.gender_id" />
            </div>

            <div>
                <InputLabel for="custom_id" value="Custom ID" />
                <TextInput id="custom_id" v-model="form.custom_id" />
                <InputError :message="form.errors.custom_id" />
            </div>

            <div>
                <InputLabel for="contact_groups" value="Contact groups" />
                <Select id="contact_groups" v-model="form.contact_groups" multiple>
                    <option v-for="group in contactGroups" :key="group.id" :value="group.id">
                        {{ group.name }}
                    </option>
                </Select>
                <InputError :message="form.errors.contact_groups" />
            </div>

            <div>
                <span class="block text-sm font-medium text-gray-700 mb-1">Status</span>
                <label class="inline-flex items-center mr-4">
                    <input
                        type="radio"
                        :value="1"
                        v-model.number="form.active"
                        class="text-indigo-600 focus:ring-indigo-500"
                    />
                    <span class="ms-2 text-sm text-gray-700">Active</span>
                </label>
                <label class="inline-flex items-center">
                    <input
                        type="radio"
                        :value="0"
                        v-model.number="form.active"
                        class="text-indigo-600 focus:ring-indigo-500"
                    />
                    <span class="ms-2 text-sm text-gray-700">Inactive</span>
                </label>
                <InputError :message="form.errors.active" />
            </div>

            <div>
                <InputLabel for="first_met" value="First met (notes)" />
                <Textarea id="first_met" v-model="form.first_met" rows="2" />
                <InputError :message="form.errors.first_met" />
            </div>

            <div>
                <InputLabel for="note" value="General notes" />
                <Textarea id="note" v-model="form.note" rows="3" />
                <InputError :message="form.errors.note" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <InputLabel for="died_at" value="Died at (DD.MM.YYYY)" />
                    <TextInput id="died_at" v-model="form.died_at" />
                    <InputError :message="form.errors.died_at" />
                </div>
                <div>
                    <InputLabel for="died_from" value="Died from" />
                    <TextInput id="died_from" v-model="form.died_from" />
                    <InputError :message="form.errors.died_from" />
                </div>
            </div>

            <div>
                <InputLabel for="nationality_id" value="Nationality" />
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
