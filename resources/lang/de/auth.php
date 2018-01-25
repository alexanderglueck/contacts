<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'Zu den angegeben Zugangsdaten konnte kein Eintrag gefunden werden.',
    'throttle' => 'Zu viele Anmeldeversuche. Bitte versuchen Sie es in :seconds Sekunden erneut.',
    'login' => [
        'title' => 'Login',
        'fields' => [
            'email' => 'E-Mail Adresse',
            'password' => 'Passwort',
            'remember' => 'Angemeldet bleiben'
        ],
        'actions' => [
            'default' => 'Login',
            'password' => [
                'reset' => 'Passwort vergessen?'
            ]
        ]
    ]

];
