<?php

use App\Http\Controllers\Account\ApiTokenController;
use App\Http\Controllers\Account\DeactivateController;
use App\Http\Controllers\Account\DeleteController;
use App\Http\Controllers\Account\ProfileController;
use App\Http\Controllers\Account\ProfileImageController;
use App\Http\Controllers\Account\SessionsController;
use App\Http\Controllers\Account\Subscription\SubscriptionCancelController;
use App\Http\Controllers\Account\Subscription\SubscriptionCardController;
use App\Http\Controllers\Account\Subscription\SubscriptionInvoiceController;
use App\Http\Controllers\Account\Subscription\SubscriptionResumeController;
use App\Http\Controllers\Account\Subscription\SubscriptionSwapController;
use App\Http\Controllers\ICalController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Setup\InstallController;
use App\Http\Controllers\Subscription\PlanController;
use App\Http\Controllers\Subscription\SubscriptionController;
use App\Http\Controllers\TenantSelectionController;
use App\Http\Controllers\Webhooks\StripeWebhookController;
use App\Http\Controllers\WebsocketTestController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

// Login, registration, password reset, email verification, two-factor challenge,
// and password confirmation routes are all registered by Laravel Fortify.
// Passkey login/registration routes are registered by laravel/passkeys.

/**
 * Install
 */
Route::get('install', [InstallController::class, 'index'])->name('install');
Route::post('install', [InstallController::class, 'store'])->name('install.store');

/**
 * Plans
 */
Route::group(['as' => 'plans.'], function () {
    Route::get('plans/', [PlanController::class, 'index'])->name('index');
});

/**
 * Subscription
 */
Route::group(['as' => 'subscription.', 'middleware' => ['auth.register', 'subscription.inactive']], function () {
    Route::get('subscription', [SubscriptionController::class, 'index'])->name('index');
    Route::post('subscription', [SubscriptionController::class, 'store'])->name('store');
});

/**
 * Stripe webhook
 */
Route::post('/webhooks/stripe', [StripeWebhookController::class, 'handleWebhook']);

/**
 * Tenant selection dashboard
 */
Route::group(['middleware' => ['auth']], function () {
    Route::get('select', [TenantSelectionController::class, 'index'])->name('tenant.index');
    Route::post('select', [TenantSelectionController::class, 'store'])->name('tenant.store');
});

/**
 * Pages
 */
Route::get('pages/terms-of-service', [PageController::class, 'termsOfService'])->name('pages.terms_of_service');

/**
 * User Settings
 */
