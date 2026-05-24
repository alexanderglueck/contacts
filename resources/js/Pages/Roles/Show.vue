<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    role: { type: Object, required: true },
    permissions: { type: Array, default: () => [] },
    users: { type: Array, default: () => [] },
    can: { type: Object, default: () => ({}) },
    deletion_blocked_reason: { type: String, default: null },
});
</script>

<template>
    <AppLayout :title="role.name">
        <Head :title="role.name" />

        <div class="text-sm">
            <Link :href="route('roles.index')" class="text-indigo-600 hover:text-indigo-500">
                &larr; Back to roles
            </Link>
        </div>

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">{{ role.name }}</h2>
                <div class="flex gap-2 text-sm">
                    <Link
                        v-if="can.edit"
                        :href="route('roles.edit', role.ulid)"
                        class="text-indigo-600 hover:text-indigo-500"
                    >
                        Edit
                    </Link>
                    <Link
                        v-if="can.delete"
                        :href="route('roles.delete', role.ulid)"
                        class="text-red-600 hover:text-red-500"
                    >
                        Delete
                    </Link>
                    <span
                        v-else-if="deletion_blocked_reason"
                        class="text-gray-400 cursor-not-allowed"
                        :title="deletion_blocked_reason"
                    >
                        Delete
                    </span>
                </div>
            </div>

            <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm">
                <div>
                    <h3 class="font-medium text-gray-700 mb-2">Permissions</h3>
                    <p v-if="permissions.length === 0" class="text-gray-500">No permissions assigned.</p>
                    <ul v-else class="space-y-1">
                        <li v-for="permission in permissions" :key="permission.id" class="text-gray-900">
                            {{ permission.name }}
                        </li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-medium text-gray-700 mb-2">Users</h3>
                    <p v-if="users.length === 0" class="text-gray-500">No users assigned.</p>
                    <ul v-else class="space-y-1">
                        <li v-for="user in users" :key="user.id" class="text-gray-900">
                            {{ user.name }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
