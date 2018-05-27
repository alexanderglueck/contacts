<?php

namespace App\Http\Controllers\Account;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Session;

class ProfileImageController extends Controller
{
    public function show()
    {
        return view('user_settings.image.show', [
            'user' => Auth::user()
        ]);
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'image' => 'required|mimes:jpeg,jpg,png'
        ]);

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $file = $request->file('image')->storePublicly('public/profile_images');

            $img = Image::make(storage_path('app/') . $file);
            $img->resize(50, 50);
            $img->save();

            if (Auth::user()->image) {
                if (file_exists(storage_path('app/public/') . Auth::user()->image)) {
                    unlink(storage_path('app/public/') . Auth::user()->image);
                }
            }

            Auth::user()->image = str_replace('public/', '', $file);

            if (Auth::user()->save()) {
                Session::flash('alert-success', trans('flash_message.user_setting.updated'));

                return redirect()->route('user_settings.image.show');
            }
        }

        Session::flash('alert-danger', trans('flash_message.user_setting.not_updated'));

        return redirect()->route('user_settings.image.show');
    }
}
