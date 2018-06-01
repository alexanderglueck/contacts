<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Events\Auth\UserRequestedActivationEmail;
use App\Http\Requests\Auth\ActivateResendRequest;

class ActivationResendController extends Controller
{
    public function index()
    {
        return view('auth.activation.resend.index');
    }

    public function store(ActivateResendRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user && ! $user->isActivated()) {
            event(new UserRequestedActivationEmail($user));
        }

        flashSuccess('An activation email has been sent.');

        return redirect()->route('login');
    }
}
