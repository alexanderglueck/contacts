<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();

const flash = computed(() => page.props.flash || {});

const banners = computed(() =>
    [
        { type: 'success', message: flash.value.success, classes: 'bg-green-50 text-green-800 ring-green-200' },
        { type: 'error', message: flash.value.error, classes: 'bg-red-50 text-red-800 ring-red-200' },
        { type: 'warning', message: flash.value.warning, classes: 'bg-yellow-50 text-yellow-800 ring-yellow-200' },
        { type: 'info', message: flash.value.info, classes: 'bg-blue-50 text-blue-800 ring-blue-200' },
    ].filter((b) => b.message)
);
</script>

<template>
    <div v-if="banners.length" class="space-y-2">
        <div
            v-for="banner in banners"
            :key="banner.type"
            class="rounded-md px-4 py-3 text-sm ring-1 ring-inset"
            :class="banner.classes"
            role="alert"
        >
            {{ banner.message }}
        </div>
    </div>
</template>
