<?php

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('home');
    }

    return view('welcome');
});

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login')->name('login.check');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Registration Routes...Route::Route::Route::
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');

// 2FA Routes
Route::get('login/token', 'Auth\LoginController@token')->name('login.token');
Route::post('login/token', 'Auth\LoginController@check')->name('login.token.check');

/**
 * Install
 */
Route::get('install', 'Setup\InstallController@index')->name('install');

/**
 * Account activation
 */
Route::group(['middleware' => ['guest']], function () {
    Route::get('activation/resend', 'Auth\ActivationResendController@index')->name('activation.resend');
    Route::post('activation/resend', 'Auth\ActivationResendController@store')->name('activation.resend.store');
    Route::get('activation/{token}', 'Auth\ActivationController@activate')->name('activation.activate');
});

/**
 * Plans
 */
Route::group(['as' => 'plans.'], function () {
    Route::get('plans/', 'Subscription\PlanController@index')->name('index');
    Route::get('plans/teams', 'Subscription\PlanTeamController@index')->name('teams.index');
});

/**
 * Subscription
 */
Route::group(['as' => 'subscription.', 'middleware' => ['auth.register', 'subscription.inactive']], function () {
    Route::get('subscription', 'Subscription\SubscriptionController@index')->name('index');
    Route::post('subscription', 'Subscription\SubscriptionController@store')->name('store');
});

/**
 * Stripe webhook
 */
Route::post('/webhooks/stripe', 'Webhooks\StripeWebhookController@handleWebhook');

/**
 * Tenant selection dashboard
 */
Route::group(['middleware' => ['auth']], function () {
    Route::get('select', 'TenantSelectionController@index')->name('tenant.index');
    Route::post('select', 'TenantSelectionController@store')->name('tenant.store');
});

/**
 * Pages
 */
Route::get('pages/terms-of-service', 'PageController@termsOfService')->name('pages.terms_of_service');

/**
 * User Settings
 */
Route::group(['namespace' => 'Account', 'as' => 'user_settings.', 'prefix' => 'settings'], function () {
    /**
     * Profile
     */
    Route::get('profile', 'ProfileController@show')->name('profile.show');
    Route::put('profile', 'ProfileController@update')->name('profile.update');

    /**
     * Change password
     */
    Route::get('password', 'PasswordController@show')->name('password.show');
    Route::put('password', 'PasswordController@update')->name('password.update');

    /**
     * Profile image
     */
    Route::get('profile/image', 'ProfileImageController@show')->name('image.show');
    Route::put('profile/image', 'ProfileImageController@update')->name('image.update');
    Route::delete('profile/image', 'ProfileImageController@destroy')->name('image.destroy');

    /**
     * 2FA Settings
     */
    Route::get('two-factor', 'TwoFactorController@edit')->name('two_factor.edit');
    Route::post('two-factor', 'TwoFactorController@enable')->name('two_factor.enable');
    Route::post('two-factor/check', 'TwoFactorController@check')->name('two_factor.check');
    Route::delete('two-factor', 'TwoFactorController@disable')->name('two_factor.destroy');

    //  Route::post('auth-settings', 'TwoFactorController@check')->name('auth_settings.check');

    /**
     * API token
     */
    Route::get('api-token', 'ApiTokenController@show')->name('api_token.show');
    Route::put('api-token', 'ApiTokenController@update')->name('api_token.update');

    /**
     * Deactivate account
     */
    Route::get('deactivate', 'DeactivateController@index')->name('deactivate.index');
    Route::post('deactivate', 'DeactivateController@store')->name('deactivate.store');

    /**
     * Delete Account
     */
    Route::get('delete-account', 'DeleteController@show')->name('delete.show');
    Route::delete('delete-account', 'DeleteController@destroy')->name('delete.destroy');

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
            Route::get('/cancel', 'SubscriptionCancelController@index')->name('subscription.cancel.index');
            Route::post('/cancel', 'SubscriptionCancelController@store')->name('subscription.cancel.store');
        });

        /**
         * Resume
         */
        Route::group(['middleware' => 'subscription.cancelled'], function () {
            Route::get('/resume', 'SubscriptionResumeController@index')->name('subscription.resume.index');
            Route::post('/resume', 'SubscriptionResumeController@store')->name('subscription.resume.store');
        });

        /**
         * Swap
         */
        Route::group(['middleware' => 'subscription.notcancelled'], function () {
            Route::get('/swap', 'SubscriptionSwapController@index')->name('subscription.swap.index');
            Route::post('/swap', 'SubscriptionSwapController@store')->name('subscription.swap.store');
        });

        /**
         * Card
         */
        Route::group(['middleware' => 'subscription.customer'], function () {
            Route::get('/card', 'SubscriptionCardController@index')->name('subscription.card.index');
            Route::post('/card', 'SubscriptionCardController@store')->name('subscription.card.store');
        });

        /**
         * Invoices
         */
        Route::group(['middleware' => 'subscription.customer'], function () {
            Route::get('/invoices', 'SubscriptionInvoiceController@index')->name('subscription.invoices.index');
            Route::get('/invoices/{invoice}', 'SubscriptionInvoiceController@show')->name('subscription.invoices.show');
        });
    });
});
