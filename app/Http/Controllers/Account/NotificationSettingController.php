<?php

namespace App\Http\Controllers\Account;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\NotificationSetting;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Account\NotificationSettingUpdateRequest;

class NotificationSettingController extends Controller
{
    public function show(Request $request): View
    {
        return view('user_settings.notifications.edit', [
            'user' => $request->user(),
            'settings' => $request->user()->notificationSettings()
        ]);
    }

    public function update(NotificationSettingUpdateRequest $request): RedirectResponse
    {
        NotificationSetting::where('user_id', Auth::id())->delete();

        $notificationSettings = new NotificationSetting();
        $notificationSettings->fill($request->all());
        $notificationSettings->user_id = Auth::id();
        $notificationSettings->save();

        return redirect()->route('user_settings.notifications.show');
    }
}
