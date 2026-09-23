<script setup>
import { computed, ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import 'temporal-polyfill/global';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/vue3/daygrid';
import listPlugin from '@fullcalendar/vue3/list';
import interactionPlugin from '@fullcalendar/vue3/interaction';
// FullCalendar's own UI strings ("Month / Week / today" buttons, weekday
// names, the "Wk" column header) come from its bundled locale packs,
// not from vue-i18n. We pass the active vue-i18n locale via `locale:`
// and let FullCalendar pick the matching pack from `locales:`. English
// is FullCalendar's built-in default so we only register `de`.
import deLocale from '@fullcalendar/vue3/locales/de';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';

// v7 ships no styling of its own: a theme is a PLUGIN (it emits the hashed
// class names) plus its CSS. All five are loaded so the picker below can switch
// between them at runtime; the class names are theme-prefixed, so they coexist.
import '@fullcalendar/vue3/skeleton.css';
import breezyTheme from '@fullcalendar/vue3/themes/breezy';
import '@fullcalendar/vue3/themes/breezy/theme.css';
import '@fullcalendar/vue3/themes/breezy/palettes/indigo.css';
import classicTheme from '@fullcalendar/vue3/themes/classic';
import '@fullcalendar/vue3/themes/classic/theme.css';
import '@fullcalendar/vue3/themes/classic/palette.css';
import formaTheme from '@fullcalendar/vue3/themes/forma';
import '@fullcalendar/vue3/themes/forma/theme.css';
import '@fullcalendar/vue3/themes/forma/palettes/purple.css';
import monarchTheme from '@fullcalendar/vue3/themes/monarch';
import '@fullcalendar/vue3/themes/monarch/theme.css';
import '@fullcalendar/vue3/themes/monarch/palettes/purple.css';
import pulseTheme from '@fullcalendar/vue3/themes/pulse';
import '@fullcalendar/vue3/themes/pulse/theme.css';
import '@fullcalendar/vue3/themes/pulse/palettes/purple.css';

const THEMES = {
    breezy: breezyTheme,
    classic: classicTheme,
    forma: formaTheme,
    monarch: monarchTheme,
    pulse: pulseTheme,
};
const THEME_STORAGE_KEY = 'calendar.theme';

const { t, locale } = useI18n();

const props = defineProps({
    hasCalendarSyncToken: { type: Boolean, default: false },
});

const syncUrl = ref(null);
const rotating = ref(false);

const rotateConfirmOpen = ref(false);

// User-facing trigger: gated on whether a token already exists. Rotating an
// existing one invalidates the old URL, so confirm; first-time generation is
// harmless and runs straight through.
const requestRotate = () => {
    if (props.hasCalendarSyncToken) {
        rotateConfirmOpen.value = true;
    } else {
        doRotate();
    }
};

const doRotate = async () => {
    rotateConfirmOpen.value = false;
    rotating.value = true;
    try {
        const { data } = await axios.post(route('calendar.sync_token'));
        syncUrl.value = data.url;
    } finally {
        rotating.value = false;
    }
};

const copySyncUrl = async () => {
    if (! syncUrl.value) return;
    try {
        await navigator.clipboard.writeText(syncUrl.value);
    } catch (e) {
        const el = document.createElement('textarea');
        el.value = syncUrl.value;
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
    }
};

const fetchEvents = (info, successCallback, failureCallback) => {
    axios
        .get(route('calendar.events'), {
            params: {
                start: info.startStr.slice(0, 10),
                end: info.endStr.slice(0, 10),
            },
        })
        .then(({ data }) => successCallback(data))
        .catch((e) => failureCallback(e));
};

const onEventClick = (info) => {
    info.jsEvent.preventDefault();
    if (info.event.url) {
        router.visit(info.event.url);
    }
};

// Which theme is active. Kept in localStorage so a choice survives reloads
// while we are evaluating; nothing server-side stores it yet.
const theme = ref('breezy');
try {
    const stored = localStorage.getItem(THEME_STORAGE_KEY);
    if (stored && stored in THEMES) {
        theme.value = stored;
    }
} catch {
    // storage unavailable (private mode) -- fall back to the default
}
const setTheme = (name) => {
    theme.value = name;
    try {
        localStorage.setItem(THEME_STORAGE_KEY, name);
    } catch {
        // non-fatal: the picker still works for this visit
    }
};

const calendarOptions = computed(() => ({
    plugins: [dayGridPlugin, listPlugin, interactionPlugin, THEMES[theme.value]],
    initialView: 'dayGridMonth',
    locales: [deLocale],
    locale: locale.value,
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,dayGridWeek,listMonth,listYear',
    },
    views: {
        listYear: { buttonText: t('calendar.list_year') },
    },
    firstDay: 1,
    weekNumbers: true,
    height: 'auto',
    events: fetchEvents,
    eventClick: onEventClick,
    eventColor: '#4F46E5',
    eventTextColor: '#ffffff',
    noEventsText: t('calendar.no_events'),
}));
</script>

