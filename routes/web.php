<?php

use App\Http\Controllers\Account\ApiTokenController;
use App\Http\Controllers\Account\DeactivateController;
use App\Http\Controllers\Account\DeleteController;
use App\Http\Controllers\Account\PasswordController;
use App\Http\Controllers\Account\ProfileController;
use App\Http\Controllers\Account\ProfileImageController;
use App\Http\Controllers\Account\Subscription\SubscriptionCancelController;
use App\Http\Controllers\Account\Subscription\SubscriptionCardController;
use App\Http\Controllers\Account\Subscription\SubscriptionInvoiceController;
use App\Http\Controllers\Account\Subscription\SubscriptionResumeController;
use App\Http\Controllers\Account\Subscription\SubscriptionSwapController;
use App\Http\Controllers\Account\TwoFactorController;
use App\Http\Controllers\Auth\ActivationController;
use App\Http\Controllers\Auth\ActivationResendController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
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

// Authentication Routes...
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login'])->name('login.check');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Registration Routes...Route::Route::Route::
Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

// Password Reset Routes...
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset']);

// 2FA Routes
Route::get('login/token', [LoginController::class, 'token'])->name('login.token');
Route::post('login/token', [LoginController::class, 'check'])->name('login.token.check');

/**
 * Install
 */
Route::get('install', [InstallController::class, 'index'])->name('install');
Route::post('install', [InstallController::class, 'store'])->name('install.store');

/**
 * Account activation
 */
Route::group(['middleware' => ['guest']], function () {
    Route::get('activation/resend', [ActivationResendController::class, 'index'])->name('activation.resend');
    Route::post('activation/resend', [ActivationResendController::class, 'store'])->name('activation.resend.store');
    Route::get('activation/{token}', [ActivationController::class, 'activate'])->name('activation.activate');
});

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

    /**
     * Change password
     */
    Route::get('password', [PasswordController::class, 'show'])->name('password.show');
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    /**
     * Profile image
     */
    Route::get('profile/image', [ProfileImageController::class, 'show'])->name('image.show');
    Route::put('profile/image', [ProfileImageController::class, 'update'])->name('image.update');
    Route::delete('profile/image', [ProfileImageController::class, 'destroy'])->name('image.destroy');

    /**
     * 2FA Settings
     */
    Route::get('two-factor', [TwoFactorController::class, 'edit'])->name('two_factor.edit');
    Route::post('two-factor', [TwoFactorController::class, 'enable'])->name('two_factor.enable');
    Route::post('two-factor/check', [TwoFactorController::class, 'check'])->name('two_factor.check');
    Route::delete('two-factor', [TwoFactorController::class, 'disable'])->name('two_factor.destroy');

    //  Route::post('auth-settings', [TwoFactorController::class, 'check'])->name('auth_settings.check');

    /**
     * API token
     */
    Route::get('api-token', [ApiTokenController::class, 'show'])->name('api_token.show');
    Route::put('api-token', [ApiTokenController::class, 'update'])->name('api_token.update');

    /**
     * Deactivate account
     */
    Route::get('deactivate', [DeactivateController::class, 'index'])->name('deactivate.index');
    Route::post('deactivate', [DeactivateController::class, 'store'])->name('deactivate.store');

    /**
     * Delete Account
     */
    Route::get('delete-account', [DeleteController::class, 'show'])->name('delete.show');
    Route::delete('delete-account', [DeleteController::class, 'destroy'])->name('delete.destroy');

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
