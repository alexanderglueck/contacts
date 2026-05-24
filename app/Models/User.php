<?php

namespace App\Models;

use App\Domain\Users\Actions\GenerateProfileImageAction;
use App\Models\Admin\Announcement;
use App\Models\Concerns\HasUlidRouteKey;
use App\Models\System\News;
use App\Models\Traits\HasRoles;
use App\Models\Traits\HasSubscriptions;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Cashier\Subscription;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Passkeys\PasskeyAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Mpociot\Teamwork\Traits\UserHasTeams;

class User extends Authenticatable implements MustVerifyEmail, PasskeyUser
{
    use Billable;
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use HasSubscriptions;
    use HasUlidRouteKey;
    use Notifiable;
    use PasskeyAuthenticatable;
    use Sluggable;
    use SoftDeletes;
    use TwoFactorAuthenticatable;
    use UserHasTeams;

    protected $fillable = [
        'name',
        'email',
        'password',
        'current_team_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'two_factor_confirmed_at' => 'datetime',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
                'reserved' => ['create', 'delete', 'edit'],
            ],
        ];
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'created_by');
    }

    public function contactGroups()
    {
        return $this->hasMany(ContactGroup::class, 'created_by');
    }

    public function logs()
    {
        return $this->hasMany(LogEntry::class, 'created_by')->orderByDesc('created_at');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'created_by')->orderBy('created_at');
    }

    public function notificationSetting()
    {
        return $this->hasOne(NotificationSetting::class, 'user_id');
    }

    public function giftIdeas()
    {
        return $this->hasMany(GiftIdea::class, 'created_by');
    }

    public function notificationSettings()
    {
        if ($this->notificationSetting == null) {
            return new NotificationSetting();
        }

        return $this->notificationSetting;
    }

    public function isOwnerOfTeam($team)
    {
        return $team->owner_id == $this->id;
    }

    public function switchTeam($team)
    {
        $this->current_team_id = $team->id;
        $this->save();
    }

    public function plan()
    {
        return $this->plans->first();
    }

    public function getPlanAttribute()
    {
        return $this->plan();
    }

    public function plans()
    {
        return $this->hasManyThrough(
            Plan::class,
            Subscription::class,
            'user_id',
            'gateway_id',
            'id',
            'stripe_price'
        )->orderBy('subscriptions.created_at', 'desc');
    }

    public function readAnnouncements()
    {
        return $this->belongsToMany(
            Announcement::class
        );
    }

    public function readNews()
    {
        return $this->belongsToMany(
            News::class
        );
    }

    public function hasImage()
    {
        return trim($this->image) !== '';
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            if (trim($user->image) === '') {
                app(GenerateProfileImageAction::class)->execute($user);
            }
        });
    }
}
