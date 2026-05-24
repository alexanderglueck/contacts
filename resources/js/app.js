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

        router.on('navigate', (event) => {
            const next = event.detail.page.props?.locale;
            if (next) setLocale(next);
        });

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
