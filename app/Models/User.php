<?php

namespace App\Models;

use App\Domain\Users\Actions\GenerateProfileImageAction;
use App\Models\Admin\Announcement;
use App\Models\System\News;
use App\Models\Traits\HasConfirmationTokens;
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
use Mpociot\Teamwork\Traits\UserHasTeams;

class User extends Authenticatable
{
    use HasFactory, Notifiable, Sluggable, HasRoles, HasConfirmationTokens, Billable, HasSubscriptions, SoftDeletes, UserHasTeams;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'activated', 'current_team_id'
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
                'reserved' => ['create', 'delete', 'edit']
            ]
        ];
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret', 'api_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Defines the has-many relationship with the Contact model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contacts()
    {
        return $this->hasMany(Contact::class, 'created_by');
    }

    /**
     * Defines the has-many relationship with the ContactGroup model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contactGroups()
    {
        return $this->hasMany(ContactGroup::class, 'created_by');
    }

    /**
     * Defines the has-many relationship with the TFABackupCode model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function backupCodes()
    {
        return $this->hasMany(TFABackupCode::class, 'user_id');
    }

    /**
     * Defines the has-many relationship with the LogEntry model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function logs()
    {
        return $this->hasMany(LogEntry::class, 'created_by')->orderByDesc('created_at');
    }

    /**
     * Defines the has-many relationship with the Comment model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
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

    public function isActivated()
    {
        return $this->activated;
    }

    public function isNotActivated()
    {
        return ! $this->activated;
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
            'stripe_plan'
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

    public function hasTwoFactorAuthentication()
    {
        return trim($this->google2fa_secret) !== '';
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
