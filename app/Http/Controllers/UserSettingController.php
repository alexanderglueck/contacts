<?php

namespace App\Http\Controllers;

use Auth;
use Session;
use App\Models\User;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class UserSettingController extends Controller
{
    private $validationRules = [
        'name' => 'required',
        'password' => 'sometimes|confirmed'
    ];

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        return view('user_settings.edit', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validate($request, $this->validationRules);

        Auth::user()->name = $request->name;

        if ($request->has('password') && strlen(trim($request->password)) > 0) {
            Auth::user()->password = bcrypt($request->password);
        }

        if (Auth::user()->save()) {
            Session::flash('alert-success', trans('flash_message.user_setting.updated'));

            return redirect()->route('user_settings.edit');
        } else {
            Session::flash('alert-danger', trans('flash_message.user_setting.not_updated'));

            return redirect()->route('user_settings.edit');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function updateImage(Request $request)
    {
        $this->validate($request, [
            'image' => 'required|mimes:jpeg,png'
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

                return redirect()->route('user_settings.edit');
            }
        }

        Session::flash('alert-danger', trans('flash_message.user_setting.not_updated'));

        return redirect()->route('user_settings.edit');
    }

    public function updateApiToken(Request $request)
    {
        Auth::user()->api_token = str_random(60);
        Auth::user()->save();

        return redirect()->route('user_settings.edit');
    }

    /**
     * Show the delete account form
     *
     * @return \Illuminate\Http\Response
     */
    public function delete()
    {
        return view('user_settings.delete');
    }

    /**
     * Delete the users account
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy()
    {
        $user = User::find(Auth::id());

        if ($user->image) {
            if (file_exists(storage_path('app/public/') . $user->image)) {
                unlink(storage_path('app/public/') . $user->image);
            }

            $user->image = null;
            $user->save();
        }

        if ($user->delete()) {
            Auth::logout();

            Session::flash('alert-success', trans('flash_message.user_setting.deleted'));

            return redirect()->route('login');
        } else {
            Session::flash('alert-danger', trans('flash_message.user_setting.not_deleted'));

            return redirect()->route('user_settings.delete');
        }
    }
}
