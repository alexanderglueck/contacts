<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

defineProps({
    form: { type: Object, required: true },
    permissions: { type: Array, default: () => [] },
    users: { type: Array, default: () => [] },
});
</script>

<template>
    <div class="space-y-4">
        <div>
            <InputLabel for="name" value="Name *" />
            <TextInput id="name" v-model="form.name" required autofocus />
            <InputError :message="form.errors.name" />
        </div>

        <div>
            <span class="block text-sm font-medium text-gray-700 mb-1">Permissions</span>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-1 max-h-72 overflow-y-auto rounded border border-gray-200 p-3">
                <label v-for="permission in permissions" :key="permission.id" class="inline-flex items-center">
                    <input
                        type="checkbox"
                        :value="permission.id"
                        v-model="form.permissions"
                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                    />
                    <span class="ms-2 text-sm text-gray-700">{{ permission.name }}</span>
                </label>
            </div>
            <InputError :message="form.errors.permissions" />
        </div>

        <div>
            <span class="block text-sm font-medium text-gray-700 mb-1">Users</span>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-1 max-h-72 overflow-y-auto rounded border border-gray-200 p-3">
                <label v-for="user in users" :key="user.id" class="inline-flex items-center">
                    <input
                        type="checkbox"
                        :value="user.id"
                        v-model="form.users"
                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                    />
                    <span class="ms-2 text-sm text-gray-700">{{ user.name }}</span>
                </label>
            </div>
            <InputError :message="form.errors.users" />
        </div>
    </div>
</template>
