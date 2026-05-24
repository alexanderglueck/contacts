<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

// TODO: Reintegrate Stripe Elements (Stripe.js v3) for confirming the SetupIntent
// client-side, then POST the resulting payment method token to
// route('user_settings.subscription.card.store') as the `token` field.
// The controller's POST endpoint is preserved.

defineProps({
    intent: { type: Object, required: true },
    stripeKey: { type: String, default: '' },
});
</script>

<template>
    <AppLayout title="Update card">
        <Head title="Update card" />

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Update card</h2>
            </div>

            <div class="px-6 py-4 space-y-3">
                <p class="text-sm text-gray-600">
                    The Stripe Elements payment widget is not wired up yet in the new Vue frontend.
                    Once integrated it will confirm the SetupIntent and POST the payment method token
                    back to the existing endpoint.
                </p>
                <p class="text-xs text-gray-500">SetupIntent ready: <code>{{ intent.client_secret ? 'yes' : 'no' }}</code></p>
            </div>
        </div>
    </AppLayout>
</template>
