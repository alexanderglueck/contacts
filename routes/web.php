<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login')->name('login.check');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Registration Routes...Route::Route::Route::
 Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
 Route::post('register', 'Auth\RegisterController@register');
/*Route::get('register', function () {
    return Redirect::to(route('login'));
})->name('register');
Route::post('register', function () {
    return false;
});*/

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');

// 2FA Routes
Route::get('login/token', 'Auth\LoginController@token')->name('login.token');
Route::post('login/token', 'Auth\LoginController@check')->name('login.token.check');

Route::group(['middleware' => ['web', 'auth']], function () {
    Route::get('/home', 'HomeController@index')->name('home');

    /**
     * Contact Export
     */
    Route::get('contacts/export', 'ContactExportController@index')->name('export.index');
    Route::post('contacts/export', 'ContactExportController@export')->name('export.export');

    /**
     * Contact Import
     */
    Route::get('contacts/import', 'ContactImportController@index')->name('import.index');
    Route::post('contacts/import', 'ContactImportController@import')->name('import.import');

    /**
     * Contacts
     */
    Route::get('contacts', 'ContactController@index')->name('contacts.index');
    Route::get('contacts/create', 'ContactController@create')->name('contacts.create');
    Route::post('contacts', 'ContactController@store')->name('contacts.store');
    Route::get('contacts/{contact}', 'ContactController@show')->name('contacts.show');
    Route::get('contacts/{contact}/edit', 'ContactController@edit')->name('contacts.edit');
    Route::put('contacts/{contact}', 'ContactController@update')->name('contacts.update');
    Route::get('contacts/{contact}/delete', 'ContactController@delete')->name('contacts.delete');
    Route::delete('contacts/{contact}', 'ContactController@destroy')->name('contacts.destroy');
    Route::get('contacts/{contact}/image', 'ContactController@image')->name('contacts.image');
    Route::put('contacts/{contact}/image', 'ContactController@updateImage')->name('contacts.update_image');

    /**
     * Contact Addresses
     */
    Route::get('contact-addresses', function () {
        return redirect('/contacts');
    });
    Route::get('contact-addresses/{contact}', 'ContactAddressController@index')->name('contact_addresses.index');
    Route::get('contact-addresses/{contact}/create', 'ContactAddressController@create')->name('contact_addresses.create');
    Route::post('contact-addresses/{contact}', 'ContactAddressController@store')->name('contact_addresses.store');

    Route::group(['middleware' => ['verify_contact']], function () {
        Route::get('contact-addresses/{contact}/{contactAddress}', 'ContactAddressController@show')->name('contact_addresses.show');
        Route::get('contact-addresses/{contact}/{contactAddress}/edit', 'ContactAddressController@edit')->name('contact_addresses.edit');
        Route::put('contact-addresses/{contact}/{contactAddress}', 'ContactAddressController@update')->name('contact_addresses.update');
        Route::get('contact-addresses/{contact}/{contactAddress}/delete', 'ContactAddressController@delete')->name('contact_addresses.delete');
        Route::delete('contact-addresses/{contact}/{contactAddress}', 'ContactAddressController@destroy')->name('contact_addresses.destroy');
    });

    /**
     * Contact Dates
     */
    Route::get('contact-dates', function () {
        return redirect('/contacts');
    });
    Route::get('contact-dates/{contact}', 'ContactDateController@index')->name('contact_dates.index');
    Route::get('contact-dates/{contact}/create', 'ContactDateController@create')->name('contact_dates.create');
    Route::post('contact-dates/{contact}', 'ContactDateController@store')->name('contact_dates.store');
    Route::group(['middleware' => ['verify_contact']], function () {
        Route::get('contact-dates/{contact}/{contactDate}', 'ContactDateController@show')->name('contact_dates.show');
        Route::get('contact-dates/{contact}/{contactDate}/edit', 'ContactDateController@edit')->name('contact_dates.edit');
        Route::put('contact-dates/{contact}/{contactDate}', 'ContactDateController@update')->name('contact_dates.update');
        Route::get('contact-dates/{contact}/{contactDate}/delete', 'ContactDateController@delete')->name('contact_dates.delete');
        Route::delete('contact-dates/{contact}/{contactDate}', 'ContactDateController@destroy')->name('contact_dates.destroy');
    });

    /**
     * Contact Emails
     */
    Route::get('contact-emails', function () {
        return redirect('/contacts');
    });
    Route::get('contact-emails/{contact}', 'ContactEmailController@index')->name('contact_emails.index');
    Route::get('contact-emails/{contact}/create', 'ContactEmailController@create')->name('contact_emails.create');
    Route::post('contact-emails/{contact}', 'ContactEmailController@store')->name('contact_emails.store');
    Route::group(['middleware' => ['verify_contact']], function () {
        Route::get('contact-emails/{contact}/{contactEmail}', 'ContactEmailController@show')->name('contact_emails.show');
        Route::get('contact-emails/{contact}/{contactEmail}/edit', 'ContactEmailController@edit')->name('contact_emails.edit');
        Route::put('contact-emails/{contact}/{contactEmail}', 'ContactEmailController@update')->name('contact_emails.update');
        Route::get('contact-emails/{contact}/{contactEmail}/delete', 'ContactEmailController@delete')->name('contact_emails.delete');
        Route::delete('contact-emails/{contact}/{contactEmail}', 'ContactEmailController@destroy')->name('contact_emails.destroy');
    });

    /**
     * Contact Numbers
     */
    Route::get('contact-numbers', function () {
        return redirect('/contacts');
    });
    Route::get('contact-numbers/{contact}', 'ContactNumberController@index')->name('contact_numbers.index');
    Route::get('contact-numbers/{contact}/create', 'ContactNumberController@create')->name('contact_numbers.create');
    Route::post('contact-numbers/{contact}', 'ContactNumberController@store')->name('contact_numbers.store');
    Route::group(['middleware' => ['verify_contact']], function () {
        Route::get('contact-numbers/{contact}/{contactNumber}', 'ContactNumberController@show')->name('contact_numbers.show');
        Route::get('contact-numbers/{contact}/{contactNumber}/edit', 'ContactNumberController@edit')->name('contact_numbers.edit');
        Route::put('contact-numbers/{contact}/{contactNumber}', 'ContactNumberController@update')->name('contact_numbers.update');
        Route::get('contact-numbers/{contact}/{contactNumber}/delete', 'ContactNumberController@delete')->name('contact_numbers.delete');
        Route::delete('contact-numbers/{contact}/{contactNumber}', 'ContactNumberController@destroy')->name('contact_numbers.destroy');
    });

    /**
     * Contact Urls
     */
    Route::get('contact-urls', function () {
        return redirect('/contacts');
    });
    Route::get('contact-urls/{contact}', 'ContactUrlController@index')->name('contact_urls.index');
    Route::get('contact-urls/{contact}/create', 'ContactUrlController@create')->name('contact_urls.create');
    Route::post('contact-urls/{contact}', 'ContactUrlController@store')->name('contact_urls.store');
    Route::group(['middleware' => ['verify_contact']], function () {
        Route::get('contact-urls/{contact}/{contactUrl}', 'ContactUrlController@show')->name('contact_urls.show');
        Route::get('contact-urls/{contact}/{contactUrl}/edit', 'ContactUrlController@edit')->name('contact_urls.edit');
        Route::put('contact-urls/{contact}/{contactUrl}', 'ContactUrlController@update')->name('contact_urls.update');
        Route::get('contact-urls/{contact}/{contactUrl}/delete', 'ContactUrlController@delete')->name('contact_urls.delete');
        Route::delete('contact-urls/{contact}/{contactUrl}', 'ContactUrlController@destroy')->name('contact_urls.destroy');
    });

    /**
     * Contact Groups
     */
    Route::get('contact-groups', 'ContactGroupController@index')->name('contact_groups.index');
    Route::get('contact-groups/create', 'ContactGroupController@create')->name('contact_groups.create');
    Route::post('contact-groups', 'ContactGroupController@store')->name('contact_groups.store');
    Route::get('contact-groups/{contactGroup}', 'ContactGroupController@show')->name('contact_groups.show');
    Route::get('contact-groups/{contactGroup}/edit', 'ContactGroupController@edit')->name('contact_groups.edit');
    Route::put('contact-groups/{contactGroup}', 'ContactGroupController@update')->name('contact_groups.update');
    Route::get('contact-groups/{contactGroup}/delete', 'ContactGroupController@delete')->name('contact_groups.delete');
    Route::delete('contact-groups/{contactGroup}', 'ContactGroupController@destroy')->name('contact_groups.destroy');

    /**
     * Calendar
     */
    Route::get('calendar', 'CalendarController@index')->name('calendar.index');
    Route::get('calendar/events', 'CalendarController@events')->name('calendar.events');

    /**
     * Map
     */
    Route::get('map', 'MapController@index')->name('map.index');
    Route::post('map/contacts', 'MapController@contacts')->name('map.contacts');

    /**
     * User Settings
     */
    Route::get('settings', 'UserSettingController@edit')->name('user_settings.edit');
    Route::put('settings', 'UserSettingController@update')->name('user_settings.update');
    Route::put('settings/image', 'UserSettingController@updateImage')->name('user_settings.update_image');
    Route::put('settings/api-token', 'UserSettingController@updateApiToken')->name('user_settings.update_api_token');

    /**
     * 2FA Settings
     */
    Route::get('auth-settings', 'AuthSettingController@edit')->name('auth_settings.edit');
    Route::post('auth-settings', 'AuthSettingController@enable')->name('auth_settings.enable');
    Route::post('auth-settings/check', 'AuthSettingController@check')->name('auth_settings.check');
    Route::delete('auth-settings', 'AuthSettingController@disable')->name('auth_settings.disable');

    //  Route::post('auth-settings', 'AuthSettingController@check')->name('auth_settings.check');

    /**
     * Notification Settings
     */
    Route::get('settings/notifications', 'NotificationSettingController@edit')->name('notification_settings.edit');
    Route::put('settings/notifications', 'NotificationSettingController@update')->name('notification_settings.update');

    /**
     * Log
     */
    Route::get('logs', 'LogEntryController@index')->name('logs.index');

    /**
     * Search
     */
    Route::post('search', 'SearchController@search')->name('search.search');

    /**
     * Reports
     */
    Route::get('reports', 'ReportController@index')->name('reports.index');
    Route::get('reports/inactive', 'ReportController@inactive')->name('reports.inactive');
    Route::get('reports/male', 'ReportController@maleGender')->name('reports.male');
    Route::get('reports/female', 'ReportController@femaleGender')->name('reports.female');
    Route::get('reports/wrong-male', 'ReportController@wrongMaleGender')->name('reports.wrong_male');
    Route::get('reports/wrong-female', 'ReportController@wrongFemaleGender')->name('reports.wrong_female');
    Route::get('reports/no-email', 'ReportController@noEmail')->name('reports.no_email');
    Route::get('reports/no-date', 'ReportController@noDate')->name('reports.no_date');
    Route::get('reports/no-address', 'ReportController@noAddress')->name('reports.no_address');
    Route::get('reports/no-number', 'ReportController@noNumber')->name('reports.no_number');
    Route::get('reports/no-url', 'ReportController@noUrl')->name('reports.no_url');
    Route::get('reports/no-lat-lng', 'ReportController@noLatLng')->name('reports.no_lat_lng');

    /**
     * Images
     */
    Route::get('profile_images/{image}', function ($image) {
        return Image::make(storage_path('app/profile_images/') . $image)->response();
    })->name('images.user');

    Route::get('contact_images/{image}', function ($image) {
        return Image::make(storage_path('app/contact_images/') . $image)->response();
    })->name('images.contact');
});
