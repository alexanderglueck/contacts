<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\Account\PasswordUpdated;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\Account\PasswordUpdateRequest;

class PasswordController extends Controller
{
    public function show()
    {
        return view('user_settings.password.show');
    }

    public function update(PasswordUpdateRequest $request)
    {
        if ($request->user()->update([
            'password' => bcrypt($request->password)
        ])) {
            Session::flash('alert-success', trans('flash_message.contact.updated'));
        } else {
            Session::flash('alert-danger', trans('flash_message.contact.not_updated'));
        }

        Mail::to($request->user())->send(new PasswordUpdated());

        return back();
    }
}
