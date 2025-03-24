<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Inertia::share([
            'autorizado' => [
                'user' => Auth::check() ? Auth::user()->only(['id', 'name', 'email', 'role']) : null,
                'notifications_count' => Auth::check()
                    ? Auth::user()->notifications()->where('leido', false)->count()
                    : 0
            ]
        ]);
    }
}
