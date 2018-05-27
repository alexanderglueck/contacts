<?php

namespace App\Http\Controllers\Account;

use Auth;
use Illuminate\Http\Request;
use App\Models\NotificationSetting;
use App\Http\Controllers\Controller;

class NotificationSettingController extends Controller
{
    public function show()
    {
        return view('user_settings.notifications.edit', [
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

        return redirect()->route('user_settings.notifications.show');
    }
}
