<script setup>
import { Head, router } from '@inertiajs/vue3';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import AppLayout from '@/Layouts/AppLayout.vue';

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
    plugins: [dayGridPlugin, interactionPlugin],
    initialView: 'dayGridMonth',
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,dayGridWeek',
    },
    firstDay: 1,
    weekNumbers: true,
    height: 'auto',
    events: fetchEvents,
    eventClick: onEventClick,
    eventColor: '#4F46E5',
    eventTextColor: '#ffffff',
};
</script>

<template>
    <AppLayout title="Calendar">
        <Head title="Calendar" />

        <div class="bg-white shadow rounded-lg p-4">
            <FullCalendar :options="calendarOptions" />
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
</style>
