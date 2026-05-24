<script setup>
import { onMounted, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const events = ref([]);
const loading = ref(true);
const error = ref(null);

const startOfMonth = () => {
    const d = new Date();
    return new Date(d.getFullYear(), d.getMonth(), 1).toISOString().slice(0, 10);
};

const endOfNextMonth = () => {
    const d = new Date();
    return new Date(d.getFullYear(), d.getMonth() + 2, 0).toISOString().slice(0, 10);
};

onMounted(async () => {
    try {
        const { data } = await axios.get(route('calendar.events'), {
            params: { start: startOfMonth(), end: endOfNextMonth() },
        });
        events.value = data;
    } catch (e) {
        error.value = e.message;
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <AppLayout title="Calendar">
        <Head title="Calendar" />

        <div class="bg-white shadow rounded-lg">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-medium text-gray-900">Upcoming dates</h2>
                <p class="text-xs text-gray-500 mt-1">
                    Next ~60 days. A proper calendar grid is a follow-up.
                </p>
            </div>

            <div v-if="loading" class="px-6 py-8 text-center text-sm text-gray-500">Loading…</div>
            <div v-else-if="error" class="px-6 py-8 text-center text-sm text-red-600">{{ error }}</div>
            <div v-else-if="events.length === 0" class="px-6 py-8 text-center text-sm text-gray-500">
                No upcoming dates.
            </div>
            <ul v-else class="divide-y divide-gray-200">
                <li v-for="event in events" :key="`${event.id}-${event.start}`" class="px-6 py-3 flex justify-between text-sm">
                    <Link
                        v-if="event.url"
                        :href="event.url"
                        class="text-indigo-600 hover:text-indigo-500"
                    >
                        {{ event.title }}
                    </Link>
                    <span v-else class="text-gray-900">{{ event.title }}</span>
                    <span class="text-gray-600">{{ event.start }}</span>
                </li>
            </ul>
        </div>
    </AppLayout>
</template>
