<script setup>
import { computed, reactive } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Checkbox from '@/Components/Checkbox.vue';

const { t } = useI18n();

const props = defineProps({
    groups: { type: Array, default: () => [] },
});

// One selection state per group, keyed by signal+value, holding up to two ulids.
// Picking a third deselects the oldest, mirroring how a strict 2-of-N picker feels.
const selections = reactive({});

const groupKey = (group, index) => `${group.signal}::${group.value}::${index}`;

const toggle = (key, ulid) => {
    if (! selections[key]) selections[key] = [];
    const current = selections[key];
    const at = current.indexOf(ulid);
    if (at >= 0) {
        current.splice(at, 1);
        return;
    }
    if (current.length >= 2) current.shift();
    current.push(ulid);
};

const isPicked = (key, ulid) => (selections[key] ?? []).includes(ulid);
const pickedCount = (key) => (selections[key] ?? []).length;

const canCompare = (key) => pickedCount(key) === 2;

const compare = (key) => {
    const [left, right] = selections[key];
    router.visit(route('duplicates.compare', { left, right }));
};

const summarise = (c) => {
    const name = [c.firstname, c.lastname].filter(Boolean).join(' ');
    const extras = [c.company, c.date_of_birth].filter(Boolean);
    return `${name}${extras.length ? ' · ' + extras.join(' · ') : ''}`;
};

const empty = computed(() => props.groups.length === 0);
</script>

<template>
    <AppLayout :title="t('duplicates.title')">
        <Head :title="t('duplicates.title')" />

        <div class="bg-white shadow rounded-lg">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-medium text-gray-900">{{ t('duplicates.title') }}</h2>
                <p class="text-sm text-gray-600 mt-1">{{ t('duplicates.intro') }}</p>
            </div>

            <div v-if="empty" class="px-6 py-8 text-sm text-gray-600">
                {{ t('duplicates.no_duplicates') }}
            </div>

            <div v-else class="divide-y divide-gray-200">
                <section
                    v-for="(group, idx) in groups"
                    :key="groupKey(group, idx)"
                    class="px-6 py-4"
                >
                    <div class="flex items-center justify-between gap-3 mb-3">
                        <div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                {{ t(`duplicates.signal.${group.signal}`) }}
                            </span>
                            <span class="ms-2 text-sm font-mono text-gray-700">{{ group.value }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500" v-if="!canCompare(groupKey(group, idx))">
                                {{ t('duplicates.pick_two_to_compare') }}
                            </span>
                            <PrimaryButton
                                type="button"
                                class="cursor-pointer"
                                :disabled="!canCompare(groupKey(group, idx))"
                                @click="compare(groupKey(group, idx))"
                            >
                                {{ t('duplicates.compare') }}
                            </PrimaryButton>
                        </div>
                    </div>

                    <ul class="divide-y divide-gray-100 border border-gray-200 rounded-md">
                        <li
                            v-for="c in group.contacts"
                            :key="c.ulid"
                            class="px-4 py-2 flex items-center justify-between gap-3"
                        >
                            <label class="flex items-center gap-3 cursor-pointer flex-1 min-w-0">
                                <Checkbox
                                    :checked="isPicked(groupKey(group, idx), c.ulid)"
                                    @update:checked="toggle(groupKey(group, idx), c.ulid)"
                                />
                                <span class="text-sm text-gray-900 truncate">{{ summarise(c) }}</span>
                            </label>
                            <Link
                                :href="route('contacts.show', { contact: c.ulid })"
                                class="text-xs text-indigo-600 hover:text-indigo-500 underline shrink-0"
                            >
                                {{ c.ulid }}
                            </Link>
                        </li>
                    </ul>
                </section>
            </div>
        </div>
    </AppLayout>
</template>
