<?php

namespace App\Http\Controllers\Account;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class ApiTokenController extends Controller
{
    public function show(Request $request): View
    {
        return view('user_settings.api_token.show', [
            'user' => $request->user()
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->user()->api_token = Str::random(60);
        $request->user()->save();

        return redirect()->route('user_settings.api_token.show');
    }
}
