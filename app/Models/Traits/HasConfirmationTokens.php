<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;
use App\Models\ConfirmationToken;

trait HasConfirmationTokens
{
    public function generateConfirmationToken($expiry = null)
    {
        $this->confirmationToken()->create([
            'token' => $token = Str::random(150),
            'expires_at' => $this->getConfirmationTokenExpiry($expiry)
        ]);

        return $token;
    }

    public function confirmationToken()
    {
        return $this->hasOne(ConfirmationToken::class);
    }

    protected function getConfirmationTokenExpiry($expiry)
    {
        return $this->freshTimestamp()->addMinutes($expiry ?: 10);
    }
}
