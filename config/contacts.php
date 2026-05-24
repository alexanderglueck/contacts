<?php

return [
    'installed' => env('CONTACTS_INSTALLED', false),
    'pagination' => [
        'default' => 20,
        'activities' => 20
    ],
    'signup_enabled' => env('CONTACTS_SIGNUP_ENABLED', true),

    // ISO 3166-1 alpha-2 region code used to disambiguate phone numbers
    // submitted in national format (no country prefix). Numbers already in
    // international format (e.g. "+43 …") ignore this. Per-user override
    // is a future enhancement once we have multi-region users.
    'phone_default_region' => env('CONTACTS_PHONE_DEFAULT_REGION', 'AT'),
];
