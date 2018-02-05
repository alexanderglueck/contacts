<?php

namespace App\Http\Controllers;

use App\NotificationSetting;
use Auth;
use Illuminate\Http\Request;

class NotificationSettingController extends Controller
{
    public function edit()
    {
        return view('notification_settings.edit', [
            'user' => Auth::user(),
            'settings' => Auth::user()->notificationSettings()
        ]);
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'send_daily' => 'nullable|boolean',
            'send_weekly' => 'nullable|boolean',
        ]);

        NotificationSetting::where('user_id', Auth::id())
            ->delete();

        $notificationSettings = new NotificationSetting();
        $notificationSettings->fill($request->all());
        $notificationSettings->user_id = Auth::id();
        $notificationSettings->save();

        return redirect()->route('notification_settings.edit');
    }
}
