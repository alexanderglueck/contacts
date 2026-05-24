<script setup>
import { nextTick, onBeforeUnmount, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.css';
import SettingsLayout from '@/Layouts/SettingsLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';

const { t } = useI18n();

const props = defineProps({
    user: { type: Object, required: true },
});

const OUTPUT_SIZE = 400;

const currentImage = ref(props.user.image ? `/storage/${props.user.image}` : null);
const sourceImage = ref(null);
const imgEl = ref(null);
const fileInputEl = ref(null);
let cropper = null;

const form = useForm({
    image: null,
});

const resetForm = useForm({});

const initCropper = async () => {
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

const destroyCropper = () => {
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
};

const clearSelection = () => {
    destroyCropper();
    sourceImage.value = null;
    form.image = null;
    form.clearErrors();
    if (fileInputEl.value) fileInputEl.value.value = '';
};

const cropCurrent = () => {
    if (! currentImage.value) return;
    form.clearErrors();
    sourceImage.value = `${currentImage.value}?t=${Date.now()}`;
    initCropper();
};

const pickNewFile = () => fileInputEl.value?.click();

const onFileChange = (event) => {
    const file = event.target.files?.[0];
    if (! file) return;
    form.clearErrors();

    const reader = new FileReader();
    reader.onload = (e) => {
        sourceImage.value = e.target.result;
        initCropper();
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
        form.image = new File([blob], 'profile.png', { type: 'image/png' });

        form.put(route('user_settings.image.update'), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => clearSelection(),
        });
    }, 'image/png', 0.92);
};

const resetImage = () => {
    resetForm.delete(route('user_settings.image.destroy'), {
        preserveScroll: true,
    });
};

onBeforeUnmount(destroyCropper);
</script>

<template>
    <SettingsLayout :title="t('settings.image_editor.page_title_profile')">
        <Head :title="t('settings.image_editor.page_title_profile')" />

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">{{ t('settings.image_editor.section_heading_profile') }}</h2>
            </div>

            <div class="px-6 py-4 space-y-4">
                <div v-if="!sourceImage" class="space-y-4">
                    <div v-if="currentImage" class="flex justify-center">
                        <img :src="currentImage" :alt="user.name" class="w-32 h-32 object-cover rounded-full" />
                    </div>

                    <input
                        ref="fileInputEl"
                        type="file"
                        accept="image/jpeg,image/png"
                        class="hidden"
                        @change="onFileChange"
                    />
                    <InputError :message="form.errors.image" />
                </div>

                <div v-else class="space-y-3">
                    <p class="text-sm text-gray-600">
                        {{ t('settings.image_editor.crop_help') }}
                    </p>
                    <div class="max-h-[60vh] overflow-hidden">
                        <img ref="imgEl" :src="sourceImage" :alt="t('settings.image_editor.crop_preview_alt')" class="block max-w-full" />
                    </div>
                    <InputError :message="form.errors.image" />
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex flex-wrap justify-end gap-2">
                <template v-if="!sourceImage">
                    <DangerButton
                        v-if="user.has_image"
                        type="button"
                        @click="resetImage"
                        :disabled="resetForm.processing"
                    >
                        {{ t('settings.image_editor.reset') }}
                    </DangerButton>
                    <SecondaryButton
                        v-if="currentImage"
                        type="button"
                        @click="cropCurrent"
                    >
                        {{ t('settings.image_editor.crop_current') }}
                    </SecondaryButton>
                    <PrimaryButton type="button" @click="pickNewFile">
                        {{ t('settings.image_editor.upload_new') }}
                    </PrimaryButton>
                </template>
                <template v-else>
                    <SecondaryButton type="button" @click="clearSelection" :disabled="form.processing">
                        {{ t('common.cancel') }}
                    </SecondaryButton>
                    <PrimaryButton type="button" @click="submit" :disabled="form.processing" :class="{ 'opacity-50': form.processing }">
                        {{ t('settings.image_editor.save_crop') }}
                    </PrimaryButton>
                </template>
            </div>
        </div>
    </SettingsLayout>
</template>
