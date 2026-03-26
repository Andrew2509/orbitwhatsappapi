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

        // Traditional Strict CSP (supports un-nonced server-injected scripts like /c7qb/)
        // Includes hashes for specific legitimate inline blocks provided by the browser
        $csp = "default-src 'none'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' 'nonce-{$nonce}' https://www.gstatic.com https://www.google.com https://cdn.jsdelivr.net https://www.googletagmanager.com https://apis.google.com https://*.firebaseapp.com https://consent.cookiebot.com https://consentcdn.cookiebot.com https://www.google-analytics.com https://app.sandbox.midtrans.com https://app.midtrans.com https://vercel.live https://*.vercel.app https://cdn.tailwindcss.com https://static.cloudflareinsights.com; " .
               "style-src 'self' 'unsafe-inline' https://fonts.bunny.net https://fonts.googleapis.com; " .
               "img-src 'self' data: blob: https: https://www.google-analytics.com https://consent.cookiebot.com https://consentcdn.cookiebot.com; " .
               "font-src 'self' https://fonts.bunny.net https://fonts.gstatic.com https://vercel.live https://assets.vercel.com; " .
               "connect-src 'self' https://bot.orbitwaapi.site https://*.firebaseio.com https://*.googleapis.com https://*.google-analytics.com https://*.analytics.google.com https://*.doubleclick.net https://stats.g.doubleclick.net https://analytics.google.com https://accounts.google.com https://www.gstatic.com https://cdn.jsdelivr.net https://consent.cookiebot.com https://consentcdn.cookiebot.com https://app.sandbox.midtrans.com https://app.midtrans.com wss://*.pusher.com https://*.pusher.com https://cloudflareinsights.com https://static.cloudflareinsights.com; " .
               "frame-src 'self' https://www.google.com https://*.firebaseapp.com https://accounts.google.com https://consent.cookiebot.com https://consentcdn.cookiebot.com https://app.sandbox.midtrans.com https://app.midtrans.com https://vercel.live/ https://nativesoft.com https://client.scalar.com https://scalar.com; " .
               "base-uri 'self'; " .
               "form-action 'self' https://app.sandbox.midtrans.com https://app.midtrans.com; " .
               "frame-ancestors 'none'; " .
               "upgrade-insecure-requests;";

        // Security Headers for PCI & SOC 2 Compliance
        return $response->withHeaders([
            'Strict-Transport-Security' => 'max-age=63072000; includeSubDomains; preload',
            'X-Frame-Options' => 'SAMEORIGIN',
            'X-Content-Type-Options' => 'nosniff',
            'X-XSS-Protection' => '1; mode=block',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=(), payment=(self "https://app.sandbox.midtrans.com" "https://app.midtrans.com")',
            'Content-Security-Policy' => $csp,
        ]);
    }
}
