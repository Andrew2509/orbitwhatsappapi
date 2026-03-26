<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PromoCode extends Model
{
    protected $fillable = [
        'code',
        'description',
        'is_active',
        'discount_type',
        'discount_value',
        'min_purchase',
        'max_discount',
        'usage_limit',
        'usage_limit_per_user',
        'times_used',
        'starts_at',
        'expires_at',
        'applicable_plans',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'discount_value' => 'decimal:2',
        'min_purchase' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'applicable_plans' => 'array',
    ];

    // Relationships
    public function usages(): HasMany
    {
        return $this->hasMany(PromoCodeUsage::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'promo_code_usages')
            ->withPivot('discount_applied', 'invoice_id')
            ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        return $query->active()
            ->where(function ($q) {
                $q->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->where(function ($q) {
                $q->whereNull('usage_limit')
                    ->orWhereColumn('times_used', '<', 'usage_limit');
            });
    }

    // Helper Methods
    public static function generateCode(int $length = 8): string
    {
        do {
            $code = strtoupper(Str::random($length));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->usage_limit && $this->times_used >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    public function isExpiringSoon(): bool
    {
        return $this->expires_at && $this->expires_at->diffInHours(now()) <= 24;
    }

    public function canBeUsedBy(User $user): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        // Check per-user limit
        $userUsageCount = $this->usages()->where('user_id', $user->id)->count();
        if ($userUsageCount >= $this->usage_limit_per_user) {
            return false;
        }

        return true;
    }

    public function isApplicableToPlan(int $planId): bool
    {
        if (empty($this->applicable_plans)) {
            return true; // Applicable to all plans
        }

        return in_array($planId, $this->applicable_plans);
    }

    public function calculateDiscount(float $amount): float
    {
        if ($amount < $this->min_purchase) {
            return 0;
        }

        if ($this->discount_type === 'percentage') {
            $discount = $amount * ($this->discount_value / 100);
            
            // Apply max discount cap if set
            if ($this->max_discount && $discount > $this->max_discount) {
                $discount = (float) $this->max_discount;
            }
        } else {
            $discount = (float) $this->discount_value;
        }

        // Discount cannot exceed the amount
        return min($discount, $amount);
    }

    public function incrementUsage(): void
    {
        $this->increment('times_used');
    }

    public function getFormattedDiscount(): string
    {
        if ($this->discount_type === 'percentage') {
            return $this->discount_value . '%';
        }
        return 'Rp ' . number_format($this->discount_value, 0, ',', '.');
    }

    public function getTotalRevenue(): float
    {
        return $this->usages()
            ->whereHas('invoice', function ($q) {
                $q->where('status', 'paid');
            })
            ->join('invoices', 'promo_code_usages.invoice_id', '=', 'invoices.id')
            ->sum('invoices.total');
    }

    public function getRemainingUses(): ?int
    {
        if ($this->usage_limit === null) {
            return null;
        }
        return max(0, $this->usage_limit - $this->times_used);
    }
}
