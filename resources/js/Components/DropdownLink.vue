<script setup>
import { Link } from '@inertiajs/vue3';
import { MenuItem } from '@headlessui/vue';

defineProps({
    href: String,
    method: { type: String, default: 'get' },
    as: { type: String, default: 'a' },
});
</script>

<template>
    <MenuItem v-slot="{ active }">
        <Link
            :href="href"
            :method="method"
            :as="as"
            :class="[
                active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                'block w-full text-left px-4 py-2 text-sm cursor-pointer',
            ]"
        >
            <slot />
        </Link>
    </MenuItem>
</template>
