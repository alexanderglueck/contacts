<script setup>
defineProps({
    modelValue: { type: [String, Number, Array, null], default: null },
    multiple: { type: Boolean, default: false },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <select
        :value="modelValue"
        :multiple="multiple"
        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
        @change="$emit('update:modelValue', multiple
            ? Array.from($event.target.selectedOptions).map(o => o.value)
            : $event.target.value)"
    >
        <slot />
    </select>
</template>
