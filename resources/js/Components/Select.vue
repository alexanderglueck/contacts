<script setup>
import { nextTick, onMounted, ref, watch } from 'vue';

const props = defineProps({
    modelValue: { type: [String, Number, Array, null], default: null },
    multiple: { type: Boolean, default: false },
});

const emit = defineEmits(['update:modelValue']);

const selectEl = ref(null);

// `<select multiple>` ignores a `:value` array — selected state has to be set
// per-option via the `selected` property. Run after Vue renders the slotted
// options so we hit the actual DOM nodes (not the v-for source list), and
// compare as strings since DOM option.value is always a string while modelValue
// items often arrive as numbers from pluck('id').
const syncSelected = async () => {
    await nextTick();
    if (! selectEl.value || ! props.multiple) return;
    const values = (props.modelValue ?? []).map((v) => String(v));
    for (const opt of selectEl.value.options) {
        opt.selected = values.includes(String(opt.value));
    }
};

watch(() => [props.modelValue, props.multiple], syncSelected, { immediate: true });
onMounted(syncSelected);

const onChange = (event) => {
    if (props.multiple) {
        emit('update:modelValue', Array.from(event.target.selectedOptions).map((o) => o.value));
    } else {
        emit('update:modelValue', event.target.value);
    }
};
</script>

<template>
    <select
        ref="selectEl"
        :value="multiple ? undefined : modelValue"
        :multiple="multiple"
        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
        @change="onChange"
    >
        <slot />
    </select>
</template>
