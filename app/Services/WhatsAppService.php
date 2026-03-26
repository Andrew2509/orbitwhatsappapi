<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $baseUrl;
    protected string $secret;

    public function __construct()
    {
        $this->baseUrl = config('services.whatsapp.url', 'https://bot.orbitwaapi.dpdns.org');
        $this->secret = config('services.whatsapp.secret', 'secret');
    }

    /**
     * Get default headers for API requests
     */
    protected function headers(): array
    {
        return [
            'X-WhatsApp-Secret' => $this->secret,
            'Accept' => 'application/json',
        ];
    }

    /**
     * Initialize a new WhatsApp session for a device
     */
    public function initSession(int $deviceId, int $userId, string $name): array
    {
        try {
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withHeaders($this->headers())->post("{$this->baseUrl}/device/{$deviceId}/connect", [
                'userId' => $userId,
                'name' => $name,
            ]);

            return $response->json() ?? ['success' => false, 'error' => 'Empty response from service'];
        } catch (\Exception $e) {
            Log::error('WhatsApp initSession failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get QR code for device
     */
    public function getQR(int $deviceId): ?string
    {
        try {
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withHeaders($this->headers())->get("{$this->baseUrl}/device/{$deviceId}/qr");
            $data = $response->json();

            return isset($data['success']) && $data['success'] ? $data['qr'] : null;
        } catch (\Exception $e) {
            Log::error('WhatsApp getQR failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Request a pairing code for a phone number
     */
    public function requestPairingCode(int $deviceId, string $phone): array
    {
        try {
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withHeaders($this->headers())
                ->timeout(60) // Increase timeout for pairing code since it depends on WA servers
                ->post("{$this->baseUrl}/device/{$deviceId}/pairing-code", [
                    'phone' => $phone,
                ]);

            return $response->json() ?? ['success' => false, 'error' => 'Empty response from service'];
        } catch (\Exception $e) {
            Log::error('WhatsApp requestPairingCode failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get device session status
     */
    public function getStatus(int $deviceId): array
    {
        try {
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withHeaders($this->headers())->get("{$this->baseUrl}/device/{$deviceId}/status");
            return $response->json() ?? ['success' => false, 'status' => 'error'];
        } catch (\Exception $e) {
            return ['success' => false, 'status' => 'error', 'error' => $e->getMessage()];
        }
    }

    /**
     * Disconnect device session
     */
    public function disconnect(int $deviceId): array
    {
        try {
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withHeaders($this->headers())->post("{$this->baseUrl}/device/{$deviceId}/disconnect");
            return $response->json() ?? ['success' => false, 'error' => 'Empty response'];
        } catch (\Exception $e) {
            Log::error('WhatsApp disconnect failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send a message via WhatsApp
     */
    public function sendMessage(int $deviceId, string $phone, string $message, string $type = 'text', ?string $mediaUrl = null): array
    {
        $payload = [
            'deviceId' => $deviceId,
            'phone' => $phone,
            'message' => $message,
            'type' => $type,
            'mediaUrl' => $mediaUrl,
        ];

        try {
            Log::info('WhatsApp Service: Sending request', ['url' => "{$this->baseUrl}/message/send", 'payload' => $payload]);

            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withHeaders($this->headers())->post("{$this->baseUrl}/message/send", $payload);

            $data = $response->json();
            
            Log::info('WhatsApp Service: Received response', [
                'status' => $response->status(),
                'body' => $data
            ]);

            // Standardize: if gateway returns success code but no 'success' key, assume true
            if ($response->successful() && !isset($data['success'])) {
                $data['success'] = true;
            }

            return $data ?? ['success' => false, 'error' => 'Empty response'];
        } catch (\Exception $e) {
            Log::error('WhatsApp sendMessage failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Check if WhatsApp service is healthy
     */
    public function healthCheck(): bool
    {
        try {
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withHeaders($this->headers())->timeout(5)->get("{$this->baseUrl}/health");
            return $response->ok();
        } catch (\Exception $e) {
            return false;
        }
    }
}
