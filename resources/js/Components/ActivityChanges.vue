<script setup>
import { useI18n } from 'vue-i18n';

const { t, te } = useI18n();

defineProps({
    changes: { type: Array, default: () => [] },
});

// The server sends a translated label; older payloads fall back to the
// contact form labels, which live under contacts.fields without the _id suffix.
const label = (change) => {
    if (change.label) return change.label;
    const key = `contacts.fields.${change.field.replace(/_id$/, '')}`;
    return te(key) ? t(key) : change.field;
};
</script>

<template>
    <ul v-if="changes && changes.length" class="mt-1 space-y-0.5 text-xs text-gray-600">
        <li v-for="change in changes" :key="change.field">
            <span class="font-medium text-gray-700">{{ label(change) }}:</span>
            <span class="line-through text-gray-400 ms-1">{{ change.old ?? '–' }}</span>
            <span class="mx-1">→</span>
            <span>{{ change.new ?? '–' }}</span>
        </li>
    </ul>
</template>
