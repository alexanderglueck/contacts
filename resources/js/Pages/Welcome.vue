<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="Welcome" />

    <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
        <div class="max-w-xl text-center">
            <h1 class="text-4xl font-bold text-gray-900">Contacts</h1>
            <p class="mt-4 text-lg text-gray-600">
                A simple contact management system to keep track of your friends and family.
            </p>

            <div class="mt-8 flex justify-center gap-4">
                <Link
                    v-if="canLogin"
                    :href="route('login')"
                    class="rounded-md bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
                >
                    Login
                </Link>
                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="rounded-md bg-white px-5 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-gray-300 hover:bg-gray-50"
                >
                    Register
                </Link>
            </div>
        </div>
    </div>
</template>
