<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfirmationToken extends Model
{
    protected $connection = 'system';

    public $timestamps = false;

    protected $dates = [
        'expires_at'
    ];

    protected $fillable = [
        'token',
        'expires_at'
    ];

    public function getRouteKeyName()
    {
        return 'token';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hasExpired()
    {
        return $this->freshTimestamp()->gt($this->expires_at);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($token) {
            optional($token->user->confirmationToken)->delete();
        });

        /*
         *  static::creating(function ($token) {
            $token->user->confirmationTokens->each(function ($token) {
                $token->delete();
            });
        });
         */
    }
}
