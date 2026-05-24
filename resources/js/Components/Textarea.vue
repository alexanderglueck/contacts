<script setup>
defineProps({
    modelValue: { type: String, default: '' },
    rows: { type: [Number, String], default: 3 },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <textarea
        :value="modelValue"
        :rows="rows"
        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
        @input="$emit('update:modelValue', $event.target.value)"
    />
</template>
