<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\DeactivateStoreRequest;

class DeactivateController extends Controller
{
    public function index()
    {
        return view('user_settings.deactivate.index');
    }

    public function store(DeactivateStoreRequest $request)
    {
        $user = $request->user();

        if ($user->subscribed('main')) {
            $user->subscription('main')->cancel();
        }

        $user->delete();

        flashSuccess('Your account has been deactivated');

        return redirect()->route('home');
    }
}
