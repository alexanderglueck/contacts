<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Events\Auth\UserRequestedActivationEmail;
use App\Http\Requests\Auth\ActivateResendRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ActivationResendController extends Controller
{
    public function index(): View
    {
        return view('auth.activation.resend.index');
    }

    public function store(ActivateResendRequest $request): RedirectResponse
    {
        $user = User::where('email', $request->email)->first();

        if ($user && ! $user->isActivated()) {
            event(new UserRequestedActivationEmail($user));
        }

        flashSuccess('An activation email has been sent.');

        return redirect()->route('login');
    }
}
