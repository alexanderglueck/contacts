<?php

namespace App\Http\Controllers\Account;

use App\Http\Requests\Account\ProfileUpdateRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function show()
    {
        return view('user_settings.profile.show');
    }

    public function update(ProfileUpdateRequest $request)
    {
        $request->user()->update($request->only('name', 'email'));

        return back();
    }
}
