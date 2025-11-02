<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerMiddleware
{
    /**
     * Handle an incoming request.
     * Only allow non-admin users (customers) to access
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Check if user is admin
        if (auth()->user()->is_admin) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Admin tidak dapat melakukan pemesanan. Silakan gunakan akun customer.');
        }

        return $next($request);
    }
}
