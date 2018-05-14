<?php

namespace App\Models;

use App\Tenant\Traits\ForSystem;
use Illuminate\Database\Eloquent\Model;

class ConfirmationToken extends Model
{
    use ForSystem;

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
        static::creating(function ($token) {
            optional($token->user->confirmationToken)->delete();
        });
    }
}
