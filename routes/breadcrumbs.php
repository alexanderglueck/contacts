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
 * Contact addresses
 */
Breadcrumbs::for('contact_addresses.index', function ($breadcrumbs, $contact) {
    $breadcrumbs->parent('contacts.show', $contact);
    $breadcrumbs->push('Adressen verwalten', route('contact_addresses.index', $contact->slug));
});

Breadcrumbs::for('contact_addresses.create', function ($breadcrumbs, $contact) {
    $breadcrumbs->parent('contact_addresses.index', $contact);
    $breadcrumbs->push('Adresse hinzufügen', route('contact_addresses.create', $contact->slug));
});

Breadcrumbs::for('contact_addresses.show', function ($breadcrumbs, $contact, $contactAddress) {
    $breadcrumbs->parent('contact_addresses.index', $contact);
    $breadcrumbs->push($contactAddress->name, route('contact_addresses.show', [$contact->slug, $contactAddress->slug]));
});

Breadcrumbs::for('contact_addresses.edit', function ($breadcrumbs, $contact, $contactAddress) {
    $breadcrumbs->parent('contact_addresses.show', $contact, $contactAddress);
    $breadcrumbs->push('Adresse bearbeiten', route('contact_addresses.edit', [$contact->slug, $contactAddress->slug]));
});

Breadcrumbs::for('contact_addresses.delete', function ($breadcrumbs, $contact, $contactAddress) {
    $breadcrumbs->parent('contact_addresses.show', $contact, $contactAddress);
    $breadcrumbs->push('Adresse löschen', route('contact_addresses.delete', [$contact->slug, $contactAddress->slug]));
});

/**
 * Contact dates
 */
Breadcrumbs::for('contact_dates.index', function ($breadcrumbs, $contact) {
    $breadcrumbs->parent('contacts.show', $contact);
    $breadcrumbs->push('Datumsangaben verwalten', route('contact_dates.index', $contact->slug));
});

Breadcrumbs::for('contact_dates.create', function ($breadcrumbs, $contact) {
    $breadcrumbs->parent('contact_dates.index', $contact);
    $breadcrumbs->push('Datum hinzufügen', route('contact_dates.create', $contact->slug));
});

Breadcrumbs::for('contact_dates.show', function ($breadcrumbs, $contact, $contactDate) {
    $breadcrumbs->parent('contact_dates.index', $contact);
    $breadcrumbs->push($contactDate->name, route('contact_dates.show', [$contact->slug, $contactDate->slug]));
});

Breadcrumbs::for('contact_dates.edit', function ($breadcrumbs, $contact, $contactDate) {
    $breadcrumbs->parent('contact_dates.show', $contact, $contactDate);
    $breadcrumbs->push('Datum bearbeiten', route('contact_dates.edit', [$contact->slug, $contactDate->slug]));
});

Breadcrumbs::for('contact_dates.delete', function ($breadcrumbs, $contact, $contactDate) {
    $breadcrumbs->parent('contact_dates.show', $contact, $contactDate);
    $breadcrumbs->push('Datum löschen', route('contact_dates.delete', [$contact->slug, $contactDate->slug]));
});

/**
 * Contact emails
 */
Breadcrumbs::for('contact_emails.index', function ($breadcrumbs, $contact) {
    $breadcrumbs->parent('contacts.show', $contact);
    $breadcrumbs->push('E-Mail Adressen verwalten', route('contact_emails.index', $contact->slug));
});

Breadcrumbs::for('contact_emails.create', function ($breadcrumbs, $contact) {
    $breadcrumbs->parent('contact_emails.index', $contact);
    $breadcrumbs->push('E-Mail Adresse hinzufügen', route('contact_emails.create', $contact->slug));
});

Breadcrumbs::for('contact_emails.show', function ($breadcrumbs, $contact, $contactEmail) {
    $breadcrumbs->parent('contact_emails.index', $contact);
    $breadcrumbs->push($contactEmail->name, route('contact_emails.show', [$contact->slug, $contactEmail->slug]));
});

