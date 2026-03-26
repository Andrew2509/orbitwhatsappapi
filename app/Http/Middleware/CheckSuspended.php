<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSuspended
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->is_suspended) {
            $reason = Auth::user()->suspension_reason;
            $message = 'Akun Anda telah dinonaktifkan. ' . ($reason ? "Alasan: {$reason}." : "Hubungi administrator.");

            if ($request->expectsJson()) {
                return response()->json([
                    'message_status' => 'Error',
                    'error' => $message,
                ], 403);
            }

            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('error', $message);
        }

        return $next($request);
    }
}
