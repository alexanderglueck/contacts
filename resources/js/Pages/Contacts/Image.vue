<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    contact: { type: Object, required: true },
});

const fileInput = ref(null);
const preview = ref(props.contact.image ? `/storage/${props.contact.image}` : null);

const form = useForm({
    file: null,
    image_x: 0,
    image_y: 0,
    image_width: 0,
    image_height: 0,
});

const onFileChange = (event) => {
    const file = event.target.files[0];
    if (!file) return;
    form.file = file;

    const reader = new FileReader();
    reader.onload = (e) => {
        preview.value = e.target.result;
    };
    reader.readAsDataURL(file);
};

const submit = () => {
    form.post(route('contacts.update_image', props.contact.ulid), {
        forceFormData: true,
    });
};
</script>

<template>
    <AppLayout :title="`${contact.fullname} — Image`">
        <Head :title="`${contact.fullname} — Image`" />

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Profile image</h2>
            </div>

            <div class="px-6 py-4 space-y-4">
                <div v-if="preview" class="flex justify-center">
                    <img :src="preview" :alt="contact.fullname" class="w-48 h-48 object-cover rounded-lg" />
                </div>

                <div>
                    <label for="file" class="block text-sm font-medium text-gray-700 mb-1">Choose image (JPEG/PNG)</label>
                    <input
                        id="file"
                        ref="fileInput"
                        type="file"
                        accept="image/jpeg,image/png"
                        class="block w-full text-sm text-gray-700"
                        @change="onFileChange"
                    />
                    <InputError :message="form.errors.file" />
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
                <Link :href="route('contacts.show', contact.ulid)">
                    <SecondaryButton type="button">Cancel</SecondaryButton>
                </Link>
                <PrimaryButton :disabled="form.processing || !form.file" :class="{ 'opacity-50': form.processing || !form.file }">
                    Upload
                </PrimaryButton>
            </div>
        </form>
    </AppLayout>
</template>
