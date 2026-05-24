<script setup>
import { Menu, MenuButton, MenuItems } from '@headlessui/vue';

defineProps({
    align: { type: String, default: 'right' },
    width: { type: String, default: '48' },
});
</script>

<template>
    <Menu as="div" class="relative inline-block text-left">
        <MenuButton as="template">
            <slot name="trigger" />
        </MenuButton>

        <transition
            enter-active-class="transition duration-100 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
        >
            <MenuItems
                class="absolute z-50 mt-2 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                :class="[
                    align === 'right' ? 'right-0' : align === 'left' ? 'left-0' : 'origin-top',
                    `w-${width}`,
                ]"
            >
                <div class="py-1">
                    <slot name="content" />
                </div>
            </MenuItems>
        </transition>
    </Menu>
</template>
