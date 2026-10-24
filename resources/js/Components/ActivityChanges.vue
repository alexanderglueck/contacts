<script setup>
import { useI18n } from 'vue-i18n';

const { t, te } = useI18n();

defineProps({
    changes: { type: Array, default: () => [] },
});

// Activity diffs carry column names; the form labels live under
// contacts.fields without the _id suffix.
const label = (field) => {
    const key = `contacts.fields.${field.replace(/_id$/, '')}`;
    return te(key) ? t(key) : field;
};
</script>

<template>
    <ul v-if="changes && changes.length" class="mt-1 space-y-0.5 text-xs text-gray-600">
        <li v-for="change in changes" :key="change.field">
            <span class="font-medium text-gray-700">{{ label(change.field) }}:</span>
            <span class="line-through text-gray-400 ms-1">{{ change.old ?? '–' }}</span>
            <span class="mx-1">→</span>
            <span>{{ change.new ?? '–' }}</span>
        </li>
    </ul>
</template>
