<script setup>
import { computed, reactive, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const { t } = useI18n();

const props = defineProps({
    left: { type: Object, required: true },
    right: { type: Object, required: true },
    fields: { type: Array, required: true },
});

// Display sides are stable (left vs right) — what changes is which one
// survives. `keptSide` flips between 'left' and 'right'.
const keptSide = ref('left');

// per-field choice: 'left' | 'right' | null. Pre-seeded so the side that
// has content (when only one side does) starts selected.
const choices = reactive(Object.fromEntries(props.fields.map((f) => {
    const l = props.left[f];
    const r = props.right[f];
    const lFilled = isFilled(l);
    const rFilled = isFilled(r);
    if (lFilled && ! rFilled) return [f, 'left'];
    if (! lFilled && rFilled) return [f, 'right'];
    if (lFilled && rFilled) return [f, 'left']; // both filled → default to left, user picks
    return [f, null]; // both empty
})));

function isFilled(value) {
    if (value === null || value === undefined) return false;
    if (typeof value === 'string' && value.trim() === '') return false;
    return true;
}

const setChoice = (field, side) => {
    // Click an already-selected radio to clear it (deselect behavior the user asked for).
    choices[field] = choices[field] === side ? null : side;
};

const requiresChoice = (field) => isFilled(props.left[field]) || isFilled(props.right[field]);
const unresolvedFields = computed(() => props.fields.filter((f) => requiresChoice(f) && !['left', 'right'].includes(choices[f])));
const canSubmit = computed(() => unresolvedFields.value.length === 0);

const displayValue = (value, field) => {
    if (! isFilled(value)) return t('duplicates.empty');
    if (field === 'active') return value ? '✓' : '✗';
    return String(value);
};

const renderImage = (value) => value ? `/storage/${value}` : null;

const form = useForm({
    kept_ulid: '',
    loser_ulid: '',
    choices: {},
});

const submit = () => {
    const kept = keptSide.value === 'left' ? props.left : props.right;
    const loser = keptSide.value === 'left' ? props.right : props.left;

    form.kept_ulid = kept.ulid;
    form.loser_ulid = loser.ulid;
    // The backend's MergeContactRequest treats 'left' as kept and 'right' as loser
    // semantically. When keptSide is 'right' (user swapped), flip each choice so
    // 'left'/'right' on the wire reads kept/loser, not display-position.
    form.choices = Object.fromEntries(props.fields.map((f) => {
        if (! choices[f]) return [f, null];
        if (keptSide.value === 'left') return [f, choices[f]];
        return [f, choices[f] === 'left' ? 'right' : 'left'];
    }));

    form.post(route('duplicates.merge'));
};

// Sub-resource lists — read-only previews, just so the user sees what's about to merge.
const subSections = [
    { key: 'urls', label: 'URLs', columns: ['name', 'url'] },
    { key: 'numbers', label: 'Numbers', columns: ['name', 'number'] },
    { key: 'emails', label: 'Emails', columns: ['name', 'email'] },
    { key: 'dates', label: 'Dates', columns: ['name', 'date'] },
    { key: 'addresses', label: 'Addresses', columns: ['name', 'street', 'city'] },
    { key: 'contact_groups', label: 'Groups', columns: ['name'] },
];
</script>

<template>
    <AppLayout :title="t('duplicates.compare_title')">
        <Head :title="t('duplicates.compare_title')" />

        <div class="bg-white shadow rounded-lg">
            <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between gap-3">
                <h2 class="text-lg font-medium text-gray-900">{{ t('duplicates.compare_title') }}</h2>
                <SecondaryButton
                    type="button"
                    class="cursor-pointer"
                    @click="keptSide = keptSide === 'left' ? 'right' : 'left'"
                >
                    {{ t('duplicates.swap_sides') }} ({{ t(`duplicates.${keptSide}`) }})
                </SecondaryButton>
            </div>

            <div class="px-6 py-4">
                <div class="grid grid-cols-[14rem_1fr_1fr] gap-x-4 text-sm">
                    <!-- Header row: side identity -->
                    <div></div>
                    <div
                        class="px-3 py-2 rounded-t-md font-semibold flex items-center justify-between"
                        :class="keptSide === 'left' ? 'bg-emerald-100 text-emerald-900' : 'bg-gray-100 text-gray-700'"
                    >
                        <span>{{ left.fullname || left.ulid }}</span>
                        <span v-if="keptSide === 'left'" class="text-xs uppercase tracking-wide">{{ t('duplicates.keep_side') }}</span>
                    </div>
                    <div
                        class="px-3 py-2 rounded-t-md font-semibold flex items-center justify-between"
                        :class="keptSide === 'right' ? 'bg-emerald-100 text-emerald-900' : 'bg-gray-100 text-gray-700'"
                    >
                        <span>{{ right.fullname || right.ulid }}</span>
                        <span v-if="keptSide === 'right'" class="text-xs uppercase tracking-wide">{{ t('duplicates.keep_side') }}</span>
                    </div>

                    <!-- Scalar field rows -->
                    <template v-for="field in fields" :key="field">
                        <div class="px-3 py-2 text-gray-600 border-t border-gray-100 font-mono">
                            {{ field }}
                            <span
                                v-if="requiresChoice(field) && !choices[field]"
                                class="ms-2 inline-block w-2 h-2 rounded-full bg-red-500"
                                :title="t('duplicates.errors.choice_required', { field })"
                            ></span>
                        </div>

                        <label
                            class="px-3 py-2 border-t border-gray-100 cursor-pointer flex items-start gap-2"
                            :class="choices[field] === 'left' ? 'bg-emerald-50' : 'hover:bg-gray-50'"
                            @click.prevent="setChoice(field, 'left')"
                        >
                            <span
                                class="mt-0.5 inline-block w-4 h-4 rounded-full border-2 flex-shrink-0"
                                :class="choices[field] === 'left' ? 'border-emerald-600 bg-emerald-600 ring-2 ring-emerald-200' : 'border-gray-400'"
                            ></span>
                            <span v-if="field === 'image' && renderImage(left[field])" class="block">
                                <img :src="renderImage(left[field])" alt="" class="h-16 w-16 object-cover rounded" />
                            </span>
                            <span v-else class="break-words">{{ displayValue(left[field], field) }}</span>
                        </label>

                        <label
                            class="px-3 py-2 border-t border-gray-100 cursor-pointer flex items-start gap-2"
                            :class="choices[field] === 'right' ? 'bg-emerald-50' : 'hover:bg-gray-50'"
                            @click.prevent="setChoice(field, 'right')"
                        >
                            <span
                                class="mt-0.5 inline-block w-4 h-4 rounded-full border-2 flex-shrink-0"
                                :class="choices[field] === 'right' ? 'border-emerald-600 bg-emerald-600 ring-2 ring-emerald-200' : 'border-gray-400'"
                            ></span>
                            <span v-if="field === 'image' && renderImage(right[field])" class="block">
                                <img :src="renderImage(right[field])" alt="" class="h-16 w-16 object-cover rounded" />
                            </span>
                            <span v-else class="break-words">{{ displayValue(right[field], field) }}</span>
                        </label>
                    </template>
                </div>

                <p class="text-xs text-gray-500 mt-4">{{ t('duplicates.deselect_hint') }}</p>

                <!-- Sub-resource preview -->
                <div class="mt-6 grid grid-cols-2 gap-4">
                    <div v-for="side in [{key:'left', data:left}, {key:'right', data:right}]" :key="side.key">
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">
                            {{ side.data.fullname || side.data.ulid }}
                        </h3>
                        <div v-for="section in subSections" :key="section.key" class="mb-3">
                            <p class="text-xs font-medium text-gray-500 uppercase">{{ section.label }}</p>
                            <ul v-if="(side.data[section.key] ?? []).length" class="text-xs text-gray-700 list-disc list-inside">
                                <li v-for="(row, i) in side.data[section.key]" :key="i">
                                    {{ section.columns.map(col => row[col]).filter(Boolean).join(' — ') }}
                                </li>
                            </ul>
                            <p v-else class="text-xs text-gray-400">{{ t('duplicates.empty') }}</p>
                        </div>
                        <div class="text-xs text-gray-500">
                            Comments: {{ side.data.comments_count }}
                            · Gift ideas: {{ side.data.gift_ideas_count }}
                            · Notes: {{ side.data.notes_count }}
                            · Calls: {{ side.data.calls_count }}
                        </div>
                    </div>
                </div>

                <p class="text-xs text-gray-500 mt-4">{{ t('duplicates.subresources_note') }}</p>

                <div class="mt-6 flex justify-end gap-3">
                    <PrimaryButton
                        type="button"
                        class="cursor-pointer"
                        :disabled="!canSubmit || form.processing"
                        @click="submit"
                    >
                        {{ t('duplicates.submit') }}
                    </PrimaryButton>
                </div>

                <ul v-if="Object.keys(form.errors).length" class="mt-4 text-sm text-red-600 list-disc list-inside">
                    <li v-for="(msg, key) in form.errors" :key="key">{{ key }}: {{ msg }}</li>
                </ul>
            </div>
        </div>
    </AppLayout>
</template>
