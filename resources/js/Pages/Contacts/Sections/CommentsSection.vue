<script setup>
import { ref, computed, watch } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import SlideOver from '@/Components/SlideOver.vue';
import Textarea from '@/Components/Textarea.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    open: { type: Boolean, required: true },
    contact: { type: Object, required: true },
    items: { type: Array, default: () => [] },
    can: { type: Object, default: () => ({}) },
});

const emit = defineEmits(['close']);
const { t } = useI18n();

// 'list' | 'create' | 'edit' | 'delete'
// (no 'show' — the list IS the detail view for comments)
const mode = ref('list');
const selected = ref(null);

const createForm = useForm({ comment: '', parent_ulid: null });
const editForm = useForm({ comment: '' });
const deleteForm = useForm({});

watch(
    () => props.open,
    (isOpen) => {
        if (isOpen) {
            mode.value = 'list';
            selected.value = null;
            createForm.reset();
            createForm.clearErrors();
            editForm.reset();
            editForm.clearErrors();
        }
    },
);

const title = computed(() => {
    switch (mode.value) {
        case 'create':
            return selected.value
                ? t('contacts.slideover.reply_to', { name: selected.value.owner?.name ?? '' })
                : t('contacts.slideover.add_comment');
        case 'edit':
            return `${t('contacts.slideover.edit')} ${t('contacts.fields.note').toLowerCase()}`;
        case 'delete':
            return `${t('contacts.slideover.delete')} ${t('contacts.section.comments').toLowerCase()}?`;
        default:
            return t('contacts.section.comments');
    }
});

const backToList = () => {
    mode.value = 'list';
    selected.value = null;
    createForm.reset();
    createForm.clearErrors();
    editForm.reset();
    editForm.clearErrors();
};

const handleHeaderClose = () => {
    switch (mode.value) {
        case 'create':
        case 'edit':
        case 'delete':
            backToList();
            break;
        case 'list':
        default:
            emit('close');
    }
};

const openCreate = () => {
    selected.value = null;
    createForm.reset();
    createForm.clearErrors();
    mode.value = 'create';
};

const openReply = (target) => {
    // Threads stay one level deep visually. When you reply to a reply, the
    // new comment is still data-attached to the original root (the reply's
    // own parent), so it shows up alongside its siblings — no third-level
    // indentation. The "Replying to X" badge still names whoever you
    // actually clicked, which is the conversational signal the user wants.
    const rootParentUlid = target.parent_ulid ?? target.ulid;
    selected.value = target;
    createForm.reset();
    createForm.clearErrors();
    createForm.parent_ulid = rootParentUlid;
    mode.value = 'create';
};

const openEdit = (comment) => {
    selected.value = comment;
    editForm.comment = comment.comment;
    editForm.clearErrors();
    mode.value = 'edit';
};

const openDelete = (comment) => {
    selected.value = comment;
    mode.value = 'delete';
};

// Threaded tree, one level deep (matches the data model — no nested replies).
const tree = computed(() => {
    const byParent = new Map();
    const roots = [];
    for (const c of props.items) {
        if (c.parent_ulid) {
            if (!byParent.has(c.parent_ulid)) byParent.set(c.parent_ulid, []);
            byParent.get(c.parent_ulid).push(c);
        } else {
            roots.push(c);
        }
    }
    return { byParent, roots };
});

const repliesOf = (ulid) => tree.value.byParent.get(ulid) ?? [];

const refresh = () => router.reload({ only: ['comments', 'contact'] });

const submitCreate = () =>
    createForm.post(route('comments.store', props.contact.ulid), {
        preserveScroll: true,
        onSuccess: () => { refresh(); backToList(); },
    });

const submitEdit = () =>
    editForm.put(route('comments.update', [props.contact.ulid, selected.value.ulid]), {
        preserveScroll: true,
        onSuccess: () => { refresh(); backToList(); },
    });

const submitDelete = () =>
    deleteForm.delete(route('comments.destroy', [props.contact.ulid, selected.value.ulid]), {
        preserveScroll: true,
        onSuccess: () => { refresh(); backToList(); },
    });
