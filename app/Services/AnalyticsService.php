<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnalyticsService
{
    protected string $measurementId;
    protected string $apiSecret;
    protected string $apiUrl = 'https://www.google-analytics.com/mp/collect';

    public function __construct()
    {
        $this->measurementId = config('analytics.ga_measurement_id', env('GA_MEASUREMENT_ID'));
        $this->apiSecret = config('analytics.ga_api_secret', env('GA_API_SECRET'));
    }

    /**
     * Send an event to Google Analytics 4 via Measurement Protocol.
     *
     * @param string $eventName The name of the event (e.g., 'purchase', 'api_call')
     * @param array $params Additional parameters for the event
     * @param string|null $clientId Unique identifier for the client (default to session ID)
     * @param string|null $userId Unique identifier for the authenticated user
     * @return bool
     */
    public function sendEvent(string $eventName, array $params = [], ?string $clientId = null, ?string $userId = null): bool
    {
        // Respect Cookiebot consent
        if (!$this->hasConsent()) {
            return false;
        }

        if (empty($this->measurementId) || empty($this->apiSecret)) {
            // Log::warning('GA4 Analytics: Measurement ID or API Secret is missing.');
            return false;
        }

        // GA4 requires a client_id.
        $clientId = $clientId ?? session()->getId();
        if (empty($clientId)) {
             $clientId = 'system_backend_' . uniqid();
        }

        // Prepare the payload
        $payload = [
            'client_id' => $clientId,
            'events' => [
                [
                    'name' => $eventName,
                    'params' => $params,
                ]
            ],
        ];

        // Add user_id if provided
        if ($userId) {
            $payload['user_id'] = $userId;
        }

        // Add timestamp for accuracy (optional but recommended)
        $payload['timestamp_micros'] = (int) (microtime(true) * 1000000);

        try {
            $response = Http::post($this->apiUrl . "?measurement_id={$this->measurementId}&api_secret={$this->apiSecret}", $payload);

            // GA4 Measurement Protocol often returns 204 No Content for success, or 200 OK.
            // It rarely returns 4xx/5xx unless the request is malformed at the HTTP level.
            // Validity of the event data is not validated synchronously.
            if ($response->successful()) {
                return true;
            }

            Log::error('GA4 Analytics Error: ' . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error('GA4 Analytics Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if the user has consented to statistics cookies via Cookiebot.
     */
    protected function hasConsent(): bool
    {
        // Bypass for console commands unless we are running tests
        if (app()->runningInConsole() && !app()->environment('testing')) {
            return true;
        }

        $consentValue = request()->cookie('CookieConsent');

        if (!$consentValue) {
            return false;
        }

        // The user is not within a region that requires consent (-1)
        if ($consentValue === "-1") {
            return true;
        }

        try {
            // Cookiebot serializes consent as a JS-like object string.
            // We need to convert it to valid JSON for PHP's json_decode.
            // Example: {preferences:true,statistics:true,marketing:true,ver:1,utc:1739800000000}

            // 1. URL Decode (Laravel usually does this, but being safe)
            $decoded = urldecode($consentValue);

            // 2. Wrap keys in quotes
            $json = preg_replace('/([{\[,])\s*([a-zA-Z0-9_]+?):/', '$1"$2":', $decoded);
            // 3. Handle values (booleans/numbers)
            $json = preg_replace('/\s*:\s*([a-zA-Z0-9_]+?)([}\[,])/', ':"$1"$2', $json);

            // Clean up any double quotes if already present or single quotes
            $json = str_replace("'", '"', $json);

            $consent = json_decode($json);

            if (isset($consent->statistics)) {
                return filter_var($consent->statistics, FILTER_VALIDATE_BOOLEAN);
            }
        } catch (\Exception $e) {
            Log::warning('Cookiebot consent parsing error: ' . $e->getMessage());
        }

        return false;
    }

    /**
     * Helper to track payment events.
     */
    public function trackPayment(float $amount, string $currency = 'IDR', string $transactionId = ''): bool
    {
        return $this->sendEvent('purchase', [
            'transaction_id' => $transactionId,
            'value' => $amount,
            'currency' => $currency,
        ]);
    }

    /**
     * Track a message sent event.
     */
    public function trackMessageSent(string $type, string $status, ?string $userId = null): bool
    {
        return $this->sendEvent('message_sent', [
            'message_type' => $type,
            'status' => $status,
        ], null, $userId);
    }

    /**
     * Track a device connection event.
     */
    public function trackDeviceConnected(string $deviceId, ?string $userId = null): bool
    {
        return $this->sendEvent('device_connected', [
            'device_id' => $deviceId,
        ], null, $userId);
    }

    /**
     * Track a device disconnection event.
     */
    public function trackDeviceDisconnected(string $deviceId, string $reason = 'unknown', ?string $userId = null): bool
    {
        return $this->sendEvent('device_disconnected', [
            'device_id' => $deviceId,
            'reason' => $reason,
        ], null, $userId);
    }
}
