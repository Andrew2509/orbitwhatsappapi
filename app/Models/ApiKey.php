<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    use SoftDeletes;

    /**
     * Available API scopes/permissions.
     */
    public const SCOPES = [
        'messages.send' => 'Kirim pesan',
        'messages.read' => 'Baca pesan',
        'devices.read' => 'Lihat daftar device',
        'devices.manage' => 'Kelola device',
        'contacts.read' => 'Baca kontak',
        'contacts.manage' => 'Kelola kontak',
        'campaigns.read' => 'Lihat campaign',
        'campaigns.manage' => 'Kelola campaign',
        'webhooks.manage' => 'Kelola webhook',
    ];

    protected $fillable = [
        'user_id',
        'name',
        'key',
        'environment',
        'last_used_at',
        'is_active',
        'allowed_ips',
        'scopes',
        'expires_at',
    ];

    protected $hidden = [
        'key',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
        'allowed_ips' => 'array',
        'scopes' => 'array',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function generateKey(string $environment = 'live'): string
    {
        $prefix = $environment === 'live' ? 'orbit_live_' : 'orbit_test_';
        return $prefix . Str::random(40);
    }

    public function getMaskedKeyAttribute(): string
    {
        return substr($this->key, 0, 20) . '...';
    }

    public function markAsUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Check if the API key has a specific scope.
     */
    public function hasScope(string $scope): bool
    {
        // If no scopes defined, allow all (backward compatibility)
        if (empty($this->scopes)) {
            return true;
        }

        // Check for wildcard
        if (in_array('*', $this->scopes)) {
            return true;
        }

        return in_array($scope, $this->scopes);
    }

    /**
     * Check if the API key has any of the given scopes.
     */
    public function hasAnyScope(array $scopes): bool
    {
        foreach ($scopes as $scope) {
            if ($this->hasScope($scope)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if the given IP is allowed to use this API key.
     */
    public function isIpAllowed(string $ip): bool
    {
        // If no IPs defined, allow all
        if (empty($this->allowed_ips)) {
            return true;
        }

        foreach ($this->allowed_ips as $allowedIp) {
            // Support CIDR notation (e.g., 192.168.1.0/24)
            if (str_contains($allowedIp, '/')) {
                if ($this->ipInCidr($ip, $allowedIp)) {
                    return true;
                }
            } elseif ($ip === $allowedIp) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if an IP is within a CIDR range.
     */
    protected function ipInCidr(string $ip, string $cidr): bool
    {
        list($subnet, $mask) = explode('/', $cidr);
        
        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $mask);
        
        return ($ip & $mask) === ($subnet & $mask);
    }

    /**
     * Check if the API key has expired.
     */
    public function isExpired(): bool
    {
        if (!$this->expires_at) {
            return false;
        }

        return $this->expires_at->isPast();
    }

    /**
     * Check if the API key is valid (active, not expired).
     */
    public function isValid(): bool
    {
        return $this->is_active && !$this->isExpired();
    }

    /**
     * Get all available scope names.
     */
    public static function getAvailableScopes(): array
    {
        return array_keys(self::SCOPES);
    }
}
