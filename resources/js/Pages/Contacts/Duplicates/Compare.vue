<script setup>
import { computed, reactive, ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';

const { t } = useI18n();

const props = defineProps({
    left: { type: Object, required: true },
    right: { type: Object, required: true },
    fields: { type: Array, required: true },
    fieldLabels: { type: Object, default: () => ({}) },
    nonDuplicateMark: { type: Object, default: null },
});

const labelFor = (field) => props.fieldLabels[field] ?? field;

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

// Both sides have content AND the content differs. We highlight these rows
// because they're the easy-to-miss case (e.g. "MSc" vs "BSc" — both look like
// plausible values, but only one survives the merge). The empty-vs-filled
// case is left unmarked since it's visually obvious already.
const bothDiffer = (field) => {
    const l = props.left[field];
    const r = props.right[field];
    if (! isFilled(l) || ! isFilled(r)) return false;
    const norm = (v) => typeof v === 'string' ? v.trim() : v;
    return norm(l) !== norm(r);
};
const unresolvedFields = computed(() => props.fields.filter((f) => requiresChoice(f) && !['left', 'right'].includes(choices[f])));
const canSubmit = computed(() => unresolvedFields.value.length === 0);

const displayValue = (value, field) => {
    if (! isFilled(value)) return t('duplicates.empty');
    if (field === 'active') return value ? '✓' : '✗';
    return String(value);
};

const renderImage = (value) => value ? `/storage/${value}` : null;

// Sub-resource sections. Each one renders a side-by-side list with a checkbox
// per row. Default checked = keep (preserves the old union behavior); unchecking
// drops that row. contactGroups is rendered separately (unified, dedup'd) since
// the same group on both sides is a single relationship, not two.
const subSections = [
    { key: 'urls', label: 'URLs', format: (r) => [r.name, r.url].filter(Boolean).join(' — ') },
    { key: 'numbers', label: 'Numbers', format: (r) => [r.name, r.number].filter(Boolean).join(' — ') },
    { key: 'emails', label: 'Emails', format: (r) => [r.name, r.email].filter(Boolean).join(' — ') },
    { key: 'dates', label: 'Dates', format: (r) => [r.name, r.date].filter(Boolean).join(' — ') },
    { key: 'addresses', label: 'Addresses', format: (r) => [r.name, r.street, r.city].filter(Boolean).join(' — ') },
    { key: 'comments', label: 'Comments', format: (r) => r.body },
    { key: 'giftIdeas', label: 'Gift ideas', format: (r) => r.name },
    { key: 'notes', label: 'Notes', format: (r) => r.body },
    { key: 'calls', label: 'Calls', format: (r) => [r.when, r.body].filter(Boolean).join(' — ') },
];

// Per-row keep state: subPicks[relationKey][id] = true|false (default true).
// Initialised to every id from both sides as true so "submit unchanged" still
// produces the historical union merge.
const subPicks = reactive({});
for (const section of subSections) {
    subPicks[section.key] = {};
    for (const side of [props.left, props.right]) {
        for (const row of (side[section.key] ?? [])) {
            subPicks[section.key][row.id] = true;
        }
    }
}
// contactGroups: dedup'd union — a group either belongs to the merged contact
// or it doesn't.
const groupUnion = computed(() => {
    const seen = new Map();
    for (const side of [props.left, props.right]) {
        for (const g of (side.contactGroups ?? [])) {
            if (! seen.has(g.id)) seen.set(g.id, g);
        }
    }
    return Array.from(seen.values());
});
subPicks.contactGroups = {};
for (const g of groupUnion.value) subPicks.contactGroups[g.id] = true;

const toggleSub = (relation, id) => {
    subPicks[relation][id] = ! subPicks[relation][id];
};

const form = useForm({
    kept_ulid: '',
    loser_ulid: '',
    choices: {},
    subResources: {},
});

const notDuplicateConfirmOpen = ref(false);
const notDuplicateSubmitting = ref(false);
const unmarkSubmitting = ref(false);

const unmarkNotDuplicate = () => {
    unmarkSubmitting.value = true;
    router.delete(route('duplicates.undo_not_duplicate'), {
        data: {
            left_ulid: props.left.ulid,
            right_ulid: props.right.ulid,
        },
        onFinish: () => { unmarkSubmitting.value = false; },
    });
};

const askNotDuplicate = () => { notDuplicateConfirmOpen.value = true; };
const confirmNotDuplicate = () => {
    notDuplicateSubmitting.value = true;
    router.post(
        route('duplicates.not_duplicate'),
        {
            left_ulid: props.left.ulid,
            right_ulid: props.right.ulid,
        },
        {
            onFinish: () => {
                notDuplicateSubmitting.value = false;
                notDuplicateConfirmOpen.value = false;
            },
        },
    );
};

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
    // Shallow copy — Inertia serialises this straight to the form post.
    form.subResources = JSON.parse(JSON.stringify(subPicks));

    form.post(route('duplicates.merge'));
};
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

            <div
                v-if="nonDuplicateMark"
                class="border-b border-amber-200 bg-amber-50 px-6 py-3 flex items-start justify-between gap-3"
            >
                <div class="text-sm text-amber-900">
                    <p class="font-medium">{{ t('duplicates.already_marked_title') }}</p>
                    <p class="text-xs mt-0.5">
                        {{ t('duplicates.already_marked_body', { date: nonDuplicateMark.marked_at }) }}
                    </p>
                </div>
                <SecondaryButton
                    type="button"
                    class="cursor-pointer shrink-0"
                    :disabled="unmarkSubmitting"
                    @click="unmarkNotDuplicate"
                >
                    {{ t('duplicates.unmark_not_duplicate') }}
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
                        <div
                            class="px-3 py-2 text-gray-600 border-t border-gray-100"
                            :class="bothDiffer(field) ? 'border-l-4 border-l-amber-400 ps-2' : ''"
                            :title="bothDiffer(field) ? t('duplicates.values_differ') : null"
                        >
                            {{ labelFor(field) }}
                            <span
                                v-if="requiresChoice(field) && !choices[field]"
                                class="ms-2 inline-block w-2 h-2 rounded-full bg-red-500"
                                :title="t('duplicates.errors.choice_required', { field: labelFor(field) })"
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
                            <span
                                v-else
                                class="break-words line-clamp-3 whitespace-pre-line"
                                :title="String(left[field] ?? '')"
                            >{{ displayValue(left[field], field) }}</span>
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
                            <span
                                v-else
                                class="break-words line-clamp-3 whitespace-pre-line"
                                :title="String(right[field] ?? '')"
                            >{{ displayValue(right[field], field) }}</span>
                        </label>
                    </template>
                </div>

                <p class="text-xs text-gray-500 mt-4">{{ t('duplicates.deselect_hint') }}</p>

                <!-- Sub-resource picker -->
                <div class="mt-8">
                    <h3 class="text-base font-semibold text-gray-900">{{ t('duplicates.subresources_title') }}</h3>
                    <p class="text-xs text-gray-500 mt-1">{{ t('duplicates.subresources_hint') }}</p>

                    <div
                        v-for="section in subSections"
                        :key="section.key"
                        v-show="(left[section.key] ?? []).length || (right[section.key] ?? []).length"
                        class="mt-4 border border-gray-200 rounded-md overflow-hidden"
                    >
                        <div class="bg-gray-50 px-3 py-1.5 text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200">
                            {{ section.label }}
                        </div>
                        <div class="grid grid-cols-2 divide-x divide-gray-200">
                            <div class="p-2">
                                <ul v-if="(left[section.key] ?? []).length" class="space-y-1">
                                    <li
                                        v-for="row in left[section.key]"
                                        :key="`l-${row.id}`"
                                        class="flex items-start gap-2 text-sm"
                                        :class="{ 'opacity-50 line-through': !subPicks[section.key][row.id] }"
                                    >
                                        <input
                                            type="checkbox"
                                            class="mt-0.5"
                                            :checked="subPicks[section.key][row.id]"
                                            @change="toggleSub(section.key, row.id)"
                                        />
                                        <span
                                            class="break-words line-clamp-2"
                                            :title="section.format(row) || ''"
                                        >{{ section.format(row) || t('duplicates.empty') }}</span>
                                    </li>
                                </ul>
                                <p v-else class="text-xs text-gray-400 px-1">{{ t('duplicates.empty') }}</p>
                            </div>
                            <div class="p-2">
                                <ul v-if="(right[section.key] ?? []).length" class="space-y-1">
                                    <li
                                        v-for="row in right[section.key]"
                                        :key="`r-${row.id}`"
                                        class="flex items-start gap-2 text-sm"
                                        :class="{ 'opacity-50 line-through': !subPicks[section.key][row.id] }"
                                    >
                                        <input
                                            type="checkbox"
                                            class="mt-0.5"
                                            :checked="subPicks[section.key][row.id]"
                                            @change="toggleSub(section.key, row.id)"
                                        />
                                        <span
                                            class="break-words line-clamp-2"
                                            :title="section.format(row) || ''"
                                        >{{ section.format(row) || t('duplicates.empty') }}</span>
                                    </li>
                                </ul>
                                <p v-else class="text-xs text-gray-400 px-1">{{ t('duplicates.empty') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Contact groups: unified list since the same group belonging to both sides is one relationship, not two. -->
                    <div
                        v-show="groupUnion.length"
                        class="mt-4 border border-gray-200 rounded-md overflow-hidden"
                    >
                        <div class="bg-gray-50 px-3 py-1.5 text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200">
                            {{ t('duplicates.section.contact_groups') }}
                        </div>
                        <ul class="p-2 space-y-1">
                            <li
                                v-for="g in groupUnion"
                                :key="`g-${g.id}`"
                                class="flex items-start gap-2 text-sm"
                                :class="{ 'opacity-50 line-through': !subPicks.contactGroups[g.id] }"
                            >
                                <input
                                    type="checkbox"
                                    class="mt-0.5"
                                    :checked="subPicks.contactGroups[g.id]"
                                    @change="toggleSub('contactGroups', g.id)"
                                />
                                <span>{{ g.name }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mt-6 flex justify-end items-center gap-3">
                    <SecondaryButton
                        type="button"
                        class="cursor-pointer"
                        @click="askNotDuplicate"
                    >
                        {{ t('duplicates.not_duplicate') }}
                    </SecondaryButton>
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

        <ConfirmModal
            :open="notDuplicateConfirmOpen"
            :title="t('duplicates.not_duplicate_title')"
            :body="t('duplicates.not_duplicate_confirm')"
            :confirm-label="t('duplicates.not_duplicate')"
            :busy="notDuplicateSubmitting"
            @confirm="confirmNotDuplicate"
            @cancel="notDuplicateConfirmOpen = false"
        />
    </AppLayout>
</template>
