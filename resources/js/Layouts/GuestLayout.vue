<script setup>
import { Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import FlashBanner from '@/Components/FlashBanner.vue';
</script>

<template>
    <div class="min-h-screen flex flex-col items-center bg-gray-50 pt-6 sm:pt-0">
        <div>
            <Link href="/">
                <ApplicationLogo class="h-16 w-16 fill-current text-gray-500" />
            </Link>
        </div>

        <div class="mt-6 w-full sm:max-w-md px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <FlashBanner class="mb-4" />
            <slot />
        </div>
    </div>
</template>
