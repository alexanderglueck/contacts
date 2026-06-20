<?php

namespace App\Models;

use App\Domain\Users\Actions\GenerateProfileImageAction;
use App\Models\Concerns\HasUlidRouteKey;
use App\Models\Traits\HasRoles;
use App\Models\Traits\HasSubscriptions;
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
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail, PasskeyUser
{
    use Billable;
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use HasSubscriptions;
    use HasTeams;
    use HasUlidRouteKey;
    use Notifiable;
    use PasskeyAuthenticatable;
    use SoftDeletes;
    use TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'password_reset_disabled',
        'current_team_id',
        'locale',
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
        'password_reset_disabled' => 'boolean',
    ];

    /**
     * Per-user opt-out: when password_reset_disabled is true, drop the
     * reset notification on the floor. The password broker still goes
     * through its normal motions and returns its usual response — so a
     * stranger hitting POST /forgot-password with this address can't
     * tell whether the email was accepted or silently discarded.
     */
    public function sendPasswordResetNotification($token): void
    {
        if ($this->passwordResetIsDisabled()) {
            return;
        }

        parent::sendPasswordResetNotification($token);
    }

    /**
     * Strictest setting wins: a team owner can enforce "no password reset"
     * across all their members, overriding whatever each member set
     * individually. So we suppress the email if either the user's own
     * flag is set OR any team they belong to has the flag set.
     */
    public function passwordResetIsDisabled(): bool
    {
        if ($this->password_reset_disabled) {
            return true;
        }

        return $this->teams()
            ->where('teams.password_reset_disabled', true)
            ->exists();
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

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    /**
     * Route notifications for the FCM (push) channel: every registered device
     * token the user has. The FcmChannel sends one message per token.
     *
     * @return array<int, string>
     */
    public function routeNotificationForFcm(): array
    {
        return $this->devices()->withDeviceToken()->pluck('device_token')->unique()->values()->all();
    }

    /**
     * The user's currently selected team. Overrides Jetstream's HasTeams
     * version, which auto-switches to a "personal team" when current_team_id
     * is null — this app has no personal teams and current_team_id is allowed
     * to be null (e.g. right after the user's last team is deleted), so a
     * plain relation is what we want.
     */
    public function currentTeam()
    {
        return $this->belongsTo(Team::class, 'current_team_id');
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

    /**
     * Add the user to a team (and make it current if they have none yet).
     * Replaces mpociot/teamwork's UserHasTeams::attachTeam().
     */
    public function attachTeam($team): void
    {
        $teamId = $team instanceof Team ? $team->id : $team;

        if (is_null($this->current_team_id)) {
            $this->current_team_id = $teamId;
            $this->save();
        }

        if (! $this->teams()->where('teams.id', $teamId)->exists()) {
            $this->teams()->attach($teamId);
        }
    }

    /**
     * Remove the user from a team, clearing current_team_id if they leave their
     * current team or run out of teams. Replaces UserHasTeams::detachTeam().
     */
    public function detachTeam($team): void
    {
        $teamId = $team instanceof Team ? $team->id : $team;

        $this->teams()->detach($teamId);

        if ($this->teams()->count() === 0 || $this->current_team_id === $teamId) {
            $this->current_team_id = null;
            $this->save();
        }
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
