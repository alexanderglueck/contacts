<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

defineProps({
    mapsKeyPresent: { type: Boolean, default: false },
    stripeKeyPresent: { type: Boolean, default: false },
});

const form = useForm({
    maps_api_key: '',
    stripe_api_key: '',
    stripe_api_secret: '',
});

const submit = () => form.post(route('install.store'));
</script>

<template>
    <GuestLayout>
        <Head title="Install" />

        <h1 class="text-xl font-semibold text-gray-900 mb-4">Install</h1>

        <form @submit.prevent="submit" class="space-y-6">
            <section>
                <h2 class="font-medium text-gray-900">1. Google Maps API</h2>
                <p v-if="mapsKeyPresent" class="text-sm text-green-700 mt-1">Configured.</p>
                <template v-else>
                    <a
                        href="https://developers.google.com/maps/documentation/javascript/get-api-key"
                        target="_blank"
                        class="inline-block mt-2 text-sm text-indigo-600 hover:text-indigo-500 underline"
                    >
                        Get a Google Maps API key
                    </a>
                    <div class="mt-3">
                        <InputLabel for="maps_api_key" value="GOOGLE_MAPS_GEOCODING_KEY" />
                        <TextInput id="maps_api_key" v-model="form.maps_api_key" required />
                        <InputError :message="form.errors.maps_api_key" />
                    </div>
                </template>
            </section>

            <section>
                <h2 class="font-medium text-gray-900">2. Stripe API</h2>
                <p v-if="stripeKeyPresent" class="text-sm text-green-700 mt-1">Configured.</p>
                <template v-else>
                    <a
                        href="https://stripe.com/"
                        target="_blank"
                        class="inline-block mt-2 text-sm text-indigo-600 hover:text-indigo-500 underline"
                    >
                        Get Stripe API credentials
                    </a>
                    <div class="mt-3 space-y-4">
                        <div>
                            <InputLabel for="stripe_api_key" value="STRIPE_KEY" />
                            <TextInput id="stripe_api_key" v-model="form.stripe_api_key" required />
                            <InputError :message="form.errors.stripe_api_key" />
                        </div>
                        <div>
                            <InputLabel for="stripe_api_secret" value="STRIPE_SECRET" />
                            <TextInput id="stripe_api_secret" v-model="form.stripe_api_secret" required />
                            <InputError :message="form.errors.stripe_api_secret" />
                        </div>
                    </div>
                </template>
            </section>

            <div class="flex justify-end">
                <PrimaryButton :disabled="form.processing" :class="{ 'opacity-50': form.processing }">
                    Save
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
