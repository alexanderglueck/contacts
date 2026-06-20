<script setup>
import { computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const page = usePage();
const impersonating = computed(() => page.props.auth?.impersonating);
const name = computed(() => page.props.auth?.user?.name);

const stop = () => {
    router.delete(route('user.impersonate.destroy'));
};
</script>

<template>
    <div
        v-if="impersonating"
        class="bg-amber-500 text-amber-950"
        role="alert"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex items-center justify-between gap-4">
            <p class="text-sm font-medium">{{ t('impersonation.banner', { name }) }}</p>
            <button
                type="button"
                class="shrink-0 inline-flex items-center rounded-md bg-amber-950/10 px-3 py-1 text-sm font-semibold hover:bg-amber-950/20 focus:outline-none cursor-pointer"
                @click="stop"
            >
                {{ t('impersonation.stop') }}
            </button>
        </div>
    </div>
</template>
