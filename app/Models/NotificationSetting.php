<?php

namespace App\Models;

use App\Tenant\Manager;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    protected $connection = 'tenant';

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

    public function getTable()
    {
        return app(Manager::class)->getTenant()->tenantConnection->database . '.' . parent::getTable();
    }
}
