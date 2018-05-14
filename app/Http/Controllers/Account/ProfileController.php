<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\Account\ProfileUpdateRequest;

class ProfileController extends Controller
{
    public function show()
    {
        return view('user_settings.profile.show');
    }

    public function update(ProfileUpdateRequest $request)
    {
        if ($request->user()->update($request->only('name', 'email'))) {
            Session::flash('alert-success', trans('flash_message.contact.updated'));
        } else {
            Session::flash('alert-danger', trans('flash_message.contact.not_updated'));
        }

        return back();
    }
}
