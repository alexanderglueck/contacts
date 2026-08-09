<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import ContactAvatar from '@/Components/ContactAvatar.vue';

const { t } = useI18n();

const term = ref('');
const results = ref([]);
const open = ref(false);
const loading = ref(false);
const highlighted = ref(-1);

const root = ref(null);
const input = ref(null);

let debounce = null;
// Responses can come back out of order; only the newest request is allowed
// to write into results, so a slow early keystroke can't overwrite a fast
// later one.
let latestRequest = 0;

const trimmed = computed(() => term.value.trim());

// The "show all results" row sits after the suggestions and is reachable
// with the arrow keys like any other row.
const rowCount = computed(() => results.value.length + (trimmed.value ? 1 : 0));
const showAllIndex = computed(() => results.value.length);

const isMac = typeof navigator !== 'undefined' && /Mac|iPhone|iPad/.test(navigator.platform || navigator.userAgent);
const shortcutHint = computed(() => (isMac ? '⌘K' : 'Ctrl K'));

watch(term, () => {
    clearTimeout(debounce);
    highlighted.value = -1;

    if (trimmed.value === '') {
        results.value = [];
        loading.value = false;
        open.value = false;
        return;
    }

    loading.value = true;
    open.value = true;
    debounce = setTimeout(runSearch, 250);
});

const runSearch = () => {
    const query = trimmed.value;
    const request = ++latestRequest;

    axios
        .get(route('search.suggest'), { params: { q: query } })
        .then(({ data }) => {
            if (request !== latestRequest) return;
            results.value = data.data ?? [];
        })
        .catch(() => {
            if (request === latestRequest) results.value = [];
        })
        .finally(() => {
            if (request === latestRequest) loading.value = false;
        });
};

const reset = () => {
    clearTimeout(debounce);
    latestRequest++;
    term.value = '';
    results.value = [];
    highlighted.value = -1;
    loading.value = false;
    open.value = false;
};

const goToContact = (contact) => {
    reset();
    input.value?.blur();
    router.visit(route('contacts.show', contact.ulid));
};

const showAll = () => {
    const query = trimmed.value;
    if (query === '') return;

    reset();
    input.value?.blur();
    router.get(route('contacts.index'), { q: query });
};

const move = (delta) => {
    if (rowCount.value === 0) return;

    open.value = true;
    const next = highlighted.value + delta;
    // Wrap around, and treat "nothing highlighted" as one step before the top.
    highlighted.value = (next + rowCount.value) % rowCount.value;
};

const onEnter = () => {
    if (highlighted.value >= 0 && highlighted.value < results.value.length) {
        goToContact(results.value[highlighted.value]);
        return;
    }

    showAll();
};

const onEscape = () => {
    if (open.value) {
        open.value = false;
        highlighted.value = -1;
        return;
    }

    reset();
    input.value?.blur();
};

const onFocus = () => {
    if (trimmed.value !== '') open.value = true;
};

const focusInput = async () => {
    await nextTick();
    input.value?.focus();
    input.value?.select();
};

// Ctrl/Cmd+K from anywhere jumps into the box — the whole point of the
// feature is reaching a contact without navigating first.
const onShortcut = (event) => {
    if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
        event.preventDefault();
        focusInput();
    }
};

const onDocumentClick = (event) => {
    if (root.value && !root.value.contains(event.target)) open.value = false;
};

onMounted(() => {
    document.addEventListener('keydown', onShortcut);
    document.addEventListener('click', onDocumentClick);
});

onBeforeUnmount(() => {
    clearTimeout(debounce);
    document.removeEventListener('keydown', onShortcut);
    document.removeEventListener('click', onDocumentClick);
});
</script>

<template>
    <div ref="root" class="relative w-full">
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3 text-gray-400">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                </svg>
            </span>

            <input
                ref="input"
                v-model="term"
                type="search"
                role="combobox"
                aria-autocomplete="list"
                :aria-expanded="open"
                aria-controls="global-search-results"
                :aria-activedescendant="highlighted >= 0 ? `global-search-option-${highlighted}` : undefined"
                :aria-label="t('search.label')"
                :placeholder="t('search.placeholder')"
                autocomplete="off"
                class="block w-full rounded-md border-gray-300 ps-9 pe-14 py-1.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                @focus="onFocus"
                @keydown.down.prevent="move(1)"
                @keydown.up.prevent="move(-1)"
                @keydown.enter.prevent="onEnter"
                @keydown.esc.prevent="onEscape"
            />

            <kbd
                v-if="!term"
                class="pointer-events-none absolute inset-y-0 end-0 my-auto me-2 h-5 rounded border border-gray-200 bg-gray-50 px-1.5 text-[10px] leading-5 font-sans text-gray-400"
            >{{ shortcutHint }}</kbd>
        </div>

        <div
            v-if="open"
            id="global-search-results"
            role="listbox"
            class="absolute z-50 mt-1 w-full overflow-hidden rounded-md border border-gray-200 bg-white shadow-lg"
        >
            <p v-if="loading && results.length === 0" class="px-3 py-3 text-sm text-gray-500">
                {{ t('search.searching') }}
            </p>

            <p v-else-if="results.length === 0" class="px-3 py-3 text-sm text-gray-500">
                {{ t('search.no_results') }}
            </p>

            <ul v-else class="max-h-80 overflow-auto divide-y divide-gray-100">
                <li v-for="(contact, index) in results" :key="contact.ulid">
                    <button
                        type="button"
                        role="option"
                        :id="`global-search-option-${index}`"
                        :aria-selected="highlighted === index"
                        class="flex w-full items-center gap-3 px-3 py-2 text-start text-sm text-gray-900 cursor-pointer"
                        :class="highlighted === index ? 'bg-indigo-50' : 'hover:bg-gray-50'"
                        @mouseenter="highlighted = index"
                        @click="goToContact(contact)"
                    >
                        <ContactAvatar :contact="contact" size-class="h-7 w-7" text-class="text-[10px]" />
                        <span class="truncate">{{ contact.fullname }}</span>
                    </button>
                </li>
            </ul>

            <button
                v-if="trimmed"
                type="button"
                role="option"
                :id="`global-search-option-${showAllIndex}`"
                :aria-selected="highlighted === showAllIndex"
                class="block w-full border-t border-gray-200 px-3 py-2 text-start text-sm text-indigo-600 cursor-pointer"
                :class="highlighted === showAllIndex ? 'bg-indigo-50' : 'hover:bg-gray-50'"
                @mouseenter="highlighted = showAllIndex"
                @click="showAll"
            >
                {{ t('search.show_all', { q: trimmed }) }}
            </button>
        </div>
    </div>
</template>
