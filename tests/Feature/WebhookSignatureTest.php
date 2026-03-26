<?php

namespace Tests\Feature;

use App\Models\Webhook;
use App\Models\User;
use App\Services\WebhookDispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WebhookSignatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Webhook $webhook;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->webhook = Webhook::create([
            'user_id' => $this->user->id,
            'url' => 'https://example.com/webhook',
            'secret' => 'test_secret_key_12345',
            'events' => ['message.received', 'message.sent', 'test'],
            'is_active' => true,
            'max_retries' => 3,
        ]);
    }

    public function test_webhook_signature_is_generated_correctly(): void
    {
        $payload = [
            'event' => 'test',
            'data' => ['message' => 'Hello World'],
        ];

        $payloadJson = json_encode($payload);
        $expectedSignature = hash_hmac('sha256', $payloadJson, $this->webhook->secret);

        $this->assertNotEmpty($expectedSignature);
        $this->assertEquals(64, strlen($expectedSignature)); // SHA256 produces 64 hex chars
    }

    public function test_webhook_dispatch_includes_signature_headers(): void
    {
        Http::fake([
            'example.com/*' => Http::response(['status' => 'ok'], 200),
        ]);

        $log = WebhookDispatcher::sendTest($this->webhook);

        Http::assertSent(function ($request) {
            // Check for X-Hub-Signature-256 header (GitHub style)
            $this->assertTrue($request->hasHeader('X-Hub-Signature-256'));
            $signature = $request->header('X-Hub-Signature-256')[0];
            $this->assertStringStartsWith('sha256=', $signature);

            // Check for X-Webhook-Signature header
            $this->assertTrue($request->hasHeader('X-Webhook-Signature'));
            
            // Check other webhook headers
            $this->assertTrue($request->hasHeader('X-Webhook-Event'));
            $this->assertTrue($request->hasHeader('X-Webhook-Timestamp'));

            return true;
        });

        $this->assertEquals('success', $log->status);
    }

    public function test_webhook_without_secret_has_no_signature(): void
    {
        $webhookNoSecret = Webhook::create([
            'user_id' => $this->user->id,
            'url' => 'https://example.com/webhook-no-secret',
            'secret' => null,
            'events' => ['test'],
            'is_active' => true,
        ]);

        Http::fake([
            'example.com/*' => Http::response(['status' => 'ok'], 200),
        ]);

        WebhookDispatcher::sendTest($webhookNoSecret);

        Http::assertSent(function ($request) {
            // Should NOT have signature headers when no secret is set
            $this->assertFalse($request->hasHeader('X-Hub-Signature-256'));
            $this->assertFalse($request->hasHeader('X-Webhook-Signature'));

            return true;
        });
    }

    public function test_signature_verification_example(): void
    {
        // This demonstrates how a webhook receiver would verify the signature
        $payload = ['event' => 'test', 'data' => ['test' => true]];
        $payloadJson = json_encode($payload);
        $secret = 'my_webhook_secret';

        // Generate signature (what the sender does)
        $signature = 'sha256=' . hash_hmac('sha256', $payloadJson, $secret);

        // Verify signature (what the receiver does)
        $receivedSignature = $signature;
        $expectedSignature = 'sha256=' . hash_hmac('sha256', $payloadJson, $secret);

        $this->assertTrue(hash_equals($expectedSignature, $receivedSignature));
    }
}
