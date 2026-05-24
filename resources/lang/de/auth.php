<?php

return [
    'failed' => 'Diese Zugangsdaten stimmen nicht mit unseren Aufzeichnungen überein.',
    'password' => 'Das angegebene Passwort ist falsch.',
    'throttle' => 'Zu viele Anmeldeversuche. Bitte versuchen Sie es in :seconds Sekunden erneut.',
    'login' => [
        'title' => 'Anmelden',
        'fields' => [
            'email' => 'E-Mail-Adresse',
            'password' => 'Passwort',
            'remember' => 'Angemeldet bleiben',
        ],
        'actions' => [
            'default' => 'Anmelden',
            'password' => [
                'request' => 'Passwort vergessen?',
            ],
            'activation' => [
                'resend' => 'Aktivierungs-E-Mail erneut senden',
            ],
        ],
    ],
    'register' => [
        'title' => 'Registrieren',
        'fields' => [
            'name' => 'Name',
            'email' => 'E-Mail-Adresse',
            'password' => 'Passwort',
            'password_confirm' => 'Passwort bestätigen',
            'terms_of_service' => 'Ich akzeptiere die <a target="_blank" href=":tos_url">Nutzungsbedingungen</a>',
        ],
        'actions' => [
            'default' => 'Registrieren',
        ],
    ],
];
