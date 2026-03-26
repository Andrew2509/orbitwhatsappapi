<?php

namespace Tests\Feature;

use App\Models\ApiKey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class ApiRateLimitingTest extends TestCase
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

    protected function tearDown(): void
    {
        RateLimiter::clear('api_rate_limit:user:' . $this->user->id);
        parent::tearDown();
    }

    public function test_request_within_rate_limit_succeeds(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey->key,
        ])->getJson('/api/v1/devices');

        $response->assertStatus(200);
        $response->assertHeader('X-RateLimit-Limit');
        $response->assertHeader('X-RateLimit-Remaining');
    }

    public function test_request_exceeding_rate_limit_returns_429(): void
    {
        $key = 'api_rate_limit:user:' . $this->user->id;
        
        // Simulate hitting rate limit
        for ($i = 0; $i < 60; $i++) {
            RateLimiter::hit($key, 60);
        }

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey->key,
        ])->getJson('/api/v1/devices');

        $response->assertStatus(429);
        $response->assertJson([
            'message_status' => 'Error',
            'error' => 'Too many requests. Please slow down.',
        ]);
        $response->assertHeader('Retry-After');
    }

    public function test_rate_limit_headers_are_present(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey->key,
        ])->getJson('/api/v1/devices');

        $this->assertTrue($response->headers->has('X-RateLimit-Limit'));
        $this->assertTrue($response->headers->has('X-RateLimit-Remaining'));
        $this->assertEquals(60, $response->headers->get('X-RateLimit-Limit'));
    }
}
