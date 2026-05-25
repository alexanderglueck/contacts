<script setup>
import { computed, reactive, ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Checkbox from '@/Components/Checkbox.vue';

const { t } = useI18n();

const props = defineProps({
    groups: { type: Array, default: () => [] },
});

// One selection state per group, keyed by signal+value, holding up to two ulids.
// Picking a third deselects the oldest, mirroring how a strict 2-of-N picker feels.
const selections = reactive({});

// Each group can carry multiple signals (e.g. same email AND same phone) when
// the contact set is identical across signals — they get collapsed server-side.
const groupKey = (group, index) =>
    `${(group.signals || []).map((s) => s.type + ':' + s.value).join('|')}::${index}`;

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

// Manual picker — two autocomplete inputs that hit /contacts/duplicates/search.
// Lets the user merge contacts the detector didn't surface (e.g. variant
// spellings, different emails for the same person).
const makePicker = () => ({
    query: '',
    results: [],
    picked: null,           // { ulid, label }
    loading: false,
    open: false,
    debounceTimer: null,
});
const leftPicker = reactive(makePicker());
const rightPicker = reactive(makePicker());

const runSearch = async (picker) => {
    const q = picker.query.trim();
    if (q === '') {
        picker.results = [];
        picker.open = false;
        return;
    }
    picker.loading = true;
    try {
        const { data } = await axios.get(route('duplicates.search'), { params: { q } });
        picker.results = data;
        picker.open = true;
    } finally {
        picker.loading = false;
    }
};

const onQueryChange = (picker) => {
    picker.picked = null;
    clearTimeout(picker.debounceTimer);
    picker.debounceTimer = setTimeout(() => runSearch(picker), 200);
};

const pick = (picker, item) => {
    picker.picked = item;
    picker.query = item.label;
    picker.open = false;
};

const clearPick = (picker) => {
    picker.picked = null;
    picker.query = '';
    picker.results = [];
    picker.open = false;
};

const canCompareManual = computed(
    () => leftPicker.picked && rightPicker.picked && leftPicker.picked.ulid !== rightPicker.picked.ulid,
);

const compareManual = () => {
    if (! canCompareManual.value) return;
    router.visit(route('duplicates.compare', {
        left: leftPicker.picked.ulid,
        right: rightPicker.picked.ulid,
    }));
};

// Close dropdowns on outside click.
const onDocClick = (event) => {
    if (! event.target.closest('[data-picker]')) {
        leftPicker.open = false;
        rightPicker.open = false;
    }
};
if (typeof window !== 'undefined') {
    document.addEventListener('click', onDocClick);
}
</script>

<template>
    <AppLayout :title="t('duplicates.title')">
        <Head :title="t('duplicates.title')" />

        <div class="bg-white shadow rounded-lg">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-medium text-gray-900">{{ t('duplicates.title') }}</h2>
                <p class="text-sm text-gray-600 mt-1">{{ t('duplicates.intro') }}</p>
            </div>

            <!-- Manual picker: merge two arbitrary contacts the detector didn't surface. -->
            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-900">{{ t('duplicates.manual_title') }}</h3>
                <p class="text-xs text-gray-600 mt-1">{{ t('duplicates.manual_intro') }}</p>
                <div class="mt-3 grid grid-cols-1 sm:grid-cols-[1fr_1fr_auto] gap-3 items-end">
                    <div data-picker class="relative">
                        <InputLabel :value="t('duplicates.contact_a')" />
                        <div class="relative">
                            <TextInput
                                v-model="leftPicker.query"
                                type="text"
                                :placeholder="t('duplicates.search_placeholder')"
                                @input="onQueryChange(leftPicker)"
                                @focus="leftPicker.results.length && (leftPicker.open = true)"
                            />
                            <button
                                v-if="leftPicker.picked || leftPicker.query"
                                type="button"
                                class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 text-sm cursor-pointer"
                                @click="clearPick(leftPicker)"
                            >×</button>
                        </div>
                        <ul
                            v-if="leftPicker.open && leftPicker.results.length"
                            class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg max-h-60 overflow-auto"
                        >
                            <li
                                v-for="item in leftPicker.results"
                                :key="item.ulid"
                                class="px-3 py-2 text-sm hover:bg-indigo-50 cursor-pointer"
                                @click="pick(leftPicker, item)"
                            >{{ item.label }}</li>
                        </ul>
                        <p v-if="leftPicker.open && !leftPicker.loading && leftPicker.query && !leftPicker.results.length"
                           class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg px-3 py-2 text-xs text-gray-500">
                            {{ t('duplicates.no_matches') }}
                        </p>
                    </div>

                    <div data-picker class="relative">
                        <InputLabel :value="t('duplicates.contact_b')" />
                        <div class="relative">
                            <TextInput
                                v-model="rightPicker.query"
                                type="text"
                                :placeholder="t('duplicates.search_placeholder')"
                                @input="onQueryChange(rightPicker)"
                                @focus="rightPicker.results.length && (rightPicker.open = true)"
                            />
                            <button
                                v-if="rightPicker.picked || rightPicker.query"
                                type="button"
                                class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 text-sm cursor-pointer"
                                @click="clearPick(rightPicker)"
                            >×</button>
                        </div>
                        <ul
                            v-if="rightPicker.open && rightPicker.results.length"
                            class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg max-h-60 overflow-auto"
                        >
                            <li
                                v-for="item in rightPicker.results"
                                :key="item.ulid"
                                class="px-3 py-2 text-sm hover:bg-indigo-50 cursor-pointer"
                                @click="pick(rightPicker, item)"
                            >{{ item.label }}</li>
                        </ul>
                        <p v-if="rightPicker.open && !rightPicker.loading && rightPicker.query && !rightPicker.results.length"
                           class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg px-3 py-2 text-xs text-gray-500">
                            {{ t('duplicates.no_matches') }}
                        </p>
                    </div>

                    <PrimaryButton
                        type="button"
                        class="cursor-pointer"
                        :disabled="!canCompareManual"
                        @click="compareManual"
                    >
                        {{ t('duplicates.compare') }}
                    </PrimaryButton>
                </div>
                <p v-if="leftPicker.picked && rightPicker.picked && leftPicker.picked.ulid === rightPicker.picked.ulid"
                   class="text-xs text-red-600 mt-2">
                    {{ t('duplicates.errors.same_contact') }}
                </p>
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
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                            <template v-for="(signal, sIdx) in group.signals" :key="sIdx">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                    {{ t(`duplicates.signal.${signal.type}`) }}
                                </span>
                                <span class="text-sm font-mono text-gray-700">{{ signal.value }}</span>
                                <span v-if="sIdx < group.signals.length - 1" class="text-xs text-gray-400">+</span>
                            </template>
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
