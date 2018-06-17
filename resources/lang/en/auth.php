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

    'failed' => 'These credentials do not match our records.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',
    'login' => [
        'title' => 'Login',
        'fields' => [
            'email' => 'Email address',
            'password' => 'Password',
            'remember' => 'Remember me'
        ],
        'actions' => [
            'default' => 'Login',
            'password' => [
                'request' => 'Forgot password?'
            ],
            'activation' => [
                'resend' => 'Resend activation email'
            ]
        ]
    ],
    'register' => [
        'title' => 'Register',
        'fields' => [
            'name' => 'Name',
            'email' => 'Email address',
            'password' => 'Password',
            'password_confirm' => 'Confirm password',
            'terms_of_service' => 'I accept the <a target="_blank" href=":tos_url">terms of service</a>',
        ],
        'actions' => [
            'default' => 'Register',
        ]
    ]

];
