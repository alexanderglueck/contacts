<?php

namespace App\Http\Controllers\Account\Subscription;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SubscriptionCardController extends Controller
{
    public function index()
    {
        return view('user_settings.subscription.card.index');
    }
}
