<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Select from '@/Components/Select.vue';
import InputError from '@/Components/InputError.vue';

defineProps({
    form: { type: Object, required: true },
    countries: { type: Array, default: () => [] },
});
</script>

<template>
    <div class="space-y-4">
        <div>
            <InputLabel for="name" value="Name *" />
            <TextInput id="name" v-model="form.name" required />
            <InputError :message="form.errors.name" />
        </div>

        <div>
            <InputLabel for="street" value="Street *" />
            <TextInput id="street" v-model="form.street" required />
            <InputError :message="form.errors.street" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <InputLabel for="zip" value="ZIP *" />
                <TextInput id="zip" v-model="form.zip" required />
                <InputError :message="form.errors.zip" />
            </div>
            <div class="sm:col-span-2">
                <InputLabel for="city" value="City *" />
                <TextInput id="city" v-model="form.city" required />
                <InputError :message="form.errors.city" />
            </div>
        </div>

        <div>
            <InputLabel for="state" value="State / region" />
            <TextInput id="state" v-model="form.state" />
            <InputError :message="form.errors.state" />
        </div>

        <div>
            <InputLabel for="country_id" value="Country *" />
            <Select id="country_id" v-model="form.country_id" required>
                <option v-for="country in countries" :key="country.id" :value="country.id">
                    {{ country.country }}
                </option>
            </Select>
            <InputError :message="form.errors.country_id" />
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <InputLabel for="latitude" value="Latitude" />
                <TextInput id="latitude" v-model="form.latitude" />
                <InputError :message="form.errors.latitude" />
            </div>
            <div>
                <InputLabel for="longitude" value="Longitude" />
                <TextInput id="longitude" v-model="form.longitude" />
                <InputError :message="form.errors.longitude" />
            </div>
        </div>
        <p class="text-xs text-gray-500">
            Geocoding from address is not wired up yet — enter coordinates manually if you need map markers.
        </p>
    </div>
</template>
