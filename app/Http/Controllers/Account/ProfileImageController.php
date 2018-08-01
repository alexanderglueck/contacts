<?php

namespace App\Http\Controllers\Account;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\Account\ProfileImageUpdateRequest;

class ProfileImageController extends Controller
{
    public function show(Request $request)
    {
        return view('user_settings.image.show', [
            'user' => $request->user()
        ]);
    }

    public function update(ProfileImageUpdateRequest $request)
    {
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $file = $request->file('image')->storePublicly('public/profile_images');

            $img = Image::make(storage_path('app/') . $file);
            $img->resize(50, 50);
            $img->save();

            if ($request->user()->image) {
                if (file_exists(storage_path('app/public/') . $request->user()->image)) {
                    unlink(storage_path('app/public/') . $request->user()->image);
                }
            }

            $request->user()->image = str_replace('public/', '', $file);

            if ($request->user()->save()) {
                Session::flash('alert-success', trans('flash_message.user_setting.updated'));

                return redirect()->route('user_settings.image.show');
            }
        }

        Session::flash('alert-danger', trans('flash_message.user_setting.not_updated'));

        return redirect()->route('user_settings.image.show');
    }

    public function destroy(Request $request)
    {
        if ($request->user()->hasImage()) {
            if (file_exists(storage_path('app/public/') . $request->user()->image)) {
                unlink(storage_path('app/public/') . $request->user()->image);
            }
        }

        $request->user()->image = null;
        $request->user()->save();

        flashSuccess(trans('flash_message.user_setting.updated'));

        return redirect()->route('user_settings.image.show');
    }
}