Breadcrumbs::for('contact_emails.edit', function ($breadcrumbs, $contact, $contactEmail) {
    $breadcrumbs->parent('contact_emails.show', $contact, $contactEmail);
    $breadcrumbs->push('E-Mail Adresse bearbeiten', route('contact_emails.edit', [$contact->slug, $contactEmail->slug]));
});

Breadcrumbs::for('contact_emails.delete', function ($breadcrumbs, $contact, $contactEmail) {
    $breadcrumbs->parent('contact_emails.show', $contact, $contactEmail);
    $breadcrumbs->push('E-Mail Adresse löschen', route('contact_emails.delete', [$contact->slug, $contactEmail->slug]));
});

/**
 * Contact numbers
 */
Breadcrumbs::for('contact_numbers.index', function ($breadcrumbs, $contact) {
    $breadcrumbs->parent('contacts.show', $contact);
    $breadcrumbs->push('Nummern verwalten', route('contact_numbers.index', $contact->slug));
});

Breadcrumbs::for('contact_numbers.create', function ($breadcrumbs, $contact) {
    $breadcrumbs->parent('contact_numbers.index', $contact);
    $breadcrumbs->push('Nummer hinzufügen', route('contact_numbers.create', $contact->slug));
});

Breadcrumbs::for('contact_numbers.show', function ($breadcrumbs, $contact, $contactNumber) {
    $breadcrumbs->parent('contact_numbers.index', $contact);
    $breadcrumbs->push($contactNumber->name, route('contact_numbers.show', [$contact->slug, $contactNumber->slug]));
});

Breadcrumbs::for('contact_numbers.edit', function ($breadcrumbs, $contact, $contactNumber) {
    $breadcrumbs->parent('contact_numbers.show', $contact, $contactNumber);
    $breadcrumbs->push('Nummer bearbeiten', route('contact_numbers.edit', [$contact->slug, $contactNumber->slug]));
});

Breadcrumbs::for('contact_numbers.delete', function ($breadcrumbs, $contact, $contactNumber) {
    $breadcrumbs->parent('contact_numbers.show', $contact, $contactNumber);
    $breadcrumbs->push('Nummer löschen', route('contact_numbers.delete', [$contact->slug, $contactNumber->slug]));
});

/**
 * Contact urls
 */
Breadcrumbs::for('contact_urls.index', function ($breadcrumbs, $contact) {
    $breadcrumbs->parent('contacts.show', $contact);
    $breadcrumbs->push('Websiten verwalten', route('contact_urls.index', $contact->slug));
});

Breadcrumbs::for('contact_urls.create', function ($breadcrumbs, $contact) {
    $breadcrumbs->parent('contact_urls.index', $contact);
    $breadcrumbs->push('Website hinzufügen', route('contact_urls.create', $contact->slug));
});

Breadcrumbs::for('contact_urls.show', function ($breadcrumbs, $contact, $contactUrl) {
    $breadcrumbs->parent('contact_urls.index', $contact);
    $breadcrumbs->push($contactUrl->name, route('contact_urls.show', [$contact->slug, $contactUrl->slug]));
});

Breadcrumbs::for('contact_urls.edit', function ($breadcrumbs, $contact, $contactUrl) {
    $breadcrumbs->parent('contact_urls.show', $contact, $contactUrl);
    $breadcrumbs->push('Website bearbeiten', route('contact_urls.edit', [$contact->slug, $contactUrl->slug]));
});

Breadcrumbs::for('contact_urls.delete', function ($breadcrumbs, $contact, $contactUrl) {
    $breadcrumbs->parent('contact_urls.show', $contact, $contactUrl);
    $breadcrumbs->push('Website löschen', route('contact_urls.delete', [$contact->slug, $contactUrl->slug]));
});

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
