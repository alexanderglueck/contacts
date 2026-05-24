<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Select from '@/Components/Select.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    teams: { type: Array, default: () => [] },
});

const form = useForm({
    team: props.teams[0]?.id ?? '',
});

const submit = () => form.post(route('tenant.store'));
</script>

<template>
    <AppLayout title="Choose team">
        <Head title="Choose team" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Choose your team</h2>
            </div>

            <div class="px-6 py-4 space-y-4">
                <div>
                    <InputLabel for="team" value="Team" />
                    <Select id="team" v-model="form.team">
                        <option v-for="team in teams" :key="team.id" :value="team.id">
                            {{ team.name }}
                        </option>
                    </Select>
                    <InputError :message="form.errors.team" />
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                <PrimaryButton :disabled="form.processing || teams.length === 0" :class="{ 'opacity-50': form.processing || teams.length === 0 }">
                    Switch team
                </PrimaryButton>
            </div>
        </form>
    </AppLayout>
</template>
