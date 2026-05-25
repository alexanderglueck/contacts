<script setup>
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';
import { useI18n } from 'vue-i18n';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const { t } = useI18n();

defineProps({
    open: { type: Boolean, required: true },
    title: { type: String, default: '' },
    body: { type: String, default: '' },
    confirmLabel: { type: String, default: '' },
    cancelLabel: { type: String, default: '' },
    // 'primary' (default) → indigo confirm button.
    // 'danger'           → red confirm button for destructive actions.
    variant: { type: String, default: 'primary' },
    busy: { type: Boolean, default: false },
});

const emit = defineEmits(['confirm', 'cancel']);
</script>

<template>
    <TransitionRoot as="template" :show="open">
        <Dialog as="div" class="relative z-50" @close="emit('cancel')">
            <TransitionChild
                as="template"
                enter="ease-out duration-200"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="ease-in duration-150"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div class="fixed inset-0 bg-gray-500/60 transition-opacity" />
            </TransitionChild>

            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <TransitionChild
                        as="template"
                        enter="ease-out duration-200"
                        enter-from="opacity-0 translate-y-2 sm:scale-95"
                        enter-to="opacity-100 translate-y-0 sm:scale-100"
                        leave="ease-in duration-150"
                        leave-from="opacity-100 translate-y-0 sm:scale-100"
                        leave-to="opacity-0 translate-y-2 sm:scale-95"
                    >
                        <DialogPanel class="w-full max-w-md bg-white rounded-lg shadow-xl">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <DialogTitle class="text-base font-semibold text-gray-900">
                                    <slot name="title">{{ title }}</slot>
                                </DialogTitle>
                            </div>

                            <div class="px-6 py-4 text-sm text-gray-700">
                                <slot>{{ body }}</slot>
                            </div>

                            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2 bg-gray-50 rounded-b-lg">
                                <SecondaryButton
                                    type="button"
                                    class="cursor-pointer"
                                    :disabled="busy"
                                    @click="emit('cancel')"
                                >
                                    {{ cancelLabel || t('common.cancel') }}
                                </SecondaryButton>
                                <DangerButton
                                    v-if="variant === 'danger'"
                                    type="button"
                                    class="cursor-pointer"
                                    :disabled="busy"
                                    @click="emit('confirm')"
                                >
                                    {{ confirmLabel || t('common.confirm') }}
                                </DangerButton>
                                <PrimaryButton
                                    v-else
                                    type="button"
                                    class="cursor-pointer"
                                    :disabled="busy"
                                    @click="emit('confirm')"
                                >
                                    {{ confirmLabel || t('common.confirm') }}
                                </PrimaryButton>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>
