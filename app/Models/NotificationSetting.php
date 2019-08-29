<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Eloquent
 */
class NotificationSetting extends Model
{
    protected $casts = [
        'send_daily' => 'boolean',
        'send_weekly' => 'boolean',
    ];

    protected $attributes = [
        'send_daily' => false,
        'send_weekly' => false,
    ];

    protected $fillable = [
        'send_daily',
        'send_weekly'
    ];
}
