<?php

namespace App\Models;

use App\Models\Traits\HasConfirmationTokens;
use App\Tenant\Traits\ForSystem;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use Sluggable;
    use HasRoles;
    use ForSystem;
    use HasConfirmationTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
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

    public function currentTeam()
    {
        return $this->belongsTo(Team::class);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, config('contacts.tenant.system') . '.team_user');
    }
}
