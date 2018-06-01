<?php

use App\Models\Contact;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['auth:api', 'subscription.active:api']], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/contacts', function () {
        return Contact::with('addresses', 'dates', 'numbers', 'urls', 'emails', 'contactGroups', 'gender')->where('contacts.active', '=', 1)->orderBy('firstname')->orderBy('lastname')->paginate();
//        return Contact::sorted()->active()->paginate();
    });

    Route::get('/contacts/{contact}', function (Contact $contact) {
        $contacts = Contact::with('addresses', 'dates', 'numbers', 'urls', 'emails', 'contactGroups', 'gender')->where('contacts.id', '=', $contact->id)->get();

        return $contacts[0];

        //return ["contact" => Contact::with('addresses', 'dates', 'numbers', 'urls', 'emails', 'contactGroups', 'gender')->where('contacts.id', '=', $contact->id)->get()];
    });

    /**
     * Images
     */
    Route::get('profile_images/{image}', function ($image) {
        return Image::make(storage_path('app/profile_images/') . $image)->response();
    });

    Route::get('contact_images/{image}', function ($image) {
        return Image::make(storage_path('app/contact_images/') . $image)->response();
    });
});
