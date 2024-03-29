<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\DeactivateStoreRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class DeactivateController extends Controller
{
    public function index(): View
    {
        return view('user_settings.deactivate.index');
    }

    public function store(DeactivateStoreRequest $request): RedirectResponse
    {
        /*
         * Retrieve the user
         */
        $user = $request->user();

        /*
         * Cancel the active subscription
         */
        if ($user->subscribed('main')) {
            $user->subscription('main')->cancel();
        }

        /*
         * Logout the user
         */
        auth()->logout();

        /*
         * Soft-delete the user
         */
        $user->delete();

        /*
         * Flash success message and redirect to homepage
         */
        flashSuccess('Your account has been deactivated');

        return redirect()->route('login');
    }
}
