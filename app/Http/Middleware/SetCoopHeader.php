<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCoopHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Generate nonce for CSP
        $nonce = base64_encode(random_bytes(16));
        $request->attributes->set('csp_nonce', $nonce);
        view()->share('csp_nonce', $nonce);
        \Illuminate\Support\Facades\Vite::useCspNonce($nonce);
        config(['app.csp_nonce' => $nonce]); // Extra fallback for global access

        $response = $next($request);

        // Define CSP based on environment
        if (app()->isLocal()) {
            // Permissive CSP for local development to ensure Vite, Cookiebot, and other tools work without friction
            $csp = "default-src 'self' http: https: data: blob: 'unsafe-inline' 'unsafe-eval'; " .
                   "script-src 'self' 'unsafe-inline' 'unsafe-eval' 'nonce-{$nonce}' 'strict-dynamic' https: http:; " .
                   "style-src 'self' 'unsafe-inline' https: http:; " .
                   "img-src 'self' data: blob: https: http:; " .
                   "font-src 'self' data: https: http:; " .
                   "connect-src 'self' https: http: ws: wss:; " .
                   "frame-src 'self' https: http:; " .
                   "base-uri 'self'; " .
                   "form-action 'self' https: http:; " .
                   "frame-ancestors 'none'; ";
        } else {
            // Strict CSP for Production (PCI & SOC 2 Compliance)
            $csp = "default-src 'none'; " .
                   "script-src 'self' 'unsafe-inline' 'unsafe-eval' 'nonce-{$nonce}' 'strict-dynamic' https:; " .
                   "style-src 'self' 'unsafe-inline' https://fonts.bunny.net https://fonts.googleapis.com; " .
                   "img-src 'self' data: blob: https: https://www.google-analytics.com https://consent.cookiebot.com https://consentcdn.cookiebot.com; " .
                   "font-src 'self' https://fonts.bunny.net https://fonts.gstatic.com https://vercel.live https://assets.vercel.com; " .
                   "connect-src 'self' http://76.13.20.150:3005 ws://76.13.20.150:3005 https://bot.orbitwaapi.site https://*.firebaseio.com https://*.googleapis.com https://*.google-analytics.com https://*.analytics.google.com https://*.doubleclick.net https://stats.g.doubleclick.net https://analytics.google.com https://accounts.google.com https://www.gstatic.com https://cdn.jsdelivr.net https://consent.cookiebot.com https://consentcdn.cookiebot.com https://app.sandbox.midtrans.com https://app.midtrans.com wss://*.pusher.com https://*.pusher.com https://cloudflareinsights.com https://static.cloudflareinsights.com; " .
                   "frame-src 'self' https://www.google.com https://*.firebaseapp.com https://accounts.google.com https://consent.cookiebot.com https://consentcdn.cookiebot.com https://app.sandbox.midtrans.com https://app.midtrans.com https://vercel.live/ https://nativesoft.com https://client.scalar.com https://scalar.com; " .
                   "base-uri 'self'; " .
                   "form-action 'self' https://app.sandbox.midtrans.com https://app.midtrans.com; " .
                   "frame-ancestors 'none'; ";
        }

        // Security Headers for PCI & SOC 2 Compliance
        $headers = [
            'X-Frame-Options' => 'SAMEORIGIN',
            'X-Content-Type-Options' => 'nosniff',
            'X-XSS-Protection' => '1; mode=block',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=(), payment=(self "https://app.sandbox.midtrans.com" "https://app.midtrans.com")',
            'Content-Security-Policy' => $csp,
        ];

        foreach ($headers as $key => $value) {
            $response->headers->set($key, $value);
        }

        return $response;
    }
}
