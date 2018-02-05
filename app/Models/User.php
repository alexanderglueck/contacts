<?php

namespace App\Models;

use App\Models\NotificationSetting;
use Illuminate\Notifications\Notifiable;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use Sluggable;

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
     * Defines the has-many relationship with the Contact model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contactGroups()
    {
        return $this->hasMany(ContactGroup::class, 'created_by');
    }

    /**
     * Defines the has-many relationship with the Contact model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function backupCodes()
    {
        return $this->hasMany(TFABackupCode::class, 'user_id');
    }

    /**
     * Defines the has-many relationship with the Contact model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function logs()
    {
        return $this->hasMany(LogEntry::class, 'created_by')->orderByDesc('created_at');
    }

    public function notificationSetting()
    {
        return $this->hasOne(NotificationSetting::class, 'user_id');
    }

    public function notificationSettings()
    {
        if ($this->notificationSetting == null) {
            return new NotificationSetting();
        }

        return $this->notificationSetting;
    }
}
