<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import SettingsLayout from '@/Layouts/SettingsLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Select from '@/Components/Select.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const { t } = useI18n();

const props = defineProps({
    plans: { type: Array, default: () => [] },
    currentPlan: { type: Object, required: true },
});

const form = useForm({
    plan: props.plans[0]?.gateway_id ?? '',
});

const submit = () => form.post(route('user_settings.subscription.swap.store'));

const formatPrice = (p) => `€ ${(p / 100).toFixed(2)}`;
</script>

<template>
    <SettingsLayout :title="t('settings.subscription.swap_title')">
        <Head :title="t('settings.subscription.swap_title')" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">{{ t('settings.subscription.swap_title') }}</h2>
            </div>

            <div class="px-6 py-4 space-y-4">
                <p class="text-sm text-gray-600">
                    {{ t('settings.subscription.current_plan', { name: currentPlan.name, price: formatPrice(currentPlan.price) }) }}
                </p>

                <div>
                    <InputLabel for="plan" :value="t('settings.subscription.new_plan')" />
                    <Select id="plan" v-model="form.plan">
                        <option v-for="plan in plans" :key="plan.gateway_id" :value="plan.gateway_id">
                            {{ plan.name }} ({{ formatPrice(plan.price) }})
                        </option>
                    </Select>
                    <InputError :message="form.errors.plan" />
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                <PrimaryButton :disabled="form.processing || plans.length === 0" :class="{ 'opacity-50': form.processing || plans.length === 0 }">
                    {{ t('settings.subscription.swap_action') }}
                </PrimaryButton>
            </div>
        </form>
    </SettingsLayout>
</template>
