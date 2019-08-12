<?php

return [
    'installed' => env('CONTACTS_INSTALLED', false),
    'googleMapsKey' => env('GOOGLE_MAPS_GEOCODING_KEY', ''),
    'pagination' => [
        'default' => 20,
        'activities' => 20
    ],
    'signup_enabled' => env('CONTACTS_SIGNUP_ENABLED', true)
];
