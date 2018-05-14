<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\PasswordUpdateRequest;
use App\Mail\Account\PasswordUpdated;
use Illuminate\Support\Facades\Mail;

class PasswordController extends Controller
{
    public function show()
    {
        return view('user_settings.password.show');
    }

    public function update(PasswordUpdateRequest $request)
    {
        $request->user()->update([
            'password' => bcrypt($request->password)
        ]);

        Mail::to($request->user())->send(new PasswordUpdated());

        return back();
    }
}
