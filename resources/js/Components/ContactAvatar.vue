<script setup>
import { computed } from 'vue';

const props = defineProps({
    contact: { type: Object, required: true },
    // Tailwind sizing classes, so callers can size the circle in place.
    sizeClass: { type: String, default: 'h-9 w-9' },
    textClass: { type: String, default: 'text-xs' },
});

const initials = computed(() => {
    const first = (props.contact.firstname ?? '').trim();
    const last = (props.contact.lastname ?? '').trim();
    const i = (first[0] ?? '') + (last[0] ?? '');
    return (i || (props.contact.fullname ?? '?')[0] || '?').toUpperCase();
});

// Stable per-contact background colour for the initials placeholder, so the
// same person always lands on the same swatch instead of flickering across
// re-renders. Hashing the ULID keeps it cheap and identity-bound.
const palette = ['bg-rose-200', 'bg-amber-200', 'bg-lime-200', 'bg-emerald-200', 'bg-sky-200', 'bg-violet-200', 'bg-fuchsia-200'];

const background = computed(() => {
    let h = 0;
    for (const c of (props.contact.ulid ?? '')) h = (h * 31 + c.charCodeAt(0)) >>> 0;
    return palette[h % palette.length];
});
</script>

<template>
    <img
        v-if="contact.image"
        :src="`/storage/${contact.image}`"
        :alt="contact.fullname"
        class="rounded-full object-cover flex-shrink-0 bg-gray-100"
        :class="sizeClass"
        loading="lazy"
    />
    <span
        v-else
        class="rounded-full flex items-center justify-center font-semibold text-gray-700 flex-shrink-0"
        :class="[sizeClass, textClass, background]"
        aria-hidden="true"
    >{{ initials }}</span>
</template>
