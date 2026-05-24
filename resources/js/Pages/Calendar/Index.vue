<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import listPlugin from '@fullcalendar/list';
import interactionPlugin from '@fullcalendar/interaction';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const { t, locale } = useI18n();

const props = defineProps({
    hasCalendarSyncToken: { type: Boolean, default: false },
});

const syncUrl = ref(null);
const rotating = ref(false);

const rotateSyncToken = async () => {
    if (props.hasCalendarSyncToken && ! confirm(t('calendar.rotate_confirm'))) {
        return;
    }

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

const calendarOptions = {
    plugins: [dayGridPlugin, listPlugin, interactionPlugin],
    initialView: 'dayGridMonth',
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
};
</script>

<template>
    <AppLayout :title="t('calendar.title')">
        <Head :title="t('calendar.title')" />

        <div class="space-y-4">
            <div class="bg-white shadow rounded-lg p-4">
                <FullCalendar :options="calendarOptions" />
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
                        <PrimaryButton type="button" @click="rotateSyncToken" :disabled="rotating" :class="{ 'opacity-50': rotating }">
                            {{ hasCalendarSyncToken ? t('calendar.rotate') : t('calendar.generate') }}
                        </PrimaryButton>
                    </div>
                </div>
            </div>
        </div>
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
