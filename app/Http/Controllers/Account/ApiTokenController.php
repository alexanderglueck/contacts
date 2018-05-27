<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ApiTokenController extends Controller
{
    public function show()
    {
        return view('user_settings.api_token.show', [
            'user' => Auth::user()
        ]);
    }

    public function update()
    {
        Auth::user()->api_token = str_random(60);
        Auth::user()->save();

        return redirect()->route('user_settings.edit');
    }
}