<template>
    <AppLayout :title="t('calendar.title')">
        <Head :title="t('calendar.title')" />

        <div class="space-y-4">
            <div class="bg-white shadow rounded-lg p-4">
                <!-- Temporary while we settle on a FullCalendar 7 theme. The choice
                     lives in localStorage only; remove this block and keep a single
                     theme import once one is picked. -->
                <div class="mb-3 flex flex-wrap items-center gap-2 border-b border-gray-200 pb-3">
                    <span class="text-xs font-medium text-gray-500">Theme</span>
                    <button
                        v-for="name in Object.keys(THEMES)"
                        :key="name"
                        type="button"
                        class="rounded px-2 py-1 text-xs font-medium capitalize"
                        :class="name === theme
                            ? 'bg-indigo-600 text-white'
                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        @click="setTheme(name)"
                    >{{ name }}</button>
                </div>
                <!-- Swapping a theme swaps a plugin, so remount rather than patch. -->
                <FullCalendar :key="theme" :options="calendarOptions" />
            </div>

            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-sm font-semibold text-gray-900">{{ t('calendar.subscribe_title') }}</h2>
                    <p class="mt-1 text-xs text-gray-500">
                        {{ t('calendar.subscribe_help') }}
                    </p>
                </div>

                <div class="px-6 py-4 space-y-3">
                    <div v-if="syncUrl" class="space-y-2">
                        <p class="text-sm text-emerald-800 font-medium">
                            {{ t('calendar.copy_now') }}
                        </p>
                        <div class="flex items-center gap-2">
                            <code class="flex-1 break-all bg-gray-50 px-3 py-2 rounded font-mono text-xs">{{ syncUrl }}</code>
                            <SecondaryButton type="button" @click="copySyncUrl">{{ t('calendar.copy') }}</SecondaryButton>
                        </div>
                    </div>

                    <p v-else-if="hasCalendarSyncToken" class="text-sm text-gray-600">
                        {{ t('calendar.has_token') }}
                    </p>
                    <p v-else class="text-sm text-gray-600">
                        {{ t('calendar.no_token') }}
                    </p>

                    <div>
                        <PrimaryButton type="button" @click="requestRotate" :disabled="rotating" :class="{ 'opacity-50': rotating }">
                            {{ hasCalendarSyncToken ? t('calendar.rotate') : t('calendar.generate') }}
                        </PrimaryButton>
                    </div>
                </div>
            </div>
        </div>

        <ConfirmModal
            :open="rotateConfirmOpen"
            :title="t('calendar.rotate_title')"
            :body="t('calendar.rotate_confirm')"
            :confirm-label="t('calendar.rotate')"
            variant="danger"
            :busy="rotating"
            @confirm="doRotate"
            @cancel="rotateConfirmOpen = false"
        />
    </AppLayout>
</template>

<style>
.fc .fc-button-primary {
    background-color: #4f46e5;
    border-color: #4f46e5;
}
.fc .fc-button-primary:hover {
    background-color: #4338ca;
    border-color: #4338ca;
}
.fc .fc-button-primary:not(:disabled).fc-button-active,
.fc .fc-button-primary:not(:disabled):active {
    background-color: #3730a3;
    border-color: #3730a3;
}
.fc .fc-daygrid-day.fc-day-today {
    background-color: #eef2ff;
}
.fc .fc-list-day-cushion {
    background-color: #eef2ff;
}
</style>
