<?php

return [
    'installed' => env('CONTACTS_INSTALLED', false),
    'googleMapsKey' => env('GOOGLE_MAPS_GEOCODING_KEY', ''),
    'pagination' => [
        'default' => 20,
        'activities' => 20
    ],
    'tenant' => [
        'prefix' => env('CONTACTS_TENANT_PREFIX', 'contact_'),
        'system' => env('DB_DATABASE', 'contact_system')
    ],
    'signup_enabled' => env('CONTACTS_SIGNUP_ENABLED', true)
];
