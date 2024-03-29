<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\ConfirmationToken;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ActivationController extends Controller
{
    protected string $redirectTo = '/home';

    /**
     * ActivationController constructor.
     */
    public function __construct()
    {
        $this->middleware(['confirmation_token.expired:/']);
    }

    public function activate(ConfirmationToken $token, Request $request): RedirectResponse
    {
        $token->user->update([
            'activated' => true
        ]);

        $token->delete();

        Auth::loginUsingId($token->user->id);

        session()->put('tenant', $token->user->currentTeam->uuid);

        return redirect()->intended($this->redirectPath());
    }

    public function redirectPath(): string
    {
        return $this->redirectTo;
    }
}
