<?php

namespace App\Models;

use App\Tenant\Traits\ForTenants;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    use ForTenants;

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
