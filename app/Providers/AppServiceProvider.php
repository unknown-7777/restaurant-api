<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

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
        // Restaurants often serve many guests behind one Wi-Fi IP. Give the
        // public API enough shared headroom while keeping a firm per-IP cap.
        RateLimiter::for('api', fn (Request $request) =>
            Limit::perMinute(600)->by($request->user()?->id ?: $request->ip())
        );
    }
}
