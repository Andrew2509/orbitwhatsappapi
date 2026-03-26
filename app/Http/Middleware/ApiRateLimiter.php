<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ApiRateLimiter
{
    /**
     * Default rate limit per minute.
     */
    protected int $defaultLimit = 60;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ?int $maxAttempts = null): Response
    {
        $key = $this->resolveRequestKey($request);
        $limit = $maxAttempts ?? $this->getLimit($request);

        if (RateLimiter::tooManyAttempts($key, $limit)) {
            return $this->buildTooManyAttemptsResponse($key, $limit);
        }

        RateLimiter::hit($key, 60);

        $response = $next($request);

        return $this->addRateLimitHeaders($response, $key, $limit);
    }

    /**
     * Resolve the request key for rate limiting.
     */
    protected function resolveRequestKey(Request $request): string
    {
        $user = $request->user();
        
        if ($user) {
            return 'api_rate_limit:user:' . $user->id;
        }

        return 'api_rate_limit:ip:' . $request->ip();
    }

    /**
     * Get the rate limit for the current request.
     */
    protected function getLimit(Request $request): int
    {
        $user = $request->user();

        if (!$user) {
            return $this->defaultLimit;
        }

        // Check if user has custom limit from subscription plan
        $plan = $user->currentPlan();
        
        if ($plan && isset($plan->features['api_rate_limit'])) {
            return (int) $plan->features['api_rate_limit'];
        }

        return $this->defaultLimit;
    }

    /**
     * Build the response for too many attempts.
     */
    protected function buildTooManyAttemptsResponse(string $key, int $limit): Response
    {
        $retryAfter = RateLimiter::availableIn($key);

        return response()->json([
            'message_status' => 'Error',
            'error' => 'Too many requests. Please slow down.',
            'retry_after' => $retryAfter,
        ], 429)->withHeaders([
            'X-RateLimit-Limit' => $limit,
            'X-RateLimit-Remaining' => 0,
            'Retry-After' => $retryAfter,
            'X-RateLimit-Reset' => now()->addSeconds($retryAfter)->timestamp,
        ]);
    }

    /**
     * Add rate limit headers to the response.
     */
    protected function addRateLimitHeaders(Response $response, string $key, int $limit): Response
    {
        $remaining = RateLimiter::remaining($key, $limit);

        $response->headers->set('X-RateLimit-Limit', $limit);
        $response->headers->set('X-RateLimit-Remaining', max(0, $remaining));

        return $response;
    }
}