Route::group(['namespace' => 'Account', 'as' => 'user_settings.', 'prefix' => 'settings'], function () {
    /**
     * Profile
     */
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

    // Form-host pages for Fortify-managed flows. The pages POST/PUT to Fortify
    // endpoints (PUT /user/password, POST /user/two-factor-authentication, etc.).
    Route::middleware('auth')->get('password', fn () => \Inertia\Inertia::render('UserSettings/Password'))
        ->name('password.show');

    Route::middleware(['auth', 'password.confirm'])->get('two-factor', function () {
        $user = auth()->user();
        $enabled = (bool) $user->two_factor_secret;
        $confirmed = $user->two_factor_confirmed_at !== null;

        return \Inertia\Inertia::render('UserSettings/TwoFactor', [
            'twoFactorEnabled' => $enabled,
            'twoFactorConfirmed' => $confirmed,
            'qrSvg' => $enabled && ! $confirmed ? $user->twoFactorQrCodeSvg() : null,
            'secretKey' => $enabled && ! $confirmed ? decrypt($user->two_factor_secret) : null,
            'recoveryCodes' => $confirmed && $user->two_factor_recovery_codes
                ? json_decode(decrypt($user->two_factor_recovery_codes), true)
                : [],
        ]);
    })->name('two_factor.edit');

    /**
     * Sanctum personal access tokens. Listing is plain auth — token names
     * aren't sensitive. Issuing / revoking are gated by password.confirm
     * because they give (or revoke) bearer access to the API.
     */
    Route::middleware('auth')->get('api-tokens', [ApiTokenController::class, 'index'])
        ->name('api_token.index');
    Route::middleware(['auth', 'password.confirm'])->group(function () {
        Route::post('api-tokens', [ApiTokenController::class, 'store'])->name('api_token.store');
        Route::delete('api-tokens/{token}', [ApiTokenController::class, 'destroy'])->name('api_token.destroy');
    });

    /**
     * Profile image
     */
    Route::get('profile/image', [ProfileImageController::class, 'show'])->name('image.show');
    Route::put('profile/image', [ProfileImageController::class, 'update'])->name('image.update');
    Route::delete('profile/image', [ProfileImageController::class, 'destroy'])->name('image.destroy');

    /**
     * Active browser sessions. Listing is plain auth — the rows are
     * the caller's own. Destruction is password-confirmed so a
     * hijacked session can't lock the legitimate owner out.
     */
    Route::middleware('auth')->get('sessions', [SessionsController::class, 'index'])
        ->name('sessions.index');
    Route::middleware(['auth', 'password.confirm'])->group(function () {
        Route::delete('sessions/{id}', [SessionsController::class, 'destroy'])
            ->where('id', '[A-Za-z0-9]+')
            ->name('sessions.destroy');
        Route::delete('sessions', [SessionsController::class, 'destroyOthers'])
            ->name('sessions.destroy_others');
    });

    /**
     * Deactivate account (sensitive — requires password confirmation)
     */
    Route::middleware('password.confirm')->group(function () {
        Route::get('deactivate', [DeactivateController::class, 'index'])->name('deactivate.index');
        Route::post('deactivate', [DeactivateController::class, 'store'])->name('deactivate.store');

        Route::get('delete-account', [DeleteController::class, 'show'])->name('delete.show');
        Route::delete('delete-account', [DeleteController::class, 'destroy'])->name('delete.destroy');
    });

    /**
     * Subscriptions
     */
    Route::group([
        'prefix' => 'subscription',
        'namespace' => 'Subscription',
        'middleware' => ['subscription.owner']
    ], function () {
        /**
         * Cancel
         */
        Route::group(['middleware' => 'subscription.notcancelled'], function () {
            Route::get('/cancel', [SubscriptionCancelController::class, 'index'])->name('subscription.cancel.index');
            Route::post('/cancel', [SubscriptionCancelController::class, 'store'])->name('subscription.cancel.store');
        });

        /**
         * Resume
         */
        Route::group(['middleware' => 'subscription.cancelled'], function () {
            Route::get('/resume', [SubscriptionResumeController::class, 'index'])->name('subscription.resume.index');
            Route::post('/resume', [SubscriptionResumeController::class, 'store'])->name('subscription.resume.store');
        });

        /**
         * Swap
         */
        Route::group(['middleware' => 'subscription.notcancelled'], function () {
            Route::get('/swap', [SubscriptionSwapController::class, 'index'])->name('subscription.swap.index');
            Route::post('/swap', [SubscriptionSwapController::class, 'store'])->name('subscription.swap.store');
        });

        /**
         * Card
         */
        Route::group(['middleware' => 'subscription.customer'], function () {
            Route::get('/card', [SubscriptionCardController::class, 'index'])->name('subscription.card.index');
            Route::post('/card', [SubscriptionCardController::class, 'store'])->name('subscription.card.store');
        });

        /**
         * Invoices
         */
        Route::group(['middleware' => 'subscription.customer'], function () {
            Route::get('/invoices', [SubscriptionInvoiceController::class, 'index'])->name('subscription.invoices.index');
            Route::get('/invoices/{invoice}', [SubscriptionInvoiceController::class, 'show'])->name('subscription.invoices.show');
        });
    });
});


/**
 * ICal
 */
Route::get('calendar/ical', [ICalController::class, 'index'])
    ->middleware(['api_token.query', 'auth:api', 'tenant'])
    ->name('ical');



Route::get('websocket', [WebsocketTestController::class, 'index'])->name('websocket.test');
