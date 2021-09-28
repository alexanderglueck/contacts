<?php

namespace App\Http\Controllers\Account;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class DeleteController extends Controller
{
    public function show(): View
    {
        return view('user_settings.delete.show');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->subscribed('main')) {
            $user->subscription('main')->cancel();
        }

        $user->delete();

        if ($user->image) {
            if (file_exists(storage_path('app/public/') . $user->image)) {
                unlink(storage_path('app/public/') . $user->image);
            }

            $user->image = null;
            $user->save();
        }

        if ($user->forceDelete()) {
            Auth::logout();

            Session::flash('alert-success', trans('flash_message.user_setting.deleted'));

            return redirect()->route('login');
        }

        Session::flash('alert-danger', trans('flash_message.user_setting.not_deleted'));

        return redirect()->route('user_settings.delete');
    }
}
