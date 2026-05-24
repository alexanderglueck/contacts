<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Textarea from '@/Components/Textarea.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    contact: { type: Object, required: true },
    comment: { type: Object, required: true },
});

const editForm = useForm({
    comment: props.comment.comment ?? '',
});

const deleteForm = useForm({});

const submit = () => editForm.put(route('comments.update', [props.contact.ulid, props.comment.ulid]));

const remove = () => deleteForm.delete(route('comments.destroy', [props.contact.ulid, props.comment.ulid]));
</script>

<template>
    <AppLayout title="Edit comment">
        <Head title="Edit comment" />

        <div class="text-sm">
            <Link :href="route('contacts.show', contact.ulid)" class="text-indigo-600 hover:text-indigo-500">
                &larr; Back to {{ contact.fullname }}
            </Link>
        </div>

        <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Edit comment</h2>
            </div>

            <div class="px-6 py-4 space-y-4">
                <div>
                    <InputLabel for="comment" value="Comment *" />
                    <Textarea id="comment" v-model="editForm.comment" rows="6" required />
                    <p class="mt-1 text-xs text-gray-500">
                        Markdown supported: **bold**, *italic*, [link](url), `code`, lists, &gt; quotes.
                    </p>
                    <InputError :message="editForm.errors.comment" />
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
                <Link :href="route('contacts.show', contact.ulid)">
                    <SecondaryButton type="button">Cancel</SecondaryButton>
                </Link>
                <PrimaryButton :disabled="editForm.processing" :class="{ 'opacity-50': editForm.processing }">
                    Save comment
                </PrimaryButton>
            </div>
        </form>

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Delete comment</h2>
            </div>

            <div class="px-6 py-4 text-sm text-gray-700">
                <p>This will permanently remove the comment.</p>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                <DangerButton
                    type="button"
                    @click="remove"
                    :disabled="deleteForm.processing"
                    :class="{ 'opacity-50': deleteForm.processing }"
                >
                    Delete comment
                </DangerButton>
            </div>
        </div>
    </AppLayout>
</template>
