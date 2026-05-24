<?php

/**
 * Home
 */

/**
 * Home
 */

use Diglactic\Breadcrumbs\Breadcrumbs;

Breadcrumbs::for('welcome', function ($breadcrumbs) {
    $breadcrumbs->push('Home', route('welcome'));
});

Breadcrumbs::for('home', function ($breadcrumbs) {
    $breadcrumbs->push('Home', route('home'));
});

/**
 * Auth: Login
 */
Breadcrumbs::for('login', function ($breadcrumbs) {
    $breadcrumbs->parent('welcome');
    $breadcrumbs->push('Login', route('login'));
});

Breadcrumbs::for('register', function ($breadcrumbs) {
    $breadcrumbs->parent('welcome');
    $breadcrumbs->push('Register', route('register'));
});

/**
 * Auth: Password reset
 */
Breadcrumbs::for('password.request', function ($breadcrumbs) {
});
Breadcrumbs::for('password.reset', function ($breadcrumbs) {
});

/**
 * Calendar
 */
Breadcrumbs::for('calendar.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Kalender', route('calendar.index'));
});

/**
 * Map
 */
Breadcrumbs::for('map.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Kontaktlandkarte', route('map.index'));
});

/**
 * Contact groups
 */
Breadcrumbs::for('contact_groups.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Kontaktgruppen', route('contact_groups.index'));
});

Breadcrumbs::for('contact_groups.create', function ($breadcrumbs) {
    $breadcrumbs->parent('contact_groups.index');
    $breadcrumbs->push('Kontaktgruppe hinzufügen', route('contact_groups.create'));
});

Breadcrumbs::for('contact_groups.show', function ($breadcrumbs, $contactGroups) {
    $breadcrumbs->parent('contact_groups.index');
    $breadcrumbs->push($contactGroups->name, route('contact_groups.show', $contactGroups->slug));
});

Breadcrumbs::for('contact_groups.edit', function ($breadcrumbs, $contactGroup) {
    $breadcrumbs->parent('contact_groups.show', $contactGroup);
    $breadcrumbs->push('Kontaktgruppe bearbeiten', route('contact_groups.edit', $contactGroup->slug));
});

Breadcrumbs::for('contact_groups.delete', function ($breadcrumbs, $contactGroup) {
    $breadcrumbs->parent('contact_groups.show', $contactGroup);
    $breadcrumbs->push('Kontaktgruppe löschen', route('contact_groups.delete', $contactGroup->slug));
});

/**
 * Contacts
 */
Breadcrumbs::for('contacts.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Kontakte', route('contacts.index'));
});

Breadcrumbs::for('contacts.create', function ($breadcrumbs) {
    $breadcrumbs->parent('contacts.index');
    $breadcrumbs->push('Kontakt hinzufügen', route('contacts.create'));
});

Breadcrumbs::for('contacts.show', function ($breadcrumbs, $contact) {
    $breadcrumbs->parent('contacts.index');
    $breadcrumbs->push($contact->fullname, route('contacts.show', $contact->slug));
});

Breadcrumbs::for('contacts.edit', function ($breadcrumbs, $contact) {
    $breadcrumbs->parent('contacts.show', $contact);
    $breadcrumbs->push('Kontakt bearbeiten', route('contacts.edit', $contact->slug));
});

Breadcrumbs::for('contacts.delete', function ($breadcrumbs, $contact) {
    $breadcrumbs->parent('contacts.show', $contact);
    $breadcrumbs->push('Kontakt löschen', route('contacts.delete', $contact->slug));
});

Breadcrumbs::for('contacts.image', function ($breadcrumbs, $contact) {
    $breadcrumbs->parent('contacts.show', $contact);
    $breadcrumbs->push('Kontaktbild bearbeiten', route('contacts.image', $contact->slug));
});

/**
 * Contact sub-resources (addresses, dates, emails, numbers, urls, notes,
 * calls, gift ideas) are now managed inline via the Contacts/Show slideover,
 * so they no longer need their own breadcrumb chains.
 */

/**
 * Contact export
 */
Breadcrumbs::for('export.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Kontakte exportieren', route('export.index'));
});

/**
 * Contact import
 */
Breadcrumbs::for('import.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Kontakte importieren', route('import.index'));
});

/**
 * User settings
 */
Breadcrumbs::for('user_settings.edit', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Einstellungen', route('user_settings.edit'));
});

/**
 * Auth settings
 */
Breadcrumbs::for('auth_settings.edit', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('2FA Einstellungen', route('auth_settings.edit'));
});

/**
 * Search
 */
Breadcrumbs::for('search.search', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Suche', route('search.search'));
});

/**
 * Reports
 */
Breadcrumbs::for('reports.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Berichte', route('reports.index'));
});
Breadcrumbs::for('reports.inactive', function ($breadcrumbs) {
    $breadcrumbs->parent('reports.index');
    $breadcrumbs->push('Inaktive Kontakte', route('reports.inactive'));
});
Breadcrumbs::for('reports.male', function ($breadcrumbs) {
    $breadcrumbs->parent('reports.index');
    $breadcrumbs->push('Männliche Kontakte', route('reports.male'));
});
Breadcrumbs::for('reports.female', function ($breadcrumbs) {
    $breadcrumbs->parent('reports.index');
    $breadcrumbs->push('Weibliche Kontakte', route('reports.female'));
});
Breadcrumbs::for('reports.wrong_male', function ($breadcrumbs) {
    $breadcrumbs->parent('reports.index');
    $breadcrumbs->push('Falsche männliche Kontakte', route('reports.wrong_male'));
});
Breadcrumbs::for('reports.wrong_female', function ($breadcrumbs) {
    $breadcrumbs->parent('reports.index');
    $breadcrumbs->push('Falsche weibliche Kontakte', route('reports.wrong_female'));
});
Breadcrumbs::for('reports.no_email', function ($breadcrumbs) {
    $breadcrumbs->parent('reports.index');
    $breadcrumbs->push('Keine E-Mail Adresse', route('reports.no_email'));
});
Breadcrumbs::for('reports.no_date', function ($breadcrumbs) {
    $breadcrumbs->parent('reports.index');
    $breadcrumbs->push('Keine Datumsangabe', route('reports.no_date'));
});
Breadcrumbs::for('reports.no_address', function ($breadcrumbs) {
    $breadcrumbs->parent('reports.index');
    $breadcrumbs->push('Keine Adresse', route('reports.no_address'));
});
Breadcrumbs::for('reports.no_number', function ($breadcrumbs) {
    $breadcrumbs->parent('reports.index');
    $breadcrumbs->push('Keine Nummer', route('reports.no_number'));
});
Breadcrumbs::for('reports.no_url', function ($breadcrumbs) {
    $breadcrumbs->parent('reports.index');
    $breadcrumbs->push('Keine Website', route('reports.no_url'));
});
Breadcrumbs::for('reports.no_lat_lng', function ($breadcrumbs) {
    $breadcrumbs->parent('reports.index');
    $breadcrumbs->push('Keine Koordinaten', route('reports.no_lat_lng'));
});
