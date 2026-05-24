<?php

use App\Http\Controllers\Account\NotificationSettingController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ContactAddressController;
use App\Http\Controllers\ContactCallController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContactDateController;
use App\Http\Controllers\ContactEmailController;
use App\Http\Controllers\ContactExportController;
use App\Http\Controllers\ContactGroupController;
use App\Http\Controllers\ContactImportController;
use App\Http\Controllers\ContactNoteController;
use App\Http\Controllers\ContactNumberController;
use App\Http\Controllers\ContactUrlController;
use App\Http\Controllers\GiftIdeaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImpersonateController;
use App\Http\Controllers\LogEntryController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Teamwork\AuthController;
use App\Http\Controllers\Teamwork\TeamController;
use App\Http\Controllers\Teamwork\TeamMemberController;
use Illuminate\Support\Facades\Route;

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::group(['middleware' => 'subscription.active'], function () {
    /**
     * Contact Export
     */
    Route::get('contacts/export', [ContactExportController::class, 'index'])->name('export.index');
    Route::post('contacts/export', [ContactExportController::class, 'export'])->name('export.export');

    /**
     * Contact Import
     */
    Route::get('contacts/import', [ContactImportController::class, 'index'])->name('import.index');
    Route::post('contacts/import', [ContactImportController::class, 'import'])->name('import.import');

    /**
     * Contacts
     */
    Route::get('contacts', [ContactController::class, 'index'])->name('contacts.index');
    Route::get('contacts/create', [ContactController::class, 'create'])->name('contacts.create');
    Route::post('contacts', [ContactController::class, 'store'])->name('contacts.store');
    Route::get('contacts/{contact}', [ContactController::class, 'show'])->name('contacts.show');
    Route::get('contacts/{contact}/edit', [ContactController::class, 'edit'])->name('contacts.edit');
    Route::put('contacts/{contact}', [ContactController::class, 'update'])->name('contacts.update');
    Route::get('contacts/{contact}/delete', [ContactController::class, 'delete'])->name('contacts.delete');
    Route::delete('contacts/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');
    Route::get('contacts/{contact}/image', [ContactController::class, 'image'])->name('contacts.image');
    Route::put('contacts/{contact}/image', [ContactController::class, 'updateImage'])->name('contacts.update_image');

    /**
     * Contact Addresses (managed inline via Contacts/Show slideover)
     */
    Route::post('contact-addresses/{contact}', [ContactAddressController::class, 'store'])->name('contact_addresses.store');
    Route::group(['middleware' => ['verify_contact']], function () {
        Route::put('contact-addresses/{contact}/{contactAddress}', [ContactAddressController::class, 'update'])->name('contact_addresses.update');
        Route::delete('contact-addresses/{contact}/{contactAddress}', [ContactAddressController::class, 'destroy'])->name('contact_addresses.destroy');
    });

    /**
     * Contact Dates (managed inline via Contacts/Show slideover)
     */
    Route::post('contact-dates/{contact}', [ContactDateController::class, 'store'])->name('contact_dates.store');
    Route::group(['middleware' => ['verify_contact']], function () {
        Route::put('contact-dates/{contact}/{contactDate}', [ContactDateController::class, 'update'])->name('contact_dates.update');
        Route::delete('contact-dates/{contact}/{contactDate}', [ContactDateController::class, 'destroy'])->name('contact_dates.destroy');
    });

    /**
     * Contact Emails (managed inline via Contacts/Show slideover)
     */
    Route::post('contact-emails/{contact}', [ContactEmailController::class, 'store'])->name('contact_emails.store');
    Route::group(['middleware' => ['verify_contact']], function () {
        Route::put('contact-emails/{contact}/{contactEmail}', [ContactEmailController::class, 'update'])->name('contact_emails.update');
        Route::delete('contact-emails/{contact}/{contactEmail}', [ContactEmailController::class, 'destroy'])->name('contact_emails.destroy');
    });

    /**
     * Contact Numbers (managed inline via Contacts/Show slideover)
     */
    Route::post('contact-numbers/{contact}', [ContactNumberController::class, 'store'])->name('contact_numbers.store');
    Route::group(['middleware' => ['verify_contact']], function () {
        Route::put('contact-numbers/{contact}/{contactNumber}', [ContactNumberController::class, 'update'])->name('contact_numbers.update');
        Route::delete('contact-numbers/{contact}/{contactNumber}', [ContactNumberController::class, 'destroy'])->name('contact_numbers.destroy');
    });

    /**
     * Contact Urls (managed inline via Contacts/Show slideover)
     */
    Route::post('contact-urls/{contact}', [ContactUrlController::class, 'store'])->name('contact_urls.store');
    Route::group(['middleware' => ['verify_contact']], function () {
        Route::put('contact-urls/{contact}/{contactUrl}', [ContactUrlController::class, 'update'])->name('contact_urls.update');
        Route::delete('contact-urls/{contact}/{contactUrl}', [ContactUrlController::class, 'destroy'])->name('contact_urls.destroy');
    });

    /**
     * Contact Notes (managed inline via Contacts/Show slideover)
     */
    Route::post('contact-notes/{contact}', [ContactNoteController::class, 'store'])->name('contact_notes.store');
    Route::group(['middleware' => ['verify_contact']], function () {
        Route::put('contact-notes/{contact}/{contactNote}', [ContactNoteController::class, 'update'])->name('contact_notes.update');
        Route::delete('contact-notes/{contact}/{contactNote}', [ContactNoteController::class, 'destroy'])->name('contact_notes.destroy');
    });

    /**
     * Contact Calls (managed inline via Contacts/Show slideover)
     */
    Route::post('contact-calls/{contact}', [ContactCallController::class, 'store'])->name('contact_calls.store');
    Route::group(['middleware' => ['verify_contact']], function () {
        Route::put('contact-calls/{contact}/{contactCall}', [ContactCallController::class, 'update'])->name('contact_calls.update');
        Route::delete('contact-calls/{contact}/{contactCall}', [ContactCallController::class, 'destroy'])->name('contact_calls.destroy');
    });

    /**
     * Contact Groups
     */
    Route::get('contact-groups', [ContactGroupController::class, 'index'])->name('contact_groups.index');
    Route::get('contact-groups/create', [ContactGroupController::class, 'create'])->name('contact_groups.create');
    Route::post('contact-groups', [ContactGroupController::class, 'store'])->name('contact_groups.store');
    Route::get('contact-groups/{contactGroup}', [ContactGroupController::class, 'show'])->name('contact_groups.show');
    Route::get('contact-groups/{contactGroup}/edit', [ContactGroupController::class, 'edit'])->name('contact_groups.edit');
    Route::put('contact-groups/{contactGroup}', [ContactGroupController::class, 'update'])->name('contact_groups.update');
    Route::get('contact-groups/{contactGroup}/delete', [ContactGroupController::class, 'delete'])->name('contact_groups.delete');
    Route::delete('contact-groups/{contactGroup}', [ContactGroupController::class, 'destroy'])->name('contact_groups.destroy');

    /**
     * Calendar
     */
    Route::get('calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('calendar/events', [CalendarController::class, 'events'])->name('calendar.events');

    /**
     * Map
     */
    Route::get('map', [MapController::class, 'index'])->name('map.index');
    Route::post('map/contacts', [MapController::class, 'contacts'])->name('map.contacts');

    /**
     * Search
     */
    Route::post('search', [SearchController::class, 'search'])->name('search.search');

    /**
     * Reports
     */
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/inactive', [ReportController::class, 'inactive'])->name('reports.inactive');
    Route::get('reports/male', [ReportController::class, 'maleGender'])->name('reports.male');
    Route::get('reports/female', [ReportController::class, 'femaleGender'])->name('reports.female');
    Route::get('reports/wrong-male', [ReportController::class, 'wrongMaleGender'])->name('reports.wrong_male');
    Route::get('reports/wrong-female', [ReportController::class, 'wrongFemaleGender'])->name('reports.wrong_female');
    Route::get('reports/no-email', [ReportController::class, 'noEmail'])->name('reports.no_email');
    Route::get('reports/no-date', [ReportController::class, 'noDate'])->name('reports.no_date');
    Route::get('reports/no-address', [ReportController::class, 'noAddress'])->name('reports.no_address');
    Route::get('reports/no-number', [ReportController::class, 'noNumber'])->name('reports.no_number');
    Route::get('reports/no-url', [ReportController::class, 'noUrl'])->name('reports.no_url');
    Route::get('reports/no-lat-lng', [ReportController::class, 'noLatLng'])->name('reports.no_lat_lng');

    /**
     * Announcements
     */
    Route::get('announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::get('announcements/create', [AnnouncementController::class, 'create'])->name('announcements.create');
    Route::post('announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::get('announcements/{announcement}', [AnnouncementController::class, 'show'])->name('announcements.show');
    Route::get('announcements/{announcement}/read', [AnnouncementController::class, 'markAsRead'])->name('announcements.mark_as_read');
    Route::get('announcements/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('announcements.edit');
    Route::put('announcements/{announcement}', [AnnouncementController::class, 'update'])->name('announcements.update');
    Route::get('announcements/{announcement}/delete', [AnnouncementController::class, 'delete'])->name('announcements.delete');
    Route::delete('announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');

    /**
     * Comments
     */
    Route::post('comments/{contact}', [CommentController::class, 'store'])->name('comments.store');
    Route::get('comments/{contact}/{comment}/edit', [CommentController::class, 'edit'])->name('comments.edit');
    Route::put('comments/{contact}/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('comments/{contact}/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    /**
     * Gift Ideas (managed inline via Contacts/Show slideover)
     */
    Route::post('gift-ideas/{contact}', [GiftIdeaController::class, 'store'])->name('gift_ideas.store');
    Route::group(['middleware' => ['verify_contact']], function () {
        Route::put('gift-ideas/{contact}/{giftIdea}', [GiftIdeaController::class, 'update'])->name('gift_ideas.update');
        Route::delete('gift-ideas/{contact}/{giftIdea}', [GiftIdeaController::class, 'destroy'])->name('gift_ideas.destroy');
    });

    /**
     * Manage roles
     */
    Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('roles/{role}', [RoleController::class, 'show'])->name('roles.show');
    Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::get('roles/{role}/delete', [RoleController::class, 'delete'])->name('roles.delete');
    Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

    /**
     * Impersonate user
     */
    Route::post('impersonate', [ImpersonateController::class, 'store'])->name('user.impersonate');
    Route::delete('impersonate', [ImpersonateController::class, 'destroy']);

    /**
     * Activities
     */
    Route::get('activities', [ActivityController::class, 'index'])->name('activities.index');
});

/**
 * User Settings
 */
Route::group(['namespace' => 'Account', 'as' => 'user_settings.', 'prefix' => 'settings'], function () {
    /**
     * Notification Settings
     */
    Route::get('notifications', [NotificationSettingController::class, 'show'])->name('notifications.show');
    Route::put('notifications', [NotificationSettingController::class, 'update'])->name('notifications.update');
});

/**
 * Log
 */
Route::get('logs', [LogEntryController::class, 'index'])->name('logs.index');

/**
 * Images
 */
//Route::get('profile_images/{image}', function ($image) {
//    return Image::make(storage_path('app/profile_images/') . $image)->response();
//})->name('images.user');

//Route::get('contact_images/{image}', function ($image) {
//    return Image::make(storage_path('app/contact_images/') . $image)->response();
//})->name('images.contact');

/**
 * Teamwork
 */
Route::group(['prefix' => 'teams', 'namespace' => 'Teamwork'], function () {
    Route::get('/', [TeamController::class, 'index'])->name('teams.index');
    Route::get('create', [TeamController::class, 'create'])->name('teams.create');
    Route::post('teams', [TeamController::class, 'store'])->name('teams.store');
    Route::get('edit/{team}', [TeamController::class, 'edit'])->name('teams.edit');
    Route::put('edit/{team}', [TeamController::class, 'update'])->name('teams.update');
    Route::delete('destroy/{team}', [TeamController::class, 'destroy'])->name('teams.destroy');
    Route::get('switch/{team}', [TeamController::class, 'switchTeam'])->name('teams.switch');

    Route::group(['middleware' => ['subscription.team']], function () {
        Route::get('members/{team}', [TeamMemberController::class, 'show'])->name('teams.members.show');
        Route::get('members/resend/{invite}', [TeamMemberController::class, 'resendInvite'])->name('teams.members.resend_invite');
        Route::post('members/{team}', [TeamMemberController::class, 'invite'])->name('teams.members.invite');
        Route::delete('members/{team}/{user}', [TeamMemberController::class, 'destroy'])->name('teams.members.destroy');
    });

    Route::get('accept/{token}', [AuthController::class, 'acceptInvite'])->name('teams.accept_invite');
});
