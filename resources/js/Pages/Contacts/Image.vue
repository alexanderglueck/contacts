<script setup>
import { nextTick, onBeforeUnmount, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import Cropper from 'cropperjs';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputError from '@/Components/InputError.vue';

const { t } = useI18n();

const props = defineProps({
    contact: { type: Object, required: true },
});

const OUTPUT_SIZE = 400;

// cropper.js v2 is web-component based and takes a markup template rather than
// the v1 flat options object. A square (1:1), full-coverage selection over a
// contained image reproduces the old aspectRatio:1 / viewMode:1 / autoCropArea:1.
const CROPPER_TEMPLATE = `
<cropper-canvas background style="height: 60vh;">
    <cropper-image rotatable scalable translatable></cropper-image>
    <cropper-shade hidden></cropper-shade>
    <cropper-handle action="move" plain></cropper-handle>
    <cropper-selection aspect-ratio="1" initial-coverage="1" movable resizable zoomable outlined>
        <cropper-grid role="grid" covered></cropper-grid>
        <cropper-crosshair centered></cropper-crosshair>
        <cropper-handle action="move" theme-color="rgba(255,255,255,0.35)"></cropper-handle>
        <cropper-handle action="n-resize"></cropper-handle>
        <cropper-handle action="e-resize"></cropper-handle>
        <cropper-handle action="s-resize"></cropper-handle>
        <cropper-handle action="w-resize"></cropper-handle>
        <cropper-handle action="ne-resize"></cropper-handle>
        <cropper-handle action="nw-resize"></cropper-handle>
        <cropper-handle action="se-resize"></cropper-handle>
        <cropper-handle action="sw-resize"></cropper-handle>
    </cropper-selection>
</cropper-canvas>
`;

const currentImage = ref(props.contact.image ? `/storage/${props.contact.image}` : null);
const sourceImage = ref(null);
const imgEl = ref(null);
const fileInputEl = ref(null);
let cropper = null;

const form = useForm({
    file: null,
});

const initCropper = async () => {
    await nextTick();
    destroyCropper();
    if (! imgEl.value) return;
    cropper = new Cropper(imgEl.value, { template: CROPPER_TEMPLATE });

    // Fit the image inside the canvas once it has loaded, then centre the
    // square selection over it.
    const image = cropper.getCropperImage();
    if (image) {
        await image.$ready();
        image.$center('contain');
        cropper.getCropperSelection()?.$center();
    }
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
    form.file = null;
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

const submit = async () => {
    const selection = cropper?.getCropperSelection();
    if (! selection) return;

    const canvas = await selection.$toCanvas({ width: OUTPUT_SIZE, height: OUTPUT_SIZE });

    canvas.toBlob((blob) => {
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
    <AppLayout :title="t('settings.image_editor.page_title_contact', { name: contact.fullname })">
        <Head :title="t('settings.image_editor.page_title_contact', { name: contact.fullname })" />

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">{{ t('settings.image_editor.section_heading_contact') }}</h2>
            </div>

            <div class="px-6 py-4 space-y-4">
                <div v-if="!sourceImage" class="space-y-4">
                    <div v-if="currentImage" class="flex justify-center">
                        <img :src="currentImage" :alt="contact.fullname" class="w-48 h-48 object-cover rounded-lg" />
                    </div>

                    <input
                        ref="fileInputEl"
                        type="file"
                        accept="image/jpeg,image/png"
                        class="hidden"
                        @change="onFileChange"
                    />
                    <InputError :message="form.errors.file" />
                </div>

                <div v-else class="space-y-3">
                    <p class="text-sm text-gray-600">
                        {{ t('settings.image_editor.crop_help') }}
                    </p>
                    <div class="max-h-[60vh] overflow-hidden">
                        <img ref="imgEl" :src="sourceImage" :alt="t('settings.image_editor.crop_preview_alt')" class="block max-w-full" />
                    </div>
                    <InputError :message="form.errors.file" />
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex flex-wrap justify-end gap-2">
                <template v-if="!sourceImage">
                    <Link :href="route('contacts.show', contact.ulid)">
                        <SecondaryButton type="button">{{ t('common.back') }}</SecondaryButton>
                    </Link>
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
    </AppLayout>
</template>
