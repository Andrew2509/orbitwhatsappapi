<?php

namespace App\Services;

use App\Models\Webhook;
use App\Models\WebhookLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookDispatcher
{
    /**
     * Dispatch webhook to all subscribed endpoints for a given event
     */
    public static function dispatch(int $userId, string $event, array $data): void
    {
        $webhooks = Webhook::where('user_id', $userId)
            ->where('is_active', true)
            ->get()
            ->filter(fn($webhook) => $webhook->subscribesTo($event));

        foreach ($webhooks as $webhook) {
            self::send($webhook, $event, $data);
        }
    }

    /**
     * Send webhook to a specific endpoint with retry logic
     */
    public static function send(Webhook $webhook, string $event, array $data, int $attempt = 1): WebhookLog
    {
        $payload = [
            'event' => $event,
            'timestamp' => now()->toIso8601String(),
            'webhook_id' => $webhook->id,
            'data' => $data,
        ];

        // Create log entry
        $log = WebhookLog::create([
            'webhook_id' => $webhook->id,
            'event' => $event,
            'payload' => $payload,
            'attempt' => $attempt,
            'status' => 'pending',
        ]);

        $startTime = microtime(true);

        try {
            // Prepare headers with signature
            $headers = [
                'Content-Type' => 'application/json',
                'X-Webhook-Event' => $event,
                'X-Webhook-Timestamp' => $payload['timestamp'],
                'X-Webhook-Delivery' => (string) $log->id,
            ];

            // Add HMAC signature if secret exists (GitHub/Stripe style)
            if ($webhook->secret) {
                $payloadJson = json_encode($payload);
                $signature = hash_hmac('sha256', $payloadJson, $webhook->secret);
                
                // Multiple signature formats for compatibility
                $headers['X-Hub-Signature-256'] = 'sha256=' . $signature;
                $headers['X-Webhook-Signature'] = $signature;
            }

            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::timeout(10)
                ->withHeaders($headers)
                ->post($webhook->url, $payload);

            $durationMs = (int) ((microtime(true) - $startTime) * 1000);

            if ($response->successful()) {
                $log->update([
                    'response_code' => $response->status(),
                    'response_body' => substr($response->body(), 0, 1000),
                    'duration_ms' => $durationMs,
                    'status' => 'success',
                ]);

                $webhook->markTriggered();
            } else {
                $log->update([
                    'response_code' => $response->status(),
                    'response_body' => substr($response->body(), 0, 1000),
                    'duration_ms' => $durationMs,
                    'status' => 'failed',
                    'error_message' => 'HTTP ' . $response->status(),
                ]);

                $webhook->incrementFailure();

                // Retry if not exceeded max retries
                if ($attempt < ($webhook->max_retries ?? 3)) {
                    self::scheduleRetry($webhook, $event, $data, $attempt + 1);
                }
            }
        } catch (\Exception $e) {
            $durationMs = (int) ((microtime(true) - $startTime) * 1000);

            $log->update([
                'duration_ms' => $durationMs,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            $webhook->incrementFailure();

            Log::error("Webhook dispatch failed", [
                'webhook_id' => $webhook->id,
                'event' => $event,
                'error' => $e->getMessage(),
            ]);

            // Retry if not exceeded max retries
            if ($attempt < ($webhook->max_retries ?? 3)) {
                self::scheduleRetry($webhook, $event, $data, $attempt + 1);
            }
        }

        return $log;
    }

    /**
     * Schedule a retry with exponential backoff
     */
    protected static function scheduleRetry(Webhook $webhook, string $event, array $data, int $attempt): void
    {
        // Exponential backoff: 10s, 30s, 90s, etc.
        $delaySeconds = 10 * pow(3, $attempt - 1);

        // For now, we'll use a simple delay. In production, use a queue job
        // ProcessWebhookRetryJob::dispatch($webhook, $event, $data, $attempt)->delay(now()->addSeconds($delaySeconds));

        Log::info("Webhook retry scheduled", [
            'webhook_id' => $webhook->id,
            'event' => $event,
            'attempt' => $attempt,
            'delay_seconds' => $delaySeconds,
        ]);
    }

    /**
     * Send a test webhook
     */
    public static function sendTest(Webhook $webhook): WebhookLog
    {
        $testData = [
            'message' => 'This is a test webhook from Orbit WhatsApp API',
            'test' => true,
        ];

        return self::send($webhook, 'test', $testData);
    }
}
