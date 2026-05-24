<script setup>
defineProps({
    message: String,
});
</script>

<template>
    <p v-if="message" class="mt-1 text-sm text-red-600">{{ message }}</p>
</template>
