<?php

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
Route::get('contacts', 'Tenant\\ContactController@index')->name('contacts.index');
Route::get('contacts/create', 'Tenant\\ContactController@create')->name('contacts.create');
Route::post('contacts', 'Tenant\\ContactController@store')->name('contacts.store');
Route::get('contacts/{contact}', 'Tenant\\ContactController@show')->name('contacts.show');
Route::get('contacts/{contact}/edit', 'Tenant\\ContactController@edit')->name('contacts.edit');
Route::put('contacts/{contact}', 'Tenant\\ContactController@update')->name('contacts.update');
Route::get('contacts/{contact}/delete', 'Tenant\\ContactController@delete')->name('contacts.delete');
Route::delete('contacts/{contact}', 'Tenant\\ContactController@destroy')->name('contacts.destroy');
Route::get('contacts/{contact}/image', 'Tenant\\ContactController@image')->name('contacts.image');
Route::put('contacts/{contact}/image', 'Tenant\\ContactController@updateImage')->name('contacts.update_image');

/**
 * Contact Addresses
 */
Route::get('contact-addresses', function () {
    return redirect()->route('contacts.index');
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
    return redirect()->route('contacts.index');
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
    return redirect()->route('contacts.index');
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
    return redirect()->route('contacts.index');
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
    return redirect()->route('contacts.index');
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
     * Subscriptions
     */
    Route::group(['prefix' => 'subscription', 'namespace' => 'Subscription'], function () {
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
        });

        /**
         * Card
         */
        Route::group(['middleware' => 'subscription.customer'], function () {
            Route::get('/card', 'SubscriptionCardController@index')->name('subscription.card.index');
        });
    });
});

/**
 * 2FA Settings
 */
Route::get('auth-settings', 'AuthSettingController@edit')->name('auth_settings.edit');
Route::post('auth-settings', 'AuthSettingController@enable')->name('auth_settings.enable');
Route::post('auth-settings/check', 'AuthSettingController@check')->name('auth_settings.check');
Route::delete('auth-settings', 'AuthSettingController@disable')->name('auth_settings.disable');

//  Route::post('auth-settings', 'AuthSettingController@check')->name('auth_settings.check');

/**
 * Delete Account
 */
Route::get('delete-account', 'UserSettingController@delete')->name('user_settings.delete');
Route::delete('delete-account', 'UserSettingController@destroy')->name('user_settings.destroy');

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

/**
 * Announcements
 */
Route::get('announcements', 'AnnouncementController@index')->name('announcements.index');
Route::get('announcements/create', 'AnnouncementController@create')->name('announcements.create');
Route::post('announcements', 'AnnouncementController@store')->name('announcements.store');
Route::get('announcements/{announcement}', 'AnnouncementController@show')->name('announcements.show');
Route::get('announcements/{announcement}/edit', 'AnnouncementController@edit')->name('announcements.edit');
Route::put('announcements/{announcement}', 'AnnouncementController@update')->name('announcements.update');
Route::get('announcements/{announcement}/delete', 'AnnouncementController@delete')->name('announcements.delete');
Route::delete('announcements/{announcement}', 'AnnouncementController@destroy')->name('announcements.destroy');

/**
 * Comments
 */
Route::post('comments/{contact}', 'CommentController@store')->name('comments.store');
Route::get('comments/{contact}/{comment}/edit', 'CommentController@edit')->name('comments.edit');
Route::put('comments/{contact}/{comment}', 'CommentController@update')->name('comments.update');
Route::delete('comments/{contact}/{comment}', 'CommentController@destroy')->name('comments.destroy');

/**
 * Gift Ideas
 */
Route::get('gift-ideas', function () {
    return redirect()->route('contacts.index');
});
Route::get('gift-ideas/{contact}', 'GiftIdeaController@index')->name('gift_ideas.index');
Route::get('gift-ideas/{contact}/create', 'GiftIdeaController@create')->name('gift_ideas.create');
Route::post('gift-ideas/{contact}', 'GiftIdeaController@store')->name('gift_ideas.store');

Route::group(['middleware' => ['verify_contact']], function () {
    Route::get('gift-ideas/{contact}/{giftIdea}', 'GiftIdeaController@show')->name('gift_ideas.show');
    Route::get('gift-ideas/{contact}/{giftIdea}/edit', 'GiftIdeaController@edit')->name('gift_ideas.edit');
    Route::put('gift-ideas/{contact}/{giftIdea}', 'GiftIdeaController@update')->name('gift_ideas.update');
    Route::get('gift-ideas/{contact}/{giftIdea}/delete', 'GiftIdeaController@delete')->name('gift_ideas.delete');
    Route::delete('gift-ideas/{contact}/{giftIdea}', 'GiftIdeaController@destroy')->name('gift_ideas.destroy');
});

/**
 * Activities
 */
Route::get('activities', 'ActivityController@index')->name('activities.index');

/**
 * Teamwork
 */
Route::group(['prefix' => 'teams', 'namespace' => 'Teamwork'], function () {
    Route::get('/', 'TeamController@index')->name('teams.index');
    Route::get('create', 'TeamController@create')->name('teams.create');
    Route::post('teams', 'TeamController@store')->name('teams.store');
    Route::get('edit/{id}', 'TeamController@edit')->name('teams.edit');
    Route::put('edit/{id}', 'TeamController@update')->name('teams.update');
    Route::delete('destroy/{id}', 'TeamController@destroy')->name('teams.destroy');
    Route::get('switch/{id}', 'TeamController@switchTeam')->name('teams.switch');

    Route::get('members/{id}', 'TeamMemberController@show')->name('teams.members.show');
    Route::get('members/resend/{invite_id}', 'TeamMemberController@resendInvite')->name('teams.members.resend_invite');
    Route::post('members/{id}', 'TeamMemberController@invite')->name('teams.members.invite');
    Route::delete('members/{id}/{user_id}', 'TeamMemberController@destroy')->name('teams.members.destroy');

    Route::get('accept/{token}', 'AuthController@acceptInvite')->name('teams.accept_invite');
});

Route::get('roles', 'RoleController@index')->name('roles.index');
Route::get('roles/create', 'RoleController@create')->name('roles.create');
Route::post('roles', 'RoleController@store')->name('roles.store');
Route::get('roles/{role}', 'RoleController@show')->name('roles.show');
Route::get('roles/{role}/edit', 'RoleController@edit')->name('roles.edit');
Route::put('roles/{role}', 'RoleController@update')->name('roles.update');
Route::get('roles/{role}/delete', 'RoleController@delete')->name('roles.delete');
Route::delete('roles/{role}', 'RoleController@destroy')->name('roles.destroy');

Route::post('impersonate', 'ImpersonateController@store')->name('user.impersonate');
Route::delete('impersonate', 'ImpersonateController@destroy');
