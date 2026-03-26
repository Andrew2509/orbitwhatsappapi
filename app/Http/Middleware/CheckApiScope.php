<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiScope
{
    /**
     * Handle an incoming request.
     *
     * @param  string  ...$scopes  Required scopes (at least one must match)
     */
    public function handle(Request $request, Closure $next, string ...$scopes): Response
    {
        $apiKey = $request->input('_api_key');

        // If no API key in request, allow (handled by other middleware)
        if (!$apiKey) {
            return $next($request);
        }

        // Check if API key has any of the required scopes
        if (!$apiKey->hasAnyScope($scopes)) {
            return response()->json([
                'message_status' => 'Error',
                'error' => 'Insufficient permissions. Required scope: ' . implode(' or ', $scopes),
                'your_scopes' => $apiKey->scopes ?? ['*'],
            ], 403);
        }

        return $next($request);
    }
}
