<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    user: { type: Object, required: true },
});

const preview = ref(props.user.image ? `/storage/${props.user.image}` : null);

const form = useForm({
    image: null,
});

const resetForm = useForm({});

const onFileChange = (event) => {
    const file = event.target.files[0];
    if (!file) return;
    form.image = file;

    const reader = new FileReader();
    reader.onload = (e) => {
        preview.value = e.target.result;
    };
    reader.readAsDataURL(file);
};

const submit = () => {
    form.post(route('user_settings.image.update'), {
        forceFormData: true,
        method: 'put',
    });
};

const resetImage = () => {
    resetForm.delete(route('user_settings.image.destroy'));
};
</script>

<template>
    <AppLayout title="Profile image">
        <Head title="Profile image" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Profile image</h2>
            </div>

            <div class="px-6 py-4 space-y-4">
                <div v-if="preview" class="flex justify-center">
                    <img :src="preview" :alt="user.name" class="w-32 h-32 object-cover rounded-full" />
                </div>

                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Choose image</label>
                    <input
                        id="image"
                        type="file"
                        accept="image/jpeg,image/png"
                        class="block w-full text-sm text-gray-700"
                        @change="onFileChange"
                    />
                    <InputError :message="form.errors.image" />
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
                <SecondaryButton
                    v-if="user.has_image"
                    type="button"
                    @click="resetImage"
                    :disabled="resetForm.processing"
                >
                    Reset
                </SecondaryButton>
                <PrimaryButton :disabled="form.processing || !form.image" :class="{ 'opacity-50': form.processing || !form.image }">
                    Update image
                </PrimaryButton>
            </div>
        </form>
    </AppLayout>
</template>
