<?php

namespace App\Jobs;

use App\Models\Webhook;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DispatchWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(
        public Webhook $webhook,
        public string $event,
        public array $data
    ) {}

    public function handle(): void
    {
        try {
            $payload = [
                'event' => $this->event,
                'data' => $this->data,
                'timestamp' => now()->toIso8601String(),
            ];

            // Add signature if secret exists
            $headers = ['Content-Type' => 'application/json'];
            if ($this->webhook->secret) {
                $signature = hash_hmac('sha256', json_encode($payload), $this->webhook->secret);
                $headers['X-Webhook-Signature'] = $signature;
            }

            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::timeout(30)
                ->withHeaders($headers)
                ->post($this->webhook->url, $payload);

            if ($response->successful()) {
                $this->webhook->markTriggered();
                Log::info('Webhook dispatched successfully', [
                    'webhook_id' => $this->webhook->id,
                    'event' => $this->event
                ]);
            } else {
                throw new \Exception("HTTP {$response->status()}");
            }
        } catch (\Exception $e) {
            Log::error('Webhook dispatch failed', [
                'webhook_id' => $this->webhook->id,
                'error' => $e->getMessage()
            ]);
            
            $this->webhook->incrementFailure();
            
            if ($this->attempts() >= $this->tries) {
                Log::warning('Webhook max retries exceeded', ['webhook_id' => $this->webhook->id]);
            } else {
                throw $e; // Retry
            }
        }
    }
}
