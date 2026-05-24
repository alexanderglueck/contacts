import './bootstrap';

import { createInertiaApp, router } from '@inertiajs/vue3';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { i18n, setLocale } from './i18n';

const appName = import.meta.env.VITE_APP_NAME || 'Contacts';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        const page = pages[`./Pages/${name}.vue`];
        if (!page) {
            throw new Error(`Inertia page not found: ${name}`);
        }
        return page;
    },
    setup({ el, App, props, plugin }) {
        setLocale(props.initialPage.props.locale ?? 'en');

        // `success` fires before `navigate` in Inertia's visit lifecycle — by
        // hooking it (and not just `navigate`) we update vue-i18n's locale
        // BEFORE Vue patches the new props, so the freshly-rendered page is
        // already in the new language. `navigate` stays as a safety net for
        // pop-state / back-forward navigations that don't go through a
        // visit.
        const applyLocaleFromEvent = (event) => {
            const next = event.detail?.page?.props?.locale;
            if (next) setLocale(next);
        };
        router.on('success', applyLocaleFromEvent);
        router.on('navigate', applyLocaleFromEvent);

        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(i18n)
            .mount(el);
    },
    progress: {
        color: '#4F46E5',
    },
});
