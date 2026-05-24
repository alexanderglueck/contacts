<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import SlideOver from '@/Components/SlideOver.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const { t } = useI18n();

const props = defineProps({
    open: { type: Boolean, required: true },
    contact: { type: Object, required: true },
    items: { type: Array, default: () => [] },
});

const emit = defineEmits(['close']);

const title = computed(() => t('contacts.section.activities'));

// "created_contact" → "Created contact". Cosmetic only; the underlying
// action string is what's stored.
const humanize = (action) => {
    if (!action) return '';
    return action
        .replace(/_/g, ' ')
        .replace(/\b\w/, (c) => c.toUpperCase());
};
</script>

<template>
    <SlideOver :open="open" :title="title" @close="emit('close')">
        <div v-if="items.length === 0" class="text-sm text-gray-500 text-center py-6">
            {{ t('contacts.slideover.empty_activities') }}
        </div>

        <ol v-else class="relative -mx-6 divide-y divide-gray-200">
            <li v-for="item in items" :key="item.id" class="px-6 py-3 text-sm">
                <div class="flex items-baseline justify-between gap-3">
                    <span class="text-gray-900">
                        <span class="font-medium">{{ item.user?.name ?? 'System' }}</span>
                        <span class="text-gray-600"> {{ humanize(item.action) }}</span>
                    </span>
                    <span class="text-xs text-gray-500 shrink-0">{{ item.created_at }}</span>
                </div>
            </li>
        </ol>

        <template #footer>
            <SecondaryButton type="button" @click="emit('close')">{{ t('contacts.slideover.close') }}</SecondaryButton>
        </template>
    </SlideOver>
</template>
