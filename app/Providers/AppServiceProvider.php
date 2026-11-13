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
            Limit::perMinute(1800)->by($request->user()?->id ?: $request->ip())
        );

        RateLimiter::for('qr-orders', function (Request $request) {
            $tableQrCode = strtolower((string) $request->input('table_qr_code', 'missing'));

            return [
                Limit::perMinute(300)->by('qr-order-ip:' . $request->ip()),
                Limit::perMinute(60)->by('qr-order-table:' . $tableQrCode),
            ];
        });
    }
}
