<?php

namespace App\Http\Controllers\Account;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ApiTokenController extends Controller
{
    public function show(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('UserSettings/ApiToken', [
            'user' => [
                'slug' => $user->slug,
                'api_token' => $user->api_token,
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->user()->api_token = Str::random(60);
        $request->user()->save();

        return redirect()->route('user_settings.api_token.show');
    }
}
