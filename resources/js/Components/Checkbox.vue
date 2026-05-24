<script setup>
defineProps({
    checked: { type: [Boolean, Array], default: false },
    value: { default: null },
});

defineEmits(['update:checked']);

const proxyChecked = (event) => {
    return event.target.checked;
};
</script>

<template>
    <input
        type="checkbox"
        :value="value"
        :checked="Array.isArray(checked) ? checked.includes(value) : checked"
        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
        @change="$emit('update:checked', $event.target.checked)"
    />
</template>
