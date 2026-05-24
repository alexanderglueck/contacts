<?php

namespace App\Http\Controllers\Account;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\NotificationSetting;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Requests\Account\NotificationSettingUpdateRequest;

class NotificationSettingController extends Controller
{
    public function show(Request $request): Response
    {
        $settings = $request->user()->notificationSettings();

        return Inertia::render('UserSettings/Notifications', [
            'settings' => [
                'send_daily' => (bool) ($settings->send_daily ?? false),
                'send_weekly' => (bool) ($settings->send_weekly ?? false),
            ],
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
