<?php

namespace Tests\Feature;

use App\Models\ApiKey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IpWhitelistingTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected ApiKey $apiKey;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->apiKey = ApiKey::create([
            'user_id' => $this->user->id,
            'name' => 'Test API Key',
            'key' => ApiKey::generateKey(),
            'environment' => 'live',
            'is_active' => true,
        ]);
    }

    public function test_request_without_ip_whitelist_succeeds(): void
    {
        // No IP whitelist set - should allow all
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey->key,
        ])->getJson('/api/v1/devices');

        $response->assertSuccessful();
    }

    public function test_request_from_whitelisted_ip_succeeds(): void
    {
        // Set IP whitelist to include localhost
        $this->apiKey->update([
            'allowed_ips' => ['127.0.0.1'],
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey->key,
        ])->getJson('/api/v1/devices');

        $response->assertSuccessful();
    }

    public function test_request_from_non_whitelisted_ip_fails(): void
    {
        // Set IP whitelist to exclude current IP
        $this->apiKey->update([
            'allowed_ips' => ['192.168.1.100'],
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey->key,
        ])->getJson('/api/v1/devices');

        $response->assertStatus(403);
        $response->assertJson([
            'message_status' => 'Error',
            'error' => 'Access denied from this IP address',
        ]);
    }

    public function test_cidr_notation_ip_whitelist(): void
    {
        // Allow all localhost range
        $this->apiKey->update([
            'allowed_ips' => ['127.0.0.0/8'],
        ]);

        // Test the CIDR check logic directly
        $this->assertTrue($this->apiKey->isIpAllowed('127.0.0.1'));
        $this->assertTrue($this->apiKey->isIpAllowed('127.0.0.2'));
        $this->assertFalse($this->apiKey->isIpAllowed('192.168.1.1'));
    }

    public function test_expired_api_key_fails(): void
    {
        $this->apiKey->update([
            'expires_at' => now()->subDay(),
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey->key,
        ])->getJson('/api/v1/devices');

        $response->assertStatus(401);
        $response->assertJson([
            'message_status' => 'Error',
            'error' => 'API key has expired',
        ]);
    }

    public function test_api_key_without_expiration_succeeds(): void
    {
        $this->apiKey->update([
            'expires_at' => null,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey->key,
        ])->getJson('/api/v1/devices');

        $response->assertSuccessful();
    }
}
