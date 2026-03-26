<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use App\Models\Application;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        // Method 1: Bearer Token (existing)
        $token = $request->bearerToken();
        
        // Method 2: Form data with appkey + authkey
        $appKey = $request->input('appkey');
        $authKey = $request->input('authkey');

        // Try Bearer Token first
        if ($token) {
            return $this->authenticateWithToken($request, $next, $token);
        }

        // Try appkey + authkey
        if ($appKey && $authKey) {
            return $this->authenticateWithAppKey($request, $next, $appKey, $authKey);
        }

        return response()->json([
            'message_status' => 'Error',
            'error' => 'API authentication required. Use Bearer token or appkey + authkey',
        ], 401);
    }

    /**
     * Authenticate using Bearer token.
     */
    protected function authenticateWithToken(Request $request, Closure $next, string $token): Response
    {
        $apiKey = ApiKey::where('key', $token)
            ->where('is_active', true)
            ->first();

        if (!$apiKey) {
            $error = 'Invalid or inactive API key';
            
            // Check if it looks like a JWT
            if (count(explode('.', $token)) === 3) {
                $error .= '. Hint: You appear to be using a JWT token. This endpoint requires a permanent API Key (orbit_live_...).';
            }

            return response()->json([
                'message_status' => 'Error',
                'error' => $error,
            ], 401);
        }

        // Validate API key
        $validationResult = $this->validateApiKey($apiKey, $request);
        if ($validationResult) {
            return $validationResult;
        }

        $apiKey->markAsUsed();
        $request->setUserResolver(fn() => $apiKey->user);
        $request->merge(['_api_key' => $apiKey]);
        
        // Final check if user is suspended
        if ($apiKey->user->is_suspended) {
            return response()->json([
                'message_status' => 'Error',
                'error' => 'Your account has been suspended. ' . ($apiKey->user->suspension_reason ? "Reason: {$apiKey->user->suspension_reason}" : "Contact administrator."),
            ], 403);
        }

        return $next($request);
    }

    /**
     * Authenticate using appkey + authkey.
     */
    protected function authenticateWithAppKey(Request $request, Closure $next, string $appKey, string $authKey): Response
    {
        // Validate Application (appkey)
        $application = Application::where('app_key', $appKey)
            ->where('is_active', true)
            ->first();

        if (!$application) {
            return response()->json([
                'message_status' => 'Error',
                'error' => 'Invalid or inactive app key',
            ], 401);
        }

        // Validate API Key (authkey)
        $apiKey = ApiKey::where('key', $authKey)
            ->where('is_active', true)
            ->where('user_id', $application->user_id)
            ->first();

        if (!$apiKey) {
            return response()->json([
                'message_status' => 'Error',
                'error' => 'Invalid or inactive auth key',
            ], 401);
        }

        // Validate API key (IP, expiration)
        $validationResult = $this->validateApiKey($apiKey, $request);
        if ($validationResult) {
            return $validationResult;
        }

        // Mark as used
        $apiKey->markAsUsed();
        $application->update(['last_used_at' => now()]);
        $application->increment('messages_count');

        // Set the user on the request
        $request->setUserResolver(fn() => $apiKey->user);
        $request->merge([
            '_application' => $application,
            '_api_key' => $apiKey,
        ]);

        // Final check if user is suspended
        if ($apiKey->user->is_suspended) {
            return response()->json([
                'message_status' => 'Error',
                'error' => 'Your account has been suspended. ' . ($apiKey->user->suspension_reason ? "Reason: {$apiKey->user->suspension_reason}" : "Contact administrator."),
            ], 403);
        }
        
        return $next($request);
    }

    /**
     * Validate API key (expiration and IP whitelist).
     */
    protected function validateApiKey(ApiKey $apiKey, Request $request): ?Response
    {
        // Check if API key has expired
        if ($apiKey->isExpired()) {
            return response()->json([
                'message_status' => 'Error',
                'error' => 'API key has expired',
            ], 401);
        }

        // Check IP whitelist
        if (!$apiKey->isIpAllowed($request->ip())) {
            return response()->json([
                'message_status' => 'Error',
                'error' => 'Access denied from this IP address',
                'your_ip' => $request->ip(),
            ], 403);
        }

        return null;
    }
}
