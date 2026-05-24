<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function show(): Response
    {
        $user = Auth::user();

        return Inertia::render('UserSettings/Profile', [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'password_reset_disabled' => (bool) $user->password_reset_disabled,
            ],
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $payload = $request->only('name', 'email');
        $payload['password_reset_disabled'] = $request->boolean('password_reset_disabled');

        if ($request->user()->update($payload)) {
            Session::flash('alert-success', trans('flash_message.contact.updated'));
        } else {
            Session::flash('alert-danger', trans('flash_message.contact.not_updated'));
        }

        return back();
    }
}
