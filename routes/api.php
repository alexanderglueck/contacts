<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CalendarController;
use App\Http\Controllers\Api\V1\ContactAddressesController;
use App\Http\Controllers\Api\V1\ContactCallsController;
use App\Http\Controllers\Api\V1\ContactCommentsController;
use App\Http\Controllers\Api\V1\ContactDatesController;
use App\Http\Controllers\Api\V1\ContactEmailsController;
use App\Http\Controllers\Api\V1\ContactNotesController;
use App\Http\Controllers\Api\V1\ContactNumbersController;
use App\Http\Controllers\Api\V1\ContactsController;
use App\Http\Controllers\Api\V1\ContactUrlsController;
use App\Http\Controllers\Api\V1\GiftIdeasController;
use App\Http\Controllers\Api\V1\ReferenceController;
use App\Http\Controllers\Api\V1\TeamsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Versioned by URL path: every endpoint lives under /api/{version}/...
| Controllers for each version live under their own App\Http\Controllers\Api\{Vn}
| namespace so v2 development can never accidentally touch v1's behaviour.
|
| Adding a v2 later: copy this block, change `v1` → `v2` (prefix + name +
| controller imports), and start diverging the implementation. v1 stays
| frozen as the contract Android-app-v1 talks to.
|
| Rate-limiting policy:
|   - Unauthenticated POSTs (login, register): 5/min/IP. Anything more is
|     credential stuffing.
|   - Everything else: the `api` named limiter (60/min per user OR IP for
|     guests, registered in AppServiceProvider). Generous because mobile
|     UIs can fire several requests on a single screen load.
|
*/

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::middleware('throttle:5,1')->group(function () {
        Route::post('auth/register', [AuthController::class, 'register'])->name('auth.register');
        Route::post('auth/login', [AuthController::class, 'login'])->name('auth.login');
    });

    Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::get('auth/me', [AuthController::class, 'me'])->name('auth.me');

        Route::get('teams', [TeamsController::class, 'index'])->name('teams.index');
        Route::post('teams/{team}/switch', [TeamsController::class, 'switchTo'])->name('teams.switch');

        // Global reference tables — not tenant-scoped, no auth required
        // beyond the user being logged in.
        Route::get('reference/genders', [ReferenceController::class, 'genders'])->name('reference.genders');
        Route::get('reference/countries', [ReferenceController::class, 'countries'])->name('reference.countries');

        // Tenant-scoped routes need the SetTenant middleware. It falls back to
        // the user's currentTeam when no session/?tenant=… is set — so mobile
        // doesn't have to send tenant context on every request.
        Route::middleware('tenant')->group(function () {
            // Tenant-scoped reference (contact groups belong to a team).
            Route::get('reference/contact-groups', [ReferenceController::class, 'contactGroups'])
                ->name('reference.contact_groups');

            // Calendar — read-only event feed plus iCal subscription URL
            // management. The iCal feed itself is served by the web route
            // `/calendar/ical` (text/calendar response) authenticated via
            // the rotating Sanctum token issued here.
            Route::get('calendar/events', [CalendarController::class, 'events'])
                ->name('calendar.events');
            Route::get('calendar/upcoming', [CalendarController::class, 'upcoming'])
                ->name('calendar.upcoming');
            Route::get('calendar/sync-url', [CalendarController::class, 'syncUrl'])
                ->name('calendar.sync_url');
            Route::post('calendar/sync-url', [CalendarController::class, 'rotateSyncUrl'])
                ->name('calendar.sync_url.rotate');
            Route::delete('calendar/sync-url', [CalendarController::class, 'revokeSyncUrl'])
                ->name('calendar.sync_url.revoke');

            Route::get('contacts', [ContactsController::class, 'index'])->name('contacts.index');
            Route::post('contacts', [ContactsController::class, 'store'])->name('contacts.store');

            // Static path declared BEFORE the {contact} route binding so the
            // word "by-number" isn't interpreted as a ULID.
            Route::get('contacts/by-number', [ContactsController::class, 'byNumber'])->name('contacts.by_number');

            Route::get('contacts/{contact}', [ContactsController::class, 'show'])->name('contacts.show');
            Route::match(['put', 'patch'], 'contacts/{contact}', [ContactsController::class, 'update'])->name('contacts.update');
            Route::delete('contacts/{contact}', [ContactsController::class, 'destroy'])->name('contacts.destroy');

            // Contact avatar — multipart upload, separate from the JSON update
            // path so clients can send raw binary without base64-bloating the
            // body. POST replaces the existing image; DELETE clears it.
            Route::post('contacts/{contact}/image', [ContactsController::class, 'uploadImage'])
                ->name('contacts.image.upload');
            Route::delete('contacts/{contact}/image', [ContactsController::class, 'destroyImage'])
                ->name('contacts.image.destroy');

            // Sub-resources. scopeBindings() makes nested route-model binding
            // verify each child belongs to its {contact} parent — saves us
            // from repeating that check in every controller method.
            Route::scopeBindings()->group(function () {
                foreach ([
                    ['numbers', 'number', ContactNumbersController::class],
                    ['emails', 'email', ContactEmailsController::class],
                    ['urls', 'url', ContactUrlsController::class],
                    ['notes', 'note', ContactNotesController::class],
                    ['addresses', 'address', ContactAddressesController::class],
                    ['dates', 'date', ContactDatesController::class],
                    ['calls', 'call', ContactCallsController::class],
                    ['gift-ideas', 'gift_idea', GiftIdeasController::class],
                    ['comments', 'comment', ContactCommentsController::class],
                ] as [$path, $param, $ctrl]) {
                    Route::get("contacts/{contact}/{$path}", [$ctrl, 'index'])
                        ->name("contacts.{$path}.index");
                    Route::get("contacts/{contact}/{$path}/{{$param}}", [$ctrl, 'show'])
                        ->name("contacts.{$path}.show");
                    Route::post("contacts/{contact}/{$path}", [$ctrl, 'store'])
                        ->name("contacts.{$path}.store");
                    Route::match(['put', 'patch'], "contacts/{contact}/{$path}/{{$param}}", [$ctrl, 'update'])
                        ->name("contacts.{$path}.update");
                    Route::delete("contacts/{contact}/{$path}/{{$param}}", [$ctrl, 'destroy'])
                        ->name("contacts.{$path}.destroy");
                }
            });
        });
    });
});
