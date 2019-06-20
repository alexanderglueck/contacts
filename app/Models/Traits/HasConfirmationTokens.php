<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;
use App\Models\ConfirmationToken;

trait HasConfirmationTokens
{
    public function generateConfirmationToken()
    {
        $this->confirmationToken()->create([
            'token' => $token = Str::random(150),
            'expires_at' => $this->getConfirmationTokenExpiry()
        ]);

        return $token;
    }

    public function confirmationToken()
    {
        return $this->hasOne(ConfirmationToken::class);
    }

    protected function getConfirmationTokenExpiry()
    {
        return $this->freshTimestamp()->addMinutes(10);
    }
}
