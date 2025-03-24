<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class ShareInertiaData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            Inertia::share([
                'auth' => [
                    'user' => Auth::user()->only(['id', 'name', 'email', 'role']),
                    'notifications_count' => Auth::user()->notifications()->where('leido', false)->count(),
                    'notifications' => Auth::user()
                        ->notifications()
                        ->latest()
                        ->take(5)
                        ->get(['id', 'mensaje', 'leido', 'created_at'])
                ]
            ]);
        }

        return $next($request);
    }
}
