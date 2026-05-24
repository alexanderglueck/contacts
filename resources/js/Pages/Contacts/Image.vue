<script setup>
import { nextTick, onBeforeUnmount, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.css';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    contact: { type: Object, required: true },
});

const OUTPUT_SIZE = 400;

const currentImage = ref(props.contact.image ? `/storage/${props.contact.image}` : null);
const sourceImage = ref(null);
const imgEl = ref(null);
const fileInputEl = ref(null);
let cropper = null;

const form = useForm({
    file: null,
});

const destroyCropper = () => {
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
};

const clearSelection = () => {
    destroyCropper();
    sourceImage.value = null;
    form.file = null;
    form.clearErrors();
    if (fileInputEl.value) fileInputEl.value.value = '';
};

const onFileChange = (event) => {
    const file = event.target.files?.[0];
    if (! file) return;

    form.clearErrors();

    const reader = new FileReader();
    reader.onload = async (e) => {
        sourceImage.value = e.target.result;
        await nextTick();
        destroyCropper();
        if (! imgEl.value) return;
        cropper = new Cropper(imgEl.value, {
            aspectRatio: 1,
            viewMode: 1,
            autoCropArea: 1,
            background: false,
            responsive: true,
            zoomable: true,
            movable: true,
        });
    };
    reader.readAsDataURL(file);
};

const submit = () => {
    if (! cropper) return;

    cropper.getCroppedCanvas({
        width: OUTPUT_SIZE,
        height: OUTPUT_SIZE,
        imageSmoothingQuality: 'high',
    }).toBlob((blob) => {
        if (! blob) return;
        form.file = new File([blob], 'contact.png', { type: 'image/png' });

        form.put(route('contacts.update_image', props.contact.ulid), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => clearSelection(),
        });
    }, 'image/png', 0.92);
};

onBeforeUnmount(destroyCropper);
</script>

<template>
    <AppLayout :title="`${contact.fullname} — Image`">
        <Head :title="`${contact.fullname} — Image`" />

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Image</h2>
            </div>

            <div class="px-6 py-4 space-y-4">
                <div v-if="!sourceImage" class="space-y-4">
                    <div v-if="currentImage" class="flex justify-center">
                        <img :src="currentImage" :alt="contact.fullname" class="w-48 h-48 object-cover rounded-lg" />
                    </div>

                    <div>
                        <label for="file" class="block text-sm font-medium text-gray-700 mb-1">Choose image (JPEG/PNG)</label>
                        <input
                            id="file"
                            ref="fileInputEl"
                            type="file"
                            accept="image/jpeg,image/png"
                            class="block w-full text-sm text-gray-700"
                            @change="onFileChange"
                        />
                        <InputError :message="form.errors.file" />
                    </div>
                </div>

                <div v-else class="space-y-3">
                    <p class="text-sm text-gray-600">
                        Drag to reposition, scroll to zoom. Crop is locked to a square.
                    </p>
                    <div class="max-h-[60vh] overflow-hidden">
                        <img ref="imgEl" :src="sourceImage" alt="Crop preview" class="block max-w-full" />
                    </div>
                    <InputError :message="form.errors.file" />
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
                <template v-if="!sourceImage">
                    <Link :href="route('contacts.show', contact.ulid)">
                        <SecondaryButton type="button">Back</SecondaryButton>
                    </Link>
                </template>
                <template v-else>
                    <SecondaryButton type="button" @click="clearSelection" :disabled="form.processing">
                        Cancel
                    </SecondaryButton>
                    <PrimaryButton type="button" @click="submit" :disabled="form.processing" :class="{ 'opacity-50': form.processing }">
                        Save crop
                    </PrimaryButton>
                </template>
            </div>
        </div>
    </AppLayout>
</template>
