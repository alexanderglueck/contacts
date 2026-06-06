<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    protected $casts = [
        'send_daily' => 'boolean',
        'send_weekly' => 'boolean',
        'send_daily_push' => 'boolean',
        'send_weekly_push' => 'boolean',
    ];

    protected $attributes = [
        'send_daily' => false,
        'send_weekly' => false,
        'send_daily_push' => false,
        'send_weekly_push' => false,
    ];

    protected $fillable = [
        'send_daily',
        'send_weekly',
        'send_daily_push',
        'send_weekly_push',
    ];
}
