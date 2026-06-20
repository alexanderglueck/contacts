<?php

use Laravel\Jetstream\Features;

return [

    /*
    |--------------------------------------------------------------------------
    | Jetstream Stack
    |--------------------------------------------------------------------------
    |
    | We adopt Jetstream purely for its team model layer (HasTeams trait +
    | Team/Membership models) — NOT its scaffolding. The stack is deliberately
    | left as null so Jetstream's JetstreamServiceProvider does NOT run its
    | bootInertia()/bootLivewire() routines, which would otherwise hijack the
    | Fortify auth views (login/register/etc.) and append ShareInertiaData
    | middleware. This app already owns its auth UI and Inertia setup.
    |
    | Jetstream route registration is additionally disabled via
    | Jetstream::ignoreRoutes() in AppServiceProvider — the app provides its
    | own team routes/controllers/Vue pages.
    |
    */

    'stack' => null,

    'middleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Jetstream Features
    |--------------------------------------------------------------------------
    |
    | Intentionally empty. Team membership works through the HasTeams trait
    | regardless of this flag; we don't use Jetstream's team routes, policies,
    | invitations, profile photos, API tokens or account-deletion features —
    | the app has its own equivalents.
    |
    */

    'features' => [],

];
