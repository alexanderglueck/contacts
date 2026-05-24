<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Select from '@/Components/Select.vue';

// TODO: Reintegrate Stripe Elements (Stripe.js v3) for confirming the SetupIntent
// client-side, then POST the resulting payment method token to
// route('subscription.store') alongside `plan` and optional `coupon`.
// The controller's POST endpoint and validation are preserved.

defineProps({
    plans: { type: Array, default: () => [] },
    intent: { type: Object, required: true },
    stripeKey: { type: String, default: '' },
    selectedPlan: { type: String, default: null },
});
</script>

<template>
    <AppLayout title="Subscribe">
        <Head title="Subscribe" />

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Subscribe</h2>
            </div>

            <div class="px-6 py-4 space-y-4">
                <p class="text-sm text-gray-600">
                    The Stripe Elements payment widget is not wired up yet in the new Vue frontend.
                    Once integrated it will confirm the SetupIntent and POST the payment method token
                    to the existing subscription endpoint.
                </p>

                <div>
                    <InputLabel for="plan" value="Plan (preview)" />
                    <Select id="plan" :model-value="selectedPlan ?? plans[0]?.gateway_id" disabled>
                        <option v-for="plan in plans" :key="plan.gateway_id" :value="plan.gateway_id">
                            {{ plan.name }} (€ {{ (plan.price / 100).toFixed(2) }})
                        </option>
                    </Select>
                </div>

                <p class="text-xs text-gray-500">
                    SetupIntent ready: <code>{{ intent.client_secret ? 'yes' : 'no' }}</code>
                </p>
            </div>
        </div>
    </AppLayout>
</template>
