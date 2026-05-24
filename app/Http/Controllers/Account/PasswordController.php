<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;
use App\Events\Auth\UserChangedPassword;
use App\Http\Requests\Account\PasswordUpdateRequest;

class PasswordController extends Controller
{
    public function show(): Response
    {
        return Inertia::render('UserSettings/Password');
    }

    public function update(PasswordUpdateRequest $request): RedirectResponse
    {
        if ($request->user()->update([
            'password' => bcrypt($request->password)
        ])) {
            Session::flash('alert-success', trans('flash_message.contact.updated'));
        } else {
            Session::flash('alert-danger', trans('flash_message.contact.not_updated'));
        }

        event(new UserChangedPassword($request->user()));

        return back();
    }
}
