<?php

namespace App\Events\TwoFactor;

use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class TwoFactorFailure
{
    use Dispatchable, SerializesModels;


    public function __construct(public User $user)
    {
        //
    }
}
