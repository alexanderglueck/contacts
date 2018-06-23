<?php

namespace App\Models;

use Laravel\Cashier\Billable;
use Laravel\Cashier\Subscription;
use App\Models\Admin\Announcement;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Traits\HasSubscriptions;
use Illuminate\Notifications\Notifiable;
use Mpociot\Teamwork\Traits\UserHasTeams;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Models\Traits\HasConfirmationTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use Sluggable;
    use HasRoles;
    use HasConfirmationTokens;
    use Billable;
    use HasSubscriptions;
    use SoftDeletes;
    use UserHasTeams;

    protected $connection = 'system';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'activated', 'current_team_id'
    ];

    protected $casts = [
        'activated' => 'boolean'
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
    public function sluggable()
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
        'password', 'remember_token', 'google2fa_secret', 'api_token'
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
        return $this->hasMany(Comment::class, 'created_by')->orderBy('created_at', 'asc');
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

    public function permissions(): MorphToMany
    {
        return $this->morphToMany(
            config('permission.models.permission'),
            'model',
            config('database.connections.tenant.database') . '.' . config('permission.table_names.model_has_permissions'),
            'model_id',
            'permission_id'
        );
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

    public function hasImage()
    {
        return trim($this->image) !== '';
    }

    public function hasTwoFactorAuthentication()
    {
        return trim($this->google2fa_secret) !== '';
    }
}
