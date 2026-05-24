<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PageJumper from '@/Components/PageJumper.vue';

const { t } = useI18n();

const props = defineProps({
    contacts: { type: Object, required: true },
    q: { type: String, default: '' },
    canCreate: { type: Boolean, default: false },
});

const search = ref(props.q);
let debounce = null;

// Live debounced search — typing rewrites the URL with ?q=... so the
// back button gives a sensible history.
watch(search, (value) => {
    clearTimeout(debounce);
    debounce = setTimeout(() => {
        router.get(
            route('contacts.index'),
            value.trim() === '' ? {} : { q: value.trim() },
            { preserveState: true, preserveScroll: true, replace: true, only: ['contacts', 'q'] },
        );
    }, 300);
});

const clearSearch = () => { search.value = ''; };
</script>

<template>
    <AppLayout :title="t('contacts.title')">
        <Head :title="t('contacts.title')" />

        <div class="bg-white shadow rounded-lg">
            <div class="border-b border-gray-200 px-6 py-4 flex flex-col sm:flex-row sm:items-center gap-3 justify-between">
                <h2 class="text-lg font-medium text-gray-900">{{ t('contacts.title') }}</h2>

                <div class="flex items-center gap-2 flex-1 sm:max-w-sm sm:ms-6">
                    <div class="relative flex-1">
                        <TextInput
                            id="search"
                            type="search"
                            v-model="search"
                            :placeholder="t('contacts.search_placeholder')"
                            autocomplete="off"
                            class="pe-9"
                        />
                        <button
                            v-if="search"
                            type="button"
                            class="absolute inset-y-0 end-0 flex items-center pe-2 text-gray-400 hover:text-gray-600 cursor-pointer"
                            @click="clearSearch"
                            :aria-label="t('contacts.clear_search')"
                        >
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>

                    <Link :href="route('import.index')">
                        <SecondaryButton type="button">{{ t('contacts.import') }}</SecondaryButton>
                    </Link>
                    <Link :href="route('export.index')">
                        <SecondaryButton type="button">{{ t('contacts.export') }}</SecondaryButton>
                    </Link>
                    <Link v-if="canCreate" :href="route('contacts.create')">
                        <PrimaryButton type="button">{{ t('contacts.create_short') }}</PrimaryButton>
                    </Link>
                </div>
            </div>

            <div v-if="contacts.data.length === 0" class="px-6 py-8 text-center text-sm text-gray-500">
                <template v-if="q">{{ t('contacts.no_results_for', { q }) }}</template>
                <template v-else>{{ t('contacts.no_contacts') }}</template>
            </div>

            <ul v-else class="divide-y divide-gray-200">
                <li v-for="contact in contacts.data" :key="contact.id">
                    <Link
                        :href="route('contacts.show', contact.ulid)"
                        class="block px-6 py-3 hover:bg-gray-50 text-sm text-gray-900"
                    >
                        {{ contact.fullname }}
                    </Link>
                </li>
            </ul>

            <div v-if="contacts.total > 0" class="border-t border-gray-200 px-6 py-3 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                <p class="text-sm text-gray-600">
                    {{ t(contacts.total === 1 ? 'contacts.showing_one' : 'contacts.showing_many', {
                        from: contacts.from,
                        to: contacts.to,
                        total: contacts.total,
                    }) }}
                </p>
                <div v-if="contacts.last_page > 1" class="flex flex-wrap items-center gap-2">
                    <PageJumper :current-page="contacts.current_page" :last-page="contacts.last_page" />
                    <div class="flex gap-1">
                    <template v-for="link in contacts.links" :key="link.label">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            class="inline-flex items-center px-3 py-1 rounded text-sm"
                            :class="link.active
                                ? 'bg-indigo-600 text-white'
                                : 'bg-white text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50'"
                            v-html="link.label"
                        />
                        <span
                            v-else
                            class="inline-flex items-center px-3 py-1 rounded text-sm text-gray-400"
                            v-html="link.label"
                        />
                    </template>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
