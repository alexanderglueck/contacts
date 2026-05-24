<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    plans: { type: Array, default: () => [] },
    teamPlans: { type: Array, default: () => [] },
});

const formatPrice = (p) => `€ ${(p / 100).toFixed(2)}`;
</script>

<template>
    <AppLayout title="Plans">
        <Head title="Plans" />

        <div class="space-y-8">
            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Personal plans</h2>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="plan in plans"
                        :key="plan.id"
                        class="bg-white shadow rounded-lg overflow-hidden flex flex-col"
                    >
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">{{ plan.name }}</h3>
                        </div>
                        <div class="px-6 py-4 flex-1">
                            <p class="text-3xl font-bold text-gray-900">{{ formatPrice(plan.price) }}</p>
                            <ul class="mt-3 text-sm text-gray-600 list-disc list-inside">
                                <li>Access to all personal features</li>
                            </ul>
                        </div>
                        <div class="px-6 py-4 border-t border-gray-200">
                            <Link
                                :href="`${route('subscription.index')}?plan=${plan.slug}`"
                                class="block w-full text-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500"
                            >
                                Join
                            </Link>
                        </div>
                    </div>
                </div>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Team plans</h2>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="plan in teamPlans"
                        :key="plan.id"
                        class="bg-white shadow rounded-lg overflow-hidden flex flex-col"
                    >
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">{{ plan.name }}</h3>
                        </div>
                        <div class="px-6 py-4 flex-1">
                            <p class="text-3xl font-bold text-gray-900">{{ formatPrice(plan.price) }}</p>
                            <ul class="mt-3 text-sm text-gray-600 list-disc list-inside">
                                <li>Up to {{ plan.teams_limit }} users</li>
                                <li>Access to all features</li>
                                <li>Invite team members</li>
                            </ul>
                        </div>
                        <div class="px-6 py-4 border-t border-gray-200">
                            <Link
                                :href="`${route('subscription.index')}?plan=${plan.slug}`"
                                class="block w-full text-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500"
                            >
                                Join
                            </Link>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
