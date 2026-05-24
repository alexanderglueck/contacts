<?php

use App\Http\Controllers\Account\DeactivateController;
use App\Http\Controllers\Account\DeleteController;
use App\Http\Controllers\Account\ProfileController;
use App\Http\Controllers\Account\ProfileImageController;
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
use App\Http\Controllers\System\NewsController;
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
    // endpoints (e.g. PUT /user/password, POST /user/two-factor-authentication).
    Route::get('password', fn () => \Inertia\Inertia::render('UserSettings/Password'))->name('password.show');
    Route::get('two-factor', fn () => \Inertia\Inertia::render('UserSettings/TwoFactor', [
        'twoFactorEnabled' => (bool) auth()->user()->two_factor_secret,
        'twoFactorConfirmed' => auth()->user()->two_factor_confirmed_at !== null,
    ]))->name('two_factor.edit');

    /**
     * Profile image
     */
    Route::get('profile/image', [ProfileImageController::class, 'show'])->name('image.show');
    Route::put('profile/image', [ProfileImageController::class, 'update'])->name('image.update');
    Route::delete('profile/image', [ProfileImageController::class, 'destroy'])->name('image.destroy');

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
Route::get('calendar/ical', [ICalController::class, 'index'])->middleware(['auth:api', 'tenant'])->name('ical');


/**
 * News
 */
Route::get('news', [NewsController::class, 'index'])->name('news.index');

Route::group(['middleware' => 'admin'], function () {
    Route::get('news/create', [NewsController::class, 'create'])->name('news.create');
    Route::post('news', [NewsController::class, 'store'])->name('news.store');
    Route::get('news/{news}/edit', [NewsController::class, 'edit'])->name('news.edit');
    Route::put('news/{news}', [NewsController::class, 'update'])->name('news.update');
    Route::get('news/{news}/delete', [NewsController::class, 'delete'])->name('news.delete');
    Route::delete('news/{news}', [NewsController::class, 'destroy'])->name('news.destroy');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('news/{news}/read', [NewsController::class, 'markAsRead'])->name('news.mark_as_read');
});

Route::get('news/{news}', [NewsController::class, 'show'])->name('news.show');

Route::get('websocket', [WebsocketTestController::class, 'index'])->name('websocket.test');