</script>

<template>
    <SlideOver :open="open" :title="title" @close="handleHeaderClose">
        <!-- List -->
        <template v-if="mode === 'list'">
            <div v-if="tree.roots.length === 0" class="text-sm text-gray-500 text-center py-6">
                {{ t('contacts.slideover.empty_comments') }}
            </div>

            <ul v-else class="space-y-4">
                <li v-for="comment in tree.roots" :key="comment.ulid">
                    <article class="text-sm">
                        <template v-if="comment.is_deleted">
                            <div class="italic text-gray-400">{{ t('contacts.slideover.comment_fields.deleted') }}</div>
                        </template>
                        <template v-else>
                            <header class="flex items-baseline justify-between gap-2 mb-1">
                                <span class="font-medium text-gray-900">{{ comment.owner?.name ?? 'Unknown' }}</span>
                                <span class="text-xs text-gray-500">{{ comment.created_at }}</span>
                            </header>

                            <div class="prose-note text-gray-800" v-html="comment.comment_html" />
                        </template>

                        <footer
                            v-if="!comment.is_deleted && (can.create || (comment.is_mine && (can.edit || can.delete)))"
                            class="mt-1 flex gap-3 text-xs"
                        >
                            <button
                                v-if="can.create"
                                type="button"
                                class="text-indigo-600 hover:text-indigo-500 cursor-pointer"
                                @click="openReply(comment)"
                            >
                                {{ t('contacts.slideover.comment_fields.reply') }}
                            </button>
                            <button
                                v-if="comment.is_mine && can.edit"
                                type="button"
                                class="text-gray-600 hover:text-gray-900 cursor-pointer"
                                @click="openEdit(comment)"
                            >
                                {{ t('common.edit') }}
                            </button>
                            <button
                                v-if="comment.is_mine && can.delete"
                                type="button"
                                class="text-red-600 hover:text-red-500 cursor-pointer"
                                @click="openDelete(comment)"
                            >
                                {{ t('common.delete') }}
                            </button>
                        </footer>
                    </article>

                    <!-- Replies, one level of indentation -->
                    <ul
                        v-if="repliesOf(comment.ulid).length"
                        class="mt-3 ms-6 space-y-3 border-l-2 border-gray-200 ps-4"
                    >
                        <li v-for="reply in repliesOf(comment.ulid)" :key="reply.ulid">
                            <article class="text-sm">
                                <template v-if="reply.is_deleted">
                                    <div class="italic text-gray-400">{{ t('contacts.slideover.comment_fields.deleted') }}</div>
                                </template>
                                <template v-else>
                                    <header class="flex items-baseline justify-between gap-2 mb-1">
                                        <span class="font-medium text-gray-900">{{ reply.owner?.name ?? 'Unknown' }}</span>
                                        <span class="text-xs text-gray-500">{{ reply.created_at }}</span>
                                    </header>

                                    <div class="prose-note text-gray-800" v-html="reply.comment_html" />
                                </template>

                                <footer
                                    v-if="!reply.is_deleted && (can.create || (reply.is_mine && (can.edit || can.delete)))"
                                    class="mt-1 flex gap-3 text-xs"
                                >
                                    <button
                                        v-if="can.create"
                                        type="button"
                                        class="text-indigo-600 hover:text-indigo-500 cursor-pointer"
                                        @click="openReply(reply)"
                                    >
                                        {{ t('contacts.slideover.comment_fields.reply') }}
                                    </button>
                                    <button
                                        v-if="reply.is_mine && can.edit"
                                        type="button"
                                        class="text-gray-600 hover:text-gray-900 cursor-pointer"
                                        @click="openEdit(reply)"
                                    >
                                        {{ t('common.edit') }}
                                    </button>
                                    <button
                                        v-if="reply.is_mine && can.delete"
                                        type="button"
                                        class="text-red-600 hover:text-red-500 cursor-pointer"
                                        @click="openDelete(reply)"
                                    >
                                        {{ t('common.delete') }}
                                    </button>
                                </footer>
                            </article>
                        </li>
                    </ul>
                </li>
            </ul>
        </template>

        <!-- Create / Reply -->
        <form
            v-else-if="mode === 'create'"
            id="comment-create-form"
            @submit.prevent="submitCreate"
            class="space-y-3"
        >
            <div v-if="selected" class="rounded-md bg-gray-50 ring-1 ring-gray-200 px-3 py-2 text-xs text-gray-600">
                <span class="font-medium text-gray-700">{{ t('contacts.slideover.reply_to', { name: selected.owner?.name ?? '' }) }}:</span>
                <div class="mt-1 prose-note text-gray-700 line-clamp-3" v-html="selected.comment_html" />
            </div>
            <Textarea v-model="createForm.comment" :rows="4" autofocus :placeholder="t('contacts.slideover.comment_fields.placeholder')" />
            <p class="text-xs text-gray-500">
                {{ t('contacts.markdown_hint') }}
            </p>
            <InputError :message="createForm.errors.comment" />
        </form>

        <!-- Edit -->
        <form
            v-else-if="mode === 'edit'"
            id="comment-edit-form"
            @submit.prevent="submitEdit"
            class="space-y-3"
        >
            <Textarea v-model="editForm.comment" :rows="4" autofocus />
            <p class="text-xs text-gray-500">
                {{ t('contacts.markdown_hint') }}
            </p>
            <InputError :message="editForm.errors.comment" />
        </form>

        <!-- Delete -->
        <template v-else-if="mode === 'delete'">
            <div class="rounded-md bg-gray-50 ring-1 ring-gray-200 px-3 py-2 text-xs text-gray-600 mb-3">
                <span class="font-medium text-gray-700">{{ selected.owner?.name ?? 'Unknown' }}</span>
                <div class="mt-1 prose-note text-gray-700 line-clamp-4" v-html="selected.comment_html" />
            </div>
            <p class="text-sm text-gray-700">
                {{ t('contacts.slideover.remove_comment_q') }}
            </p>
        </template>

        <template #footer>
            <template v-if="mode === 'list'">
                <SecondaryButton type="button" @click="emit('close')">{{ t('contacts.slideover.close') }}</SecondaryButton>
                <PrimaryButton v-if="can.create" type="button" @click="openCreate">
                    {{ t('contacts.slideover.add_comment') }}
                </PrimaryButton>
            </template>

            <template v-else-if="mode === 'create'">
                <SecondaryButton type="button" @click="backToList">{{ t('contacts.slideover.cancel') }}</SecondaryButton>
                <PrimaryButton
                    type="submit"
                    form="comment-create-form"
                    :disabled="createForm.processing || !createForm.comment.trim()"
                    :class="{ 'opacity-50': createForm.processing || !createForm.comment.trim() }"
                >
                    {{ selected ? t('contacts.slideover.comment_fields.reply') : t('contacts.slideover.create') }}
                </PrimaryButton>
            </template>

            <template v-else-if="mode === 'edit'">
                <SecondaryButton type="button" @click="backToList">{{ t('contacts.slideover.cancel') }}</SecondaryButton>
                <PrimaryButton
                    type="submit"
                    form="comment-edit-form"
                    :disabled="editForm.processing || !editForm.comment.trim()"
                    :class="{ 'opacity-50': editForm.processing || !editForm.comment.trim() }"
                >
                    {{ t('contacts.slideover.save') }}
                </PrimaryButton>
            </template>

            <template v-else-if="mode === 'delete'">
                <SecondaryButton type="button" @click="backToList">{{ t('contacts.slideover.cancel') }}</SecondaryButton>
                <DangerButton
                    type="button"
                    :disabled="deleteForm.processing"
                    :class="{ 'opacity-50': deleteForm.processing }"
                    @click="submitDelete"
                >
                    {{ t('contacts.slideover.delete') }}
                </DangerButton>
            </template>
        </template>
    </SlideOver>
</template>
