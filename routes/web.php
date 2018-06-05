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
