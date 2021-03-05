<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Cashier\Subscription as BaseSubscription;

class Subscription extends BaseSubscription
{
    use HasFactory;
}
