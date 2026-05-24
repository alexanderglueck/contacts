<script setup>
defineProps({
    for: String,
    value: String,
});
</script>

<template>
    <label :for="for" class="block text-sm font-medium text-gray-700">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
