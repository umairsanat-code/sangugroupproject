<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

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
        // Rate limiting for the public AI chatbot endpoints, keyed by visitor IP.
        RateLimiter::for('chat', function (Request $request) {
            return [
                Limit::perMinute(12)->by('chat-min:' . $request->ip()),
                Limit::perDay(300)->by('chat-day:' . $request->ip()),
            ];
        });
    }
}
