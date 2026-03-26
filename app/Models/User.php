<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'email' => $this->email,
            'role' => $this->role,
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'is_suspended',
        'suspended_at',
        'suspension_reason',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function templates(): HasMany
    {
        return $this->hasMany(Template::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function autoReplies(): HasMany
    {
        return $this->hasMany(AutoReply::class);
    }

    public function apiKeys(): HasMany
    {
        return $this->hasMany(ApiKey::class);
    }

    public function webhooks(): HasMany
    {
        return $this->hasMany(Webhook::class);
    }

    // Billing Relationships

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->latest();
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->where('ends_at', '>', now());
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function usageLogs(): HasMany
    {
        return $this->hasMany(UsageLog::class);
    }

    // Billing Helpers

    public function currentPlan(): ?Plan
    {
        return $this->activeSubscription?->plan;
    }

    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription()->exists();
    }

    public function isOnFreePlan(): bool
    {
        $plan = $this->currentPlan();
        return !$plan || $plan->isFree();
    }

    public function canUseFeature(string $feature): bool
    {
        $plan = $this->currentPlan();
        if (!$plan) {
            // Free plan features
            return in_array($feature, ['basic_messaging']);
        }
        return $plan->hasFeature($feature);
    }

    public function hasReachedLimit(string $limitType): bool
    {
        $plan = $this->currentPlan();
        $todayUsage = UsageLog::getOrCreateForToday($this->id);

        return match($limitType) {
            'devices' => $plan 
                ? (!$plan->isUnlimited('devices') && $this->devices()->count() >= $plan->max_devices)
                : $this->devices()->count() >= 1,
            'messages' => $plan 
                ? (!$plan->isUnlimited('messages') && $todayUsage->messages_sent >= $plan->max_messages_per_day)
                : $todayUsage->messages_sent >= 100,
            'contacts' => $plan
                ? (!$plan->isUnlimited('contacts') && $this->contacts()->count() >= $plan->max_contacts)
                : $this->contacts()->count() >= 100,
            default => false,
        };
    }

    public function getRemainingQuota(string $limitType): int
    {
        $plan = $this->currentPlan();
        $todayUsage = UsageLog::getOrCreateForToday($this->id);

        return match($limitType) {
            'devices' => $plan 
                ? ($plan->isUnlimited('devices') ? -1 : max(0, $plan->max_devices - $this->devices()->count()))
                : max(0, 1 - $this->devices()->count()),
            'messages' => $plan
                ? ($plan->isUnlimited('messages') ? -1 : max(0, $plan->max_messages_per_day - $todayUsage->messages_sent))
                : max(0, 100 - $todayUsage->messages_sent),
            'contacts' => $plan
                ? ($plan->isUnlimited('contacts') ? -1 : max(0, $plan->max_contacts - $this->contacts()->count()))
                : max(0, 100 - $this->contacts()->count()),
            default => 0,
        };
    }

    public function getTodayUsage(): UsageLog
    {
        return UsageLog::getOrCreateForToday($this->id);
    }
}
