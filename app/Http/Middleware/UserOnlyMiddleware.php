<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserOnlyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // If user is an admin or super_admin, they should not be in the user dashboard
            if (in_array($user->role, ['admin', 'super_admin'])) {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'Administrator tidak diizinkan mengakses dashboard pengguna.');
            }
        }

        return $next($request);
    }
}
