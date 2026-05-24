import { createI18n } from 'vue-i18n';
import en from './lang/en.json';
import de from './lang/de.json';

/**
 * Per-request locale catalog hand-off from Laravel.
 *
 * Inertia's first response already contains `translations` (the active
 * locale's JSON file rendered server-side) so the user never sees a
 * pre-translation flash. We seed every known locale's catalog at boot
 * so a runtime locale switch — done by Inertia reloading the page
 * after a Profile save — can route-swap without an extra fetch.
 *
 * The exported `setLocale()` helper is what UI code calls when a new
 * Inertia visit lands with a fresh `locale` prop; it keeps vue-i18n
 * and Laravel-side `App::getLocale()` aligned.
 */
const messages = { en, de };

export const i18n = createI18n({
    legacy: false,
    locale: 'en',
    fallbackLocale: 'en',
    messages,
    missingWarn: false,
    fallbackWarn: false,
});

export const setLocale = (locale) => {
    if (messages[locale]) {
        i18n.global.locale.value = locale;
    }
};
