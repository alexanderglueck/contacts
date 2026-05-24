<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    currentPage: { type: Number, required: true },
    lastPage: { type: Number, required: true },
});

const value = ref(String(props.currentPage));

watch(() => props.currentPage, (page) => {
    value.value = String(page);
});

const jump = () => {
    const target = Math.min(Math.max(parseInt(value.value, 10) || 1, 1), props.lastPage);
    if (target === props.currentPage) {
        value.value = String(props.currentPage);
        return;
    }

    const url = new URL(window.location.href);
    url.searchParams.set('page', String(target));
    router.visit(url.pathname + url.search, { preserveScroll: true });
};
</script>

<template>
    <form
        v-if="lastPage > 1"
        @submit.prevent="jump"
        class="flex items-center gap-1 text-sm text-gray-600"
    >
        <label :for="`page-jump-${currentPage}`" class="sr-only">Jump to page</label>
        <span>Page</span>
        <input
            :id="`page-jump-${currentPage}`"
            v-model="value"
            type="number"
            min="1"
            :max="lastPage"
            class="w-14 px-2 py-1 text-sm text-center border border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500"
            @blur="jump"
        />
        <span>of {{ lastPage }}</span>
    </form>
</template>
