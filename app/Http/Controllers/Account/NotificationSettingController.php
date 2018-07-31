<?php

namespace App\Http\Controllers\Account;

use Illuminate\Http\Request;
use App\Models\NotificationSetting;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationSettingController extends Controller
{
    public function show(Request $request)
    {
        return view('user_settings.notifications.edit', [
            'user' => $request->user(),
            'settings' => $request->user()->notificationSettings()
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
