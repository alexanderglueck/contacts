<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    protected string $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showResetForm(Request $request, ?string $token = null): Response
    {
        return Inertia::render('Auth/ResetPassword', [
            'token' => $token ?? $request->route('token'),
            'email' => $request->email ?? '',
        ]);
    }

    protected function sendResetResponse(Request $request, string $response): RedirectResponse
    {
        if ( ! $this->guard()->user()->isActivated()) {
            $this->guard()->logout();
        } else {
            session()->put('tenant', $this->guard()->user()->currentTeam->uuid);
        }

        return redirect($this->redirectPath())
            ->with('status', trans($response));
    }
}
